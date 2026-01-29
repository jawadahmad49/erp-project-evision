<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Deliverycharges extends CI_Controller {

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
            "mod_area","mod_common"
        ));
        
    }
	public function index()
	{
		$data['area_list'] = $this->mod_area->manage_areas();


		$table='deliverycharges';       
        $data['area_list'] = $this->mod_common->get_all_records($table,"*"); 
        

		
		$data["filter"] = '';
		#----load view----------#
		$data["title"] = "Manage Delivery Charges";

		$this->load->view($this->session->userdata('language')."/deliverycharges/manage_delivery",$data);
	}

	public function add_area()
	{	
		$data['url'] = "add";
		$table='tbl_country';  
		$lastrecord = array();

        $data['country_list'] = $this->mod_common->get_all_records($table,"*"); 


       $lastrecord = $this->mod_common->get_all_records("deliverycharges","*"); 
         $data['mylastrecord'] = end($lastrecord);
      

		$table='tbl_city';       
        $data['city_list'] = $this->mod_common->get_all_records($table,"*"); 
		$this->load->view($this->session->userdata('language')."/deliverycharges/add_deliverycharges",$data);
	}

	public function delete($id) {
		#-------------delete record--------------#
        $table = "deliverycharges";
        $where = "id = '" . $id . "'";
        $delete_area = $this->mod_common->delete_record($table, $where);

        if ($delete_area) {
            $this->session->set_flashdata('ok_message', 'You have succesfully deleted.');
            redirect(SURL . 'Deliverycharges/');
        } else {
            $this->session->set_flashdata('err_message', 'Deleting Operation Failed.');
            redirect(SURL . 'Deliverycharges/');
        }
    }

	

	public function add(){
		
		if($this->input->server('REQUEST_METHOD') == 'POST'){
			
			 $udata['StartingPoint'] = ($this->input->post('startingpoint'));
			 $udata['EndingPoint'] = $this->input->post('endinggpoint'); 
			
			$udata['NormalCharges'] = $this->input->post('normalcharges'); 
			$udata['Expresscharges'] = $this->input->post('expresscharges');
			$udata['Status'] = $this->input->post('status');

			
			$table='deliverycharges';
			$res = $this->mod_common->insert_into_table($table,$udata);


			if ($res) {
			 	$this->session->set_flashdata('ok_message', 'You have succesfully added.');
	            redirect(SURL . 'Deliverycharges/');
	        } else {
	            $this->session->set_flashdata('err_message', 'Adding Operation Failed.');
	            redirect(SURL . 'Deliverycharges/');
	        }
	    }
	}

	public function edit($id){
		if($id){
			$table='deliverycharges';
			$where = "id='$id'";
			$data['area'] = $this->mod_common->select_single_records($table,$where);
			//var_dump($data['area']);
			$data['url'] = "update";
			//pme($data['country']);
			$this->load->view($this->session->userdata('language')."/Deliverycharges/add_deliverycharges", $data);
		}
	}

	public function update(){ //var_dump($this->input->post());
		if($this->input->server('REQUEST_METHOD') == 'POST'){
		    $udata['StartingPoint'] = ($this->input->post('startingpoint'));
			$udata['EndingPoint'] = $this->input->post('endinggpoint');
			
			$udata['NormalCharges'] = $this->input->post('normalcharges'); 
			$udata['Expresscharges'] = $this->input->post('expresscharges');
			$udata['Status'] = $this->input->post('status');

			
			$id = $this->input->post('myid');
				

			$where = "id='$id'";
			$table='deliverycharges';

			$res=$this->mod_common->update_table($table,$where,$udata);

			if ($res) {
			 	$this->session->set_flashdata('ok_message', 'You have succesfully updated.');
	            redirect(SURL . 'Deliverycharges/');
	        } else {
	            $this->session->set_flashdata('err_message', 'Adding Operation Failed.');
	            redirect(SURL . 'Deliverycharges/');
	        }
	    }

	}
}
