<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Restaurant extends CI_Controller {

	
	public function __construct() {
        parent::__construct();

        $this->load->model(array(
            "mod_restaurant","mod_common"
        ));
        
    }
    public function index() {

    	$table='tbl_resturant_reg';
        $data['restaurant_list'] = $this->mod_restaurant->get_all_restaurants($table,"*");

        //pm($data['restaurant_list']);
		$data["filter"] = '';
		#----load view----------#
		$data["title"] = "Manage Restaurant";   		
	  $this->load->view('restaurant/manage_restaurant', $data);

	}
	public function add() {

    	$table='tbl_country';        
        $data['country_list'] = $this->mod_common->get_all_records($table,"*");

			#---------- add restaurant record---------------#

        	if($this->input->post('add_restaurant_submit')){

        		//pm($this->input->post());

			 $add_restaurant =  $this->mod_restaurant->add_restaurant($this->input->post());
            
		        if ($add_restaurant) {
		            $this->session->set_flashdata('ok_message', '- Restaurant deleted successfully!');
		            redirect(SURL . 'restaurant/');
		        } else {
		            $this->session->set_flashdata('err_message', '- Error in deleteting Restaurant please try again!');
		            redirect(SURL . 'restaurant/');
		        }
		    }
		#--------- load view----------------#
		$data["title"] = "Add Restaurant";
	  	$this->load->view('restaurant/add', $data);
    }
	public function edit ($rid)
	{
		$data['restaurant'] = $this->mod_restaurant->edit_record($rid);
		if(empty($data['restaurant']))
		{
 			$this->session->set_flashdata('err_message', '-recode Restaurant not exist!');			
			redirect(SURL.'restaurant');
		}
		//pm($data['restaurant']);
    	$table='tbl_country';       
        $data['country_list'] = $this->mod_common->get_all_records($table,"*");    	
        
        $where_id = array('country_id' => $data['restaurant']['restaurant_country']);

        $table='tbl_city';       
        $data['city_list']= $this->mod_common->select_array_records($table,"*",$where_id);


        $data['form_data'] = 'sss';
	  	$this->load->view('restaurant/edit', $data);
	}
	public function detail($rid) {
			#---------- detail restaurant record---------------#
		$where = array('restaurant_id' =>$rid);
		$data['restaurant'] =  $this->mod_restaurant->select_single_restaurant($where);
		if(empty($data['restaurant']))
		{
 			$this->session->set_flashdata('err_message', '-recode Restaurant not exist!');			
			redirect(SURL.'restaurant');
		}
	  	$this->load->view('restaurant/detail', $data);
    }
	public function update($rid) {
        #------------- if post--------------#
        if ($this->input->post("update_restaurant_submit")) {
			#---------- update restaurant record---------------#
			 $update_restaurant =  $this->mod_restaurant->update_restaurant($this->input->post());
            
				if ($update_restaurant) {
					$this->session->set_flashdata('ok_message', '- restaurant updated successfully!');
					redirect(SURL . 'restaurant');
				} else {
                $this->session->set_flashdata('err_message', '- Error in adding restaurant please try again!');
                redirect(SURL . 'restaurant/edit/'.$rid);
            	}
        }

    }
    public function changeStatus($id){
		$status = 1 - $this->input->post('status');
		$update_data = array("status"=>$status,"approve_date"=>date('Y-m-d'),"approve_by"=>$this->session->userdata('id'),"approve_time"=>date('h:i:s'));
		$where = array("restaurant_id"=>$id);
		
		$update = $this->mod_common->update_table('tbl_resturant_reg',$where,$update_data);
			
			$this->session->set_flashdata('ok_message', 'Status changed successfully!');
			 redirect(SURL . 'restaurant/');
	}
	public function delete($id) {
		#-------------delete record--------------#
        $table = "tbl_resturant_reg";
        $where = "restaurant_id = '" . $id . "'";
        $delete_restaurant = $this->mod_common->delete_record($table, $where);

        if ($delete_restaurant) {
            $this->session->set_flashdata('ok_message', '- Restaurant deleted successfully!');
            redirect(SURL . 'restaurant/');
        } else {
            $this->session->set_flashdata('err_message', '- Error in deleteting Restaurant please try again!');
            redirect(SURL . 'restaurant/');
        }
    }
	function email_exist()
	{
	    $table='tbl_resturant_reg';
		$email=	$this->input->post('email');
		$where = array('restaurant_email' => $email);
		$data['restaurant_list'] = $this->mod_common->select_array_records($table,"restaurant_email",$where);
		if (!empty($data['restaurant_list'])) {
			echo '1';
		 	exit;
		 }
		 else {
		 	echo '0';
		 	exit;
		 }
	}
	function website_exist()
	{
	    $table='tbl_resturant_reg';
		$website_name=	$this->input->post('website_name');
		$where = array('restaurant_website_name' => $website_name);
		$data['restaurant_list'] = $this->mod_common->select_array_records($table,"restaurant_website_name",$where);
		if (!empty($data['restaurant_list'])) {
			echo '1';
		 	exit;
		 } 
		 else {
		 	echo '0';
		 	exit;
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

}
