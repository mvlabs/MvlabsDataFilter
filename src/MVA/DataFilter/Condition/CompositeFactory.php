<?php

namespace MVA\DataFilter\Condition;

/**
 *
 *
 *
 * @author Stefano Maraspin <s.maraspin@mvassociates.it>
 * @copyright MvLabs 2011
 * @link http://www.mvlabs.it
 * @package mva-libs
 * @since 0.5
 */
class CompositeFactory {

	public static function fromArray(
	                        $s_sourceField,
	                        $am_values,
	                        \MVA\DataFilter\Operator\Logical $I_logicalOperator,
	                        \MVA\DataFilter\Operator\Comparison $I_comparisonOperator = null) {

		if (null == $I_comparisonOperator) {
			$I_comparisonOperator = new \MVA\DataFilter\Operator\NumericComparison(\MVA\DataFilter\Operator\NumericComparison::EQUAL);
		}

		$am_conditionList = array();

	    foreach ($am_values as $m_value) {
			$am_conditionList[] = new  \MVA\DataFilter\Condition\InputData($s_sourceField, $m_value, $I_comparisonOperator);
		}

		$I_composite = new \MVA\DataFilter\Condition\Composite($am_conditionList, $I_logicalOperator);
		return $I_composite;
	}

}
