<?php
/**
 * @package    falcon
 * @author     Hryvinskyi Volodymyr <volodymyr@hryvinskyi.com>
 * @copyright  Copyright (c) 2018. Hryvinskyi Volodymyr
 * @version    0.0.1-alpha.0.1
 */

namespace falcon\backend\models\menu\item;

use yii\base\InvalidConfigException;

/**
 * @api
 */
class Factory {


	/**
	 * Create menu item from array
	 *
	 * @param array $data
	 *
	 * @return \falcon\backend\models\menu\Item
	 * @throws InvalidConfigException
	 */
	public function create(array $data = []) {
		/**
		 * @var $item \falcon\backend\models\menu\Item
		 */
		$item = \Yii::$container->get(\falcon\backend\models\menu\Item::class, [2 => $data]);

		return $item;
	}
}
