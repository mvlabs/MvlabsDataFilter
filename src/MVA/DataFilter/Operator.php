<?php

namespace MVA\DataFilter;

use MVA\Exception;

/**
 * Abstract Operator Class
 *
 * @author Stefano Maraspin <s.maraspin@mvassociates.it>
 * @copyright MvLabs 2011
 * @link http://www.mvlabs.it
 * @package mva-libs
 * @since 0.5
 */
abstract class Operator {

	/**
	 * Valid Operators
	 * @var array
	 */
	protected $am_validOperators;

	/**
	 * Current Operator Type
	 * @var mixed
	 */
	protected $m_operatorType;

	/**
	 * Constructor
	 *
	 * An operator is constructed and defined to be of the type specified
	 * as a parameter. Only valid operators, defined as class constants, are accepted
	 * @param mixed Operator Type
	 * @throws Utility_OperatorException
	 */
	public function __construct($m_operatorType) {

		// A list of class constants is fetched and saved into am_validOperators
		$I_currentClassRef = new \ReflectionClass($this);
		$this->am_validOperators = $I_currentClassRef->getConstants();

		// If an invalid operator is specified, an Exception is thrown
		if (!in_array($m_operatorType,$this->am_validOperators)) {
			throw new \MVA\DataFilter\Exception\OperatorException('Invalid Operator Specified');
		}

		$this->m_operatorType = $m_operatorType;

	}

	/**
	 * Returns Operator Type
	 * @return mixed Operator Type
	 */
	public function getType() {
		return $this->m_operatorType;
	}

	/**
	 *
	 * @return string Operator Type Name
	 */
	public function getTypeName() {
		foreach ($this->am_validOperators as $s_constantName => $m_value) {
			if ($this->m_operatorType == $m_value) {
				return $s_constantName;
			}
		}
		return 'UNDEFINED';
	}

	public static function getValidOperators() {

		// A list of class constants is fetched and saved into am_validOperators
		$I_currentClassRef = new \ReflectionClass(get_called_class());
		$am_validOperators = $I_currentClassRef->getConstants();
		return $am_validOperators;
	}

}
