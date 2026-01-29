<?php

class Mod_sale extends CI_Model {

    function __construct() {

        parent::__construct();
        error_reporting(0);
    
    }
	
	public function get_by_name($name) {
        $query = $this->db->select('*')
        				->from('tbl_sales_point')
        				->where('sp_name', $name)
            			->get();
        //return $query->num_rows > 0 ? $query->row_array() : FALSE;
		//return $query->result_array();
        return $query->num_rows();
    }

    public function add_salepoint($data,$id){

        $filename = "";

        if ($_FILES['company_image']['name'] != "") {

            $projects_folder_path = './assets/images/company/';


            $orignal_file_name = $_FILES['company_image']['name'];

            $file_ext = ltrim(strtolower(strrchr($_FILES['company_image']['name'], '.')), '.');

            $rand_num = rand(1, 1000);

            $config['upload_path'] = $projects_folder_path;
            $config['allowed_types'] = 'jpg|jpeg|gif|tiff|tif|png';
            $config['overwrite'] = false;
            $config['encrypt_name'] = TRUE;
            //$config['file_name'] = $file_name;

            $this->load->library('upload', $config);
            $this->upload->initialize($config);

            if (!$this->upload->do_upload('company_image')) {

                $error_file_arr = array('error' => $this->upload->display_errors());
                //print_r($error_file_arr); exit;
                //return $error_file_arr;
            } else {
            
                $data_image_upload = array('upload_image_data' => $this->upload->data());
                $filename = $data_image_upload['upload_image_data']['file_name'];
                $full_path =   $data_image_upload['upload_image_data']['full_path'];
            }
        }else{
            $filename = $data['sp_logo'];
        }
       

        if($id==TRUE && $id!=""){
$in_array_master = array(
            "sp_name" =>$data['sp_name'],
            "sp_logo" =>$filename,
            "incharge_name" =>$data['incharge_name'],
            "assistant_name" =>$data['assistant_name'],
            "address" =>$data['address'],
            "city_id" =>$data['city'],
            "area_id" =>$data['area'],
            "phone_num" =>$data['phoneno'],
            "ptcl_num" =>$data['ptclno'],
            //"acode" =>$data['acode'],
            "sp_type" =>'Salepoint',
            "email_id" =>$data['email'],
            "sts" => $data["status"],
            "modify_dt" =>date('Y-m-d'),
            "modify_by" =>$this->session->userdata('id')    
        );

            $where = "sale_point_id='$id'";
            $table='tbl_sales_point';
            $add=$this->mod_common->update_table($table,$where,$in_array_master);
        }else{
            $in_array_master = array(
            "sp_name" =>$data['sp_name'],
            "sp_logo" =>$filename,
            "incharge_name" =>$data['incharge_name'],
            "assistant_name" =>$data['assistant_name'],
            "address" =>$data['address'],
            "city_id" =>$data['city'],
            "area_id" =>$data['area'],
			"sp_type" =>'Salepoint',
            "phone_num" =>$data['phoneno'],
            "ptcl_num" =>$data['ptclno'],
            //"acode" =>$data['acode'],
            "email_id" =>$data['email'],
            "sts" => $data["status"],
            "created_dt" =>date('Y-m-d'),
            "created_by" =>$this->session->userdata('id')    
         );

            $table = "tbl_sales_point";
            $add = $this->mod_common->insert_into_table($table, $in_array_master);
					$insert_id = $add;
			
			
			///////////// here is to create accounting heads
				//customers
				//2004002000 2004000000
				  
			// 	$gen='2004000000'; 
			// 	$query = "  select max(acode) as acode from tblacode where general ='$gen'  ";
			// 	$result = $this->db->query($query);
			// 	$max_code = $result->row_array();

			// 	$acode=	$max_code['acode'];
			// 	if(substr($gen,-6) == '000000')
			// 	{
			// 	if($acode){
			// 	$rest = substr($acode,0,7);

			// 	$rest_in =$rest+1;

			// 	$customer_code=$rest_in.'000'; 
			// 	 $acode_new_self=$rest_in.'001';
			// 	}else{

			// 	$rest = substr($gen,0,6);
			// 	$customer_code=$rest.'1000'; 
			// 	$acode_new_self=$rest.'001'; 

			// 	}
			// 	$atype='Parent';
			// 	}
		 
			// $aname=$data['sp_name'].'Trade Debtors / Customers'; 
			// $in_array_master = array(
			// "acode" =>$customer_code,
			// "aname" =>$aname,
			// "general" =>'2004000000',
			// "atype" =>'Parent',
			// );
   //          $table = "tblacode";
   //          $add = $this->mod_common->insert_into_table($table, $in_array_master);
			
			
				//self	//////////////////////////////////////////////////////////////////////////////			
				//2004002001 2004002000
				
				 
			// $aname=$data['sp_name'].'Trade Debtors / Customers'; 
			// $in_array_master = array(
			// "acode" =>$acode_new_self,
			// "aname" =>$data['sp_name'],
			// "general" =>$customer_code,
			// "atype" =>'Child',
			// "ac_status" =>'Active',	"loccode" =>$insert_id,
			// );
   //          $table = "tblacode";
   //          $add = $this->mod_common->insert_into_table($table, $in_array_master);
			
			
			
				
					
				
				//expenses 4001000000 4001002000
				
				// 	$gen='4001000000';
				 
				// 	$query = "  select max(acode) as acode from tblacode where general ='$gen'  ";
				// 	$result = $this->db->query($query);
				// 	$max_code = $result->row_array();

				// 	$acode=	$max_code['acode'];
				// 	if(substr($gen,-6) == '000000')
				// 	{
				// 	if($acode){
				// 	$rest = substr($acode,0,7);

				// 	$rest_in =$rest+1;

				// 	$expenses_code=$rest_in.'000'; 
					 
				// 	}else{

				// 	$rest = substr($gen,0,6);
				// 	$expenses_code=$rest.'1000'; 
					 

				// 	}
				// 	$atype='Parent';
				// 	}

				// 	$aname='EXPENSES ('.$data['sp_name'].')'; 
				// 	$in_array_master = array(
				// 	"acode" =>$expenses_code,
				// 	"aname" =>$aname,
				// 	"general" =>'4001000000',
				// 	"atype" =>'Parent',
				// 	);
				// 	$table = "tblacode";
				// 	$add = $this->mod_common->insert_into_table($table, $in_array_master);

 
				
				// //cash in hand	2003002002 2003002000
				// 	$gen='2003002000';
				// 	$query = "  select max(acode) as acode from tblacode where general ='$gen'  ";
				// 	$result = $this->db->query($query);
				// 	$max_code = $result->row_array();
				// 	$acode=	$max_code['acode'];
				// 	if(substr($gen,-3) == '000')
				// 	{

				// 	if($acode){
				// 	$rest = substr($acode,0,7);

				// 	$rest_in = substr($acode,7,3)+1;


				// 	if($rest_in<=9){$rest_in_n='00'.$rest_in;}
				// 	if($rest_in>9 && $rest_in<=99){$rest_in_n='0'.$rest_in;}
				// 	if($rest_in>99){$rest_in_n=$rest_in;}
				// 	$cash_inhand_code=$rest.$rest_in_n; 
				// 	}else{
				// 	$rest = substr($gen,0,7);
				// 	$cash_inhand_code=$rest.'001'; 
				// 	}

				// 	$atype='Child';
				// 	}
				// 	$aname='CASH IN HAND ('.$data['sp_name'].')'; 
				// 	$in_array_master = array(
				// 	"acode" =>$cash_inhand_code,
				// 	"aname" =>$aname,
				// 	"general" =>$gen,
				// 	"atype" =>'Child', 		
				// 	"ac_status" =>'Active',	"loccode" =>$insert_id,
				// 	);
				// 	$table = "tblacode";
				// 	$add = $this->mod_common->insert_into_table($table, $in_array_master);

			
				// //sales
				// 	$gen='3001001000';
				// 	$query = "  select max(acode) as acode from tblacode where general ='$gen'  ";
				// 	$result = $this->db->query($query);
				// 	$max_code = $result->row_array();
				// 	$acode=	$max_code['acode'];
				// 	if(substr($gen,-3) == '000')
				// 	{

				// 	if($acode){
				// 	$rest = substr($acode,0,7);

				// 	$rest_in = substr($acode,7,3)+1;


				// 	if($rest_in<=9){$rest_in_n='00'.$rest_in;}
				// 	if($rest_in>9 && $rest_in<=99){$rest_in_n='0'.$rest_in;}
				// 	if($rest_in>99){$rest_in_n=$rest_in;}
				// 	$sales_acode=$rest.$rest_in_n; 
				// 	}else{
				// 	$rest = substr($gen,0,7);
				// 	$sales_acode=$rest.'001'; 
				// 	}

				// 	$atype='Child';
				// 	}
				// 	$aname='Sales ('.$data['sp_name'].')'; 
				// 	$in_array_master = array(
				// 	"acode" =>$sales_acode,
				// 	"aname" =>$aname,
				// 	"general" =>$gen,
				// 	"atype" =>'Child',			"ac_status" =>'Active',	"loccode" =>$insert_id,
				// 	);
				// 	$table = "tblacode";
				// 	$add = $this->mod_common->insert_into_table($table, $in_array_master);

			
				// //security
				// $gen='1001002000';
				// 	$query = "  select max(acode) as acode from tblacode where general ='$gen'  ";
				// 	$result = $this->db->query($query);
				// 	$max_code = $result->row_array();
				// 	$acode=	$max_code['acode'];
				// 	if(substr($gen,-3) == '000')
				// 	{

				// 	if($acode){
				// 	$rest = substr($acode,0,7);

				// 	$rest_in = substr($acode,7,3)+1;


				// 	if($rest_in<=9){$rest_in_n='00'.$rest_in;}
				// 	if($rest_in>9 && $rest_in<=99){$rest_in_n='0'.$rest_in;}
				// 	if($rest_in>99){$rest_in_n=$rest_in;}
				// 	$security_acode=$rest.$rest_in_n; 
				// 	}else{
				// 	$rest = substr($gen,0,7);
				// 	$security_acode=$rest.'001'; 
				// 	}

				// 	$atype='Child';
				// 	}
				// 	$aname='Security of Cylinders ('.$data['sp_name'].')'; 
				// 	$in_array_master = array(
				// 	"acode" =>$security_acode,
				// 	"aname" =>$aname,
				// 	"general" =>$gen,
				// 	"atype" =>'Child',			"ac_status" =>'Active',	"loccode" =>$insert_id,
				// 	);
				// 	$table = "tblacode";
				// 	$add = $this->mod_common->insert_into_table($table, $in_array_master);

			
				
				// //tax pay
				// 	$gen='1001003000';
				// 	$query = "  select max(acode) as acode from tblacode where general ='$gen'  ";
				// 	$result = $this->db->query($query);
				// 	$max_code = $result->row_array();
				// 	$acode=	$max_code['acode'];
				// 	if(substr($gen,-3) == '000')
				// 	{

				// 	if($acode){
				// 	$rest = substr($acode,0,7);

				// 	$rest_in = substr($acode,7,3)+1;


				// 	if($rest_in<=9){$rest_in_n='00'.$rest_in;}
				// 	if($rest_in>9 && $rest_in<=99){$rest_in_n='0'.$rest_in;}
				// 	if($rest_in>99){$rest_in_n=$rest_in;}
				// 	$tax_payable_acode=$rest.$rest_in_n; 
				// 	}else{
				// 	$rest = substr($gen,0,7);
				// 	$tax_payable_acode=$rest.'001'; 
				// 	}

				// 	$atype='Child';
				// 	}
				// 	$aname='Sales Tax Payables ('.$data['sp_name'].')'; 
				// 	$in_array_master = array(
				// 	"acode" =>$tax_payable_acode,
				// 	"aname" =>$aname,
				// 	"general" =>$gen,
				// 	"atype" =>'Child',			"ac_status" =>'Active',	"loccode" =>$insert_id,
				// 	);
				// 	$table = "tblacode";
				// 	$add = $this->mod_common->insert_into_table($table, $in_array_master);

				// //tax recv
				// 	$gen='2006001000';
				// 	$query = "  select max(acode) as acode from tblacode where general ='$gen'  ";
				// 	$result = $this->db->query($query);
				// 	$max_code = $result->row_array();
				// 	$acode=	$max_code['acode'];
				// 	if(substr($gen,-3) == '000')
				// 	{

				// 	if($acode){
				// 	$rest = substr($acode,0,7);

				// 	$rest_in = substr($acode,7,3)+1;


				// 	if($rest_in<=9){$rest_in_n='00'.$rest_in;}
				// 	if($rest_in>9 && $rest_in<=99){$rest_in_n='0'.$rest_in;}
				// 	if($rest_in>99){$rest_in_n=$rest_in;}
				// 	$tax_recv_acode=$rest.$rest_in_n; 
				// 	}else{
				// 	$rest = substr($gen,0,7);
				// 	$tax_recv_acode=$rest.'001'; 
				// 	}

				// 	$atype='Child';
				// 	}
				// 	$aname='Sales Tax Receiveables ('.$data['sp_name'].')'; 
				// 	$in_array_master = array(
				// 	"acode" =>$tax_recv_acode,
				// 	"aname" =>$aname,
				// 	"general" =>$gen,
				// 	"atype" =>'Child',			"ac_status" =>'Active',	"loccode" =>$insert_id,
				// 	);
				// 	$table = "tblacode";
				// 	$add = $this->mod_common->insert_into_table($table, $in_array_master);

 
			
				// 		$update_data_code = array(
				// 		"acode" =>$acode_new_self,
				// 		"customer_acode" =>$customer_code,
				// 		"expense_acode" =>$expenses_code,
				// 		"cash_acode" =>$cash_inhand_code,
				// 		"sales_acode" =>$sales_acode,
				// 		"security_acode" =>$security_acode,
				// 		"taxpay_acode" =>$tax_payable_acode,
				// 		"taxrecv_acode" =>$tax_recv_acode 
				// 		);

				// 		$where = "sale_point_id='$insert_id'";
				// 		$table='tbl_sales_point';
				// 		$add=$this->mod_common->update_table($table,$where,$update_data_code);
			
			
			
    //     }
        
        

        if($add){
            return $add;
        }else{
            return false;
        }
    }
}

    public function edit_by_name($name,$id) {
        $query = $this->db->select('*')
                        ->from('tbl_sales_point')
                        ->where('sp_name', $name)
                        ->where('sale_point_id!=', $id)
                        ->get();
        //return $query->num_rows > 0 ? $query->row_array() : FALSE;
        return $query->num_rows();
    }

	public function manage_salepoints(){
		 
		        $query = $this->db->select('*')
                        ->from('tbl_sales_point')
                        ->where('sp_type', 'Salepoint') 
						->order_by("sale_point_id", "desc")
                        ->get();
						
		return $query->result_array();
	}
 
}

?>