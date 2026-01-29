<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class StockReport extends CI_Controller {

	public function __construct() {
        parent::__construct();

        $this->load->model(array(
            "mod_stockreport","mod_common"
        ));
    }

	public function index()
	{
		$table='tbltrans_detail';
		$data['stock_report_list'] = $this->mod_common->get_all_records($table,"*");
		$data["filter"] = '';
		#----load view----------#
		$data["title"] = "Stock Report";	
		$table='tblcategory';       
        $data['category_list'] = $this->mod_common->get_all_records($table,"*");
 
        $data['openging_date'] = $this->mod_common->select_last_records('tbl_shop_opening')['date'];
        $this->load->view($this->session->userdata('language')."/stock_report/search",$data);       	
	}

	public function details()
	{
		 
		if($this->input->server('REQUEST_METHOD') == 'POST'){

			  $from_date=$this->input->post('from_date');

			  $to_date=$this->input->post('to_date');

			$category_id=$this->input->post('category');

 

			$last_post_date=$this->mod_common->select_last_records('tbl_posting_stock')['post_date'];

			$where_last = "post_date = '" . $last_post_date . "' AND itemcode = '" . $id . "'";

			$last_day=$this->mod_common->select_single_records('tbl_posting_stock',$where_last);

			//pm();
			$data['report']=  $this->mod_stockreport->get_details($this->input->post());
//pm($data['report']);
			$report_count=0;
			foreach ($data['report'] as $key => $value) {

				$id=$value['materialcode'];
			 
			 
				$today_stock=$this->mod_common->stock($id,'empty',$from_date,1);
	
		 

				$empty_filled= explode('_', $today_stock);
				$data['report'][$report_count]['filled']=$empty_filled[0];
				$data['report'][$report_count]['empty']=$empty_filled[1];
		
			 
				$report_count++;
 
			}
  	//pm($data['report']);
		//pm($empty_filled);
			$data['category_id']=  $this->input->post('category');


			$data['daterange'] = trim($this->input->post('from_date').'/'.$this->input->post('to_date'));
			$table='tbl_company';       
       		$data['company'] = $this->mod_common->get_all_records($table,"*");
			
			
			
			 
			if ($data['report']) {
			     $data["title"] = "Stock Report";
	            $this->load->view($this->session->userdata('language')."/stock_report/detail",$data);
	        } else {
	            $this->session->set_flashdata('err_message', 'No Record Found.');
	            redirect(SURL . 'StockReport/');
	        }
	    }else{
	        //$data["filter"] = 'add';
	        $data["title"] = "Stock Report";    			
			$this->load->view($this->session->userdata('language')."/stock_report/detail",$data);
		}
	}

	public function add()
	{
		if($this->input->server('REQUEST_METHOD') == 'POST'){

			$add=  $this->mod_transaction->add_transaction($this->input->post());
			#----check name already exist---------#
			// if ($this->mod_city->get_by_title($data['city_name'])) {
			// 	$this->session->set_flashdata('err_message', 'Name Already Exist.');
			// 	redirect(SURL . 'city/add_city');
			// 	exit();
			// }

			if ($add) {
			 	$this->session->set_flashdata('ok_message', 'You have succesfully added.');
	            redirect(SURL . 'stock_report/');
	        } else {
	            $this->session->set_flashdata('err_message', 'Adding Operation Failed.');
	            redirect(SURL . 'stock_report/');
	        }
	    }
        $data["filter"] = 'add';
        $data["title"] = "Add Stock Report";    			
		$this->load->view($this->session->userdata('language')."/stock_report/add",$data);
	}


	public function edit($id){
		if($id){
			$table='tbltrans_detail';
			$where = "vno='$id'";
			$data['payemetreceipt'] = $this->mod_common->select_single_records($table,$where);
			//pm($data['payemetreceipt']);exit;
	        $data["filter"] = 'edit';
        	$data["title"] = "Update Stock Report";
			$this->load->view($this->session->userdata('language')."/stock_report/add", $data);
		}
	 
		if($this->input->server('REQUEST_METHOD') == 'POST'){
			$update=  $this->mod_transaction->update_transaction($this->input->post());
		 	if ($res) {
			 	$this->session->set_flashdata('ok_message', 'You have succesfully updated.');
	            redirect(SURL . 'stock_report/');
	        } else {
	            $this->session->set_flashdata('err_message', 'Operation Failed.');
	            redirect(SURL . 'stock_report/');
	        }
	    }
	}

	public function delete($id) {
	 
        $table = "tbltrans_detail";
        $where = "vno = '" . $id . "'";
        $delete = $this->mod_common->delete_record($table, $where);

        if ($delete) {
            $this->session->set_flashdata('ok_message', 'You have succesfully deleted.');
            redirect(SURL . 'stock_report/');
        } else {
            $this->session->set_flashdata('err_message', 'Deleting Operation Failed.');
            redirect(SURL . 'stock_report/');
        }
    }

	function get_expensetypename()
	{
	    $table='tbl_exptype_coding';
		$t_id=	$this->input->post('t_id');
		$where = array('type' => $t_id);
		$data['expense_name'] = $this->mod_common->select_array_records($table,"*",$where);

		foreach ($data['expense_name'] as $key => $value) {
			?>
			<option value="<?php echo  $value['id']; ?>"><?php echo  $value['name']; ?></option>
			
		<?php }
		
	}

}
