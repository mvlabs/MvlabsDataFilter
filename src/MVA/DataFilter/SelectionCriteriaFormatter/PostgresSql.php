<?php

namespace MVA\DataFilter\SelectionCriteriaFormatter;

/**
 * Bridges the GAP between MVA FilteringLogic Objects and SQL
 *
 * This class allows for filtering and ordering operations to be specified in Model terms and then
 * converted in SQL only when needed
 * @author Stefano Maraspin <s.maraspin@mvassociates.it>
 * @copyright MvLabs 2011
 * @link http://www.mvlabs.it
 * @package mva-libs
 * @since 0.5
 *
 */
class PostgresSql extends \MVA\DataFilter\SelectionCriteriaFormatter {

	/**
	 * Wildcard character
	 * @var string SQL WildChar
	 */
	protected $s_wildchar = '%';

	/**
	 * @var array mapping domain entities -> ORM entities/aliases
	 */
	protected $am_mapping = array();

	/**
	 * @var Utility_SelectionCriteria selection criteria
	 */
	protected $I_selectionCriteria;

	/**
	 *
	 * @var string SQL (Result) String
	 */
	protected $s_sqlWhere = null;

	/**
	 * @var array bound parameters for SQL prepared statement
	 */
	protected $am_params = null;

	protected $i_paramCount = 0;

	/**
	 * Item Constructor
	 * @param \MVA\DataFilter\SelectionCriteria Selection Criteria Item
	 * @param array Mapping from Model entities to Doctrine DQL Entities / Aliases
	 */
	public function __construct( array $am_mapping) {
		// We start up with an empty selection criteria
		$this->am_mapping = $am_mapping;
		$this->am_params = array();
	}

	public function setSelectionCriteria(\MVA\DataFilter\SelectionCriteria $I_selectionCriteria) {
		$this->I_selectionCriteria = $I_selectionCriteria;
		$s_sqlWhere = $this->extractWhereCriteria($this->I_selectionCriteria->getCondition());
		$this->s_sqlWhere = $s_sqlWhere;
	}

	/**
	 * Returns SQL Query
	 * @return string SQL String
	 */
	public function getQuery() {
		$s_outString = '';
		$s_outString .= $this->getWhere();
		$s_outString .= $this->getOrderBy();
		$s_outString .= $this->getLimit();
		$s_outString .= $this->getOffset();
		return $s_outString;
	}

	public function getWhere($b_ignoreCachedQuery = false) {
		$s_out = $this->extractWhere($b_ignoreCachedQuery, 'WHERE');
		return $s_out;
	}

	public function getAndWhere($b_ignoreCachedQuery = false) {
		return $this->extractWhere($b_ignoreCachedQuery, 'AND');
	}

	public function getOrWhere($b_ignoreCachedQuery = false) {
		return $this->extractWhere($b_ignoreCachedQuery, 'OR');
	}

	public function getWhereBasis($b_ignoreCachedQuery = false) {
		return $this->extractWhere($b_ignoreCachedQuery);
	}

	protected function extractWhere($b_ignoreCachedQuery = false, $s_destination = '') {

		$this->i_paramCount = 0;
		if ($b_ignoreCachedQuery) {
			$this->s_sqlWhere = $this->extractWhereCriteria($this->I_selectionCriteria->getCondition());
		}

		if (strlen(trim($this->s_sqlWhere))>0) {
			$s_out = ' ' . $s_destination . ' ' . $this->s_sqlWhere;
			return $s_out;
		} else {
			return '';
		}
	}


	public function getParams() {
		return $this->am_params;
	}


	/**
	 * Returns SQL Order clauses for orderby clauses
	 * @param mixed Order Criteria (either one or more)
	 * @return string DQL Order clause
	 */
	public function getOrderBy() {

		if ($this->I_selectionCriteria instanceof \MVA\DataFilter\SelectionCriteria) {
			$s_tempResult = $this->extractOrderBy($this->I_selectionCriteria->getCondition());
			if (strlen(trim($s_tempResult))>0) {
				return ' ORDER BY ' . $s_tempResult;
			}
		}
		return null;
	}


