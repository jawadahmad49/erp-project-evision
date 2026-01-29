<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Termcondition extends CI_Controller {

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
            "mod_item","mod_common"
        ));
        
    }

	public function index()
	{
		$data['item_list'] = $this->mod_item->manage_item();
		$data["filter"] = '';
		#----load view----------#
		$data["title"] = "Manage Item";

		$this->load->view($this->session->userdata('language')."/term_condition/add_term_cond",$data);
		  redirect(SURL . 'Termcondition/add_term/');
	}


	
	public function add_term($id){

		
		
		  if(isset($_POST['submit'])){
			$udata['term_cond'] = $_POST["term"];
        	$udata['created_by']= $this->session->userdata('id');
			$udata['created_dt'] =  date('Y-m-d');
			
			$res = $this->db->insert("tbl_term_condition",$udata);
			
				
			if ($res) {
				 	$this->session->set_flashdata('ok_message', 'You have successfully added.');
		             redirect(SURL . 'Termcondition/add_term/');
		            //$this->load->view('Company/add',$add);
		        } else {
		            $this->session->set_flashdata('err_message', 'Adding Operation Failed.');
		            redirect(SURL . 'Termcondition/add_term/');
		        }
		}
			
			
		$this->load->view($this->session->userdata('language')."/term_condition/add_term_cond",$data);
	}

	
	public function delete_term($id) {

		
		
		#-------------delete record--------------#
        $table = "tbl_term_condition";
        $where = "id = '" . $id . "'";
        $delete_area = $this->mod_common->delete_record($table, $where);

        if ($delete_area) {
            $this->session->set_flashdata('ok_message', 'You have succesfully deleted.');
            redirect(SURL . 'Termcondition/add_term');
        } else {
            $this->session->set_flashdata('err_message', 'Deleting Operation Failed.');
            redirect(SURL . 'Termcondition/add_term');
        }
    }

 
}
