<?php
/**
 * @package    falcon
 * @author     Hryvinskyi Volodymyr <volodymyr@hryvinskyi.com>
 * @copyright  Copyright (c) 2018. Hryvinskyi Volodymyr
 * @version    0.0.1-alpha.0.1
 */

namespace falcon\backend\app;

use falcon\core\components\UrlRulesLoader;

class Application extends \yii\web\Application
{

    /**
     * {@inheritdoc}
     */
    public function bootstrap()
    {
        parent::bootstrap();

        $container = \Yii::$container;
        /** @var UrlRulesLoader $urlRulesLoader */
        $urlRulesLoader = $container->get(UrlRulesLoader::class);

        // Init Url Rules
        $this->getUrlManager()->addRules($urlRulesLoader->getRules());
    }

    /**
     * {@inheritdoc}
     */
    public function coreComponents()
    {
        return array_merge(parent::coreComponents(), [
            'view' => ['class' => '\falcon\backend\app\View'],
        ]);
    }
}