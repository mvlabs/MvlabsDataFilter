<?php

namespace MVA\DataFilter\Operator;

/**
 * Comparison Operator Base Abstract Class
 *
 * @author Stefano Maraspin <s.maraspin@mvassociates.it>
 * @copyright MvLabs 2011
 * @link http://www.mvlabs.it
 * @package mva-libs
 * @since 0.5
 */
abstract class Comparison extends \MVA\DataFilter\Operator {

	/**
	 * Equal
	 * @var int
	 */
	const EQUAL = 1;


	/**
	 * Different, Even Only by Case
	 * @var int
	 */
	const DIFFERENT = 15;

}
