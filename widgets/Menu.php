<?php
/**
 * @package    falcon
 * @author     Hryvinskyi Volodymyr <volodymyr@hryvinskyi.com>
 * @copyright  Copyright (c) 2018. Hryvinskyi Volodymyr
 * @version    0.0.1-alpha.0.1
 */

// @codingStandardsIgnoreFile

namespace falcon\backend\widgets;

use falcon\backend\models\menu\Config;
use falcon\backend\models\menu\filter\Iterator;
use falcon\backend\models\menu\filter\IteratorFactory;
use falcon\backend\models\menu\Item;
use falcon\core\widgets\CacheWidget;
use yii\base\InvalidConfigException;
use yii\caching\Cache;
use yii\helpers\Html;

/**
 * Backend menu block
 */
class Menu extends CacheWidget {
	const CACHE_TAGS = 'BACKEND_MAIN_MENU';

	/**
	 * @var string|Cache
	 */
	public $cache = 'cache';

	/**
	 * @var int
	 */
	public $cacheDuration;

	/**
	 * @var \yii\caching\Dependency
	 */
	public $cacheDependency;

	/**
	 * @var string
	 */
	protected $_containerRenderer;

	/**
	 * @var string
	 */
	protected $_itemRenderer;

	/**
	 * Current selected item
	 *
	 * @var Item|false|null
	 */
	protected $_activeItemModel = null;

	/**
	 * @var IteratorFactory
	 */
	protected $_iteratorFactory;


	/**
	 * @var Config
	 */
	protected $_menuConfig;

	/**
	 * @var MenuItemChecker
	 */
	private $menuItemChecker;

	/**
	 * @var AnchorRenderer
	 */
	private $anchorRenderer;

	/**
	 * @param Config               $menuConfig
	 * @param IteratorFactory      $iteratorFactory
	 * @param MenuItemChecker|null $menuItemChecker
	 * @param AnchorRenderer       $anchorRenderer
	 * @param array                $data
	 * @param array                $config
	 */
	public function __construct(
		Config $menuConfig, IteratorFactory $iteratorFactory, MenuItemChecker $menuItemChecker, AnchorRenderer $anchorRenderer, array $data = [], array $config = []
	) {
		$this->_menuConfig      = $menuConfig;
		$this->menuItemChecker  = $menuItemChecker;
		$this->_iteratorFactory = $iteratorFactory;
		$this->anchorRenderer   = $anchorRenderer;

		parent::__construct($config);
	}

	/**
	 * Initialize template and cache settings
	 *
	 * @return void
	 */
	public function init() {
		parent::init();
	}

	/**
	 * {@inheritdoc}
	 */
	public function run() {
		return $this->renderMenu($this->getMenuModel());
	}

	/**
	 * Render menu
	 *
	 * @param \falcon\backend\models\Menu $menu
	 * @param int                              $level
	 *
	 * @return string
	 * @throws InvalidConfigException
	 * @throws \Exception
	 * @throws \yii\di\NotInstantiableException
	 */
	public function renderMenu($menu, $level = 0) {
		$output = Html::beginTag('ul', [
			'id'   => (0 == $level ? 'nav' : null),
			'role' => (0 == $level ? 'menubar' : null)
		]);

		/** @var $menuItem \falcon\backend\models\menu\Item */
		foreach ($this->_getMenuIterator($menu) as $menuItem) {
			$output .= Html::beginTag('li', [
				'class' => $this->_renderItemCssClass($menuItem, $level),
				'role'  => 'menuitem'
			]);
			$output .= $this->anchorRenderer->renderAnchor($this->getActiveItemModel(), $menuItem, $level);
			if ($menuItem->hasChildren()) {
				$output .= $this->renderMenu($menuItem->getChildren(), $level + 1);
			}
			$output .= Html::endTag('li');
		}
		$output .= Html::endTag('ul');

		return $output;
	}

	/**
	 * Get menu filter iterator
	 *
	 * @param \falcon\backend\models\Menu $menu
	 *
	 * @return Iterator
	 *
	 * @throws InvalidConfigException
	 * @throws \yii\di\NotInstantiableException
	 */
	protected function _getMenuIterator($menu) {
		return $this->_iteratorFactory->create([$menu->getIterator()]);
	}

	/**
	 * Render item css class
	 *
	 * @param Item $menuItem
	 * @param int  $level
	 *
	 * @return array
	 * @throws InvalidConfigException
	 * @throws \Exception
	 */
	protected function _renderItemCssClass(Item $menuItem, int $level): array {
		$output = [];

		if ($this->menuItemChecker->isItemActive($this->getActiveItemModel(), $menuItem, $level)) {
			$output[] = 'current active';
		}

		if ($menuItem->hasChildren()) {
			$output[] = 'parent';
		}

		if ($level == 0 && (bool) $this->getMenuModel()->isLast($menuItem)) {
			$output[] = 'last';
		}

		$output[] = 'level-' . $level;

		return $output;
	}

	/**
	 * Get current selected menu item
	 *
	 * @return Item|false
	 * @throws InvalidConfigException
	 * @throws \Exception
	 */
	public function getActiveItemModel() {
//        if ($this->_activeItemModel === null) {
//            $this->_activeItemModel = $this->getMenuModel()->get($this->getActive());
//            if (false == $this->_activeItemModel instanceof Item) {
//                $this->_activeItemModel = false;
//            }
//        }
		return $this->_activeItemModel;
	}

