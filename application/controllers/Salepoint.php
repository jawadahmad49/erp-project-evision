<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Salepoint extends CI_Controller {

	public function __construct() {
        parent::__construct();

        $this->load->model(array(
            "mod_sale","mod_common"
        ));
        
    }

	public function index()
	{
		
		
		$data['salepoints_list'] = $this->mod_sale->manage_salepoints();
		
		$data["filter"] = '';
		#----load view----------#
		$data["title"] = "Manage Sale Points";		
		$this->load->view($this->session->userdata('language')."/salepoint/manage_salepoints",$data);
		
	}

	public function add_salepoint()
	{   
		$login_user=$this->session->userdata('id');
	    	$role = $this->db->query("select * from tbl_user_rights where uid = '$login_user' and pageid = '2' limit 1")->row_array();
		if ($role['add']!=1) {
			$this->session->set_flashdata('err_message', 'You have no authority to Complete this task .');
			redirect(SURL . 'Salepoint/index/');
			}
        if(isset($_POST['submit'])){

        	$sp_name = $_POST['sp_name'];

        	if($this->mod_sale->get_by_name($sp_name)) {
				$this->session->set_flashdata('err_message', 'Sale Point with this <strong>'.$sp_name.'</strong> name already exists.');
				//redirect(SURL . 'vehicle/add_vehicle');
				//exit();
			}else{
				$add=  $this->mod_sale->add_salepoint($this->input->post());

				if ($add) {
				 	$this->session->set_flashdata('ok_message', 'You have succesfully added.');
		            redirect(SURL . 'Salepoint/');
		            //$this->load->view('Company/add',$add);
		        } else {
		            $this->session->set_flashdata('err_message', 'Adding Operation Failed.');
		            redirect(SURL . 'Salepoint/');
		        }
		        
			}	
	    }
        $table='tbl_city';       
        $data['city_list'] = $this->mod_common->get_all_records($table,"*");
        $this->load->view($this->session->userdata('language')."/salepoint/add",$data);
		
	}

	public function edit($id){
		$login_user=$this->session->userdata('id');
	    	$role = $this->db->query("select * from tbl_user_rights where uid = '$login_user' and pageid = '2' limit 1")->row_array();
		if ($role['edit']!=1) {
			$this->session->set_flashdata('err_message', 'You have no authority to Complete this task .');
			redirect(SURL . 'Salepoint/index/');
			}
		if($id){
			$table='tbl_sales_point';
			$where = "sale_point_id='$id'";
			$data['salepoint'] = $this->mod_common->select_single_records($table,$where);

			$table='tbl_city';       
        	$data['city_list'] = $this->mod_common->get_all_records($table,"*");
			
			$where_id = array('city_id' => $data['salepoint']['city_id']);
	    	$table='tbl_area';       
	    	$data['area_list']= $this->mod_common->select_array_records($table,"*",$where_id);

	    	if(isset($_POST['submit'])){
	    		$sp_name = $_POST['sp_name'];
	    		$id = $_POST['id'];

	        	if($this->mod_sale->edit_by_name($sp_name,$id)) {
					$this->session->set_flashdata('err_message', 'Sale Point with this <strong>'.$sp_name.'</strong> name already exists.');
					//redirect(SURL . 'vehicle/add_vehicle');
					//exit();
				}else{
					$add=  $this->mod_sale->add_salepoint($this->input->post(),$id);

					if ($add) {
					 	$this->session->set_flashdata('ok_message', 'You have succesfully Update the record.');
			            redirect(SURL . 'Salepoint/');
			            //$this->load->view('Company/add',$add);
			        } else {
			            $this->session->set_flashdata('err_message', 'Adding Operation Failed.');
			            redirect(SURL . 'Salepoint/');
			        }   
				}	
	    	}
	    	$this->load->view($this->session->userdata('language')."/salepoint/edit",$data);
			
		}
	}


	public function delete($id) {
		$login_user=$this->session->userdata('id');
	    	$role = $this->db->query("select * from tbl_user_rights where uid = '$login_user' and pageid = '2' limit 1")->row_array();
		if ($role['delete']!=1) {
			$this->session->set_flashdata('err_message', 'You have no authority to Complete this task .');
			redirect(SURL . 'Salepoint/index/');
			}
		#-------------delete record--------------#
        $table = "tbl_sales_point";
        $where = "sale_point_id = '" . $id . "'";
        $delete_vehicle = $this->mod_common->delete_record($table, $where);

        if ($delete_vehicle) {
            $this->session->set_flashdata('ok_message', 'You have succesfully deleted.');
            redirect(SURL . 'Salepoint/');
        } else {
            $this->session->set_flashdata('err_message', 'Deleting Operation Failed.');
            redirect(SURL . 'Salepoint/');
        }
    }

}
