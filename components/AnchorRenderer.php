<?php
/**
 * @package    falcon
 * @author     Hryvinskyi Volodymyr <volodymyr@hryvinskyi.com>
 * @copyright  Copyright (c) 2018. Hryvinskyi Volodymyr
 * @version    0.0.1-alpha.0.1
 */

namespace falcon\backend\components;

use falcon\backend\models\menu\Item;
use yii\helpers\Html;

/**
 * Class AnchorRenderer
 */
class AnchorRenderer {
	/**
	 * @var MenuItemChecker
	 */
	private $menuItemChecker;

	/**
	 * @param MenuItemChecker $menuItemChecker
	 */
	public function __construct(MenuItemChecker $menuItemChecker) {
		$this->menuItemChecker = $menuItemChecker;
	}

	/**
	 * Render menu item anchor.
	 *
	 *  It is used in backend menu to render anchor menu.
	 *
	 * @param Item|false $activeItem Can be false if menu item is inaccessible
	 * but was triggered directly using controller. It is a legacy code behaviour.
	 * @param Item       $menuItem
	 * @param int        $level
	 *
	 * @return string
	 *
	 * @throws \yii\base\InvalidConfigException
	 */
	public function renderAnchor($activeItem, Item $menuItem, $level) {
		if ($level == 1 && $menuItem->getUrl() == '#') {
			$output = '';
			if ($menuItem->hasChildren()) {
				$output = Html::beginTag('strong', ['class' => 'submenu-group-title', 'role' => 'presentation']);
				$output .= Html::tag('span', Html::encode($menuItem->getTitle()));
				$output .= Html::endTag('strong');
			}
		} else {
			$output = Html::a('<span>' . Html::encode($menuItem->getTitle()) . '</span>', $menuItem->getUrl(), [
				'target'  => $menuItem->getTarget() ? $menuItem->getTarget() : null,
				'title'   => $menuItem->hasTooltip() ? $menuItem->getTooltip() : null,
				'onclick' => $menuItem->hasClickCallback() ? $menuItem->getClickCallback() : null,
				'class'   => $this->menuItemChecker->isItemActive($activeItem, $menuItem, $level) ? 'active' : null
			]);
		}

		return $output;
	}
}