	/**
	 * Get menu config model
	 *
	 * @return \falcon\backend\models\Menu
	 *
	 * @throws \Exception
	 */
	public function getMenuModel() {
		return $this->_menuConfig->getMenu();
	}

	/**
	 * Retrieve cache lifetime
	 *
	 * @return int
	 */
	public function getCacheLifetime() {
		return 86400;
	}

	/**
	 * Get Key pieces for caching block content
	 *
	 * @return array
	 */
	public function getCacheKeyInfo() {
		$cacheKeyInfo = [
			'admin_top_nav',
			\Yii::$app->getUser()->getId(),
			\Yii::$app->language,
		];

		return $cacheKeyInfo;
	}

	/**
	 * Add sub menu HTML code for current menu item
	 *
	 * @param Item $menuItem
	 * @param int  $level
	 * @param int  $limit
	 * @param      $id int
	 *
	 * @return string
	 * @throws InvalidConfigException
	 * @throws \Exception
	 * @throws \yii\di\NotInstantiableException
	 */
	protected function _addSubMenu($menuItem, $level, $limit, $id = null) {
		$output = '';
		if ( ! $menuItem->hasChildren()) {
			return $output;
		}
		$output   .= '<div class="submenu"' . ($level == 0 && isset($id) ? ' aria-labelledby="' . $id . '"' : '') . '>';
		$colStops = null;
		if ($level == 0 && $limit) {
			$colStops = $this->_columnBrake($menuItem->getChildren(), $limit);
			$output   .= '<strong class="submenu-title">' . $this->_getAnchorLabel($menuItem) . '</strong>';
			$output   .= '<a href="#" class="action-close _close" data-role="close-submenu"></a>';
		}

		$output .= $this->renderNavigation($menuItem->getChildren(), $level + 1, $limit, $colStops);
		$output .= '</div>';

		return $output;
	}

	/**
	 * Building Array with Column Brake Stops
	 *
	 * @param \falcon\backend\models\Menu $items
	 * @param int                              $limit
	 *
	 * @return array|void
	 * @throws InvalidConfigException
	 */
	protected function _columnBrake($items, $limit) {
		$total = $this->_countItems($items);
		if ($total <= $limit) {
			return;
		}
		$result[] = ['total' => $total, 'max' => ceil($total / ceil($total / $limit))];
		$count    = 0;
		foreach ($items as $item) {
			$place = $this->_countItems($item->getChildren()) + 1;
			$count += $place;
			if ($place - $result[0]['max'] > $limit - $result[0]['max']) {
				$colbrake = true;
				$count    = 0;
			} else if ($count - $result[0]['max'] > $limit - $result[0]['max']) {
				$colbrake = true;
				$count    = $place;
			} else {
				$colbrake = false;
			}
			$result[] = ['place' => $place, 'colbrake' => $colbrake];
		}

		return $result;
	}

	/**
	 * Count All Subnavigation Items
	 *
	 * @param \falcon\backend\models\Menu $items
	 *
	 * @return int
	 *
	 * @throws InvalidConfigException
	 */
	protected function _countItems($items) {
		$total = count($items);
		foreach ($items as $item) {
			/** @var $item Item */
			if ($item->hasChildren()) {
				$total += $this->_countItems($item->getChildren());
			}
		}

		return $total;
	}

	/**
	 * Render menu item anchor label
	 *
	 * @param Item $menuItem
	 *
	 * @return string
	 */
	protected function _getAnchorLabel($menuItem) {
		return Html::encode($menuItem->getTitle());
	}

	/**
	 * Render Navigation
	 *
	 * @param \falcon\backend\models\Menu $menu
	 * @param int                              $level
	 * @param int                              $limit
	 * @param array                            $colBrakes
	 *
	 * @return string HTML
	 * @SuppressWarnings(PHPMD.NPathComplexity)
	 * @SuppressWarnings(PHPMD.CyclomaticComplexity)
	 *
	 * @throws InvalidConfigException
	 * @throws \Exception
	 * @throws \yii\di\NotInstantiableException
	 */
	public function renderNavigation($menu, $level = 0, $limit = 0, $colBrakes = []) {
		$itemPosition = 1;
		$outputStart  = '<ul ' . (0 == $level ? 'id="nav" role="menubar"' : 'role="menu"') . ' >';
		$output       = '';

		/** @var $menuItem Item */
		foreach ($this->_getMenuIterator($menu) as $menuItem) {
			$menuId    = $menuItem->getId();
			$itemName  = substr($menuId, strrpos($menuId, '::') + 2);
			$itemClass = str_replace('_', '-', strtolower($itemName));

			if (count($colBrakes) && $colBrakes[ $itemPosition ]['colbrake'] && $itemPosition != 1) {
				$output .= '</ul></li><li class="column"><ul role="menu">';
			}

			$id      = $this->getJsId($menuItem->getId());
			$subMenu = $this->_addSubMenu($menuItem, $level, $limit, $id);
			$anchor  = $this->anchorRenderer->renderAnchor($this->getActiveItemModel(), $menuItem, $level);
			$output  .= '<li ' . $this->getUiId($menuItem->getId()) . ' class="item-' . $itemClass . ' ' . $this->_renderItemCssClass($menuItem, $level) . ($level == 0 ? '" id="' . $id . '" aria-haspopup="true' : '') . '" role="menu-item">' . $anchor . $subMenu . '</li>';
			$itemPosition ++;
		}

		if (count($colBrakes) && $limit) {
			$output = '<li class="column"><ul role="menu">' . $output . '</ul></li>';
		}

		return $outputStart . $output . '</ul>';
	}
}
