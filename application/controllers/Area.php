<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Area extends CI_Controller {

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
		
		$data["filter"] = '';
		#----load view----------#
		$data["title"] = "Manage Area";

		$this->load->view($this->session->userdata('language')."/area/manage_area",$data);
	}

	public function add_area()
	{
		$login_user=$this->session->userdata('id');
	    	$role = $this->db->query("select * from tbl_user_rights where uid = '$login_user' and pageid = '5' limit 1")->row_array();
		if ($role['add']!=1) {
			$this->session->set_flashdata('err_message', 'You have no authority to Complete this task .');
			redirect(SURL . 'Area/index/');
			}
		$table='tbl_country';       
        $data['country_list'] = $this->mod_common->get_all_records($table,"*"); 
		$table='tbl_city';       
        $data['city_list'] = $this->mod_common->get_all_records($table,"*"); 
		$this->load->view($this->session->userdata('language')."/area/add_area",$data);
	}

	public function delete($id) {
		$login_user=$this->session->userdata('id');
	    	$role = $this->db->query("select * from tbl_user_rights where uid = '$login_user' and pageid = '5' limit 1")->row_array();
		if ($role['delete']!=1) {
			$this->session->set_flashdata('err_message', 'You have no authority to Complete this task .');
			redirect(SURL . 'Area/index/');
			}
		#-------------delete record--------------#
        $table = "tbl_area";
        $where = "area_id = '" . $id . "'";
        $delete_area = $this->mod_common->delete_record($table, $where);

        if ($delete_area) {
            $this->session->set_flashdata('ok_message', 'You have succesfully deleted.');
            redirect(SURL . 'Area/');
        } else {
            $this->session->set_flashdata('err_message', 'Deleting Operation Failed.');
            redirect(SURL . 'Area/');
        }
    }

	function get_city()
	{
	    $table='tbl_city';
		$country_id=	$this->input->post('country_id');
		$where = array('country_id' => $country_id);
		$data['city_list'] = $this->mod_common->select_array_records($table,"*",$where);

		foreach ($data['city_list'] as $key => $value) {
			?>
			<option value="<?php echo  $value['city_id']; ?>"><?php echo  $value['city_name']; ?></option>
			
		<?php }
		
	}

	public function add(){
		
		if($this->input->server('REQUEST_METHOD') == 'POST'){
			$udata['city_id'] = $this->input->post('city');
			$udata['aname'] = trim($this->input->post('aname'));

			#----check name already exist---------#
				if ($this->mod_area->get_by_title($udata['aname'])) {
					$this->session->set_flashdata('err_message', 'Name Already Exist.');
					redirect(SURL . 'Area/add_area');
					exit();
				}
				
				
			$udata['status'] = $this->input->post('status');
			
			$table='tbl_area';
			$res = $this->mod_common->insert_into_table($table,$udata);


			if ($res) {
			 	$this->session->set_flashdata('ok_message', 'You have succesfully added.');
	            redirect(SURL . 'Area/');
	        } else {
	            $this->session->set_flashdata('err_message', 'Adding Operation Failed.');
	            redirect(SURL . 'Area/');
	        }
	    }
	}

	public function edit($id){
		$login_user=$this->session->userdata('id');
	    	$role = $this->db->query("select * from tbl_user_rights where uid = '$login_user' and pageid = '5' limit 1")->row_array();
		if ($role['edit']!=1) {
			$this->session->set_flashdata('err_message', 'You have no authority to Complete this task .');
			redirect(SURL . 'Area/index/');
			}
		if($id){
			$table='tbl_area';
			$where = "area_id='$id'";
			$data['area'] = $this->mod_common->select_single_records($table,$where);
			$tablecountry='tbl_country';
			$data['country_list'] = $this->mod_common->get_all_records($tablecountry,"*");

        	
			$data['area'] = $this->mod_area->edit_record($id);
			if(empty($data['area']))
			{
	 			$this->session->set_flashdata('err_message', 'Record Not Exist!');			
				redirect(SURL.'Area');
			}
        	$where_id = array('country_id' => $data['area']['country_id']);
        	$table='tbl_city';       
        	$data['city_list']= $this->mod_common->select_array_records($table,"*",$where_id);

			//pme($data['country']);
			$this->load->view($this->session->userdata('language')."/area/edit", $data);
		}
	}

	public function update(){
		if($this->input->server('REQUEST_METHOD') == 'POST'){
			$cdata['country_id'] = $this->input->post('country');
			$cdata['city_id'] = $this->input->post('city');
			$cdata['aname'] = trim($this->input->post('aname'));
			$cdata['status'] = $this->input->post('status');
			$id = $_POST['id'];
						#----check name already exist---------#
				if ($this->mod_area->edit_by_title($cdata['aname'],$id)) {
					$this->session->set_flashdata('err_message', 'Name Already Exist.');
					redirect(SURL . 'Area/edit/'.$id);
					exit();
				}

			$where = "area_id='$id'";
			$table='tbl_area';
			$res=$this->mod_common->update_table($table,$where,$cdata);

			if ($res) {
			 	$this->session->set_flashdata('ok_message', 'You have succesfully updated.');
	            redirect(SURL . 'Area/');
	        } else {
	            $this->session->set_flashdata('err_message', 'Adding Operation Failed.');
	            redirect(SURL . 'Area/');
	        }
	    }

	}
}
