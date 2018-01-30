<?php
/**
 * @package    falcon
 * @author     Hryvinskyi Volodymyr <volodymyr@hryvinskyi.com>
 * @copyright  Copyright (c) 2018. Hryvinskyi Volodymyr
 * @version    0.0.1-alpha.0.1
 */

namespace falcon\backend\app;

use falcon\backend\models\menu\Item;
use falcon\backend\components\Menu;

class Controller extends \yii\web\Controller
{

    /**
     * Define active menu item in menu block
     *
     * @param string $itemId current active menu item
     * @return $this
     *
     * @throws \Exception
     * @throws \yii\base\InvalidConfigException
     */
    protected function _setActiveMenu($itemId)
    {
        /** @var $menu Menu */
        $menu = \Yii::$app->get('menu');
        $menu->setActive($itemId);
        $parents = $menu->getMenuModel()->getParentItems($itemId);
        foreach ($parents as $item) {
            /** @var $item Item */
            \Yii::$app->getView()->title = $item->getTitle();
        }
        return $this;
    }
}