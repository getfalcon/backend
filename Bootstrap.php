<?php
/**
 * @package    falcon
 * @author     Hryvinskyi Volodymyr <volodymyr@hryvinskyi.com>
 * @copyright  Copyright (c) 2018. Hryvinskyi Volodymyr
 * @version    0.0.1-alpha.0.1
 */

namespace app\modules\backend;


class Bootstrap implements \yii\base\BootstrapInterface {

	public function bootstrap($app) {
		\Yii::$container->set(\app\modules\backend\models\menu\AbstractDirector::class, \app\modules\backend\models\menu\director\Director::class);
	}
}