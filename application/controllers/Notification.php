<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Notification extends CI_Controller {

	/**
	 * Index Page for this controller.
	 *
	 * Maps to the following URL
	 * 		http://example.com/index.php/welcome
	 *	- or -
	 * 		http://example.com/index.php/welcome/index
	 *	- or -
	 * Since this controller is set as the default controller in
	 * config/routes.php, it's displayed at http://example.com/
	 *
	 * So any other public methods not prefixed with an underscore will
	 * map to /index.php/welcome/<method_name>
	 * @see https://codeigniter.com/user_guide/general/urls.html
	 */

	public function __construct() {
        parent::__construct();

        $this->load->model(array(
            "Mod_notification","mod_common"
        ));
        
    }

	public function index()
	{
		//$data['promo_list'] = $this->Mod_notification->manage_cities();
		$data['notification_list'] = $this->Mod_notification->manage_notification();
		
		$data["filter"] = '';
		#----load view----------#
		$data["title"] = "Manage Notifications";		

		$this->load->view($this->session->userdata('language')."/notification/manage_notification",$data);
		
			 
			
	}

	public function add_notification()
	{
    	$table='tbl_promo_code';       
        $data['promo_list'] = $this->mod_common->get_all_records($table,"*");    			
		$this->load->view($this->session->userdata('language')."/notification/add_notification",$data);
	}

	public function add(){
		
		{
		if($this->input->server('REQUEST_METHOD') == 'POST'){

			$add=  $this->Mod_notification->add_notification($this->input->post());

			if ($add) {
			 	$this->session->set_flashdata('ok_message', 'You have succesfully added.');
	            //redirect(SURL . 'notification/');
	            //$this->load->view('Company/add',$add);
	        } else {
	            $this->session->set_flashdata('err_message', 'Adding Operation Failed.');
	            //redirect(SURL . 'notification/');
	        }
	    }
	   	$data["filter"] = 'notification';
		#----load view----------#
		$data["title"] = "notification";	
		$this->load->view($this->session->userdata('language')."/notification/add",$data);
	}
	}

	public function edit($id){
		if($id){
			$table='tbl_promo_code';       
        $data['promo_list'] = $this->mod_common->get_all_records($table,"*"); 
			$table='tbl_notifications';
			$where = "transid='$id'";
			$data['notification'] = $this->mod_common->select_single_records($table,$where);
			
			//pme($data['country']);
			$this->load->view($this->session->userdata('language')."/notification/edit", $data);
		}
	}

	public function update(){
			if($this->input->server('REQUEST_METHOD') == 'POST'){
			
			
			$update=  $this->Mod_notification->update_notification($this->input->post());

			if ($update) {
			 	$this->session->set_flashdata('ok_message', 'You have succesfully updated.');
	            redirect(SURL . 'notification/');
	        } else {
	            $this->session->set_flashdata('err_message', 'Operation Failed.');
	            redirect(SURL . 'notification/');
	        }
	    }

	}

	public function delete($promo) {
		   // $udata['promo_code'] = $this->input->post('pcode');
		 
		    if ($this->Mod_notification->under_area($promo)) {
			$this->session->set_flashdata('err_message', 'This PromoCode is Used by order you can not delete it.');
			redirect(SURL . 'notification/');
			exit();
		}

	 
		#-------------delete record--------------#
        $table = "tbl_notifications";
        $where = "transid = '" . $promo . "'";
        $delete_country = $this->mod_common->delete_record($table, $where);
		

        if ($delete_country) {
            $this->session->set_flashdata('ok_message', 'You have succesfully deleted.');
            redirect(SURL . 'notification/');
        } else {
            $this->session->set_flashdata('err_message', 'Deleting Operation Failed.');
            redirect(SURL . 'notification/');
        }
    }
	

       function send_message1(){ 
    $content = array(
        "en" => 'Testing Message'
        );

    $fields = array(
        'app_id' => "be4a92de-bfe3-4cc4-9a9c-be96773bbd2f",
        'included_segments' => array('All'),
        //'data' => array("foo" => "bar"),
        //'large_icon' =>"ic_launcher_round.png",
        'contents' => $content
    );

    $fields = json_encode($fields);  //var_dump($fields);
//print("\nJSON sent:\n");
//print($fields);

    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, "https://app.onesignal.com/apps/be4a92de-bfe3-4cc4-9a9c-be96773bbd2f/notifications");
    curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json; charset=utf-8',
                                               'Authorization: Basic ZmM3NTU4OTItYmE3MS00ZWFkLTgzOWUtMzdiZGIzZWNlOWNi'));
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
    curl_setopt($ch, CURLOPT_HEADER, FALSE);
    curl_setopt($ch, CURLOPT_POST, TRUE);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $fields);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);    

    $response = curl_exec($ch);
    curl_close($ch);

    //var_dump($response);
}


function send_message(){

	$title = $this->input->post("title"); 
	$short = $this->input->post("short"); 

        $message = "just a test";
       
        $content = array(
            "en" => "$short"
        );

        $fields = array(
            'app_id' => "be4a92de-bfe3-4cc4-9a9c-be96773bbd2f",
             //'filters' => array(array("field" => "tag", "key" => "user_id", "relation" => "=", "value" => "$user_id")),
            'included_segments' => array('All'),
            'headings' => array("en"=>"$title"),  
            'large_icon' => 'https://evtsolutionz.com/lpgapp/icon/company.png',
            'chrome_web_image' => 'https://cdn4.iconfinder.com/data/icons/iconsimple-logotypes/512/github-512.png',
            'contents' => $content
        );

        $fields = json_encode($fields);
        // print("\nJSON sent:\n");
        // print($fields);

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, "https://onesignal.com/api/v1/notifications");
        curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json; charset=utf-8',
            'Authorization: Basic ZmM3NTU4OTItYmE3MS00ZWFkLTgzOWUtMzdiZGIzZWNlOWNi'));
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
        curl_setopt($ch, CURLOPT_HEADER, FALSE);
        curl_setopt($ch, CURLOPT_POST, TRUE);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $fields);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);

        $response = curl_exec($ch);
        curl_close($ch);
        if(response){
        	
        	if($this->input->server('REQUEST_METHOD') == 'POST'){

			$add=  $this->Mod_notification->add_notification($this->input->post());

			if ($add) {
			 	$this->session->set_flashdata('ok_message', 'You have succesfully added.');
	            $data['notification_list'] = $this->Mod_notification->manage_notification();
		
		$data["filter"] = '';
		#----load view----------#
		$data["title"] = "Manage Notifications";		

		$this->load->view($this->session->userdata('language')."/notification/manage_notification",$data);
	        } else {
	            $this->session->set_flashdata('err_message', 'Adding Operation Failed.');
	            //redirect(SURL . 'notification/');
	        }
	      }
        }
       

        	
         //redirect(SURL . 'notification/');
       //var_dump($response);
    }





}



  