	public function extractOrderBy(){

		$s_outString = '';
		$i_validClauses = 0;

		$as_orderByConstructs = array(\MVA\DataFilter\SelectionCriteria::ORDER_ASCENDING => 'ASC',
		                              \MVA\DataFilter\SelectionCriteria::ORDER_DESCENDING => 'DESC',
		                              );
		$as_ordering = $this->I_selectionCriteria->getOrdering();

		foreach ($as_ordering as $s_field => $m_ordering) {

			$as_parts = explode(".", $s_field);
			$i_partCount = count($as_parts);

			if (2 == $i_partCount) {
				if (array_key_exists($as_parts[0], $this->am_mapping)) {
					if ($i_validClauses > 0) { $s_outString .= ', '; }
					$s_outString .= $this->am_mapping[$as_parts[0]].'.'.$as_parts[1].' '.$as_orderByConstructs[$m_ordering];
					$i_validClauses++;
				} else {
					throw new \MVA\DataFilter\Exception('Invalid order by field: '.\MVA\Core\Debug::dumpToString($s_field).
					                    ' passed to '.__CLASS__.' - No Item named '.$as_parts[0].' within the Model');
				}
			} else {
				throw new \MVA\DataFilter\Exception('Invalid order by field: '.\MVA\Core\Debug::dumpToString($s_field).
				                    ' passed to '.__CLASS__.' - Wrong Syntax');
			}
			return $s_outString;
		}
	}


	/**
	 * Returns Limit clauses
	 *
	 * @todo: Implement!
	 *
	 * @return string DQL Order clause
	 */
	public function getLimit() {
		if ($this->I_selectionCriteria instanceof \MVA\DataFilter\SelectionCriteria) {
			$i_limit = $this->I_selectionCriteria->getLimit();
			if (!empty($i_limit)) {
				return ' LIMIT '.$i_limit;
			}
		}
	}


	public function getOffset() {
		if ($this->I_selectionCriteria instanceof \MVA\DataFilter\SelectionCriteria) {
			$i_offset = $this->I_selectionCriteria->getOffset();
			if (!empty($i_offset)) {
				return ' OFFSET '.$i_offset;
			}
		}
	}


