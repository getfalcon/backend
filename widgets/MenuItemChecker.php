<?php
/**
 * @package    falcon
 * @author     Hryvinskyi Volodymyr <volodymyr@hryvinskyi.com>
 * @copyright  Copyright (c) 2018. Hryvinskyi Volodymyr
 * @version    0.0.1-alpha.0.1
 */

namespace falcon\backend\widgets;

use falcon\backend\models\menu\Item;
use yii\base\InvalidConfigException;

/**
 * Class MenuItemChecker
 */
class MenuItemChecker {
	/**
	 * Check whether given menu item is currently selected.
	 *
	 * It is used in backend menu to highlight active menu item.
	 *
	 * @param Item|false $activeItem Can be false if menu item is inaccessible
	 * but was triggered directly using controller. It is a legacy code behaviour.
	 * @param Item       $item
	 * @param int        $level
	 *
	 * @return bool
	 *
	 * @throws InvalidConfigException
	 */
	public function isItemActive($activeItem, Item $item, $level) {
		$output = false;

		if ($level == 0 && $activeItem instanceof Item && $this->isActiveItemEqualOrChild($activeItem, $item)) {
			$output = true;
		}

		return $output;
	}

	/**
	 * @param Item $activeItem
	 * @param Item $item
	 *
	 * @return bool
	 *
	 * @throws InvalidConfigException
	 */
	private function isActiveItemEqualOrChild(Item $activeItem, Item $item) {
		return ($activeItem->getId() == $item->getId()) || ($item->getChildren()->get($activeItem->getId()) !== null);
	}
}
