<?php
/**
 * Documentation, may be multiline
 * 
 * @service Calculator
 */
class Calculator {
	
	/**
	 * @param float $a
	 * @param float $b
	 * @return float
	 */
	function add($a, $b) { 
	   return $a + $b;
	}

	/**
	 * @param float $a
	 * @param float $b
	 * @return float
	 */
	function mul($a, $b) { 
	   return $a * $b;
	}

	/**
	 * @param float $a
	 * @param float $b
	 * @return float
	 */
	function sub($a, $b) { 
	   return $a - $b;
	}

	/**
	 * @param float $a
	 * @param float $b
	 * @return float
	 */
	function div($a, $b) { 
	   return $a / $b;
	}
}
?>
