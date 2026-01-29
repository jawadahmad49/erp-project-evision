<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class API extends CI_Controller {

	public function __construct() {
        parent::__construct();  
    }

    public function login(){
    	if($_SERVER['REQUEST_METHOD']=="POST"){
    		if(count($_POST)!="" && $_POST['phone']!="" && $_POST['password']!=""){

    			$cell = $_POST['phone'];
    			$password = $_POST['password'];
    			$checkUser = $this->db->get_where("tblacode",array("cell"=>$cell,"password"=>$password,"ac_status"=>"Active"));

    			if($checkUser->num_rows() > 0){
    				$data = $checkUser->row();
    				$response['error'] = "false";
    				$response['result'] = "Login successfully.";
    				$response['id'] = $data->id;
    				$response['aname'] = $data->aname;

    			}else if($checkUser->num_rows() == 0){
    				$response['error'] = "true";
    				$response['result'] = "Invalid phone or password.";
    			}

    		}else if(count($_POST)==""){
    			$response["error"] = "true";
    			$response['result'] =  "Parameters are missing!";
    		}
    	}else{
    		$response["error"] = "true";
    		$response['result'] =  "invalid request method";
    	}

    	echo json_encode($response);
    }
}
