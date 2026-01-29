<?php

defined('BASEPATH') OR exit('No direct script access allowed');

if ( ! function_exists('pme'))
{
	
	function pme(array $array_data)
	{
		echo '<pre>';
		print_r($array_data);
		exit;

	}
if ( ! function_exists('q'))
{
	function q(){
		$CI =& get_instance();
		echo  $CI->db->last_query();
		exit;
	}
}	
}
