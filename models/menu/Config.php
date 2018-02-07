<?php
/**
 * @package    falcon
 * @author     Hryvinskyi Volodymyr <volodymyr@hryvinskyi.com>
 * @copyright  Copyright (c) 2018. Hryvinskyi Volodymyr
 * @version    0.0.1-alpha.0.1
 */

namespace falcon\backend\models\menu;

use yii\base\InvalidConfigException;

/**
 * Class Config
 * @api
 */
class Config {
	const CACHE_ID = 'backend_menu_config';

	/**
	 * @var \falcon\backend\models\MenuFactory
	 */
	protected $_menuFactory;

	/**
	 * Menu model
	 *
	 * @var \falcon\backend\models\Menu
	 */
	protected $_menu;

	/**
	 * @var AbstractDirector
	 */
	protected $_director;

	/**
	 * @var \falcon\backend\models\menu\Builder
	 */
	protected $_menuBuilder;

	/**
	 * @var \falcon\backend\models\menu\config\Reader
	 */
	protected $_configReader;

	/**
	 * Config constructor.
	 *
	 * @param Builder                                        $menuBuilder
	 * @param \falcon\backend\models\MenuFactory        $menuFactory
	 * @param \falcon\backend\models\menu\config\Reader $configReader
	 * @param AbstractDirector                               $menuDirector
	 */
	public function __construct(
        \falcon\backend\models\menu\Builder $menuBuilder,
        \falcon\backend\models\MenuFactory $menuFactory,
        \falcon\backend\models\menu\config\Reader $configReader,
        AbstractDirector $menuDirector
	) {
		$this->_director     = $menuDirector;
		$this->_menuBuilder  = $menuBuilder;
		$this->_menuFactory  = $menuFactory;
		$this->_configReader = $configReader;
	}

	/**
	 * Build menu model from config
	 *
	 * @return \falcon\backend\models\Menu
	 *
	 * @throws \Exception|\InvalidArgumentException
	 * @throws \Exception
	 * @throws \BadMethodCallException|\Exception
	 * @throws \Exception|\OutOfRangeException
	 */
	public function getMenu() {
		try {
			$this->_initMenu();

			return $this->_menu;
		} catch (\InvalidArgumentException $e) {
			\Yii::trace($e);
			throw $e;
		} catch (\BadMethodCallException $e) {
			\Yii::trace($e);
			throw $e;
		} catch (\OutOfRangeException $e) {
			\Yii::trace($e);
			throw $e;
		} catch (\Exception $e) {
			throw $e;
		}
	}

	/**
	 * Initialize menu object
	 *
	 * @return void
	 *
	 * @throws InvalidConfigException
	 */
	protected function _initMenu() {
		if ( ! $this->_menu) {
			$this->_menu = $this->_menuFactory->create();

			$cache = $this->getCache()->get(self::CACHE_ID);
			if ($cache) {
				$this->_menu->unserialize($cache);

				return;
			}

			$this->_director->direct($this->_configReader->read(), $this->_menuBuilder);

			$this->_menu = $this->_menuBuilder->getResult($this->_menu);
			$this->getCache()->set(self::CACHE_ID, $this->_menu->serialize());
		}
	}

	/**
	 * @return \yii\caching\CacheInterface
	 */
	protected function getCache() {
		return \Yii::$app->getCache();
	}
}
