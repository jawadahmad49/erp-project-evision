<?php

defined('BASEPATH') OR exit('No direct script access allowed');

if ( ! function_exists('pme'))
{
	
	function pm(array $array_data)
	{
		echo '<pre>';
		print_r($array_data);
		exit;
	}
}


if ( ! function_exists('q'))
{
	function q(){
		$CI =& get_instance();
		echo  $CI->db->last_query();
		exit;
	}
}

if ( ! function_exists('set_value'))
{
	function set_value($value){

		if(isset($value) && $value !='')
		{
			return $value;
		}
	}
}

if ( ! function_exists('select_element'))
{
	function select_element($form_value,$list){

		if(isset($form_value) && $form_value ==$list)
		{
			return 'selected';
		}
		else {
			return '';
		}
	}
}
if ( ! function_exists('set_checkbox'))
{
	function set_checkbox($value){

		if(isset($value) && $value =='Yes')
		{
			return 'checked';
		}
		if(isset($value) && $value =='No')
		{
			return '';
		}
	}
}
