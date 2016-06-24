<?php

namespace MVA\DataFilter\Condition;

/**
 * Composite Condition Entry
 *
 * A composite condition is made up by one or more Atomic conditions, tied together
 * by an OR or AND clause
 * @author Stefano Maraspin <s.maraspin@mvassociates.it>
 * @copyright MvLabs 2011
 * @link http://www.mvlabs.it
 * @package mva-libs
 * @package mva-libs
 * @since 0.5
 *
 */
class Composite extends \MVA\DataFilter\Condition {

	/**
	 * Composite Condition sub-conditions
	 * @var array conditions
	 */
	protected $am_conditions;

	/**
	 * Creates a composite condition object
	 * @param MVA\DataFilter\Operator\Logical $I_operator
	 * @param array $aI_conditions
	 */
	public function __construct(array $aI_conditions, \MVA\DataFilter\Operator\Logical $I_operator = null) {

		if (null == $I_operator) {
			$I_operator = new \MVA\DataFilter\Operator\Logical(\MVA\DataFilter\Operator\Logical::ET);
		}

		$this->I_operator = $I_operator;
		$this->am_conditions = array();
		$this->setConditions($aI_conditions);
	}

	/**
	 * Sets Current Condition Operator
	 * @param MVA\DataFilter\Operator\Logical Logical Operator
	 * @see FilteringLogic/MVA\DataFilter.Condition::setOperator()
	 */
	public function setOperator(\MVA\DataFilter\Operator $I_operator) {

	    if (!($I_operator instanceof \MVA\DataFilter\Operator\Logical)) {
	        throw new \Exception("Instance of \MVA\DataFilter\Operator\Logical expected as a parameter. Received " .
	                             get_class($I_operator) . " instead."
	                            );
	    }

		$this->I_operator = $I_operator;
	}

	/**
	 * Returns Current Composite Condition Operator
	 * @return MVA\DataFilter\Operator\Logical Logical Operator
	 * @see FilteringLogic/MVA\DataFilter.Condition::getOperator()
	 */
	public function getOperator() {
		return $this->I_operator;
	}

	/**
	 * Returns an array of current level conditions (objects)
	 * @return array Current Level Conditions
	 */
	public function setConditions(array $aI_conditions) {
		foreach ($aI_conditions as $I_condition) {
				$this->addCondition($I_condition);
		}
	}

	/**
	 * Adds a sub-condition to current condition list
	 * @param Condition Condition to be added
	 */
	public function addCondition(\MVA\DataFilter\Condition $I_condition = null) {
		if ($I_condition instanceof \MVA\DataFilter\Condition) {
			$this->am_conditions[] = $I_condition;
		} elseif ($I_condition != null) {
			throw new \Exception('Attempt to add an invalid condition');
		}
	}

	/**
	 * Returns an array of current level conditions (objects)
	 * @return array Current Level Conditions
	 */
	public function getConditions() {
		return $this->am_conditions;
	}

	/**
	 * Returns a multidimensional array, representing the conditions as a tree
	 * @return array Condition Tree
	 */
	public function asArray() {
		$am_partialResults = array();
		$i_validConditionCount = 0;

		foreach($this->getConditions() as $I_condition) {
			if ($I_condition instanceof \MVA\DataFilter\Condition) {
				$am_partialResults[] = $I_condition->asArray($I_condition);
				$i_validConditionCount++;
			}
 		}	// foreach

		switch($i_validConditionCount) {
			case 0:
				return array();
			case 1:
				return $am_partialResults[0];
			default:
				$am_condition['type'] = \MVA\DataFilter\Condition::COMPOSITETYPE;
				$am_condition['operator'] = $this->getOperator()->getType();
				$am_condition['data'] = $am_partialResults;
				$as_result[] = $am_condition;
				return $as_result;
		} // switch
	}

	/**
	 * Returns a one dimension array, representing the single condition components as a list
	 *
	 * It's worth noticing that by using this method logical combinations will be lost.
	 * Hence it has to be used only when it's necessary to fetch all Condition parts
	 * completely ignoring the relations between them
	 * @return array Condition List
	 */
	public function asList() {

		$am_partialResults = array();
		$i_validConditionCount = 0;

		$am_conditions = $this->getConditions();

		foreach($am_conditions as $I_condition) {
			if ($I_condition instanceof \MVA\DataFilter\Condition) {
				$am_partialResults = array_merge($am_partialResults, $I_condition->asList($I_condition));
				$i_validConditionCount++;
			}
	 	}	// foreach

		switch($i_validConditionCount) {
			case 0:
				return array();
			default:
				return $am_partialResults;
		} // switch
	}


}
