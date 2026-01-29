<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class CylinderConversion extends CI_Controller {

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
            "mod_customer","mod_common","mod_salelpg","mod_cylinderconversion","mod_stockreport"
        ));
        
    }
	public function index()
	{
		
		if(isset($_POST['submit'])){			
			$from_date = date("Y-m-d", strtotime($_POST['from']));
			
			$to_date = date("Y-m-d", strtotime($_POST['to']));
			
		}else{
			$from_date = date('Y-m-d', strtotime('-15 day'));
			$to_date = date('Y-m-d');
		}
		$login_user=$this->session->userdata('id');
        $sale_point_id = $this->db->query("select location from tbl_admin where id='$login_user'")->row_array()['location'];
		$data['cylinderconversion_list'] = $this->mod_cylinderconversion->manage_cylinderconversion($from_date,$to_date,$sale_point_id);
		
		$data["filter"] = '';
		#----load view----------#
		$data["title"] = "Manage Cylinder Conversion";

		$this->load->view($this->session->userdata('language')."/cylinder_conversion/cylinder_conversion",$data);
	}

	public function add_cylinder_conversion()
	{
		$login_user=$this->session->userdata('id');
        $sale_point_id = $this->db->query("select location from tbl_admin where id='$login_user'")->row_array()['location'];
        $sale_point_id = $this->db->query("select location from tbl_admin where id='$login_user'")->row_array()['location'];
		  if ($sale_point_id=='0') {
	  	$this->session->set_flashdata('err_message', '- Admin Has No Rights To Add Cylinder Conversion!');
			redirect(SURL . 'CylinderConversion');
			exit();
	  }
		$data['customer_list'] = $this->mod_customer->getOnlyCustomers();
		//echo "<pre>";print_r($data['vendor_list']);exit;
		$table='tblmaterial_coding';  
		 $where = "catcode = '" . 1 . "'";   	
        $data['item_list'] = $this->mod_common->select_array_records($table,"*",$where);
          $table='tbl_company';       
		$data['pricing_centralized'] = $this->mod_common->get_all_records($table,"*");
        // q();	
        // pm($data['item_list']);

		$this->load->view($this->session->userdata('language')."/cylinder_conversion/add_cylinder_conversion",$data);
	}
	public function add(){


		if($this->input->server('REQUEST_METHOD') == 'POST'){


			$sale_date=$this->input->post('date');
			$date_array = array('post_date>=' => $sale_date);
			$last_date =  $this->mod_common->select_single_records('tbl_posting_stock',$date_array);
 
			if(!empty($last_date))
			{
				
			 
				//echo "string";
				$this->session->set_flashdata('err_message', 'Already closed for this date');
				redirect(SURL . 'CylinderConversion/add_cylinder_conversion');
			}
 
			
			//echo "<pre>";print_r($this->input->post());exit;
			$add=  $this->mod_cylinderconversion->add_cylinder_conversion($this->input->post());


            //echo "<pre>";print_r($add);exit;
		        if ($add) {
		            $this->session->set_flashdata('ok_message', 'Added Successfully!');
		            redirect(SURL . 'CylinderConversion/');
		        } else {
		            $this->session->set_flashdata('err_message', '- Error in adding please try again!');
		            redirect(SURL . 'CylinderConversion/');
		        }
		}
		//$this->add_direct_girn();
	}

	public function delete($id) {

		
			/////////////////////////////////////////////////////////////////////////////////////////////////////////
			//$sale_date=$this->input->post('date');
			$date_array = array('trans_id' => $id);
			$get_rec_date =  $this->mod_common->select_single_records('tbl_cylinderconversion_master',$date_array);

			//$sale_date=$this->input->post('date');
			$date_array = array('post_date>=' => $get_rec_date['trans_date']);
			$last_date =  $this->mod_common->select_single_records('tbl_posting_stock',$date_array);

			if(!empty($last_date))
			{
				//echo "string";
				$this->session->set_flashdata('err_message', 'Already closed for this date');
				redirect(SURL . 'CylinderConversion/');
			}
			/////////////////////////////////////////////////////////////////////////////////////////////////////////
 
 
        $tabledr = "tbl_cylinderconversion_master";
        $wheredr = "trans_id = '".$id."'";
        $deletedr = $this->mod_common->delete_record($tabledr, $wheredr);

        $tabledr = "tbl_cylinderconversion_detail";
        $wheredr = "trans_id = '".$id."'";
        $deletedr = $this->mod_common->delete_record($tabledr, $wheredr);

        if ($deletedr) {
            $this->session->set_flashdata('ok_message', 'You have successfully deleted.');
            redirect(SURL . 'CylinderConversion/');
        } else {
            $this->session->set_flashdata('err_message', 'Deleting Operation Failed.');
            redirect(SURL . 'CylinderConversion/');
        }
    }
	public function edit($id){
		if($id){
			
						/////////////////////////////////////////////////////////////////////////////////////////////////////////
			//$sale_date=$this->input->post('date');
			$date_array = array('trans_id' => $id);
			$get_rec_date =  $this->mod_common->select_single_records('tbl_cylinderconversion_master',$date_array);

			//$sale_date=$this->input->post('date');
			$date_array = array('post_date>=' => $get_rec_date['trans_date']);
			$last_date =  $this->mod_common->select_single_records('tbl_posting_stock',$date_array);
			 $table='tbl_company';       
		$data['pricing_centralized'] = $this->mod_common->get_all_records($table,"*");

			if(!empty($last_date))
			{
				//echo "string";
				$this->session->set_flashdata('err_message', 'Already closed for this date');
				redirect(SURL . 'CylinderConversion/');
			}
			/////////////////////////////////////////////////////////////////////////////////////////////////////////
 
 
		$data['customer_list'] = $this->mod_customer->getOnlyCustomers();

		$table='tblmaterial_coding';  
		 $where = "catcode = '" . 1 . "'";   	
        $data['item_list'] = $this->mod_common->select_array_records($table,"*",$where);


		$table='tbl_cylinderconversion_master';
		$wheredr = "trans_id = '".$id."'";
		$data['single_edit'] = $this->mod_common->select_single_records($table,$wheredr);

		$table='tbl_cylinderconversion_detail';
		$wheredr = "trans_id = '".$id."' AND type = 'from'";
		$data['edit_from'] = $this->mod_common->select_single_records($table,$wheredr);
		

		$table='tbl_cylinderconversion_detail';
		$wheredr = "trans_id = '".$id."' AND type = 'to'";
		$data['to_edit_list'] = $this->mod_common->select_array_records($table,'*',$wheredr);
		 
		$data["filter"] = '';
		#----load view----------#
		$data["title"] = "Update Cylinder Conversion";
		$this->load->view($this->session->userdata('language')."/cylinder_conversion/edit",$data);
		}
	}

	public function makenew($id){
		if($id){
		$data['customer_list'] = $this->mod_customer->getOnlyCustomers();
		$table='tblmaterial_coding';
        $data['item_list'] = $this->mod_common->get_all_records($table,"*");
		$table='tbl_orderbooking';
		$where = "id='$id'";
		$data['single_edit'] = $this->mod_common->select_single_records($table,$where);

		$data['edit_list'] = $this->mod_cylinderconversion->edit_makeneworder($id);

		foreach ($data['edit_list'] as $key => $value) {
			$data['filledstock'][]=  $this->mod_cylinderconversion->get_details($value['itemid'],$data['single_edit']['issuedate']);
			//$itemids = $value['itemid'];
			//$wherem = "materialcode!='$itemids'";
			//$data['item_lists'] = $this->mod_common->select_array_records($tablem,'*',$wherem);
		}
		//echo '<pre>';print_r($data['edit_list']);exit;
		$data["filter"] = '';
		$data["id"] = $id;
		#----load view----------#
		$data["title"] = "Update Cylinder Conversion";
		$this->load->view($this->session->userdata('language')."/cylinder_conversion/add_sale_lpg",$data);
		}
	}
	public function update(){
		if($this->input->server('REQUEST_METHOD') == 'POST'){

			$sale_date=$this->input->post('date');

			$date_array = array('post_date' => $sale_date);
			$last_date =  $this->mod_common->select_single_records('tbl_posting_stock',$date_array);

			if(!empty($last_date))
			{
				//echo "string";
				$this->session->set_flashdata('err_message', 'Already closed for this date');
				redirect(SURL . 'CylinderConversion');
			}
			
			$add_cylinder_conversion=  $this->mod_cylinderconversion->update_cylinder_conversion($this->input->post());
            //echo "<pre>";print_r($add_salelpg);exit;
		        if ($add_cylinder_conversion || $add_cylinder_conversion==0) {
		            $this->session->set_flashdata('ok_message', '- Updated Successfully!');
		           	redirect(SURL . 'CylinderConversion/');
		        } else {
		            $this->session->set_flashdata('err_message', '- Error in updating please try again!');
		            redirect(SURL . 'CylinderConversion/');
		        }
		}
		//$this->add_direct_girn();
	}

	function record_delete()
	{
		#-------------delete record ajax--------------#
        $table = "tbl_cylinderconversion_detail";
        $deleteid=	$this->input->post('deleteid');
        $where = "detail_id = '" . $deleteid . "'";
        $delete_goods = $this->mod_common->delete_record($table, $where);

        if ($delete_goods) {
            echo '1';
		 	exit;
		 }
		 else {
		 	echo '0';
		 	exit;
		 }
	}

	public function detail($id){
		if($id){
		$table='tblmaterial_coding';  
		 $where = "catcode = '" . 1 . "'";   	
        $data['item_list'] = $this->mod_common->select_array_records($table,"*",$where);


		$table='tbl_cylinderconversion_master';
		$wheredr = "trans_id = '".$id."'";
		$data['single_edit'] = $this->mod_common->select_single_records($table,$wheredr);

		$wheredr = "tbl_cylinderconversion_detail.trans_id = '".$id."' AND type = 'from'";
		$data['edit_from'] = $this->mod_cylinderconversion->select_from_records($wheredr);
		//pm($data['edit_from']);


		$wheredr = "tbl_cylinderconversion_detail.trans_id = '".$id."' AND type = 'to'";
		$data['to_edit_list'] = $this->mod_cylinderconversion->select_to_records($wheredr);
		 
		$data["filter"] = '';
		#----load view----------#
		$data["title"] = "Detail Cylinder Conversion";

		//echo '<pre>';print_r($data);
		$table='tbl_company';       
        $data['company'] = $this->mod_common->get_all_records($table,"*");
		//exit;

		$this->load->view($this->session->userdata('language')."/cylinder_conversion/single",$data);
		}
	}

	function get_filledstock()
	{
		$data['report']=  $this->mod_cylinderconversion->get_details($this->input->post());
		//pm($data['report']);
		foreach ($data['report'] as $key => $value) {
			//pm($value);
		 	//echo $value['filled'];
		 	//print $value;
		 	echo json_encode($value);
		}
		
	}
	function get_filledstockdate()
	{
		$data['report']=  $this->mod_cylinderconversion->get_details($this->input->post());
		//pm($data['report']);
		foreach ($data['report'] as $key => $value) {
			//pm($value);
		 	echo $value['empty'];
		}
		
	}
}
