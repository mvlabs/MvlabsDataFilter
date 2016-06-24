<?php

namespace MVA\DataFilter;

/**
 * Clause (disgiuntion of , represented as a List)
 *
 * @author Stefano Maraspin <s.maraspin@mvassociates.it>
 * @author Stefano Valle <s.valle@mvassociates.it>
 * @copyright MvLabs 2011
 * @link http://www.mvlabs.it
 * @package mva-libs
 *
 */
class SelectionCriteria {

	/**
	 * Filtering Criteria
	 * @var \MVA\DataFilter\Condition
	 */
	protected $I_condition;

	/**
	 * Ascending Order Constant
	 * @var int
	 */
	const ORDER_ASCENDING = 1;

	/**
	 * Descending Order Constant
	 * @var int
	 */
	const ORDER_DESCENDING = 2;

	/**
	 * By default, no limit is set
	 */
	const DEFAULT_LIMIT = 0;

	/**
	 * Valid Ordering Clauses
	 * @var array
	 */
	protected $am_validOrderClauses;

	/**
	 * Ordering Criteria
	 * @var array
	 */
	protected $am_orderClauses;

	/**
	 * Filtering Limit on the Number of Returned Results
	 * @var int
	 */
	protected $i_limit;

	/**
	 * Filtering Returned Results Offset
	 * @var int
	 */
	protected $i_offset;

	/**
	 * Constructs new selection criteria object
	 * @param \MVA\DataFilter\Condition Filtering Condition
	 * @param array Ordering Clauses
	 * @param int Number of items to be returned
	 * @param int Offset
	 */
	public function __construct(\MVA\DataFilter\Condition $I_condition = null,
		                        array $am_orderClauses = null,
		                        $i_limit = self::DEFAULT_LIMIT,
		                        $i_offset = 0) {

		// A list of class constants is fetched and saved into am_validOrderClauses
		$I_currentClassRef = new \ReflectionClass($this);
		$am_const =  $I_currentClassRef->getConstants();
		foreach ($am_const as $s_name => $m_value) {
			$s_prefix = substr($s_name,0,5);
			if ('ORDER' == $s_prefix) {
				$this->am_validOrderClauses[$s_name] = $m_value;
			}
		}

		$this->I_condition = $I_condition;
		$this->setOrdering($am_orderClauses);
		$this->setLimit($i_limit);
		$this->setOffset($i_offset);

	}

	/**
	 * Sets Filtering Condition
	 * @param \MVA\DataFilter\Condition condition object
	 */
	public function setCondition(\MVA\DataFilter\Condition $I_condition = null) {
		$this->I_condition = $I_condition;
	}

	/**
	 * Removes Filtering Condition
	 */
	public function clearCondition() {
		$this->I_condition = null;
	}

	/**
	 * Gets Current Condition Object
	 * @return \MVA\DataFilter\Condition current condition object
	 */
	public function getCondition() {
		return $this->I_condition;
	}

	/**
	 * Removes all ordering clauses
	 */
	public function clearOrdering() {
		$this->am_orderClauses = null;
	}

	/**
	 * Sets ordering clauses
	 *
	 * If second parameter is set to true and a parameter clause is already
	 * present within ordering clauses, parameter ordering is actually ignored
	 * and current ordering is simply inverted. So, no matter if attribute X
	 * is sorted ascending and a new array('x' => ASC) is provided, the result
	 * will always be 'x' => 'DESC'. This is useful for all those cases when
	 * you need to quickly invert ordering without needing to know what's currently
	 * ordering criteria
	 * @param array Ordering clauses
	 * @param bool If false user submitted ordering overrides default behaviour
	 * @throws \Exception
	 */
	public function setOrdering(array $am_orderClauses = null, $b_overridePreference = false) {

		$f_sanitize = function($m_val) {
			if ($m_val == null) {
					return SelectionCriteria::ORDER_ASCENDING;
				} else {
					return $m_val;
				}
		};

		if(is_array($am_orderClauses))
		{
			// First off we veryfiy that ordering clauses are valid
			foreach ($am_orderClauses as $s_key => $m_ordering) {
				if (!in_array($m_ordering, $this->am_validOrderClauses) && $m_ordering != null) {
					throw new \Exception('Invalid Ordering Specified: '.$m_ordering.' for '.$s_key.' in '.__CLASS__);
				}
			}
		}

		if (!is_array($this->am_orderClauses)) {

			// If current ordering clause is not an array, we simply replace it with new stuff
			$this->am_orderClauses = $am_orderClauses;

		} else {

			// Things are messier otherwise. We need to take account existing clauses and possibly replace them
			$as_reversed = array_reverse($am_orderClauses, true);

			foreach ($as_reversed as $s_key => $m_ordering) {

				// There were previously defined clauses; let's see how to combine the two...
				if (array_key_exists($s_key,$this->am_orderClauses)) {
					 if($s_key == key($this->am_orderClauses)) {
					    // If item is first in list, sorting is just inverted
						if (!$b_overridePreference) {
							// New value is determined by previous one
							$m_newvalue = ((current($this->am_orderClauses)==self::ORDER_ASCENDING)?self::ORDER_DESCENDING:self::ORDER_ASCENDING);
						} else {
							// New value is user specified, no matter how it was set before
							$m_newvalue = $m_ordering;
						}
					 } else {
						    // If item is there but not first in list,
							$m_newvalue = $m_ordering;
					}
					// It was in, we need to delete old one
					unset($this->am_orderClauses[$s_key]);
				} else {
					// It wasn't there, we simply need to add it

					$m_newvalue = $m_ordering;
				}

				$ai_newElement = array($s_key => $m_newvalue);
				$this->am_orderClauses =  array_merge($ai_newElement, $this->am_orderClauses);

			} // End Foreach

		} // End Else

		if (is_array($this->am_orderClauses)) {
			$this->am_orderClauses = array_map($f_sanitize, $this->am_orderClauses);
		}
	} // End Method

	/**
	 * Returns Current Ordering
	 * @return array Ordering
	 */
	public function getOrdering() {
		return ((null !== $this->am_orderClauses?$this->am_orderClauses:array()));
	}

	/**
	 * Sets Limit
	 * @param int $i_limit
	 * @throws \Exception Limit has to be of type integer or an exception will be generated
	 */
	public function setLimit($i_limit) {
		if (!is_int($i_limit) && null !== $i_limit) {
			throw new \Exception('Unsupported Limit Type: '.gettype($i_limit).' in class '.__CLASS__);
		}
		$this->i_limit = $i_limit;
	}

	/**
	 * Returns Current Limit
	 * @return int Limit
	 */
	public function getLimit() {
		return $this->i_limit;
	}

	/**
	 * Sets Offset
	 * @param int $i_offset
	 * @throws \Exception Offset has to be of type integer or an exception will be generated
	 */
	public function setOffset($i_offset) {
		if (!is_int($i_offset)) {
			throw new \Exception('Unsupported Offset Type: '.gettype($i_offset).' in class '.__CLASS__);
		}
		$this->i_offset = $i_offset;
	}

	/**
	 * Returns Current Offset
	 * @return int Offset
	 */
	public function getOffset() {
		return $this->i_offset;
	}

}
