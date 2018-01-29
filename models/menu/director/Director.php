<?php
/**
 * @package    falcon
 * @author     Hryvinskyi Volodymyr <volodymyr@hryvinskyi.com>
 * @copyright  Copyright (c) 2018. Hryvinskyi Volodymyr
 * @version    0.0.1-alpha.0.1
 */

namespace falcon\backend\models\menu\director;

use falcon\backend\models\menu\AbstractDirector;
use falcon\backend\models\menu\Builder;
use falcon\backend\models\menu\builder\AbstractCommand;
use yii\base\InvalidConfigException;

/**
 * @api
 */
class Director extends AbstractDirector {
	/**
	 * Log message patterns
	 *
	 * @var array
	 */
	protected $_messagePatterns = ['update' => 'Item %s was updated', 'remove' => 'Item %s was removed'];

	/**
	 * Build menu instance
	 *
	 * @param array   $config
	 * @param Builder $builder
	 *
	 * @return void
	 *
	 * @throws InvalidConfigException
	 */
	public function direct(
		array $config, Builder $builder
	) {
		foreach ($config as $data) {
			$builder->processCommand($this->_getCommand($data));
		}
	}

	/**
	 * Get command object
	 *
	 * @param array $data command params
	 *
	 * @return AbstractCommand
	 *
	 * @throws InvalidConfigException
	 */
	protected function _getCommand($data) {
		$command = $this->_commandFactory->create($data['type'], ['data' => $data]);

		if (isset($this->_messagePatterns[ $data['type'] ])) {
			\Yii::info(sprintf($this->_messagePatterns[ $data['type'] ], $command->getId()));
		}

		return $command;
	}
}
