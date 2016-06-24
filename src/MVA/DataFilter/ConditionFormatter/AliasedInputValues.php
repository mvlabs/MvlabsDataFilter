<?php

namespace MVA\DataFilter\ConditionFormatter;

/**
 *
 *
 * @author Stefano Maraspin &lt;s.maraspin@mvassociates.it&gt;
 * @copyright MvLabs 2011
 * @link http://www.mvlabs.it
 * @package mva-libs
 *
 */
 class AliasedInputValues extends \MVA\DataFilter\ConditionFormatter {

	/**
	 * @var array mapping table names -> aliases
	 */
	protected $as_mapping;

	/**
	 * @var Utility_SelectionCriteria selection criteria
	 */
	protected $I_condition;


	protected $aI_output;


	protected $am_values;


	/**
	 * Item Constructor
	 * @param array Mapping from Model entities to Doctrine DQL Aliases
	 * @return unknown_type
	 */
	public function __construct( \MVA\DataFilter\Condition $I_condition = null, array $as_mapping = null) {

			$this->as_mapping = $as_mapping;
			$this->I_condition = $I_condition;
			$this->am_values = array();
			$this->aI_output = array();

			if ($I_condition instanceof \MVA\DataFilter\Condition) {

				$aI_conditions = $I_condition->asList();

				foreach($aI_conditions as $I_leafCondition) {
					if ($I_leafCondition instanceof \MVA\DataFilter\Condition\InputData) {

						 $s_key = $I_leafCondition->getSourceField();

						 // Aliasing... If alias is specified for a field, that is used as key
						 if (is_array($as_mapping) && array_key_exists($s_key, $as_mapping)) {
						 	$s_key = $as_mapping[$s_key];
						 }

						 $am_element['operator'] = $I_leafCondition->getOperatorName();
						 $am_element['value'] = $I_leafCondition->getComparisonValue();
						 $this->aI_output[$s_key][] = $am_element;

						 $this->am_values[$s_key][] = $I_leafCondition->getComparisonValue();
					}
				}
			}

	}

	/**
	 * Returns Table Aliases
	 * @param string field name
	 * @return array Input Conditions
	 */
	public function getConditions() {
		return $this->aI_output;
	}

	/**
	 * Returns Conditions for a Specific Key
	 * @param string Key To Look For
	 * @throws \Exception If a not existing key is specified
	 */
	public function getConditionsFor($s_key) {
		if (!array_key_exists($s_key, $this->aI_output)) {
			return array();
		}
		return $this->aI_output[$s_key];
	}


	public function getValues() {
		return $this->am_values;
	}


	public function getValuesFor($s_key) {
		if (!array_key_exists($s_key, $this->am_values)) {
			return array();
		}
		return $this->am_values[$s_key];
	}

 	public function getFirstValueFor($s_key) {
		if (!array_key_exists($s_key, $this->am_values) || count($this->am_values[$s_key]) == 0) {
			return null;
		}
		return $this->am_values[$s_key][0];
	}

}
