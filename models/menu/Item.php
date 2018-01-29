<?php
/**
 * @package    falcon
 * @author     Hryvinskyi Volodymyr <volodymyr@hryvinskyi.com>
 * @copyright  Copyright (c) 2018. Hryvinskyi Volodymyr
 * @version    0.0.1-alpha.0.1
 */

namespace app\modules\backend\models\menu;

use app\modules\backend\models\Menu;
use app\modules\backend\models\menu\item\Validator;
use app\modules\backend\models\MenuFactory;
use yii\base\InvalidConfigException;
use yii\helpers\Url;

/**
 * Menu item. Should be used to create nested menu structures with Menu
 */
class Item {
	/**
	 * Menu item id
	 *
	 * @var string
	 */
	protected $_id;

	/**
	 * Menu item title
	 *
	 * @var string
	 */
	protected $_title;

	/**
	 * Module of menu item
	 *
	 * @var string
	 */
	protected $_moduleName;

	/**
	 * Menu item sort index in list
	 *
	 * @var string
	 */
	protected $_sortIndex = null;

	/**
	 * Menu item action
	 *
	 * @var string
	 */
	protected $_action = null;

	/**
	 * Parent menu item id
	 *
	 * @var string
	 */
	protected $_parentId = null;

	/**
	 * Acl resource of menu item
	 *
	 * @var string
	 */
	protected $_resource;

	/**
	 * Item tooltip text
	 *
	 * @var string
	 */
	protected $_tooltip;

	/**
	 * Path from root element in tree
	 *
	 * @var string
	 */
	protected $_path = '';

	/**
	 * Module that item is dependent on
	 *
	 * @var string|null
	 */
	protected $_dependsOnModule;

	/**
	 * Global config option that item is dependent on
	 *
	 * @var string|null
	 */
	protected $_dependsOnConfig;

	/**
	 * Submenu item list
	 *
	 * @var Menu
	 */
	protected $_submenu;

	/**
	 * @var Validator
	 */
	protected $_validator;

	/**
	 * @var MenuFactory
	 */
	protected $_menuFactory;

	/**
	 * Serialized submenu string
	 */
	protected $_serializedSubmenu;

	/**
	 * Menu item target
	 *
	 * @var string|null
	 */
	private $target;

	/**
	 * @param Validator   $validator
	 * @param MenuFactory $menu_factory
	 * @param array       $data
	 *
	 * @throws InvalidConfigException
	 */
	public function __construct(
		Validator $validator, MenuFactory $menu_factory, array $data = []
	) {
		$this->_menuFactory = $menu_factory;
		$this->_validator   = $validator;
		$this->_validator->validate($data);
		$this->populateFromArray($data);
	}

	/**
	 * Populate the menu item with data from array
	 *
	 * @param array $data
	 *
	 * @return void
	 *
	 * @throws \yii\base\InvalidConfigException
	 */
	public function populateFromArray(array $data) {
		$this->_parentId        = $this->_getArgument($data, 'parent_id');
		$this->_moduleName      = $this->_getArgument($data, 'module_name', 'Magento_Backend');
		$this->_sortIndex       = $this->_getArgument($data, 'sort_index');
		$this->_dependsOnConfig = $this->_getArgument($data, 'depends_on_config');
		$this->_id              = $this->_getArgument($data, 'id');
		$this->_resource        = $this->_getArgument($data, 'resource');
		$this->_path            = $this->_getArgument($data, 'path', '');
		$this->_action          = $this->_getArgument($data, 'action');
		$this->_dependsOnModule = $this->_getArgument($data, 'depends_on_module');
		$this->_tooltip         = $this->_getArgument($data, 'tooltip', '');
		$this->_title           = $this->_getArgument($data, 'title');
		$this->target           = $this->_getArgument($data, 'target');

		if (isset($data['sub_menu'])) {
			$menu = $this->_menuFactory->create();
			$menu->populateFromArray($data['sub_menu']);
			$this->_submenu = $menu;
		} else {
			$this->_submenu = null;
		}
	}

	/**
	 * Retrieve argument element, or default value
	 *
	 * @param array  $array
	 * @param string $key
	 * @param mixed  $defaultValue
	 *
	 * @return mixed
	 */
	protected function _getArgument(array $array, $key, $defaultValue = null) {
		return isset($array[ $key ]) ? $array[ $key ] : $defaultValue;
	}

	/**
	 * Retrieve item id
	 *
	 * @return string
	 */
	public function getId(): string {
		return $this->_id;
	}

	/**
	 * Retrieve item target
	 *
	 * @return string|null
	 */
	public function getTarget() {
		return $this->target;
	}

	/**
	 * Check whether item has subnodes
	 *
	 * @return bool
	 */
	public function hasChildren(): bool {
		return (null !== $this->_submenu) && (bool) $this->_submenu->count();
	}

