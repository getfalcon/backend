<?php
/**
 * @package    falcon
 * @author     Hryvinskyi Volodymyr <volodymyr@hryvinskyi.com>
 * @copyright  Copyright (c) 2018. Hryvinskyi Volodymyr
 * @version    0.0.1-alpha.0.1
 */

namespace falcon\backend\models\menu\filter;

class IteratorFactory {

	/**
	 * @param array $data
	 *
	 * @return Iterator|object
	 * @throws \yii\base\InvalidConfigException
	 * @throws \yii\di\NotInstantiableException
	 */
	public function create(array $data = []) {
		/**
		 * @var $item Iterator
		 */
		$item = \Yii::$container->get(Iterator::class, $data);

		return $item;
	}
}