<?php

include("database.php");

include("db.class.php");

$wdb = new db_class;

//$wdb->db_connect();


function getvalue($action){

		$requestdata = array_merge($_POST,$_GET);

		if(array_key_exists($action,$requestdata))

		{

			return $requestdata[$action];		   

		}

	}

?>