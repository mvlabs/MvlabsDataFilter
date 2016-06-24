<?php

namespace MVA\DataFilter\Condition;

/**
 * Abstract Atomic Condition Entity
 *
 * Atomic conditions are all those who contain one specific comparison criteria and can be
 * all part of a composite condition
 * @author Stefano Maraspin <s.maraspin@mvassociates.it>
 * @copyright MvLabs 2011
 * @link http://www.mvlabs.it
 * @package mva-libs
 * @since 0.5
 */
abstract class Atomic extends \MVA\DataFilter\Condition {

	/**
	 * Source field for comparison
	 * @var string
	 */
	protected $s_sourceField;

	/**
	 * Gets source field for comparison
	 * @return string Source field
	 */
	public function getSourceField() {
		return $this->s_sourceField;
	}

	/**
	 * Returns current condition as a list
	 * @return array Containing Current Condition Only
	 * @see self::asArray()
	 */
	public function asList() {
		return array($this);
	}


}
