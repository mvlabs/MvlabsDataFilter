<?php

namespace MVA\DataFilter\Operator;

/**
 * StringComparisonOperator Class
 *
 * @author Stefano Maraspin <s.maraspin@mvassociates.it>
 * @copyright MvLabs 2011
 * @link http://www.mvlabs.it
 * @package mva-libs
 * @since 0.5
 */
class StringComparison extends \MVA\DataFilter\Operator\Comparison {

	/**
	 * Equal, Case Sensitive
	 * Thwo entities are considered equale if they are identical
	 * or if there's a case only difference between the two
	 * @var int
	 */
	const EQUALCI = 2;

	/**
	 * Strictly Shorter Than
	 * @var int
	 */
	const SHORTERTHAN = 17;

	/**
	 * Strictly Longer than
	 * @var int
	 */
	const LONGERTHAN = 18;

	/**
	 * Shorter or equal than
	 * @var int
	 */
	const SHORTEROREQUALTHAN = 19;

	/**
	 * Longer or equal than
	 * @var int
	 */
	const LONGEROREQUALTHAN = 20;

	/**
	 * Entity contained within other entity, Case Sensitive
	 * @var int
	 */
	const CONTAINEDCI = 7;

	/**
	 * Entity contained within other entity, Case Insensitive
	 * @var int
	 */
	const CONTAINED = 8;

	/**
	 * Part of entity contained within other entity, Case Sensitive
	 * @var int
	 */
	const LOOSEMATCHCI = 9;

	/**
	 * Part of entity contained within other entity, Case Insensitive
	 * @var int
	 */
	const LOOSEMATCH = 10;

	/**
	 * Entity Starts With, Caswe Sensitive
	 * @var int
	 */
	const STARTSWITHCI = 11;

	/**
	 * Entity Starts With, Case Insensitive
	 * @var int
	 */
	const STARTSWITH = 12;

	/**
	 * Entity Ends With, Case Sensitive
	 * @var int
	 */
	const ENDSWITHCI = 13;

	/**
	 * Entity Ends With, Case Insensitive
	 * @var int
	 */
	const ENDSWITH = 14;

	/**
	 * Different, Ignoring Case
	 * @var int
	 */
	const DIFFERENTCI = 16;

}