	/**
	 * Retrieve submenu
	 *
	 * @return Menu
	 *
	 * @throws InvalidConfigException
	 */
	public function getChildren(): Menu {
		if ( ! $this->_submenu) {
			$this->_submenu = $this->_menuFactory->create();
		}

		return $this->_submenu;
	}

	/**
	 * Retrieve menu item action
	 *
	 * @return string
	 */
	public function getAction(): string {
		return $this->_action;
	}

	/**
	 * Set Item action
	 *
	 * @param string $action
	 *
	 * @return $this
	 * @throws \InvalidArgumentException
	 */
	public function setAction($action): self {
		$this->_validator->validateParam('action', $action);
		$this->_action = $action;

		return $this;
	}

	/**
	 * Check whether item has javascript callback on click
	 *
	 * @return bool
	 */
	public function hasClickCallback(): bool {
		return $this->getUrl() == '#';
	}

	/**
	 * Retrieve menu item url
	 *
	 * @return string
	 */
	public function getUrl(): string {
		if ((bool) $this->_action) {
			return Url::to([(string) '/backend/' . $this->_action]);
		}

		return '#';
	}

	/**
	 * Retrieve item click callback
	 *
	 * @return string
	 */
	public function getClickCallback(): string {
		if ($this->getUrl() == '#') {
			return 'return false;';
		}

		return '';
	}

	/**
	 * Retrieve tooltip text title
	 *
	 * @return string
	 */
	public function getTitle(): string {
		return $this->_title;
	}

	/**
	 * Set Item title
	 *
	 * @param string $title
	 *
	 * @return $this
	 * @throws \InvalidArgumentException
	 */
	public function setTitle($title): self {
		$this->_validator->validateParam('title', $title);
		$this->_title = $title;

		return $this;
	}

	/**
	 * Check whether item has tooltip text
	 *
	 * @return bool
	 */
	public function hasTooltip(): bool {
		return (bool) $this->_tooltip;
	}

	/**
	 * Retrieve item tooltip text
	 *
	 * @return string
	 */
	public function getTooltip(): string {
		return $this->_tooltip;
	}

	/**
	 * Set Item tooltip
	 *
	 * @param string $tooltip
	 *
	 * @return $this
	 * @throws \InvalidArgumentException
	 */
	public function setTooltip($tooltip): self {
		$this->_validator->validateParam('toolTip', $tooltip);
		$this->_tooltip = $tooltip;

		return $this;
	}

	/**
	 * Set Item module
	 *
	 * @param string $module
	 *
	 * @return $this
	 * @throws \InvalidArgumentException
	 */
	public function setModule($module): self {
		$this->_validator->validateParam('module', $module);
		$this->_moduleName = $module;

		return $this;
	}

	/**
	 * Set Item module dependency
	 *
	 * @param string $moduleName
	 *
	 * @return $this
	 * @throws \InvalidArgumentException
	 */
	public function setModuleDependency($moduleName): self {
		$this->_validator->validateParam('dependsOnModule', $moduleName);
		$this->_dependsOnModule = $moduleName;

		return $this;
	}

	/**
	 * Set Item config dependency
	 *
	 * @param string $configPath
	 *
	 * @return $this
	 * @throws \InvalidArgumentException
	 */
	public function setConfigDependency($configPath): self {
		$this->_validator->validateParam('dependsOnConfig', $configPath);
		$this->_dependsOnConfig = $configPath;

		return $this;
	}

	/**
	 * Check whether item is allowed to the user
	 *
	 * @return bool
	 */
	public function isAllowed(): bool {
		return true;
	}

	/**
	 * Get menu item data represented as an array
	 *
	 * @return array
	 */
	public function toArray(): array {
		return [
			'parent_id'         => $this->_parentId,
			'module_name'       => $this->_moduleName,
			'sort_index'        => $this->_sortIndex,
			'depends_on_config' => $this->_dependsOnConfig,
			'id'                => $this->_id,
			'resource'          => $this->_resource,
			'path'              => $this->_path,
			'action'            => $this->_action,
			'depends_on_module' => $this->_dependsOnModule,
			'tooltip'           => $this->_tooltip,
			'title'             => $this->_title,
			'target'            => $this->target,
			'sub_menu'          => isset($this->_submenu) ? $this->_submenu->toArray() : null
		];
	}

	/**
	 * Check whether item is disabled. Disabled items are not shown to user
	 *
	 * @return bool
	 */
	public function isDisabled() {
		return false;
	}

	/**
	 * Check whether module that item depends on is active
	 *
	 * @return bool
	 */
	protected function _isModuleDependenciesAvailable(): bool {
		if ($this->_dependsOnModule) {
			$module = $this->_dependsOnModule;

			return \Yii::$app->hasModule($module);
		}

		return true;
	}
}

