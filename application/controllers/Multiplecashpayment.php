<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Multiplecashpayment extends CI_Controller {
 
	public function __construct() {
        parent::__construct();

        $this->load->model(array(
            "mod_transaction","mod_common","mod_admin","mod_customerledger","mod_voucher"
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
		

	   
		$data['paymentreceipt_list'] = $this->db->query("select * from tbltrans_master where vtype='CP' and created_date between '$from_date' and '$to_date' and sale_point_id='$sale_point_id' order by masterid  desc")->result_array();
		
 		//pm($data['paymentreceipt_list']);

		$data["filter"] = '';
		#----load view----------#
		$data["title"] = "Manage Cash Payments";	
		$this->load->view($this->session->userdata('language')."/Multiplecashpayment/manage_paymentreceipt",$data);
	}


	public function add()
	{
		$login_user=$this->session->userdata('id');
	    $role = $this->db->query("select * from tbl_user_rights where uid = '$login_user' and pageid = '304' limit 1")->row_array();
		if ($role['add']!=1) {
			$this->session->set_flashdata('err_message', 'You have no authority to Complete this task .');
			redirect(SURL . 'Multiplecashpayment/index/');
			}
	
		if($this->input->server('REQUEST_METHOD') == 'POST'){
	   $login_user=$this->session->userdata('id');
       $sale_point_id = $this->db->query("select location from tbl_admin where id='$login_user'")->row_array()['location'];
       $fix_code = $this->db->query("select * from tbl_code_mapping where sale_point_id='$sale_point_id'")->row_array();
        $cash_code=$fix_code['cash_code'];

			//pm($this->input->post());
			$this->db->trans_start();
			
			if(empty($this->input->post("acode"))){
				 $this->session->set_flashdata('err_message', 'Something went wrong.');
	             redirect(SURL . 'Multiplecashpayment/');
			}
			$vno =$this->input->post("transcode");
			$transid=explode('-', $vno);
			$trans_id=$transid[2];

			$totalvalue = array_sum($this->input->post("amount"));
			$created_date =$this->input->post("date");

			$array = array(
				            "vno"=>$this->input->post("transcode"),
							"vtype"=>"CP",
							"damount"=>$totalvalue,
							"camount"=>$totalvalue,
							"created_date"=>$this->input->post("date"),
							"sale_point_id"=>$sale_point_id,
							"trans_id"=>$trans_id,
						  );

			if(!empty($this->input->post("edit"))){
				$vno =$this->input->post("transcode");
				$cash_in_hand= $this->db->query("select sum(damount)-sum(camount) as cash_in_hand from tbltrans_detail where acode='$cash_code' and vno!='$vno' and vdate<='$created_date'")->row_array()['cash_in_hand'];
 
				if($totalvalue>$cash_in_hand)
			{
				$this->session->set_flashdata('err_message', 'Plz Enter the amount less than cash in hand ');
				redirect(SURL . 'Multiplecashpayment');
			}

				$insert = $this->input->post("edit");
				$this->mod_common->update_table("tbltrans_master",array("masterid"=>$insert), $array);
				$this->db->query("delete from tbltrans_detail where ig_detail_id='$insert'");
			 	
			}else{
				$insert = $this->mod_common->insert_into_table("tbltrans_master", $array);
				 $vno =$this->input->post("transcode");
			}
			
			$transid=explode('-', $vno);
			$trans_id=$transid[2];

			//$this->db->query("update tbltrans_master set vno='$vno' where masterid='$insert'");


			$i=0;$j=1;
			foreach ($this->input->post("acode") as $key => $value) {
				$acode=$this->input->post("acode")[$i];
				$aname = $this->db->query("select aname  from tblacode where acode='$acode'")->row_array()['aname'];

				$array = array(
							"vno"=>$vno,
							"ig_detail_id"=>$insert,
							"srno"=>$j,
							"trans_id"=>$trans_id,
							"sale_point_id"=>$sale_point_id,
							"acode"=>$this->input->post("acode")[$i],
							"damount"=>$this->input->post("amount")[$i],
							"camount"=>0,
							"remarks"=>$this->input->post("remarks")[$i],
							"vtype"=>"CP",
							"vdate"=>$this->input->post("date")
						  );

				$this->mod_common->insert_into_table("tbltrans_detail", $array);
				$j++;
				$nar =$this->input->post("remarks")[$i]." PAID TO ".$aname;
			$array = array(
							"vno"=>$vno,
							"ig_detail_id"=>$insert,
							"srno"=>$j,
							"acode"=>$cash_code,
							"damount"=>0,
							"camount"=>$this->input->post("amount")[$i],
							"remarks"=>$nar,
							"vtype"=>"CP",
							"trans_id"=>$trans_id,
							"sale_point_id"=>$sale_point_id,
							"vdate"=>$this->input->post("date")
						  );

			$add = $this->mod_common->insert_into_table("tbltrans_detail", $array);

				
				$i++;$j++;
			}

			
			
			$this->db->trans_complete();

			
			if ($add) {
				if(!empty($this->input->post("edit"))){
			 	$this->session->set_flashdata('ok_message', 'Updated Successfully!');
	            redirect(SURL . 'Multiplecashpayment/');
	        }else{
	        	$this->session->set_flashdata('ok_message', 'Added Successfully!');
	            redirect(SURL . 'Multiplecashpayment/add');
	     
	        }
	        } else {
	            $this->session->set_flashdata('err_message', 'Adding Operation Failed.');
	            redirect(SURL . 'Multiplecashpayment/');
	        }
	    }
	 
	   $login_user=$this->session->userdata('id');
       $sale_point_id = $this->db->query("select location from tbl_admin where id='$login_user'")->row_array()['location'];
       if ($sale_point_id=='0') {
	  	$this->session->set_flashdata('err_message', '- Admin Has No Rights To Add Cash Payment!');
			redirect(SURL . 'Multiplecashpayment');
			exit();
	  }
       $fix_code = $this->db->query("select * from tbl_code_mapping where sale_point_id='$sale_point_id'")->row_array();
       $customer_code=$fix_code['customer_code'];
       $vendor_code=$fix_code['vendor_code'];
       $cash_code=$fix_code['cash_code'];
       $tax_pay=$fix_code['tax_pay'];
       $tax_receive=$fix_code['tax_receive'];
       $sales_code=$fix_code['sales_code'];
       $stock_code=$fix_code['stock_code'];
       $bank_code=$fix_code['bank_code'];
       $expense_code=$fix_code['expense_code'];
       $cost_of_goods_code=$fix_code['cost_of_goods_code'];
       $empty_stock_code=$fix_code['empty_stock_code'];
       $empty_sale_code=$fix_code['empty_sale_code'];
       $security_code=$fix_code['security_code'];

        $sale_point_id=$fix_code['sale_point_id'];
        $exp_code=$expense_code[0].$expense_code[1].$expense_code[2].$expense_code[3].$expense_code[4].$expense_code[5];
           if($sale_point_id==''){
        	// $data['aname'] =  $this->db->query("select * from tblacode  where atype='Child' ")->result_array();

        }else{
        	  $data['aname'] =  $this->db->query("select * from tblacode  where atype='Child' and general in('$customer_code','$vendor_code','$bank_code','$expense_code','$empty_stock_code','$empty_sale_code','$security_code') or left(acode,6)='$exp_code' or left(acode,6)='200100' or general in ('1002003000','2006001000','1003000000','1002004000','1004001000','2002001000') or tblacode.acode in ('$cash_code','$sale_point_id','$tax_pay','$tax_receive','$sales_code','$stock_code','$cost_of_goods_code')")->result_array();
        }
        

		$userid=$this->session->userdata('id'); 

		$type="CP"; 
	
		$data['cash_in_hand'] = $this->db->query("select sum(damount)-sum(camount) as cash_in_hand from tbltrans_detail where acode='$cash_code'")->row_array()['cash_in_hand'];
        // pm($data['cash_in_hand']);
         $trans_id = $this->db->query("select max(trans_id) as trans_id from tbltrans_detail where sale_point_id='$sale_point_id' and vtype='$type'")->row_array()['trans_id'];
      if($trans_id==''){
      	 $trans_id=1;
      	}else{
      		 $trans_id=$trans_id+1;
      	}
	
		
		$data['jv']=$sale_point_id . "-" . $type . "-" . $trans_id;
		//q();


        $data["filter"] = 'add';
        $data["title"] = "Add Cash Payment";    			
		$this->load->view($this->session->userdata('language')."/Multiplecashpayment/add",$data);
	}
function get_cashhand()
	{  
	$date = $this->input->post("date");  
	$login_user=$this->session->userdata('id');
       $sale_point_id = $this->db->query("select location from tbl_admin where id='$login_user'")->row_array()['location'];
       $fix_code = $this->db->query("select * from tbl_code_mapping where sale_point_id='$sale_point_id'")->row_array();
       $cash_code=$fix_code['cash_code'];
        

		  $opngbl = $this->db->query("select opngbl  from tblacode where acode='$cash_code'")->row_array()['opngbl'];
			$cash_in_hand = $this->db->query("select sum(damount)-sum(camount) as cash_in_hand from tbltrans_detail where acode='$cash_code' and vdate <='$date'")->row_array()['cash_in_hand'];
			echo $cash_in_hand+$opngbl;
	}
	public function edit($id)
	{
		 $login_user=$this->session->userdata('id');
		$role = $this->db->query("select * from tbl_user_rights where uid = '$login_user' and pageid = '304' limit 1")->row_array();
		if ($role['edit']!=1) {
			$this->session->set_flashdata('err_message', 'You have no authority to Complete this task .');
			redirect(SURL . 'Multiplecashpayment/index/');
			}
		$login_user=$this->session->userdata('id');
       $sale_point_id = $this->db->query("select location from tbl_admin where id='$login_user'")->row_array()['location'];
       $fix_code = $this->db->query("select * from tbl_code_mapping where sale_point_id='$sale_point_id'")->row_array();
        $customer_code=$fix_code['customer_code'];
       $vendor_code=$fix_code['vendor_code'];
       $cash_code=$fix_code['cash_code'];
       $tax_pay=$fix_code['tax_pay'];
       $tax_receive=$fix_code['tax_receive'];
       $sales_code=$fix_code['sales_code'];
       $stock_code=$fix_code['stock_code'];
       $bank_code=$fix_code['bank_code'];
       $expense_code=$fix_code['expense_code'];
       $cost_of_goods_code=$fix_code['cost_of_goods_code'];
       $empty_stock_code=$fix_code['empty_stock_code'];
       $empty_sale_code=$fix_code['empty_sale_code'];
       $security_code=$fix_code['security_code'];

        $sale_point_id=$fix_code['sale_point_id'];
        $exp_code=$expense_code[0].$expense_code[1].$expense_code[2].$expense_code[3].$expense_code[4].$expense_code[5];
           if($sale_point_id==''){
        	// $data['aname'] =  $this->db->query("select * from tblacode  where atype='Child' ")->result_array();

        }else{
        	  $data['aname'] =  $this->db->query("select * from tblacode  where atype='Child' and general in('$customer_code','$vendor_code','$bank_code','$expense_code','$empty_stock_code','$empty_sale_code','$security_code') or left(acode,6)='$exp_code' or left(acode,6)='200100' or general in ('1002003000','2006001000','1003000000','1002004000','1004001000','2002001000') or tblacode.acode in ('$cash_code','$sale_point_id','$tax_pay','$tax_receive','$sales_code','$stock_code','$cost_of_goods_code')")->result_array();
        }
		//q();

		$data['record'] = $this->db->query("select tbltrans_detail.*,tblacode.aname from tbltrans_detail inner join tblacode on tblacode.acode=tbltrans_detail.acode where ig_detail_id='$id' and vtype='CP' and damount !=''")->result_array();
		//pm($data['record']);
		$data['cash_in_hand'] = $this->db->query("select sum(damount)-sum(camount) as cash_in_hand from tbltrans_detail where acode='$cash_code'")->row_array()['cash_in_hand'];

        $data["filter"] = 'add';
        $data["title"] = "Edit Payment";    			
		$this->load->view($this->session->userdata('language')."/Multiplecashpayment/add",$data);
	}

	

	public function delete($id) {
 $login_user=$this->session->userdata('id');
		$role = $this->db->query("select * from tbl_user_rights where uid = '$login_user' and pageid = '304' limit 1")->row_array();
		if ($role['delete']!=1) {
			$this->session->set_flashdata('err_message', 'You have no authority to Complete this task .');
			redirect(SURL . 'Multiplecashpayment/index/');
			}
    // echo $id;exit;
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
            redirect(SURL . 'Multiplecashpayment/');
		}else {
            $this->session->set_flashdata('err_message', 'Deleting Operation Failed.');
            redirect(SURL . 'Multiplecashpayment/');
        }
    }
    	public function detail($id){

		if($id){
			//echo $id;
			$login_user=$this->session->userdata('id');
       $sale_point_id = $this->db->query("select location from tbl_admin where id='$login_user'")->row_array()['location'];
       $fix_code = $this->db->query("select * from tbl_code_mapping where sale_point_id='$sale_point_id'")->row_array();
       $cash_code=$fix_code['cash_code'];
		
			$table='tbl_company';       
        	$data['company'] = $this->mod_common->get_all_records($table,"*");


			$table='tbltrans_master';
			$where = "vno='$id'";
			$data['single_edit'] = $this->mod_common->select_single_records($table,$where);
        	//pm($data['single_edit'] );
			
			$tables='tbltrans_detail';
			$wheres = "vno='$id' and tbltrans_detail.acode!='$cash_code'";
			$data['paymentreceipt_list'] = $this->mod_voucher->select_trans_print_records($wheres);

			//pm($data['paymentreceipt_list']);

	        $data["filter"] = 'edit';
        	$data["title"] = "Voucher Payment/Receipt";
     
        	$this->load->view($this->session->userdata('language')."/Multiplecashpayment/single_new",$data);
        	
		}
		else{
			redirect(SURL.'Multiplecashpayment');
		}
	}

}
