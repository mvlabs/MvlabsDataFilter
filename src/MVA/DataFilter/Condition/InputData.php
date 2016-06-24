<?php

namespace MVA\DataFilter\Condition;

/**
 * Input Data Condition Entity
 *
 * Particular type of Data Condition, used to make comparisons within data model
 * existing entities (IE two database fields, two files...)
 * @author Stefano Maraspin <s.maraspin@mvassociates.it>
 * @copyright MvLabs 2011
 * @link http://www.mvlabs.it
 * @package mva-libs
 * @since 0.5
 *
 */
class InputData extends Atomic {

	protected $m_comparisonValue;

	public function __construct($s_sourceField, $m_comparisonValue, \MVA\DataFilter\Operator\Comparison $I_operator = null) {

		if (empty($s_sourceField)) {
			throw new \MVA\DataFilter\Exception\ConditionException('Condition '.__CLASS__.' needs to have a source field for comparison');
		}

		if (null == $I_operator) {
			$I_operator = new \MVA\DataFilter\Operator\NumericComparison(\MVA\DataFilter\Operator\NumericComparison::EQUAL);
		}

		$this->s_sourceField = $s_sourceField;
		$this->m_comparisonValue = $m_comparisonValue;
		$this->setOperator($I_operator);
	}

	/**
	 * Returns current condition, as an array
	 * @return array Currenty Condition
	 */
	public function asArray() {
		$am_condition['type'] = \MVA\DataFilter\Condition::INPUTDATATYPE;
		$am_condition['operator'] = $this->getOperator()->getType();
		$am_condition['source_field'] = $this->s_sourceField;
		$am_condition['comparison_value'] = $this->m_comparisonValue;
		return $am_condition;
	}

	public function getComparisonValue() {
		return $this->m_comparisonValue;
	}

}
