<?php

namespace mh\Common;

class Random
{
	/**
	 * Generate the new password
	 *
	 * @access public
	 * @param array $params
	 * @return string
	 **/
	public static function generate($params = array())
	{

		$length     = (!array_key_exists('length', $params))     ? 15     :     $params['length'];
		$use_lower  = (!array_key_exists('use_lower', $params))  ? TRUE   :  $params['use_lower'];
		$use_upper  = (!array_key_exists('use_upper', $params))  ? TRUE   :  $params['use_upper'];
		$use_number = (!array_key_exists('use_number', $params)) ? TRUE   : $params['use_number'];
		//$use_custom = (!array_key_exists('use_custom', $params)) ? '-_()' : $params['use_custom'];

		$upper = "ABCDEFGHIJKLMNOPQRSTUVWXYZ";
		$lower = "abcdefghijklmnopqrstuvwxyz";
		$number = "0123456789";

		$seed_length = 0;
		$seed        = '';

		if($use_upper === TRUE){
			$seed_length += 26;
			$seed .= $upper;
		}
		if($use_lower === TRUE){
			$seed_length += 26;
			$seed .= $lower;
		}
		if($use_number === TRUE){
			$seed_length += 10;
			$seed .= $number;
		}
		if(!empty($use_custom)){
			$seed_length +=strlen($use_custom);
			$seed .= $use_custom;
		}
		$password = '';
		for($i = 1; $i <= $length; $i++){
			$password .= $seed{mt_rand(0,$seed_length-1)};
		}
		return $password;
	} // End of generate
} // End of Class Password