<?php
/**
 * @package    falcon
 * @author     Hryvinskyi Volodymyr <volodymyr@hryvinskyi.com>
 * @copyright  Copyright (c) 2018. Hryvinskyi Volodymyr
 * @version    0.0.1-alpha.0.1
 */

namespace app\modules\backend\models\menu\builder\command;

use app\modules\backend\models\menu\builder\AbstractCommand;

/**
 * Command to update menu item data
 * @api
 */
class Update extends AbstractCommand {
	/**
	 * Update item data
	 *
	 * @param array $itemParams
	 *
	 * @return array
	 */
	protected function _execute(array $itemParams): array {
		foreach ($this->_data as $key => $value) {
			$itemParams[ $key ] = $value;
		}

		return $itemParams;
	}
}
