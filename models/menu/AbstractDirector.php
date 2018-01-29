<?php
/**
 * @package    falcon
 * @author     Hryvinskyi Volodymyr <volodymyr@hryvinskyi.com>
 * @copyright  Copyright (c) 2018. Hryvinskyi Volodymyr
 * @version    0.0.1-alpha.0.1
 */

namespace falcon\backend\models\menu;

use falcon\backend\models\menu\builder\CommandFactory;

/**
 * @api
 */
abstract class AbstractDirector {
	/**
	 * Factory model
	 * @var CommandFactory
	 */
	protected $_commandFactory;

	/**
	 * @param CommandFactory $factory
	 */
	public function __construct(CommandFactory $factory) {
		$this->_commandFactory = $factory;
	}

	/**
	 * Build menu instance
	 *
	 * @param array   $config
	 * @param Builder $builder
	 *
	 * @return void
	 */
	abstract public function direct(array $config, Builder $builder);
}
