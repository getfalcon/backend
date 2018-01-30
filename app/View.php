<?php
/**
 * @package    falcon
 * @author     Hryvinskyi Volodymyr <volodymyr@hryvinskyi.com>
 * @copyright  Copyright (c) 2018. Hryvinskyi Volodymyr
 * @version    0.0.1-alpha.0.1
 */

namespace falcon\backend\app;


use falcon\backend\components\Menu;

class View extends \yii\web\View
{

    /**
     * @var array
     */
    public $bodyClass = [];

    /**
     * @var Menu
     */
    protected $menu;

    /**
     * {@inheritdoc}
     */
    public function init()
    {
        parent::init();

        $this->menu = \Yii::$app->get('menu');
    }
}