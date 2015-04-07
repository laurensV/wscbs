<?php
/**
 * Documentation, may be multiline
 * 
 * @service Calculator
 */
class Calculator {
	/**
	 * @param integer $a
	 * @param integer $b
	 * @return int
	 */
	function add($a, $b) { 
	   return $a + $b;
	}

	/**
	 * @param integer $a
	 * @param integer $b
	 * @return int
	 */
	function mul($a, $b) { 
	   return $a * $b;
	}

	/**
	 * @param integer $a
	 * @param integer $b
	 * @return int
	 */
	function sub($a, $b) { 
	   return $a - $b;
	}

	/**
	 * @param integer $a
	 * @param integer $b
	 * @return int
	 */
	function div($a, $b) { 
	   return $a / $b;
	}
}
?>
