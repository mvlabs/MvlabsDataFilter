<?php

namespace MVA\DataFilter\Operator;

/**
 * NumericComparisonOperator Class
 *
 * @author Stefano Maraspin <s.maraspin@mvassociates.it>
 * @copyright MvLabs 2011
 * @link http://www.mvlabs.it
 * @package mva-libs
 * @since 0.5
 */
class NumericComparison extends \MVA\DataFilter\Operator\Comparison {

	/**
	 * Entity 1 is strictly less than entity 2
	 * @var int
	 */
	const LESSTHAN = 3;

	/**
	 * Entity 1 is strictly more than entity 2
	 * @var int
	 */
	const MORETHAN = 4;

	/**
	 * Entity 1 is less or equal to entity 2
	 * @var int
	 */
	const LESSOREQUALTHAN = 5;

	/**
	 * Entity 1 is more or equal to entity 2
	 * @var int
	 */
	const MOREOREQUALTHAN = 6;




}