	/**
	 * Extracts Where Criteria and prepares where statement content.
	 *
	 * Does not add the WHERE word, though. Which is added through getWhere method
	 * but only if this method returns a non empty string
	 * @param $I_condition condition tree
	 * @return string Query where clauses
	 */
	protected function extractWhereCriteria(\MVA\DataFilter\Condition $I_condition = null) {

		// Base Case - Condition is Atomic (IE there are no AND/OR combinations involved)
		if ($I_condition instanceof \MVA\DataFilter\Condition\InputData) {

			// @FIXME: Take advantage of polimorphism here. Temp code, until decided how to refactor
			$as_param = array();
			$s_name = $I_condition->getSourceField();
			$s_placeholder = str_replace(".","_",$s_name).'_'.$this->i_paramCount++;
			$s_composedQuery = '';
			$I_operator = $I_condition->getOperator();
			$i_operator = $I_operator->getType();
			$s_term = $I_condition->getComparisonValue();

			if ($I_operator instanceof \MVA\DataFilter\Operator\NullComparison) {

				$as_output = array (\MVA\DataFilter\Operator\NumericComparison::EQUAL => ' IS NULL ',
									\MVA\DataFilter\Operator\NumericComparison::DIFFERENT => ' IS NOT NULL',
									);

				if (!array_key_exists($i_operator, $as_output)) {
					throw new \MVA\DataFilter\Exception\OperatorException('Invalid Null Comparison Operator: '.$I_condition->getOperator());
				}

				$as_param[$s_placeholder] = $s_term;
				$s_composedQuery = $this->getAliasedFieldName($s_name) . $as_output[$i_operator];
				$as_param = array();
				goto assignparams;

			}

			switch($i_operator) {
				case \MVA\DataFilter\Operator\Comparison::EQUAL:
					$s_composedQuery = $this->getAliasedFieldName($s_name) . '= :' . $s_placeholder;
					$as_param[$s_placeholder] = $s_term;
					goto assignparams;
					break;

				case \MVA\DataFilter\Operator\Comparison::DIFFERENT:
					$s_composedQuery = $this->getAliasedFieldName($s_name).'<> :' . $s_placeholder;
					$as_param[$s_placeholder] = $s_term;
					goto assignparams;
					break;

				default:
					break;
			}

			if ($I_operator instanceof \MVA\DataFilter\Operator\NumericComparison) {

				$as_output = array (\MVA\DataFilter\Operator\NumericComparison::LESSTHAN => '< :',
									\MVA\DataFilter\Operator\NumericComparison::LESSOREQUALTHAN => '<= :',
									\MVA\DataFilter\Operator\NumericComparison::MOREOREQUALTHAN => '>= :',
									\MVA\DataFilter\Operator\NumericComparison::MORETHAN => '> :'
									);

				if (!array_key_exists($i_operator, $as_output)) {
					throw new \MVA\DataFilter\Exception\OperatorException('Invalid Atomic Comparison Operator: '.$I_condition->getOperator());
				}

				$as_param[$s_placeholder] = $s_term;
				$s_composedQuery = $this->getAliasedFieldName($s_name) . $as_output[$i_operator] . $s_placeholder;


			} elseif ($I_operator instanceof \MVA\DataFilter\Operator\StringComparison) {

				$as_output = array (\MVA\DataFilter\Operator\StringComparison::EQUALCI => array('match' => 'UPPER('  . $this->getAliasedFieldName($s_name) . ') = :', 'value' => strtoupper($s_term)),
				                    \MVA\DataFilter\Operator\StringComparison::SHORTERTHAN => array('match' => 'LENGTH('  . $this->getAliasedFieldName($s_name) . ') < :', 'value' => strlen($s_term)),
									\MVA\DataFilter\Operator\StringComparison::LONGERTHAN => array('match' => 'LENGTH('  . $this->getAliasedFieldName($s_name) . ') > :', 'value' => strlen($s_term)),
									\MVA\DataFilter\Operator\StringComparison::SHORTEROREQUALTHAN => array('match' => 'LENGTH(' . $this->getAliasedFieldName($s_name) . ') <= :', 'value' => strlen($s_term)),
				                    \MVA\DataFilter\Operator\StringComparison::LONGEROREQUALTHAN => array('match' => 'LENGTH(' . $this->getAliasedFieldName($s_name) . ') >= :', 'value' => strlen($s_term)),
				                    \MVA\DataFilter\Operator\StringComparison::CONTAINEDCI => array('match' => 'UPPER(' . $this->getAliasedFieldName($s_name) . ') LIKE :', 'value' => '%' . strtoupper($s_term) . '%'),
				                    \MVA\DataFilter\Operator\StringComparison::CONTAINED => array('match' => $this->getAliasedFieldName($s_name) . ' LIKE :', 'value' => '%' . $s_term . '%'),
				                    \MVA\DataFilter\Operator\StringComparison::STARTSWITHCI => array('match' => 'UPPER(' . $this->getAliasedFieldName($s_name) . ') LIKE :', 'value' => strtoupper($s_term) . '%'),
				                    \MVA\DataFilter\Operator\StringComparison::STARTSWITH => array('match' => $this->getAliasedFieldName($s_name) . ' LIKE :', 'value' => strtoupper($s_term) . '%'),
				                    \MVA\DataFilter\Operator\StringComparison::ENDSWITHCI => array('match' => 'UPPER(' . $this->getAliasedFieldName($s_name) . ') = :', 'value' => '%' . strtoupper($s_term)),
				                    \MVA\DataFilter\Operator\StringComparison::ENDSWITH => array('match' => $this->getAliasedFieldName($s_name) . ' LIKE :', 'value' => '%' . $s_term),
				                    \MVA\DataFilter\Operator\StringComparison::DIFFERENTCI => array('match' => 'UPPER(' . $this->getAliasedFieldName($s_name) . ') <> :', 'value' => strtoupper($s_term)),
									);

				if (array_key_exists($i_operator, $as_output)) {

					$as_param[$s_placeholder] = $as_output[$i_operator]['value'];
					$s_composedQuery = $as_output[$i_operator]['match'] . $s_placeholder;


				} else if (\MVA\DataFilter\Operator\StringComparison::LOOSEMATCHCI == $i_operator) {

					$as_param = array();
					$s_composedQuery = '(';

					$as_parts = explode(' ', $s_term);
					$i_partsCount = count($as_parts);

					for ($x = 0; $x < $i_partsCount; $x++) {
						if ($x != 0) {
							$s_composedQuery .= ' OR ';
						}
						$as_param[$s_placeholder . '_' . $x] = '%' . strtoupper($as_parts[$x]) . '%';
						$s_composedQuery .= 'UPPER (' . $this->getAliasedFieldName($s_name) . ') LIKE :' . $s_placeholder . '_' . $x;
					}
					$s_composedQuery .= ')';

				} else if (\MVA\DataFilter\Operator\StringComparison::LOOSEMATCH == $i_operator) {

					$as_param = array();
					$s_composedQuery = '(';

					$as_parts = explode(' ', $s_term);
					$i_partsCount = count($as_parts);

					for ($x = 0; $x < $i_partsCount; $x++) {
						if ($x != 0) {
							$s_composedQuery .= ' OR ';
						}
						$as_param[$s_placeholder . '_' . $x] = '%' .$as_parts[$x] . '%';
						$s_composedQuery .= $this->getAliasedFieldName($s_name) . ' LIKE :' . $s_placeholder . '_' . $x;
					}
					$s_composedQuery .= ')';

				} else {
					throw new \MVA\DataFilter\Exception\OperatorException('Invalid String Comparison Operator: ' . $I_condition->getOperator());
				}

			}

			assignparams:
			foreach ($as_param as $s_placeholder => $s_currentPar) {
				$this->am_params[$s_placeholder] = $s_currentPar;
			}

			return $s_composedQuery;


		} else if ($I_condition instanceof \MVA\DataFilter\Condition\ExistingData) {

				/*
				$am_temp = $I_condition->getRaw();
				$s_composedQuery = '';

				$as_parts1 = explode(".", $am_temp['Source']);
				$as_parts2 = explode(".", $am_temp['Dest']);
				if (!in_array($as_parts1[0], $this->as_unused) &&
				    !in_array($as_parts2[0], $this->as_unused)) {

					switch ($am_temp['Operator']) {
						case Utility_InternalCondition::EQUAL:
							$s_Operator = ' = ';
							break;

						case Utility_InternalCondition::LESSTHAN:
							$s_Operator = ' < ';
							break;

						case Utility_InternalCondition::LESSOREQUALTHAN:
							$s_Operator = ' <= ';
							break;

						case Utility_InternalCondition::MOREOREQUALTHAN:
							$s_Operator = ' >= ';
							break;

						case Utility_InternalCondition::MORETHAN:
							$s_Operator = ' > ';
							break;

						default:
							throw new exception('Invalid Internal Comparison Operator: '.$am_temp['Operator']);
							break;
					}
					return $this->getAliasedFieldName($am_temp['Source']).$s_Operator.$this->getAliasedFieldName($am_temp['Dest']);
				} else {
					return '';
				}
				*/

				return 'Existing';

			} else if($I_condition instanceof \MVA\DataFilter\Condition\Composite) {

				// Complex Case - Condition is Composite (IE there are one or more AND/OR combinations involved)
				$s_outString = '';

				$aI_conditions = $I_condition->getConditions();
				$i_bound = count($aI_conditions);

				if (0 != $i_bound) {
					$s_outString .= '(';
					for($i = 0; $i < $i_bound; $i++) {
						$I_currentCondition = $aI_conditions[$i];
						$s_nextCond = $this->extractWhereCriteria($I_currentCondition);
						if ($i != 0 && strlen(trim($s_nextCond)) > 0) {
							$i_op = $I_condition->getOperator()->getType();
							switch ($i_op) {
								case \MVA\DataFilter\Operator\Logical::ET:
									 $s_logicOperator = ' AND ';
									break;
								case \MVA\DataFilter\Operator\Logical::VEL:
									$s_logicOperator = ' OR ';
									break;
								default:
									throw new \Exception('Invalid Logic Combination Operator: '.$i_op);
									break;
							}

							$s_outString .= ' '.$s_logicOperator.' ';
						}
						$s_outString .= $s_nextCond;
					}
					$s_outString .= ')';
				}

				return $s_outString;

			} elseif (null == $I_condition) {
				return null;
			} else {
				throw new Exception('Invalid Condition has been Provided');
			}

	}


