<?php
/**
 * @service CalculatorSoapClient
 */
class CalculatorSoapClient{
	/**
	 * The WSDL URI
	 *
	 * @var string
	 */
	public static $_WsdlUri='http://localhost/Assign1/calculator/server.php?WSDL';
	/**
	 * The PHP SoapClient object
	 *
	 * @var object
	 */
	public static $_Server=null;

	/**
	 * Send a SOAP request to the server
	 *
	 * @param string $method The method name
	 * @param array $param The parameters
	 * @return mixed The server response
	 */
	public static function _Call($method,$param){
		if(is_null(self::$_Server))
			self::$_Server=new SoapClient(self::$_WsdlUri);
		return self::$_Server->__soapCall($method,$param);
	}

	/**
	 * @param float $a
	 * @param float $b
	 * @return float
	 */
	public function add($a,$b){
		return self::_Call('add',Array(
			$a,
			$b
		));
	}

	/**
	 * @param float $a
	 * @param float $b
	 * @return float
	 */
	public function mul($a,$b){
		return self::_Call('mul',Array(
			$a,
			$b
		));
	}

	/**
	 * @param float $a
	 * @param float $b
	 * @return float
	 */
	public function sub($a,$b){
		return self::_Call('sub',Array(
			$a,
			$b
		));
	}

	/**
	 * @param float $a
	 * @param float $b
	 * @return float
	 */
	public function div($a,$b){
		return self::_Call('div',Array(
			$a,
			$b
		));
	}
}