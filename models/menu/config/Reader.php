<?php
/**
 * @package    falcon
 * @author     Hryvinskyi Volodymyr <volodymyr@hryvinskyi.com>
 * @copyright  Copyright (c) 2018. Hryvinskyi Volodymyr
 * @version    0.0.1-alpha.0.1
 */

namespace falcon\backend\models\menu\config;

class Reader extends \falcon\core\config\reader\Filesystem {
	/**
	 * Reader constructor.
	 *
	 * @param                           $fileName
	 * @param \falcon\core\config\FileResolver $fileResolver
	 * @param string                    $defaultScope
	 */
	public function __construct(
		$fileName = 'menu.yaml', \falcon\core\config\FileResolver $fileResolver, $defaultScope = 'backend'
	) {
		parent::__construct($fileName, $fileResolver, $defaultScope);
	}

	public function read($scope = null): array {
		return parent::read($scope)['menu'];
	}
}