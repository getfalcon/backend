<?php
/**
 * @package    falcon
 * @author     Hryvinskyi Volodymyr <volodymyr@hryvinskyi.com>
 * @copyright  Copyright (c) 2018. Hryvinskyi Volodymyr
 * @version    0.0.1-alpha.0.1
 */

namespace app\modules\backend\models\menu\builder;

/**
 * Menu builder command
 */
abstract class AbstractCommand {
	/**
	 * List of required params
	 *
	 * @var string[]
	 */
	protected $_requiredParams = ["id"];

	/**
	 * Command params array
	 *
	 * @var array
	 */
	protected $_data = [];

	/**
	 * Next command in the chain
	 *
	 * @var AbstractCommand
	 */
	protected $_next = null;

	/**
	 * @param array $data
	 *
	 * @throws \InvalidArgumentException
	 */
	public function __construct(array $data = []) {
		foreach ($this->_requiredParams as $param) {
			if ( ! isset($data[ $param ]) || $data[ $param ] === null) {
				throw new \InvalidArgumentException("Missing required param " . $param);
			}
		}
		$this->_data = $data;
	}

	/**
	 * Retrieve id of element to apply command to
	 *
	 * @return int
	 */
	public function getId() {
		return $this->_data['id'];
	}

	/**
	 * Add command as last in the list of callbacks
	 *
	 * @param AbstractCommand $command
	 *
	 * @return $this
	 * @throws \InvalidArgumentException if invalid chaining command is supplied
	 */
	public function chain(AbstractCommand $command): self {
		if ($this->_next === null) {
			$this->_next = $command;
		} else {
			$this->_next->chain($command);
		}

		return $this;
	}

	/**
	 * Execute command and pass control to chained commands
	 *
	 * @param array $itemParams
	 *
	 * @return array
	 */
	public function execute(array $itemParams = []): array {
		$itemParams = $this->_execute($itemParams);
		if ($this->_next !== null) {
			$itemParams = $this->_next->execute($itemParams);
		}

		return $itemParams;
	}

	/**
	 * Execute internal command actions
	 *
	 * @param array $itemParams
	 *
	 * @return array
	 */
	abstract protected function _execute(array $itemParams): array;
}
