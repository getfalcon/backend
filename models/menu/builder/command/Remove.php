<?php
/**
 * @package    falcon
 * @author     Hryvinskyi Volodymyr <volodymyr@hryvinskyi.com>
 * @copyright  Copyright (c) 2018. Hryvinskyi Volodymyr
 * @version    0.0.1-alpha.0.1
 */

namespace falcon\backend\models\menu\builder\command;

use falcon\backend\models\menu\builder\AbstractCommand;

/**
 * Command to remove menu item
 * @api
 */
class Remove extends AbstractCommand {
	/**
	 * Mark item as removed
	 *
	 * @param array $itemParams
	 *
	 * @return array
	 */
	protected function _execute(array $itemParams): array {
		$itemParams['id']      = $this->getId();
		$itemParams['removed'] = true;

		return $itemParams;
	}
}
