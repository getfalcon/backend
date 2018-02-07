<?php
/**
 * @package    falcon
 * @author     Hryvinskyi Volodymyr <volodymyr@hryvinskyi.com>
 * @copyright  Copyright (c) 2018. Hryvinskyi Volodymyr
 * @version    0.0.1-alpha.0.1
 */

namespace falcon\backend;

use falcon\backend\components\Menu;

class Bootstrap implements \yii\base\BootstrapInterface
{

    /**
     * @param \yii\base\Application $app
     *
     * @throws \yii\base\InvalidConfigException
     */
    public function bootstrap($app) {
        \Yii::$container->set(\falcon\backend\models\menu\AbstractDirector::class, \falcon\backend\models\menu\director\Director::class);

        \Yii::$app->set('menu', Menu::class);
    }
}