<?php
/**
 * @package    falcon
 * @author     Hryvinskyi Volodymyr <volodymyr@hryvinskyi.com>
 * @copyright  Copyright (c) 2018. Hryvinskyi Volodymyr
 * @version    0.0.1-alpha.0.1
 */

namespace falcon\backend\models\menu\builder\command;

use falcon\backend\models\menu\builder\AbstractCommand;

/**
 * Builder command to add menu items
 * @api
 */
class Add extends AbstractCommand {
	/**
	 * List of params that command requires for execution
	 *
	 * @var string[]
	 */
	protected $_requiredParams = ["id", "title", "module", "resource"];

	/**
	 * Add command as last in the list of callbacks
	 *
	 * @param AbstractCommand $command
	 *
	 * @return $this
	 * @throws \InvalidArgumentException
	 */
	public function chain(AbstractCommand $command): parent {
		if ($command instanceof Add) {
			throw new \InvalidArgumentException("Two 'add' commands cannot have equal id (" . $command->getId() . ")");
		}

		return parent::chain($command);
	}

	/**
	 * Add missing data to item
	 *
	 * @param array $itemParams
	 *
	 * @return array
	 */
	protected function _execute(array $itemParams): array {
		foreach ($this->_data as $key => $value) {
			$itemParams[ $key ] = isset($itemParams[ $key ]) ? $itemParams[ $key ] : $value;
		}

		return $itemParams;
	}
}
