<?php
/**
 * @package    falcon
 * @author     Hryvinskyi Volodymyr <volodymyr@hryvinskyi.com>
 * @copyright  Copyright (c) 2018. Hryvinskyi Volodymyr
 * @version    0.0.1-alpha.0.1
 */

namespace falcon\backend\models\menu\builder;

/**
 * Menu builder command factory
 */
class CommandFactory {
	/**
	 * @param       $commandName
	 * @param array $data
	 *
	 * @return AbstractCommand
	 * @throws \yii\base\InvalidConfigException
	 * @throws \yii\di\NotInstantiableException
	 */
	public function create($commandName, array $data = []) {
		/**
		 * @var $command AbstractCommand
		 */
		$command = \Yii::$container->get('falcon\backend\models\menu\builder\command\\' . ucfirst($commandName), [$data['data']]);

		return $command;
	}
}
