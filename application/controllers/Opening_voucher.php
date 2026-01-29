<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Opening_voucher extends CI_Controller {
 
	public function __construct() {
        parent::__construct();

        $this->load->model(array(
            "mod_common","mod_voucher"
        ));
        
    }

	public function index()
	{
		if(isset($_POST['submit'])){			
			$from_date=$data["from_date"] = date("Y-m-d", strtotime($_POST['from']));
			
			$to_date=$data["to_date"] = date("Y-m-d", strtotime($_POST['to']));
			
		}else{
			$from_date=$data["from_date"] = date('Y-m-d', strtotime('-7 day'));
			$to_date =$data["to_date"]= date('Y-m-d');
		}

		$login_user=$this->session->userdata('id');
        $sale_point_id = $this->db->query("select location from tbl_admin where id='$login_user'")->row_array()['location'];
	   
		$data['paymentreceipt_list'] = $this->db->query("select * from tbltrans_master where vno='1-JV-1' and sale_point_id='$sale_point_id' order by masterid  desc")->result_array();
		
 		//pm($data['paymentreceipt_list']);

		$data["filter"] = '';
		#----load view----------#
		$data["title"] = "Manage Opening Voucher";	
		$this->load->view($this->session->userdata('language')."/Opening_voucher/manage_paymentreceipt",$data);
	}
public function add()
	{
		$login_user=$this->session->userdata('id');
		
		if($this->input->server('REQUEST_METHOD') == 'POST'){
			
			
			$this->db->trans_start();
			//pm($this->input->post());
			
			if(empty($this->input->post("acode"))){
				 $this->session->set_flashdata('err_message', 'Something went wrong.');
	             redirect(SURL . 'Opening_voucher/');
			}
			$login_user=$this->session->userdata('id');
            $sale_point_id = $this->db->query("select location from tbl_admin where id='$login_user'")->row_array()['location'];

			$totaldebit =$this->input->post("debit");
			$totalcredit =$this->input->post("credit");
			$fc_totaldebit =$this->input->post("fc_debit");
			$vno =$this->input->post("transcode");
			$array = array(
				            "vno"=>$this->input->post("transcode"),
							"vtype"=>"JV",
							"damount"=>$totaldebit,
							"camount"=>$totalcredit,
							"damount_dollar"=>$fc_totaldebit,
							"camount_dollar"=>$fc_totaldebit,
							"created_date"=>$this->input->post("date"),
							"sale_point_id"=>$sale_point_id,
						  );
			 $insert = $this->db->query("select masterid from tbltrans_master where vno = '$vno'")->row_array()['masterid'];
		

			if(!empty($insert)){

				$this->mod_common->update_table("tbltrans_master",array("masterid"=>$insert), $array);
				
			}else{
				$insert = $this->mod_common->insert_into_table("tbltrans_master", $array);
		          }
			

			

		      $damount=$this->input->post("debit");
				if($damount>0){
					$fc_debit=$this->input->post("fc_debit");
					$fc_credit=0;

				}else{
					$fc_debit=0;
					$fc_credit=$this->input->post("fc_debit");
				}
				 $srno = $this->db->query("select max(srno) as srno from tbltrans_detail where vno = '$vno'")->row_array()['srno'];
				 if($srno==''){
				 	$srno=1;
				 }else{
				 	$srno=$srno+1;
				 }
		

				$array = array(
							"vno"=>$vno,
							"ig_detail_id"=>$insert,
							"srno"=>$srno,
							"acode"=>$this->input->post("acode"),
							"scode"=>$this->input->post("scode"),
						    "damount"=>$this->input->post("debit"),
							"camount"=>$this->input->post("credit"),
							 "damount_dollar"=>$fc_debit,
							"camount_dollar"=>$fc_credit,
							"remarks"=>$this->input->post("remarks"),
							"vtype"=>"JV",
							"vdate"=>$this->input->post("date"),
							"sale_point_id"=>$sale_point_id
						  );
     //pm($array);
				$add =$this->mod_common->insert_into_table("tbltrans_detail", $array);
				$this->db->trans_complete();
				//echo $add;exit;
				if ($add>0) {
					echo '1';exit;
				}else{
					echo '0';exit;
				}
	
        
			

			
		
	    }
	 //    $role = $this->db->query("select * from tbl_user_rights where uid = '$login_user' and pageid = '27' limit 1")->row_array();
		// if ($role['add']!=1) {
		// 	$this->session->set_flashdata('err_message', 'You have no authority to Complete this task .');
		// 	redirect(SURL . 'Opening_voucher/index/');
		// 	}
	    $table='tblacode';
		$where = array('atype' => 'Child','ac_status ='=>'Active');
		$data['aname'] = $this->mod_common->select_array_records($table,"*",$where);
		$userid=1; 
		$type="JV"; 
		$billno = 1;
	    $data['jv']=$userid . "-" . $type . "-" . $billno;
		//q();
		 $data["filter"] = 'add';
        $data["title"] = "Add Opening Voucher";    			
		$this->load->view($this->session->userdata('language')."/Opening_voucher/add",$data);
	}
	public function get_row()
	{
		$vno=$this->input->post('vno');
	    $totaldebit=0; $totalcredit=0;
	    $record = $this->db->query("select tbltrans_detail.*,tblacode.aname from tbltrans_detail inner join tblacode on tblacode.acode=tbltrans_detail.acode where vno='$vno' and vtype='JV' ")->result_array();
		$i=1; if(!empty($record)){ 
											?>
												<input type="hidden" name="edit" value="<?php  echo $value['ig_detail_id']?>">
											<?php		foreach($record as $key=>$value){

															$totaldebit += round($value['damount'],3);
															$totalcredit += round($value['camount'],3);

															if($value['damount_dollar']>0){
																	$fc_totaldebi= $value['damount_dollar'];
																	$fc_totaldebit+= $value['damount_dollar'];
																}else{
																	$fc_totaldebi= $value['camount_dollar'];
																	$fc_totaldebit+= $value['camount_dollar'];
																}
															$scode=$value['scode'];
															$acode=$value['acode'];
															$branch_name=$this->db->query("select stitle from tblsledger where scode='$scode' and acode='$acode'")->row_array()['stitle'];
														
															
											?>
												<tr id='row_<?php echo $i;?>'>
													<td><?php echo $i;?></td>
													<td hidden="">
														<input readonly type='text' name='acode[]' value='<?php echo $value['acode'];?>'/>
													</td>
													<td><?php echo $value['aname'];?></td>
													<td  hidden="">
														<input readonly type='text' name='scode[]' value='<?php echo $value['scode'];?>'/>
													</td>
													<td><?php echo $branch_name;?></td>
													<td>
														<input readonly type='text' style='width: 100%;' name='remarks[]' id='remarks_<?php echo $i;?>' value='<?php echo $value['remarks'];?>'/>
													</td>
													<td>
														<input class='fc_debit' style='width: 100%;'  maxlength='11' readonly type='text' name='fc_debit[]'  style='width: 95%;' id='fc_debit_<?php echo $i;?>' value='<?php echo $fc_totaldebi;?>'/>
													</td>
													
													<td>
														<input class='debit'  style='width: 100%;' maxlength='11' readonly type='text' name='debit[]' style='width: 95%;' id='debit_<?php echo $i;?>' value='<?php echo round($value['damount'],3);?>'/>
													</td>
													<td>
														<input class='credit' style='width: 100%;'  maxlength='11' readonly type='text' name='credit[]' style='width: 95%;' id='credit_<?php echo $i;?>' value='<?php echo round($value['camount'],3);?>'/>
													</td>
													
													<td>
														
														<input type='button' value='Edit' style='width: 40%;' data-id1='<?php echo $i;?>' id='editrow_<?php echo $i;?>' class='btn btn-xs btn-info editrow'/>
														
														<input type='button' value='Save' style='width: 40%;' data-id1='<?php echo $i;?>' data-id2='<?php echo $value['testid'];?>' id='saverow_<?php echo $i;?>' class='btn btn-xs btn-primary saverow'/>
														<input type='button' value='Delete' style='width: 50%;' id='delrow_<?php echo $i;?>' data-id2='<?php echo $value['testid'];?>' data-id1='row_<?php echo $i;?>' class='btn btn-xs btn-danger dltrow'/>
													</td>
												</tr>
											<?php $i++; }} ?>	
											<tr id="totalamtwrpr">
												<td colspan="3"><b>Total</b></td>
												<td><b id="fc_totaldebit"><?php echo $fc_totaldebit;?></b></td>
												 <td><b id="totaldebit"><?php echo round($totaldebit,3);?></b></td>
												<td><b id="totalcredit"><?php echo round($totalcredit,3);?></b></td>
												<input readonly type='hidden'  id='fc_totaldebit' name='fc_totaldebit' value='<?php echo $fc_totaldebit;?>'/>
												<input readonly type='hidden' id='totaldebit' name='totaldebit' value='<?php echo round($totaldebit,3);?>'/>
												<input readonly type='hidden' id='totalcredit' name='totalcredit' value='<?php echo round($totalcredit,3);?>'/>
											</tr>



<?php 	}

	function del_row()
	{
		$id = $_POST['id'];

		 $delete_goods =$this->db->query("delete from tbltrans_detail where testid='$id'");
	 
		
        if ($delete_goods) {
            echo '1';
		 	exit;
		 }
		 else {
		 	echo '0';
		 	exit;
		 }
	}

public function update()
	{
		
			$this->db->trans_start();
			//pm($this->input->post());
			
			  $testid=$this->input->post("testid");
		      $damount=$this->input->post("debit");
				if($damount>0){
					$fc_debit=$this->input->post("fc_debit");
					$fc_credit=0;

				}else{
					$fc_debit=0;
					$fc_credit=$this->input->post("fc_debit");
				}

				$array = array(
							
							 "damount"=>$this->input->post("debit"),
							"camount"=>$this->input->post("credit"),
							 "damount_dollar"=>$fc_debit,
							"camount_dollar"=>$fc_credit,
							"remarks"=>$this->input->post("remarks"),
						 );
     //pm($array);
				$this->mod_common->update_table("tbltrans_detail",array("testid"=>$testid), $array);
				$this->db->trans_complete();
				
			
	    
	
	}
public function add_master()
	{
	
		if(empty($this->input->post("acode"))){
				 $this->session->set_flashdata('err_message', 'Something went wrong.');
	             redirect(SURL . 'Opening_voucher/');
			}
  

			 $this->session->set_flashdata('ok_message', 'Added Successfully!');
	            redirect(SURL . 'Opening_voucher/');
	      
	  
	}
	public function add_master_res()
	{
	// pm($this->input->post());exit;
			$this->db->trans_start();
			
		
  
			$totaldebit = $this->input->post("totaldebit");
			$totalcredit =$this->input->post("totalcredit");
			$fc_totaldebit =$this->input->post("fc_totaldebit");
			$vno = $this->input->post("transcode");

			$array = array(
				            "damount"=>$totaldebit,
							"camount"=>$totalcredit,
							"damount_dollar"=>$fc_totaldebit,
							"camount_dollar"=>$fc_totaldebit,
						  );

			
				$this->mod_common->update_table("tbltrans_master",array("vno"=>$vno), $array);
			
			
			$this->db->trans_complete();

			
	  
	}

	// public function add()
	// {
	// 	$login_user=$this->session->userdata('id');
		
	// 	if($this->input->server('REQUEST_METHOD') == 'POST'){
	// 		ini_set('memory_limit','2048M');
	// 		ini_set('max_execution_time', '300');

	// 		//pm($this->input->post());
	// 		$this->db->trans_start();
			
	// 		if(empty($this->input->post("acode"))){
	// 			 $this->session->set_flashdata('err_message', 'Something went wrong.');
	//              redirect(SURL . 'Opening_voucher/');
	// 		}

	// 		$totaldebit = array_sum($this->input->post("debit"));
	// 		$totalcredit = array_sum($this->input->post("credit"));
	// 		$fc_totaldebit = array_sum($this->input->post("fc_debit"));
		

	// 		$array = array(
	// 			            "vno"=>$this->input->post("transcode"),
	// 						"vtype"=>"JV",
	// 						"damount"=>$totaldebit,
	// 						"camount"=>$totalcredit,
	// 						"damount_dollar"=>$fc_totaldebit,
	// 						"camount_dollar"=>$fc_totaldebit,
	// 						"created_date"=>$this->input->post("date"),
	// 					  );

	// 		if(!empty($this->input->post("edit"))){

	// 			$insert = $this->input->post("edit");
	// 			$this->mod_common->update_table("tbltrans_master",array("masterid"=>$insert), $array);
	// 			$this->db->query("delete from tbltrans_detail where ig_detail_id='$insert'");
	// 			 $vno =$this->input->post("transcode");
	// 		}else{
	// 			$insert = $this->mod_common->insert_into_table("tbltrans_master", $array);
	// 			 $vno =$this->input->post("transcode");
	// 		}
			

	// 		//$this->db->query("update tbltrans_master set vno='$vno' where masterid='$insert'");


	// 		$i=0;$j=1;
	// 		foreach ($this->input->post("acode") as $key => $value) {
	// 			$damount=$this->input->post("debit")[$i];
	// 			if($damount>0){
	// 				$fc_debit=$this->input->post("fc_debit")[$i];
	// 				$fc_credit=0;

	// 			}else{
	// 				$fc_debit=0;
	// 				$fc_credit=$this->input->post("fc_debit")[$i];
	// 			}

	// 			$array = array(
	// 						"vno"=>$vno,
	// 						"ig_detail_id"=>$insert,
	// 						"srno"=>$j,
	// 						"acode"=>$this->input->post("acode")[$i],
	// 					    "damount"=>$this->input->post("debit")[$i],
	// 						"camount"=>$this->input->post("credit")[$i],
	// 						 "damount_dollar"=>$fc_debit,
	// 						"camount_dollar"=>$fc_credit,
	// 						"remarks"=>$this->input->post("remarks")[$i],
	// 						"vtype"=>"JV",
	// 						"vdate"=>$this->input->post("date")
	// 					  );

	// 			$add =$this->mod_common->insert_into_table("tbltrans_detail", $array);
	
 //          $i++;$j++;
	// 		}

			
			
	// 		$this->db->trans_complete();

			
	// 		if ($add) {
	// 		 	if(!empty($this->input->post("edit"))){
	// 		 	$this->session->set_flashdata('ok_message', 'Updated Successfully!');
	//             redirect(SURL . 'Opening_voucher/');
	//         }else{
	//         	$this->session->set_flashdata('ok_message', 'Added Successfully!');
	//             redirect(SURL . 'Opening_voucher/');
	//         }
	//         } else {
	//             $this->session->set_flashdata('err_message', 'Adding Operation Failed.');
	//             redirect(SURL . 'Opening_voucher/');
	//         }
	//     }
	//     $role = $this->db->query("select * from tbl_user_rights where uid = '$login_user' and pageid = '27' limit 1")->row_array();
	// 	if ($role['add']!=1) {
	// 		$this->session->set_flashdata('err_message', 'You have no authority to Complete this task .');
	// 		redirect(SURL . 'Opening_voucher/index/');
	// 		}
	//     $table='tblacode';
	// 	$where = array('atype' => 'Child','ac_status ='=>'Active');
	// 	$data['aname'] = $this->mod_common->select_array_records($table,"*",$where);
	// 	$userid=1; 
	// 	$type="JV"; 
	// 	$billno = 1;
	//     $data['jv']=$userid . "-" . $type . "-" . $billno;
	// 	//q();
	// 	 $data["filter"] = 'add';
 //        $data["title"] = "Add Opening Voucher";    			
	// 	$this->load->view($this->session->userdata('language')."/Opening_voucher/add",$data);
	// }

	public function edit($id)
	{
		$login_user=$this->session->userdata('id');
		// $role = $this->db->query("select * from tbl_user_rights where uid = '$login_user' and pageid = '27' limit 1")->row_array();
		// if ($role['edit']!=1) {
		// 	$this->session->set_flashdata('err_message', 'You have no authority to Complete this task .');
		// 	redirect(SURL . 'Opening_voucher/index/');
		// 	}
	    $table='tblacode';
		$where = array('atype' => 'Child','ac_status ='=>'Active');
		$data['aname'] = $this->mod_common->select_array_records($table,"*",$where);
		//q();

		$data['record'] = $this->db->query("select tbltrans_detail.*,tblacode.aname from tbltrans_detail inner join tblacode on tblacode.acode=tbltrans_detail.acode where ig_detail_id='$id' and vtype='JV' ")->result_array();
		//pm($data['record']);

        $data["filter"] = 'add';
        $data["title"] = "Edit Opening Voucher";    			
		$this->load->view($this->session->userdata('language')."/Opening_voucher/add",$data);
	}

	

	public function delete($id) {

$login_user=$this->session->userdata('id');
		// $role = $this->db->query("select * from tbl_user_rights where uid = '$login_user' and pageid = '27' limit 1")->row_array();
		// if ($role['delete']!=1) {
		// 	$this->session->set_flashdata('err_message', 'You have no authority to Complete this task .');
		// 	redirect(SURL . 'Opening_voucher/index/');
		// 	}
		$this->db->trans_start();
        $table = "tbltrans_master";
        $where = array("masterid"=>$id);
       	$delete = $this->mod_common->delete_record($table, $where);

        $table = "tbltrans_detail";
        $where = array("ig_detail_id"=>$id);
        $delete = $this->mod_common->delete_record($table, $where);
		$this->db->trans_complete();

		if ($this->db->trans_status() === TRUE)
		{
		    $this->session->set_flashdata('ok_message', 'You have succesfully deleted.');
            redirect(SURL . 'Opening_voucher/');
		}else {
            $this->session->set_flashdata('err_message', 'Deleting Operation Failed.');
            redirect(SURL . 'Opening_voucher/');
        }
    }
    public function detail($id){
$login_user=$this->session->userdata('id');
	
		if($id){
			$table='tblacode';
			$where = array('atype' => 'Child','acode!='=>'2003013001');
			$data['aname'] = $this->mod_common->select_array_records($table,"*",$where);

			$table='tbl_company';       
        	$data['company'] = $this->mod_common->get_all_records($table,"*");


			$table='tbltrans_master';
			$where = "vno='$id'";
			$data['single_edit'] = $this->mod_common->select_single_records($table,$where);
        	//pm($data['company'] )
			
			$tables='tbltrans_detail';
			$wheres = "vno='$id'";
			$data['paymentreceipt_list'] = $this->mod_voucher->select_trans_print_records($wheres);

			//pm($data['paymentreceipt_list']);

	        $data["filter"] = 'edit';
        	$data["title"] = "Journal Voucher Dollar";
			$this->load->view($this->session->userdata('language')."/Opening_voucher/single_new_dollar",$data);
		}
		else{
			redirect(SURL.'PaymentReceipt');
		}
	}

}
