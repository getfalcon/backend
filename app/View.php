<?php
/**
 * @package    falcon
 * @author     Hryvinskyi Volodymyr <volodymyr@hryvinskyi.com>
 * @copyright  Copyright (c) 2018. Hryvinskyi Volodymyr
 * @version    0.0.1-alpha.0.1
 */

namespace falcon\backend\app;

use falcon\backend\components\Menu;
use falcon\theme\app\Theme;

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
     * @var Theme|array|string the theme object or the configuration for creating the theme object.
     * If not set, it means theming is not enabled.
     */
    public $theme;

    /**
     * {@inheritdoc}
     */
    public function init()
    {
        parent::init();

        $this->menu = \Yii::$app->get('menu');
    }
}