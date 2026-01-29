<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Branch_coding extends CI_Controller {

	public function __construct() {
        parent::__construct();

        $this->load->model(array(
            "mod_admin", "mod_vendor","mod_common","mod_girndirect","mod_salelpg","mod_bank","mod_vendorledger","mod_user","mod_customerledger"
        ));
    }
    
	public function index()
	{	


        $login_user=$this->session->userdata('id');
        $sale_point_id = $this->db->query("select location from tbl_admin where id='$login_user'")->row_array()['location'];
        $fix_code = $this->db->query("select * from tbl_code_mapping where sale_point_id='$sale_point_id'")->row_array();
        $data['sale_point_id']=$sale_point_id=$fix_code['sale_point_id'];
        $customer_code=$fix_code['customer_code'];
        if($customer_code !=''){ $where_customer_code= " and tblacode.general='$customer_code'  "; }else{ $where_customer_code =""; }

        if($sale_point_id !=''){ $where_sale_point_id= "where sale_point_id='$sale_point_id'  "; }else{ $where_sale_point_id =""; }
		$data['location']=$this->db->query("select * from tbl_sales_point $where_sale_point_id")->result_array();
		
		 $data['custumer'] =$this->db->query("select * from tblacode where atype='Child' and sledger='Y' $where_customer_code ")->result_array();
		
		$data["filter"] = '';
		#----load view----------#
		$data["title"] = "Manage Branch Coding";
		
		$this->load->view($this->session->userdata('language')."/Branch_coding/add_direct_girn",$data);
	}

	// public function add_direct_girn()
	// {
	// 	$data['custumer'] =$this->db->query("select * from tblacode where atype='Child' and general in ('1001001000','2004001000')")->result_array();
	// 	$this->load->view($this->session->userdata('language')."/Branch_coding/add_direct_girn",$data);
	// }

		
		public function insert_ledger()
	{       
		$login_user=$this->session->userdata('id');
	    	$role = $this->db->query("select * from tbl_user_rights where uid = '$login_user' and pageid = '22' limit 1")->row_array();
		if ($role['add']!=1) {
			$this->session->set_flashdata('err_message', 'You have no authority to Complete this task .');
			redirect(SURL . 'Salepoint/index/');
			}
		 	$v_no=$this->input->post('v_no');
    	
    	    $Customer=$this->input->post('Customer');
    	    $location=$this->input->post('location');

			$udata['stitle'] = $v_no;
    		
    		$udata['acode'] = $Customer;
		    $scode =$this->db->query("SELECT MAX(scode) as scode FROM tblsledger where acode='$Customer'")->row_array()['scode'];
			$scode = $scode+1;

    		$udata['scode'] = $scode;
    		$udata['sale_point_id'] =$location;

			$table='tblsledger';
			$res = $this->mod_common->insert_into_table($table,$udata);


    	
	}
			public function update_ledger()
	{
		// $login_user=$this->session->userdata('id');
	 //    	$role = $this->db->query("select * from tbl_user_rights where uid = '$login_user' and pageid = '22' limit 1")->row_array();
		// if ($role['edit']!=1) {
		// 	$this->session->set_flashdata('err_message', 'You have no authority to Complete this task .');
		// 	redirect(SURL . 'Salepoint/index/');
		// 	}
		 	$v_no=$this->input->post('v_no');
		 	$location=$this->input->post('location');
    	    $open_bal=$this->input->post('open_bal');
         	$id=$this->input->post('id');
    
			$udata['stitle'] = $v_no;
    		$udata['opngbl'] = $open_bal;
    		$udata['sale_point_id'] = $location;

		 $this->mod_common->update_table("tblsledger",array("id"=>$id), $udata);
		

    	
	}
	public function get_row()
	{
		$Customer=$this->input->post('Customer');
		$edit_list =$this->db->query("select *  from tblsledger where acode='$Customer'")->result_array();
		   $count=0; for ($i=0; $i < count($edit_list) ; $i++) {

$count++;		
			?>
												

			<tr id="row<?php echo $i;?>">
				<td    style="width: 10%;">
					<input style="width: 100%;" type="text"  readonly=""   value="<?php echo  $count; ?>">

				</td>

									<td  id="stitle<?php echo $i;?>"  style="width: 60%;">

													
								<input style="width: 100%;" type="text" id="stitle_<?php echo $i;?>"   readonly=""   value="<?php echo  $edit_list[$i]['stitle']; ?>" name="stitle[]">
								<input style="width: 100%;" type="hidden" id="id_<?php echo $i;?>"   readonly=""   value="<?php echo  $edit_list[$i]['id']; ?>" name="id[]">
													</td>			


								<!-- <td  id="opngbl<?php echo $i;?>"  style="width: 20%;">

												
								<input style="width: 100%;" type="text" id="opngbl_<?php echo $i;?>"   readonly=""   value="<?php echo  $edit_list[$i]['opngbl']; ?>" name="opngbl[]">
													</td>		


							<td  id="OPTYPE<?php echo $i;?>"  style="width: 20%;">

												
								<input style="width: 100%;" type="text" id="OPTYPE_<?php echo $i;?>"  tabindex="-1" readonly=""   value="<?php echo  $edit_list[$i]['OPTYPE']; ?>" name="OPTYPE[]">
													</td>				
												
							 -->
						<td style='display: inline-flex; border: 0px;width: 100%'>
													<input style='width: 100%' type="button" id="edit_button<?php echo $i;?>" value="Edit" data-id1='<?php echo $i;?>'   class="editrow btn btn-xs btn-success" onclick="edit_row(<?php echo $i;?>)"> 
														<input style='display:none;width:100%' type="button" id="save_button<?php echo $i;?>" data-id1='<?php echo $i;?>'  value="Save" class="btn btn-xs btn-warning" onclick="savechecking(<?php echo $i;?>)" >
														<input style='width: 100%' type="button" value="Delete" class="btn btn-xs btn-danger btn_del"   onclick="confirmDelete(<?php echo  $edit_list[$i]['id']; ?>)"></td>
												</tr>
												<input type="hidden" name="id"  value="<?php echo $edit_list[$i]['issuenos']; ?>" />

												<style type="text/css">
	#data_table1{display: block;}
</style>	
																						
											
		
<?php	
	}

}

public function delete() {
	// $login_user=$this->session->userdata('id');
	//     	$role = $this->db->query("select * from tbl_user_rights where uid = '$login_user' and pageid = '22' limit 1")->row_array();
	// 	if ($role['delete']!=1) {
	// 		$this->session->set_flashdata('err_message', 'You have no authority to Complete this task .');
	// 		redirect(SURL . 'Salepoint/index/');
	// 		}
    $id=$this->input->post('id');
//echo $id;exit;
			
		$this->db->query("delete from tblsledger where id='$id'");
	
		if ($this->db->trans_status() === FALSE) {
			$this->session->set_flashdata('err_message', 'Deleting Operation Failed.');
            redirect(SURL . 'Branch_coding/');
		}else{
			$this->session->set_flashdata('ok_message', 'You have succesfully deleted.');
            redirect(SURL . 'Branch_coding/');
		}
}
public function get_customer()
	{ 
	   
		$location=$this->input->post('location');
		//echo $location;

		$customer_code=$this->db->query("select customer_code from tbl_code_mapping where sale_point_id='$location' ")->row_array()['customer_code'];


        $acode=$this->db->query("select * from tblacode where general='$customer_code' and atype='Child' ")->result_array();

	

		?>
			<option value="">Choose a Customer...</option>
		<?php
			foreach ($acode as $key => $value) {
				?>
				
				<option value="<?php echo  $value['acode']; ?>"><?php echo  $value['aname']; ?></option>
				
			<?php }
		
		
	}
    










}
