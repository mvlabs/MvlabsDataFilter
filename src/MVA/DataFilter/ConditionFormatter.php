<?php

namespace MVA\DataFilter;

/**
 * Abstract Formatter Class
 *
 * Filtering Logic Formatters (IE to turn condition trees into commonly used tools - IE SQL)
 * need to extend this class
 * @author Stefano Maraspin <s.maraspin@mvassociates.it>
 * @copyright MvLabs 2011
 * @link http://www.mvlabs.it
 * @package mva-libs
 * @since 0.5
 *
 */
abstract class ConditionFormatter {

	/**
	 * @var \MVA\DataFilter\Condition Selection Criteria Item
	 */
	protected $I_condition;

}
