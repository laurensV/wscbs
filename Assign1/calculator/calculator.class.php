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
	* @throws Exception magic
	*/
	function div($a, $b) {
		if($b == 0) {
			return new SoapFault("Server", "Division By Zero");
		}
		return $a / $b;
	}
}
?>
