<?php
/**
 * @package    falcon
 * @author     Hryvinskyi Volodymyr <volodymyr@hryvinskyi.com>
 * @copyright  Copyright (c) 2018. Hryvinskyi Volodymyr
 * @version    0.0.1-alpha.0.1
 */

namespace app\modules\backend\models;

use yii\base\InvalidConfigException;

class MenuFactory {

	/**
	 * Create menu item from array
	 *
	 * @param array $data
	 *
	 * @return Menu
	 * @throws InvalidConfigException
	 */
	public function create(array $data = []): Menu {
		/**
		 * @var $item Menu
		 */
		$item = \Yii::$container->get(Menu::class, ['data' => $data]);

		return $item;
	}
}
