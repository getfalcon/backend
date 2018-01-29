<?php
/**
 * @package    falcon
 * @author     Hryvinskyi Volodymyr <volodymyr@hryvinskyi.com>
 * @copyright  Copyright (c) 2018. Hryvinskyi Volodymyr
 * @version    0.0.1-alpha.0.1
 */

namespace app\modules\backend\models\menu\item;

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
	 * @return \app\modules\backend\models\menu\Item
	 * @throws InvalidConfigException
	 */
	public function create(array $data = []) {
		/**
		 * @var $item \app\modules\backend\models\menu\Item
		 */
		$item = \Yii::$container->get(\app\modules\backend\models\menu\Item::class, [2 => $data]);

		return $item;
	}
}
