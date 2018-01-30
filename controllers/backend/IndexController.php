<?php
/**
 * @package    falcon
 * @author     Hryvinskyi Volodymyr <volodymyr@hryvinskyi.com>
 * @copyright  Copyright (c) 2018. Hryvinskyi Volodymyr
 * @version    0.0.1-alpha.0.1
 */

namespace falcon\backend\controllers\backend;

use falcon\backend\app\Controller;

class IndexController extends Controller {
	/**
	 * @return string
	 * @throws \Exception
	 */
	public function actionIndex() {
        $this->_setActiveMenu('elements');
		return $this->render('index');
	}
}