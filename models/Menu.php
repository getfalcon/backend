<?php
/**
 * @package    falcon
 * @author     Hryvinskyi Volodymyr <volodymyr@hryvinskyi.com>
 * @copyright  Copyright (c) 2018. Hryvinskyi Volodymyr
 * @version    0.0.1-alpha.0.1
 */

namespace app\modules\backend\models;

use app\modules\backend\models\menu\Item;
use app\modules\backend\models\menu\Item\Factory;
use falcon\core\helpers\Serialize;
use yii\base\InvalidConfigException;

/**
 * Backend menu model
 *
 * @api
 */
class Menu extends \ArrayObject {
	/**
	 * Path in tree structure
	 *
	 * @var string
	 */
	protected $_path = '';

	/**
	 * @var Factory
	 */
	private $menuItemFactory;

	/**
	 * Menu constructor
	 *
	 * @param string       $pathInMenuStructure
	 * @param Factory|null $menuItemFactory
	 *
	 * @throws InvalidConfigException
	 */
	public function __construct(
		$pathInMenuStructure = '', Factory $menuItemFactory = null
	) {
		if ($pathInMenuStructure) {
			$this->_path = $pathInMenuStructure . '/';
		}
		$this->setIteratorClass(\app\modules\backend\models\menu\Iterator::class);
		$this->menuItemFactory = $menuItemFactory ?: \Yii::$container->get(Factory::class);
	}

	/**
	 * Move menu item
	 *
	 * @param string   $itemId
	 * @param string   $toItemId
	 * @param int|null $sortIndex
	 *
	 * @throws InvalidConfigException
	 * @throws \InvalidArgumentException
	 */
	public function move(string $itemId, string $toItemId, $sortIndex = null) {
		$item = $this->get($itemId);
		if ($item === null) {
			throw new \InvalidArgumentException("Item with identifier {$itemId} does not exist");
		}
		$this->remove($itemId);
		$this->add($item, $toItemId, $sortIndex);
	}

	/**
	 * Retrieve menu item by id
	 *
	 * @param string $itemId
	 *
	 * @return Item|null
	 * @throws InvalidConfigException
	 */
	public function get($itemId) {
		$result = null;
		/** @var Item $item */
		foreach ($this as $item) {
			if ($item->getId() == $itemId) {
				$result = $item;
				break;
			}

			if ($item->hasChildren() && ($result = $item->getChildren()->get($itemId))) {
				break;
			}
		}

		return $result;
	}

	/**
	 * Remove menu item by id
	 *
	 * @param string $itemId
	 *
	 * @return bool
	 * @throws InvalidConfigException
	 */
	public function remove($itemId) {
		$result = false;
		/** @var Item $item */
		foreach ($this as $key => $item) {
			if ($item->getId() == $itemId) {
				unset($this[ $key ]);
				$result = true;
				\Yii::info(sprintf('Remove on item with id %s was processed', $item->getId()));
				break;
			}

			if ($item->hasChildren() && ($result = $item->getChildren()->remove($itemId))) {
				break;
			}
		}

		return $result;
	}

	/**
	 * Add child to menu item
	 *
	 * @param Item   $item
	 * @param string $parentId
	 * @param int    $index
	 *
	 * @return void
	 *
	 * @throws \InvalidArgumentException
	 * @throws InvalidConfigException
	 */
	public function add(Item $item, $parentId = null, $index = null) {
		if ($parentId !== null) {
			$parentItem = $this->get($parentId);
			if ($parentItem === null) {
				throw new \InvalidArgumentException("Item with identifier {$parentId} does not exist");
			}
			$parentItem->getChildren()->add($item, null, $index);
		} else {
			$index = intval($index);
			if ( ! isset($this[ $index ])) {
				$this->offsetSet($index, $item);
				\Yii::info(sprintf('Add of item with id %s was processed', $item->getId()));
			} else {
				$this->add($item, $parentId, $index + 1);
			}
		}
	}

	/**
	 * Change order of an item in its parent menu
	 *
	 * @param string $itemId
	 * @param int    $position
	 *
	 * @return bool
	 *
	 * @throws InvalidConfigException
	 */
	public function reorder($itemId, $position) {
		$result = false;
		/** @var Item $item */
		foreach ($this as $key => $item) {
			if ($item->getId() == $itemId) {
				unset($this[ $key ]);
				$this->add($item, null, $position);
				$result = true;
				break;
			} else if ($item->hasChildren() && $result = $item->getChildren()->reorder($itemId, $position)) {
				break;
			}
		}

		return $result;
	}

	/**
	 * Check whether provided item is last in list
	 *
	 * @param Item $item
	 *
	 * @return bool
	 */
	public function isLast(Item $item) {
		return $this->offsetGet(max(array_keys($this->getArrayCopy())))->getId() == $item->getId();
	}

	/**
	 * Find first menu item that user is able to access
	 *
	 * @return Item|null
	 *
	 * @throws InvalidConfigException
	 */
	public function getFirstAvailable() {
		$result = null;
		/** @var Item $item */
		foreach ($this as $item) {
			if ($item->isAllowed() && ! $item->isDisabled()) {
				if ($item->hasChildren()) {
					$result = $item->getChildren()->getFirstAvailable();
					if (false == ($result === null)) {
						break;
					}
				} else {
					$result = $item;
					break;
				}
			}
		}

		return $result;
	}

	/**
	 * Get parent items by item id
	 *
	 * @param string $itemId
	 *
	 * @return Item[]
	 *
	 * @throws InvalidConfigException
	 */
	public function getParentItems($itemId) {
		$parents = [];
		$this->_findParentItems($this, $itemId, $parents);

		return array_reverse($parents);
	}

	/**
	 * Find parent items
	 *
	 * @param \app\modules\backend\models\Menu $menu
	 * @param string                           $itemId
	 * @param array                            &$parents
	 *
	 * @return bool
	 *
	 * @throws InvalidConfigException
	 */
	protected function _findParentItems($menu, $itemId, &$parents) {
		/** @var Item $item */
		foreach ($menu as $item) {
			if ($item->getId() == $itemId) {
				return true;
			}
			if ($item->hasChildren()) {
				if ($this->_findParentItems($item->getChildren(), $itemId, $parents)) {
					$parents[] = $item;

					return true;
				}
			}
		}

		return false;
	}

	/**
	 * Serialize menu
	 *
	 * @return string
	 */
	public function serialize() {
		return Serialize::serialize($this->toArray());
	}

	/**
	 * Get menu data represented as an array
	 *
	 * @return array
	 */
	public function toArray() {
		$data = [];
		foreach ($this as $item) {
			$data[] = $item->toArray();
		}

		return $data;
	}

	/**
	 * Unserialize menu
	 *
	 * @param string $serialized
	 *
	 * @return void
	 *
	 * @throws InvalidConfigException
	 */
	public function unserialize($serialized) {
		$data = Serialize::unserialize($serialized);
		$this->populateFromArray($data);
	}

	/**
	 * Populate the menu with data from array
	 *
	 * @param array $data
	 *
	 * @return void
	 *
	 * @throws InvalidConfigException
	 */
	public function populateFromArray(array $data) {
		$items = [];
		foreach ($data as $itemData) {
			$item    = $this->menuItemFactory->create($itemData);
			$items[] = $item;
		}
		$this->exchangeArray($items);
	}
}
