<?php
/**
 * @package    falcon
 * @author     Hryvinskyi Volodymyr <volodymyr@hryvinskyi.com>
 * @copyright  Copyright (c) 2018. Hryvinskyi Volodymyr
 * @version    0.0.1-alpha.0.1
 */

namespace falcon\backend\models\menu;

use falcon\backend\models\Menu;
use falcon\backend\models\menu\builder\AbstractCommand;
use falcon\backend\models\menu\item\Factory;

/**
 * Menu builder object. Retrieves commands (\Magento\Backend\Model\Menu\Builder\AbstractCommand)
 * to build menu (\Magento\Backend\Model\Menu)
 * @api
 * @since 100.0.2
 */
class Builder {
	/**
	 * @var AbstractCommand[]
	 */
	protected $_commands = [];

	/**
	 * @var Factory
	 */
	protected $_itemFactory;

	/**
     * @param Factory $factory
	 */
    public function __construct(Factory $factory)
    {
        $this->_itemFactory = $factory;
	}

	/**
	 * Process provided command object
	 *
	 * @param AbstractCommand $command
	 *
	 * @return $this
	 */
	public function processCommand(AbstractCommand $command): self {
		if ( ! isset($this->_commands[ $command->getId() ])) {
			$this->_commands[ $command->getId() ] = $command;
		} else {
			$this->_commands[ $command->getId() ]->chain($command);
		}

		return $this;
	}

	/**
	 * Populate menu object
	 *
	 * @param Menu $menu
	 *
	 * @return Menu
	 * @throws \yii\base\InvalidConfigException in case given parent id does not exists
	 */
	public function getResult(Menu $menu) {
		/** @var $items Item[] */
		$params = [];
		$items  = [];

		// Create menu items
		foreach ($this->_commands as $id => $command) {
			$params[ $id ] = $command->execute();
			$item          = $this->_itemFactory->create($params[ $id ]);
			$items[ $id ]  = $item;
		}

		// Build menu tree based on "parent" param
		foreach ($items as $id => $item) {
			$sortOrder = $this->_getParam($params[ $id ], 'sortOrder');
			$parentId  = $this->_getParam($params[ $id ], 'parent');
			$isRemoved = isset($params[ $id ]['removed']);

			if ($isRemoved) {
				continue;
			}
			if ( ! $parentId) {
				$menu->add($item, null, $sortOrder);
			} else {
				if ( ! isset($items[ $parentId ])) {
					throw new \OutOfRangeException(sprintf('Specified invalid parent id (%s)', $parentId));
				}
				if (isset($params[ $parentId ]['removed'])) {
					continue;
				}
				$items[ $parentId ]->getChildren()->add($item, null, $sortOrder);
			}
		}

		return $menu;
	}

	/**
	 * Retrieve param by name or default value
	 *
	 * @param array  $params
	 * @param string $paramName
	 * @param mixed  $defaultValue
	 *
	 * @return mixed
	 */
	protected function _getParam($params, $paramName, $defaultValue = null) {
		return isset($params[ $paramName ]) ? $params[ $paramName ] : $defaultValue;
	}
}
