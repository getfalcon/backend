<?php
/**
 * @package    falcon
 * @author     Hryvinskyi Volodymyr <volodymyr@hryvinskyi.com>
 * @copyright  Copyright (c) 2018. Hryvinskyi Volodymyr
 * @version    0.0.1-alpha.0.1
 */

namespace app\modules\backend\models\menu;

/**
 * Menu iterator
 * @api
 */
class Iterator extends \ArrayIterator {
	/**
	 * Rewind to first element
	 *
	 * @return void
	 */
	public function rewind() {
		$this->ksort();
		parent::rewind();
	}
}
