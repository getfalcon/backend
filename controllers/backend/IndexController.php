<?php
/**
 * @package    falcon
 * @author     Hryvinskyi Volodymyr <volodymyr@hryvinskyi.com>
 * @copyright  Copyright (c) 2018. Hryvinskyi Volodymyr
 * @version    0.0.1-alpha.0.1
 */

namespace falcon\backend\controllers\backend;

use falcon\backend\models\menu\Config;
use yii\web\Controller;

class IndexController extends Controller {
	/**
	 * @return string
	 * @throws \Exception
	 */
	public function actionIndex() {
		/**
		 * @var $menu Config
		 */

		//VarDumper::dump($menu->getMenu(), 10, true);
		return $this->render('index');
	}
}