	/**
	 * Returns Aliased Field Name
	 * @param string field name
	 * @return string field alias
	 */
	public function getAliasedFieldName($s_field) {
		$as_parts = explode(".", $s_field);
		$i_partCount = count($as_parts);

		if (2 == $i_partCount) {
			if (array_key_exists($as_parts[0], $this->am_mapping)) {
				$s_outString = $this->am_mapping[$as_parts[0]] . '.' . $as_parts[1];
			} else {
				throw new \MVA\DataFilter\Exception('Invalid field: ' . \MVA\Core\Debug::dumpToString($s_field) .
				                    ' passed to ' . __CLASS__ . ' - No Item named ' . $as_parts[0] . ' within the Model');
			}
		} else {
			throw new \MVA\DataFilter\Exception('Invalid field: ' . \MVA\Core\Debug::dumpToString($s_field) .
			                    ' passed to ' . __CLASS__ . ' - Wrong Syntax');
		}
		return $s_outString;
	}


	/**
	 * Returns Table and Alias Name construct
	 * @param string Table name
	 * @return string Table and table aliased name
	 */
	public function getAliasedTable($s_businessModelEntity) {
		return $s_businessModelEntity.' AS ' . $this->am_mapping[$s_businessModelEntity];
	}


	public function getEntityAlias($s_businessModelEntity) {
		return $this->am_mapping[$s_businessModelEntity];
	}


}
