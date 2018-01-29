<?php
/**
 * @package    falcon
 * @author     Hryvinskyi Volodymyr <volodymyr@hryvinskyi.com>
 * @copyright  Copyright (c) 2018. Hryvinskyi Volodymyr
 * @version    0.0.1-alpha.0.1
 */

namespace falcon\backend\models\menu\item;

use yii\base\DynamicModel;
use yii\base\InvalidConfigException;

/**
 * @api
 */
class Validator {

	/**
	 * All support attribute for menu item
	 *
	 * @var array
	 */
	public $attributes = [
		'type',
		'id',
		'parent',
		'title',
		'action',
		'resource',
		'dependsOnModule',
		'dependsOnConfig',
		'toolTip',
		'module',
		'sortOrder',
		'target',
		'path',

		'parent_id',
		'depends_on_config',
		'depends_on_module',
		'sub_menu',
		'module_name',
		'sort_index',
		'tooltip',
	];

	/**
	 * List of created item ids
	 *
	 * @var array
	 */
	protected $_ids = [];

	/**
	 * The list of primitive validators
	 *
	 * @var DynamicModel
	 */
	protected $_model = [];

	/**
	 * Constructor
	 *
	 * @throws InvalidConfigException
	 */
	public function __construct() {
		$this->_model = \Yii::$container->get(DynamicModel::class, [$this->attributes]);

		$this->_model->addRule(['id'], 'match', ['pattern' => '/^[A-Za-z0-9\/:_]+$/']);
		$this->_model->addRule(['resource'], 'string', ['min' => 8]);
		$this->_model->addRule(['resource'], 'match', ['pattern' => '/^[A-Z][A-Za-z0-9]+_[A-Z][A-Za-z0-9]+::[A-Za-z_0-9]+$/']);
		$this->_model->addRule(['id', 'title', 'resource'], 'required');
		$this->_model->addRule(['title', 'toolTip'], 'string', ['min' => 3, 'max' => 50]);
		$this->_model->addRule(['id', 'dependsOnConfig', 'dependsOnModule', 'action'], 'string', ['min' => 3]);
		$this->_model->addRule([
			'dependsOnConfig',
			'dependsOnModule',
			'action'
		], 'match', ['pattern' => '/^[A-Za-z0-9\/_]+$/']);

		$this->_model->addRule([
			'depends_on_config',
			'depends_on_module',
			'sub_menu',
			'module_name',
			'sort_index',
			'tooltip'
		], 'safe');

	}

	/**
	 * Validate menu item params
	 *
	 * @param array $data
	 *
	 * @return void
	 *
	 * @throws \InvalidArgumentException
	 */
	public function validate($data) {
		if (array_search($data['id'], $this->_ids) !== false) {
			throw new \InvalidArgumentException('Item with id ' . $data['id'] . ' already exists');
		}

		foreach ($data as $param => $value) {
			if ($data[ $param ] === null) {
				//throw new \InvalidArgumentException("Param " . $param . " can not be empty.");
			}

			$this->validateParam($param, $value);
		}
		$this->_ids[] = $data['id'];
	}

	/**
	 * Validate incoming param
	 *
	 * @param string $param
	 * @param mixed  $value
	 *
	 * @return void
	 *
	 * @throws \InvalidArgumentException
	 */
	public function validateParam($param, $value) {
		if ( ! $this->issetParam($param)) {
			throw new \InvalidArgumentException("Param " . $param . " not supported.");
		}

		$this->_model->offsetSet($param, $value);

		if ( ! $this->_model->validate([$param])) {
			throw new \InvalidArgumentException("Param " . $param . " doesn't pass validation: " . implode('; ', $this->_model->getErrors($param)));
		}
	}

	/**
	 * @param $param
	 *
	 * @return bool
	 */
	protected function issetParam($param): bool {
		return in_array($param, $this->attributes);
	}
}
