<?php

namespace MVA\DataFilter\Condition;

/**
 * Existing Data Condition Entity
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
class ExistingData extends Atomic {

	/**
	 * Destination Field of Comparison
	 * @var string
	 */
	protected $s_comparisonField;

	/**
	 * Class Constructor
	 * @param MVA\DataFilter\Operator\Comparison Comparison Operator
	 * @param string Source Field for Comparison
	 * @param string Target Field for Comparison
	 * @throws Exception
	 */
	public function __construct($s_sourceField, $s_comparisonField, \MVA\DataFilter\Operator\Comparison $I_operator = null) {

		if (empty($s_sourceField)) {
			throw new Exception('Condition '.__CLASS__.' needs to have a source field for comparison');
		}
		$this->s_sourceField = $s_sourceField;

		if (empty($s_comparisonField)) {
			throw new Exception('Condition '.__CLASS__.' needs to have a target field for comparison');
		}
		$this->s_comparisonField = $s_comparisonField;

		if (null == $I_operator) {
			$I_operator = new \MVA\DataFilter\Operator\NumericComparison(\MVA\DataFilter\Operator\NumericComparison::EQUAL);
		}

		$this->setOperator($I_operator);
	}

	/**
	 * Returns Source Field for Comparison
	 * @return string Source Field for Comparison
	 */
	public function getSourceField() {
		return $this->s_sourceField;
	}

	/**
	 * Returns Target Field for Comparison
	 * @return string Destination Field for Comparison
	 */
	public function getComparisonField() {
		return $this->s_comparisonField;
	}

	/**
	 * Returns current condition, as an array
	 * @return array Currenty Condition
	 */
	public function asArray() {
		$am_condition['type'] = \MVA\DataFilter\Condition::EXISTINGDATATYPE;
		$am_condition['operator'] = $this->getOperator()->getType();
		$am_condition['source_field'] = $this->s_sourceField;
		$am_condition['comparison_field'] = $this->s_comparisonField;
		return $am_condition;
	}

}

