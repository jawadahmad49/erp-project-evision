<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Decanting extends CI_Controller {

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
        error_reporting(0);

        $this->load->model(array(
            "mod_customer","mod_common","mod_decanting","mod_stockreport"
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
		
		$data['decanting_list'] = $this->mod_decanting->manage_decanting($from_date,$to_date);
		//pm($data['decanting_list']);
		$i=0;
		foreach ($data['decanting_list'] as $key => $value) {

			 // $value['issuenos'];

			//echo $value['issuenos'];
			$amount_qty=$this->mod_decanting->get_detail_decanting($value['issuenos']);

		
			$data['decanting_list'][$i]['total_amount'] = $amount_qty['dec_amount'];
			$data['decanting_list'][$i++]['dec_qty'] = $amount_qty['dec_qty'];
		
		}
		//pm($data['decanting_list']);
	

		
		$data["filter"] = '';
		#----load view----------#
		$data["title"] = "Manage Decanting";
		$this->load->view($this->session->userdata('language')."/decanting/decanting",$data);
	}

	public function add_decanting()
	{


		$data['date_c']=date('Y-m-d'); 

		$data['customer_list'] = $this->mod_customer->getOnlyCustomers();
		//echo "<pre>";print_r($data['vendor_list']);exit;
		$table='tblmaterial_coding';     
		$where = "catcode='1'";  
        $data['item_list'] = $this->mod_common->select_array_records($table,"*",$where);
        $table='tbl_company';       
		$data['pricing_centralized'] = $this->mod_common->get_all_records($table,"*");

		$this->load->view($this->session->userdata('language')."/decanting/add_decanting",$data);
	}
	public function add_item(){
		// $data['item_list'] = $this->mod_decanting->get_decanting();

		// pm($data['item_list']);

		if($this->input->server('REQUEST_METHOD') == 'POST'){

			$udetail_static = $this->input->post('itemid');
			$message = $this->input->post('message');

			if($message==111)
			{
	        	$table='tblmaterial_coding';     
				$where = "catcode='1'";  
	        	$item_list_drop = $this->mod_common->select_array_records($table,"*",$where);

				$udata['createdby'] = $this->session->userdata('id');
				$udata['decanting_status'] = 'Inprocess';
				$udata['decanting'] = 'Yes';
				$udata['decanting_start_dt'] = date('Y-m-d H:i:s');
				$udata['issuedate'] = date('Y-m-d');
				$udata['decanting_start_by'] =$this->session->userdata('id');
				
				$table='tbl_issue_goods';
				$add = $this->mod_common->insert_into_table($table,$udata);
				
				$udetail['itemid'] = $this->input->post('itemid');
				$udetail['ig_detail_id'] = $add;
				$udetail['qty'] = 1;

				$table='tbl_issue_goods_detail';
				$add_detail = $this->mod_common->insert_into_table($table,$udetail);
			}
			if($udetail_static=='sss')
			{
				$issuenos = $this->input->post('issuenos');
				
				$data['dec_item_list'] = $this->mod_decanting->get_detail_decanting($issuenos);

	        	$dec_qty=		$data['dec_item_list']['dec_qty'];
	        	$dec_amount=	$data['dec_item_list']['dec_amount'];

				$where_complete = "issuenos='$issuenos'";
				$mdata['decanting_status'] = 'Completed';
				$mdata['gas_amt'] = $dec_amount;
				$mdata['total_received'] = $dec_amount;
				$mdata['issuedto'] = '2004003001';
				$mdata['decanting_closed_dt'] = date('Y-m-d H:i:s');
				$mdata['decanting_closed_by'] =$this->session->userdata('id');
				$table='tbl_issue_goods';
				$res=$this->mod_common->update_table($table,$where_complete,$mdata);

				$detail_data['returns'] =1;
				$table='tbl_issue_goods_detail';
				$where_complete = "ig_detail_id='$issuenos'";
				$res=$this->mod_common->update_table($table,$where_complete,$detail_data);

				$data['dec_item_list_trans'] = $this->mod_decanting->get_detail_decanting($issuenos);
				//q();

				//pm($data['dec_item_list_trans']);

				$this->mod_decanting->update_transdetail($data['dec_item_list_trans']['dec_amount'],$issuenos);

			}

	        $data['item_list'] = $this->mod_decanting->get_decanting();
	        foreach ($data['item_list'] as $key => $value) {

	        	$serail_num=$value['issuenos'];

	        	$data['dec_item_list'] = $this->mod_decanting->get_detail_decanting($serail_num);
	        	$dec_item_list_detail = $this->mod_decanting->get_detail_decanting_item($serail_num);


	        	$dec_qty=		$data['dec_item_list']['dec_qty'];
	        	$simpl_qty=		number_format($data['dec_item_list']['dec_qty'],2);
	        	$dec_amount=	$data['dec_item_list']['dec_amount'];
	        	//q();
			?>
				<tr>
					<td class="hidden-480">
						<?php if(count($dec_item_list_detail)>0) { ?>

						<input id="edit_row<?php echo $value['issuenos'] ?>" class="btn btn-xs btn-info edit" type="button" value="+">
						<?php } else { ?>

							No Sale
					<?php } ?>
					</td>

					<td class="center"><?php echo $value['issuenos'] ?>	</td>
					<td><?php echo $value['itemname'] ?></td>
					<td><?php echo $value['itemnameint'] ?></td>
					<td><?php echo number_format($dec_qty,2); ?></td>
					<td><?php echo $value['itemnameint']-$simpl_qty; ?></td>
					<td><?php echo $dec_amount; ?></td>

					<?php if ($this->session->userdata('language')=='en') { ?>
					<td>
						<input type="button" class="btn btn-xs btn-info show-details-btn" value="Make Sale">
					</td>

					<td>
						<?php if($value['itemnameint']-$simpl_qty>0) { echo ''; } else { ?>
						<input onclick="finish_decanting(<?php echo $value['issuenos'] ?>);" type="button" class="btn btn-xs btn-info" value="Finish Sale">
					<?php } ?>
					</td>
					<td> <a href="<?php echo SURL .'decanting/detail/'. $value['issuenos']; ?>"> View </a></td>
				
					<?php } else { ?>

					<td style="width: 21px;">
						<div class="action-buttons">
							<input type="button" class="btn btn-xs btn-info show-details-btn" value="فروخت کرو">
						</div>
					</td>
					<td style="width: 21px;">
						<input onclick="finish_decanting(<?php echo $value['issuenos'] ?>);" type="button" class="btn btn-xs btn-info" value="ختم">
					</td>
				
					<td> <a href="<?php echo SURL .'decanting/detail/'. $value['issuenos']; ?>"> رپورٹ</a></td>

				<?php  } ?>
					
				</tr>
				<tr class="detail-row <?php echo 'tr_'.$value['issuenos'] ?>">
					<td colspan="11">
						<div class="table-detail">
							<div class="row">
								<div class="col-xs-12 col-sm-7">
									<div class="space visible-xs"></div>
									<table>
										<thead>
											<tr>
												<?php if ($this->session->userdata('language')=='en') { ?>
												<th>Cylinder Selected</th>
												<th>Balance Gas (KG)</th>
												<th>Customer Name</th>
												<th> Mobile (03008000001)</th>
												<th> Sale Qty (KG)</th>
												<th> Amount</th>
												<th> Remarks Any</th>
												<?php } else 
												{ ?>
												<th style="text-align: center;">سلنڈر </th>
												<th>کبیلنس    (کلوگرام) </th>
												<th style="text-align: center;">کسٹمر</th>
												<th> موبائل  (03008000001)  </th>
												<th> مقدار  (کلوگرام )</th>
												<th style="text-align: center;"> رقم</th>
												<th style="text-align: center;">ریمارکس</th>

											<?php } ?>
											</tr>
										</thead>
										<tbody>
											
										
										<td> 
											<input type="text" style="width: 123px;" readonly="readonly" disabled="disabled" maxlength="4" value="<?php echo $value['itemname'];?>" name="test<?php echo $serail_num; ?>"  id="test<?php echo $serail_num; ?>">

											<input type="hidden" readonly="readonly" disabled="disabled" value="<?php echo $value['materialcode'];?>" name="item_2<?php echo $serail_num; ?>"  id="item_2<?php echo $serail_num; ?>">

										</td>

										<td> 
											<input type="text" style="width: 60px;" readonly="readonly" disabled="disabled" maxlength="4" value="<?php echo number_format($value['itemnameint']-$dec_qty,2); ?>" pattern="^[0-9]+$" name="balance_gas<?php echo $serail_num; ?>"  id="balance_gas<?php echo $serail_num; ?>">

											
										</td>
										<td>
											<input type="text" maxlength="20" name="customer_name<?php echo $serail_num; ?>" pattern="^[A-Za-z]+$" id="customer_name<?php echo $serail_num; ?>">
										</td>

										<td>
											<input maxlength="11" type="text" name="mobile_num<?php echo $serail_num; ?>" pattern="^[0-9]+$" id="mobile_num<?php echo $serail_num; ?>">
										</td>
										<td style="display: none;">
											<input style="margin-bottom: 24px;"  maxlength="50" type="text" name="address<?php echo $serail_num; ?>" id="address<?php echo $serail_num; ?>">
										</td>
										<td>
											<input type="text" style="width: 60px;" pattern="^[0-9]+$" maxlength="4" name="qty<?php echo $serail_num; ?>" id="qty<?php echo $serail_num; ?>">
										</td>
										<td>
											<input pattern="^[0-9]+$"  maxlength="5" type="text" name="amount<?php echo $serail_num; ?>" id="amount<?php echo $serail_num; ?>">
										</td>
										<td>

											<input pattern="^[0-9]+$"  maxlength="80" type="text" name="remarks<?php echo $serail_num; ?>" id="remarks<?php echo $serail_num; ?>">

										</td>

									</tbody>

									</table>
									
								</div>
								
							</div>
							<?php if ($this->session->userdata('language')=='en') { ?>
							<div class="row" style="margin-left:337px; margin-top: 23px;">


								<input style=" height:34px !important;" onclick="add_decanting(<?php echo $value['issuenos'] ?>);" id="save" class="btn btn-xs btn-info" type="button" value="Save">
								<input style=" height:34px !important;" id="cancel" class="show-details-btn_1 btn btn-xs btn-info" type="button" onclick="add_close(<?php echo $value['issuenos'] ?>)" value="Cancel">
							</div>
							<?php } else 
							{ ?>
							<div class="row" style=" margin-right: 356px;">
								<input style=" height:34px !important;" onclick="add_decanting(<?php echo $value['issuenos'] ?>);" id="save" class="btn btn-xs btn-info" type="button" value="محفوظ">
								<input style=" height:34px !important;" id="cancel" class="show-details-btn_1 btn btn-xs btn-info" type="button" onclick="add_close(<?php echo $value['issuenos'] ?>)" value="ختم">
							</div>
						<?php } ?>


						</div>
					</td>
				
				<?php if(count($dec_item_list_detail)>0) { ?>
			

					<td>

						<tr class="detail-row edit_row<?php echo $value['issuenos'] ?>">
					
					<?php if ($this->session->userdata('language')=='en') { ?>
						<td>Customer Name</td>
						<td>Mobile</td>
						<td>Qty</td>
						<td>Amount</td>
					<?php } else 
					{ ?>
						<th>کسٹمر</th>
						<th> موبائل</th>
						<th> مقدار </th>
						<th> رقم</th>

				<?php } ?>
					<td>Edit</td>
					<td>Delete</td>
					<td>View</td>

				</tr>
				<?php } ?>
				<?php
					 foreach ($dec_item_list_detail as $key => $value) {
						$decanting_num=$value['itemcode'];
						?>
						<tr id="<?php echo 'id_'.$value['decant_id'] ?>" class="detail-row edit_row<?php echo $value['issuenos'] ?>">

						<td> <input maxlength="20" readonly="readonly" disabled="disabled" type="text" id="customer_<?php echo $value['decant_id'] ?>" value="<?php echo $value['customer_name'];?>"> </td>
						<td><input maxlength="11" readonly="readonly" disabled="disabled" type="text" id="mobile_<?php echo $value['decant_id'] ?>" value="<?php echo $value['mobile_num'];?>"></td>
						<td><input maxlength="4" readonly="readonly" disabled="disabled" type="text" id="qty_<?php echo $value['decant_id'] ?>" value="<?php echo $value['qty'];?>">
						<input readonly="readonly" disabled="disabled" type="hidden" id="oldqty_<?php echo $value['decant_id'] ?>" value="<?php echo $value['qty'];?>">
						</td>
						<td><input maxlength="5" readonly="readonly" disabled="disabled" type="text" id="amount_<?php echo $value['decant_id'] ?>" value="<?php echo $value['amount'];?>"></td>
						<td>
							<input id="<?php echo 'edit_'.$value['decant_id']?>" onclick="edit_item(<?php echo $value['decant_id'] ?>)" type="button" class="btn btn-xs btn-info" name="sub_edit" value="Edit">

							<input id="<?php echo 'save_'.$value['decant_id']?>" style="display: none;" onclick="save_item(<?php echo $value['decant_id'] ?>,<?php echo $serail_num; ?>)" type="button" class="btn btn-xs btn-warning savebtn form-control" name="sub_save" value="Save">
						</td>
						<td>
							<input type="button" onclick="sub_delete_rec(<?php echo $value['decant_id'] ?>)" class="btn btn-xs btn-danger" name="sub_delete" value="Delete">
						</td>
						<td>
							<a href="<?php echo SURL.'decanting/view_report/'. $value['decant_id'] ?>" class="btn btn-xs btn-info">Report</a>
						</td>
					</tr>

				<?php } ?>

			
		</td>
	</tr>
					
				<?php } ?>
				<script type="text/javascript">
					$('.show-details-btn').on('click', function(e) {
						e.preventDefault();
						$(this).closest('tr').next().toggleClass('open');
						$(this).find(ace.vars['.icon']).toggleClass('fa-angle-double-down').toggleClass('fa-angle-double-up');
						});
					$('.edit').on('click', function(e) {
					var dynamic_class= $(this).attr("id");
					e.preventDefault();
					$('.'+dynamic_class).toggleClass('open');
					$(this).find(ace.vars['.icon']).toggleClass('fa-angle-double-down').toggleClass('fa-angle-double-up');
				});

				</script>

				<?php 

exit;

		}
		//$this->add_direct_girn();
	}

	public function update_item($decant_id){
		if($this->input->server('REQUEST_METHOD') == 'POST'){
			$decant_id = $this->input->post('decant_id');
			$udata['customer_name'] = $this->input->post('customer_name');
			$udata['mobile_num'] = $this->input->post('mobile_num');
			$udata['qty'] = $this->input->post('qty');
			$udata['amount'] = $this->input->post('amount');
			$udata['remarks'] = $this->input->post('remarks');
			$udata['created_by'] = $this->session->userdata('id');
			$udata['created_date'] = date('Y-m-d');
			$table='tbl_decanting';
			$where = "decant_id='$decant_id'";
			$res=$this->mod_common->update_table($table,$where,$udata);
		}
	}

	public function checkdateposting(){
					
				
				//$sale_date=$this->input->post('date');
				 $sale_date=date('Y-m-d');
				$date_array = array('post_date>=' => $sale_date);
				$last_date =  $this->mod_common->select_single_records('tbl_posting_stock',$date_array);

				if(!empty($last_date))
				{
				 echo "posted";
				 
				}

				
	}

	public function add(){

		if($this->input->server('REQUEST_METHOD') == 'POST'){

			$udata['issuenos'] = $this->input->post('issuenos');
			$udata['customer_name'] = $this->input->post('customer_name');
			$udata['mobile_num'] = $this->input->post('mobile_num');
			$udata['address'] = $this->input->post('address');
			$udata['itemcode'] = $this->input->post('itemid');
			$udata['qty'] = $this->input->post('qty');
			$udata['amount'] = $this->input->post('amount');
			$udata['remarks'] = $this->input->post('remarks');
			$udata['created_by'] = $this->session->userdata('id');
			$udata['created_date'] = date('Y-m-d');
			$udata['sale_date'] = $this->input->post('sale_date');
			
			$table='tbl_decanting';
			echo $add = $this->mod_common->insert_into_table($table,$udata);
			echo "|";
			exit;
		}
		//$this->add_direct_girn();
	}

	public function edit($id){
		if($id){
		$data['customer_list'] = $this->mod_customer->getOnlyCustomers();
		$tablem='tblmaterial_coding';       
        $data['item_list'] = $this->mod_common->get_all_records($tablem,"*");
		$table='tbl_issue_goods';
		$where = "issuenos='$id'";
		$data['single_edit'] = $this->mod_common->select_single_records($table,$where);
//echo '<pre>';print_r($data['single_edit']);exit;
		$data['edit_list'] = $this->mod_salelpg->edit_salelpg($id);
		  $table='tbl_company';       
		$data['pricing_centralized'] = $this->mod_common->get_all_records($table,"*");
		//pm($data['edit_list'] );
		//echo '<pre>';print_r($data['customer_list']);exit;
		foreach ($data['edit_list'] as $key => $value) {
			$data['filledstock'][]=  $this->mod_salelpg->get_details($value['itemid'],$data['single_edit']['issuedate']);
	 	}
		 
		$data["filter"] = '';
		#----load view----------#
		$data["title"] = "Update Decanting ";
		$this->load->view($this->session->userdata('language')."/decanting/edit",$data);
		}
	}

	public function detail($id){
		if($id){
     	
     	$table='tbl_company';       
        $data['company'] = $this->mod_common->get_all_records($table,"*");

		$table='tbl_issue_goods';
		$where = "issuenos='$id'";


		 $data['single_edit'] = $this->mod_decanting->get_single_decanting($where);
		 $data['serail_num'] = $id;

		// pm($data['single_edit']);



	    $data['dec_item_list'] = $this->mod_decanting->get_detail_decanting($id);

		$data['dec_qty']=		$data['dec_item_list']['dec_qty'];
		$data['dec_amount']=	$data['dec_item_list']['dec_amount'];

		$table='tbl_decanting';
		$where = "issuenos='$id'";
		$data['decanting_list'] = $this->mod_common->select_array_records($table,"*",$where);

		$data["filter"] = '';
		#----load view----------#
		$data["title"] = "Detail Decanting ";
		$this->load->view($this->session->userdata('language')."/decanting/single",$data);
		}
	}
	public function view_report($id){
		if($id){
     	
     	$table='tbl_company';       
        $data['company'] = $this->mod_common->get_all_records($table,"*");



		$table='tbl_decanting';
		$where = "decant_id='$id'";
		$data['decanting_list'] = $this->mod_decanting->select_one_decanting($where);

		$data["filter"] = '';
		#----load view----------#
		$data["title"] = "Detail Decanting ";
		$this->load->view($this->session->userdata('language')."/decanting/sub_single",$data);
		}
	}


	public function update(){
		if($this->input->server('REQUEST_METHOD') == 'POST'){
			$add_salelpg=  $this->mod_salelpg->update_sale_lpg($this->input->post());
            //echo "<pre>";print_r($add_salelpg);exit;
		        if ($add_salelpg || $add_salelpg==0) {
		            $this->session->set_flashdata('ok_message', '- Updated Successfully!');
		            redirect(SURL . 'SaleLPG/');
		        } else {
		            $this->session->set_flashdata('err_message', '- Error in updating please try again!');
		            redirect(SURL . 'SaleLPG/');
		        }
		}
		//$this->add_direct_girn();
	}



	function record_delete()
	{
		#-------------delete record ajax--------------#
        $table = "tbl_issue_goods_detail";
        $deleteid=	$this->input->post('deleteid');
        $where = "srno = '" . $deleteid . "'";
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



	function sub_delete_rec($decant_id)
	{

		$table = "tbl_decanting";
        $where = "decant_id = '" . $decant_id . "'";
        $delete_issuenos = $this->mod_common->delete_record($table, $where);
        if($delete_issuenos)
        {
        	echo 111;
        	exit;
        }


	}

	function delete($issuenos)
	{
		
			/////////////////////////////////////////////////////////////////////////////////////////////////////////
			//$sale_date=$this->input->post('date');
			$date_array = array('issuenos' => $issuenos);
			$get_rec_date =  $this->mod_common->select_single_records('tbl_issue_goods',$date_array);

			//$sale_date=$this->input->post('date');
			$date_array = array('post_date>=' => $get_rec_date['issuedate']);
			$last_date =  $this->mod_common->select_single_records('tbl_posting_stock',$date_array);

			if(!empty($last_date))
			{
				//echo "string";
				$this->session->set_flashdata('err_message', 'Already closed for this date');
				redirect(SURL . 'decanting/');
			}
			/////////////////////////////////////////////////////////////////////////////////////////////////////////
 
		$table = "tbl_issue_goods";
        $where = "issuenos = '" . $issuenos . "'";
        $delete_issuenos = $this->mod_common->delete_record($table, $where);

		$table = "tbl_issue_goods_detail";
        $where = "ig_detail_id = '" . $issuenos . "'";
        $delete_issuenos = $this->mod_common->delete_record($table, $where);

		$table = "tbl_decanting";
        $where = "issuenos = '" . $issuenos . "'";
        $delete_issuenos = $this->mod_common->delete_record($table, $where);


		$goodsidt=$issuenos."-Sale decanting";
		$goodsidr=$issuenos."-Receive decanting";
		#-------------delete record--------------#
        $tablems = "tbltrans_master";
        $wherems = "vno = '".$goodsidt."'";
        $deletems = $this->mod_common->delete_record($tablems, $wherems);

        $tablems = "tbltrans_master";
        $wherems = "vno = '".$goodsidr."'";
        $deletems = $this->mod_common->delete_record($tablems, $wherems);

        $tableds = "tbltrans_detail";
        $whereds = "vno = '".$goodsidt."'";
        $deleteds = $this->mod_common->delete_record($tableds, $whereds);

        $tableds = "tbltrans_detail";
        $whereds = "vno = '".$goodsidr."'";
        $deleteds = $this->mod_common->delete_record($tableds, $whereds);



        if ($delete_issuenos) {
            $this->session->set_flashdata('ok_message', '- Deleted Successfully!');
            redirect(SURL . 'decanting/');
        } else {
            $this->session->set_flashdata('err_message', '- Error in updating please try again!');
            redirect(SURL . 'decanting/');
        }




	}

	public function detail_old($id){
		if($id){
		$data['customer_list'] = $this->mod_customer->getOnlyCustomers();
		$table='tblmaterial_coding';       
        $data['item_list'] = $this->mod_common->get_all_records($table,"*");
		$table='tbl_issue_goods';
		$where = "issuenos='$id'";
		$data['single_edit'] = $this->mod_common->select_single_records($table,$where);

		$data['edit_list'] = $this->mod_salelpg->edit_salelpg($id);
		//echo '<pre>';print_r($data);
		$table='tbl_company';       
        $data['company'] = $this->mod_common->get_all_records($table,"*");
		//exit;
		$data["filter"] = '';
		#----load view----------#
		$data["title"] = "Customer Invoice";
		$this->load->view($this->session->userdata('language')."/sale_lpg/single",$data);
		}
	}

	function get_filledstock()
	{
		$data['report']=  $this->mod_salelpg->get_details($this->input->post());
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
		$data['report']=  $this->mod_salelpg->get_details($this->input->post());
		//pm($data['report']);
		foreach ($data['report'] as $key => $value) {
			//pm($value);
		 	echo $value['empty'];
		}
		
	}
}
