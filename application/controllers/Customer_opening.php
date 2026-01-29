<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Customer_opening extends CI_Controller {

	public function __construct() {
        parent::__construct();

        $this->load->model(array(
            "mod_admin", "mod_vendor","mod_common","mod_customeropbal"
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
		
		 $data['custumer'] =$this->db->query("select * from tblacode where atype='Child'  $where_customer_code ")->result_array();
		$table='tblmaterial_coding';       
			$data['item_list'] = $this->mod_common->get_all_records($table,"*");
		$data["filter"] = '';
		#----load view----------#
		$data["title"] = "Manage Customer Opening";
		
		$this->load->view($this->session->userdata('language')."/Customer_opening/add_direct_girn",$data);
	}



		
		public function insert_ledger()
	{
           $login_user=$this->session->userdata('id');
           
           $sale_point_id = $this->db->query("select location from tbl_admin where id='$login_user'")->row_array()['location'];
           $sale_date=$this->input->post('date');
		   $date_array = array('post_date' => $sale_date,'sale_point_id =' => $sale_point_id);
		   $last_date =  $this->mod_common->select_single_records('tbl_posting_stock',$date_array);



  	if(!empty($last_date))
				{
					//echo "string";
					echo 'Already closed for this date';exit;
				}
                $adata['acode']=trim($_POST["Customer"]);
				$adata['sale_point_id']=trim($_POST["sale_point_id"]);
				$adata['materialcode']=trim($_POST["item"]);
				$adata['qty']=trim($_POST["Quantity"]);
				$adata['scode']=trim($_POST["scode"]);
				$adata['date']=$_POST["date"];
				$adata['created_by'] = $login_user;
				$adata['created_date']= date('Y-m-d');
              if ($this->mod_customeropbal->check_already($adata['materialcode'],$adata['acode'])) {
				    echo 'Item is Already Exist,Please Update Item';exit;
					
				}

               $table='tbl_customer_opening';
				$res = $this->mod_common->insert_into_table($table,$adata);
				 echo 'You have succesfully added';exit;




    	
	}
			public function update_ledger()
	{
		 	$Quantity=$this->input->post('Quantity');
		 	$trans_id=$this->input->post('trans_id');
    	  
			$udata['qty'] = $Quantity;
    	
		 $this->mod_common->update_table("tbl_customer_opening",array("trans_id"=>$trans_id), $udata);
		 echo 'updated succesfully';
		

    	
	}
	public function get_row()
	{
		$Customer=$this->input->post('Customer');
		$edit_list =$this->db->query("select *  from tbl_customer_opening where acode='$Customer'")->result_array();
		   $count=0; for ($i=0; $i < count($edit_list) ; $i++) {
		   	$c_name= $edit_list[$i]['acode'];
$customer=$this->db->query("select aname from tblacode where acode='$c_name'")->row_array()['aname'];
$materialcode= $edit_list[$i]['materialcode'];
$itemname=$this->db->query("select itemname from tblmaterial_coding where materialcode='$materialcode'")->row_array()['itemname'];
$sale_point_id= $edit_list[$i]['sale_point_id'];
$location=$this->db->query("select sp_name from tbl_sales_point where sale_point_id='$sale_point_id'")->row_array()['sp_name'];

$count++;		
			?>
												

			<tr id="row<?php echo $i;?>">
				<td    style="width: 10%;">
					<input style="width: 100%;" type="text"  readonly=""   value="<?php echo  $count; ?>">

				</td>
								<td    style="width: 30%;">
							 <input style="width: 100%;" type="text" id="Customer_<?php echo $i;?>"   readonly=""   value="<?php echo  $customer; ?>" name="Customer[]">	
							 <input style="width: 100%;" type="hidden" id="id_<?php echo $i;?>"   readonly=""   value="<?php echo  $edit_list[$i]['id']; ?>" name="id[]">
								</td>
								<td    style="width: 30%;">
							 <input style="width: 100%;" type="text" id="sale_point_id_<?php echo $i;?>"   readonly=""   value="<?php echo  $location; ?>" required name="sale_point_id[]">	
							
								</td>	
								<td    style="width: 30%;">
							 <input style="width: 100%;" type="text" id="item_<?php echo $i;?>"   readonly=""   value="<?php echo  $itemname; ?>" name="item[]">	
							
								</td>
									<td    style="width: 20%;">
							 <input style="width: 100%;" type="text" id="Quantity_<?php echo $i;?>"   readonly=""   value="<?php echo  $edit_list[$i]['qty']; ?>" name="Quantity[]">	
							
								</td>

			
												
							 -->
						<td style='display: inline-flex; border: 0px;width: 100%'>
													<input style='width: 100%' type="button" id="edit_button<?php echo $i;?>" value="Edit" data-id1='<?php echo $i;?>'   class="editrow btn btn-xs btn-success" onclick="edit_row(<?php echo $i;?>)"> 
														<input style='display:none;width:100%' type="button" id="save_button<?php echo $i;?>" data-id1='<?php echo $i;?>'  value="Save" class="btn btn-xs btn-warning" onclick="savechecking(<?php echo $i;?>)" >
														<input style='width: 100%' type="button" value="Delete" class="btn btn-xs btn-danger btn_del"   onclick="confirmDelete(<?php echo  $edit_list[$i]['trans_id']; ?>)"></td>
												</tr>
												<input type="hidden" name="id" id="trans_id_<?php echo $i;?>"   value="<?php echo $edit_list[$i]['trans_id']; ?>" />

												<style type="text/css">
	#data_table1{display: block;}
</style>	
																						
											
		
<?php	
	}

}

public function delete() {
    $id=$this->input->post('id');
//echo $id;exit;
			
		$this->db->query("delete from tbl_customer_opening where trans_id='$id'");
	
	echo 'Deleted succesfully';
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
