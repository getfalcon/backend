<?php
/**
 * @package    falcon
 * @author     Hryvinskyi Volodymyr <volodymyr@hryvinskyi.com>
 * @copyright  Copyright (c) 2018. Hryvinskyi Volodymyr
 * @version    0.0.1-alpha.0.1
 */

namespace app\modules\backend\models\menu\filter;

class Iterator extends \FilterIterator {
	/**
	 * Constructor
	 *
	 * @param \Iterator $iterator
	 */
	public function __construct(\Iterator $iterator) {
		parent::__construct($iterator);
	}

	/**
	 * Check whether the current element of the iterator is acceptable
	 *
	 * @return bool true if the current element is acceptable, otherwise false.
	 */
	public function accept() {
		return ! ($this->current()->isDisabled() || ! $this->current()->isAllowed());
	}
}