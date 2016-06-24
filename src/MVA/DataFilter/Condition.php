<?php

namespace MVA\DataFilter;

/**
 * Abstract Condition Entity
 *
 * @author Stefano Maraspin <s.maraspin@mvassociates.it>
 * @copyright MvLabs 2011
 * @link http://www.mvlabs.it
 * @package mva-libs
 * @since 0.5
 */
abstract class Condition {

	/**
	 * Identifies a condition of type composite, when such is returned as array
	 * @var itn Composite Type Id
	 */
	const COMPOSITETYPE = 1;

	/**
	 * Identifies a condition of type existing, when such is returned as array
	 * @var itn Existing Type Id
	 */
	const EXISTINGDATATYPE = 2;

	/**
	 * Identifies a condition of type input, when such is returned as array
	 * @var itn Input Type Id
	 */
	const INPUTDATATYPE = 3;

	/**
	 * Condition Operator
	 * @var Operator operator
	 */
	protected $I_operator;

	/**
	 * Operator Setter
	 * @param object Operator
	 */
	public function setOperator(\MVA\DataFilter\Operator $I_operator) {
		$this->I_operator = $I_operator;
	}

	/**
	 * Returns Operator
	 * @return Operator operator
	 */
	public function getOperator() {
		return $this->I_operator;
	}

	public function getOperatorName() {
		return $this->I_operator->getTypeName();
	}

	abstract public function asArray();

}
