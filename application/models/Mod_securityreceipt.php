<?php

class Mod_securityreceipt extends CI_Model {

    function __construct() {

        parent::__construct();
        error_reporting(E_ALL);
    
    }


    public function all_bank_transaction($from,$to,$sum)
	{

	  	$query = " SELECT t.*,a.aname, m.itemname
 from tbl_security_receipt t, tblmaterial_coding m, tblacode a
 where 
 t.customercode=a.acode and t.itemid=m.materialcode
    and  t.dt >='$from' and t.dt <='$to' ORDER BY t.trans_id DESC
	  ";  
		  $result = $this->db->query($query); 
          return $result->result_array();
    }


    public function addrecord($data){

         $in_array_master = array(
           
            "date" =>$data['date'],
            "fromcustomer" =>$data['acodes'],
            "itemid" =>$data['itemid'],
            "qty" =>$data['qty'],
            "tocustomer" =>$data['to_acodes'],
            "remarks" =>$data['remarks'],
            "type" =>"Empty"
              
        );
        
        $table = "tbltransfercylinder";
        return $add = $this->mod_common->insert_into_table($table, $in_array_master);

    }
    
    
	public function add_transaction($data){
        // pm($data);
        $transaction = $data['transactions'];
       
        $this->db->trans_start();
       
        #----------- add record trans master---------------#
        $in_array_master = array(
           
            "customercode" =>$data['acodes'],
            "itemid" =>$data['itemid'],
            "qty" =>$data['qty'],
            "security_recv" =>$data['security_recv'],
            "remarks" =>$data['remarks'],
            "pay_mode" =>$data['pay_mode'],
            "acode" =>$data['bank'],
            "cheque_dt" =>$data['chequedate'],
            "cheque_no" =>$data['chequeno'],
            "dt" =>$data['date'],
            "created_dt" =>date('Y-m-d'),
            "created_by" =>$this->session->userdata('id')    
        );
        
        $table = "tbl_security_receipt";
        $add = $this->mod_common->insert_into_table($table, $in_array_master);
		  $trans_id = $add;
		
		
	  	$vno=$trans_id.'-SecurityReceipt';
		$vtype='SR';
        #----------- add record trans master---------------#
        $in_array_master = array(
            "vno" =>$vno,
            "vtype" =>$vtype,
            "camount" =>$data['security_recv'],
            "damount" =>$data['security_recv'],
            "created_date" =>$data['date'],
            "created_by" =>$this->session->userdata('id')    
        );
        
        $table = "tbltrans_master";
        $add = $this->mod_common->insert_into_table($table, $in_array_master);
        //q();

		
		
		/////////////////// Customer First Dr then Cr ///////////////////////////////
		 #----------- Customer Cr ---------------#
        $in_array_detail = array(
            "vno" =>$vno,
            "vtype" =>$vtype,
            "damount" =>'0',
            "camount" =>$data['security_recv'],
            "vdate" =>$data['date'],
            "chequeno" =>$data['chequeno'],
            "chequedate" =>$data['chequedate'],
			"remarks" =>$data['remarks'], 
            "acode" =>$data['acodes'], 
        );
        $table_detail = "tbltrans_detail";
        $add_detail = $this->mod_common->insert_into_table($table_detail, $in_array_detail);
		#----------- Customer Dr ---------------#
		$nar="Security Amount Booked Against Cyclinder";
        $in_array_detail = array(
            "vno" =>$vno,
            "vtype" =>'SRE',
            "camount" =>'0',
            "damount" =>$data['security_recv'],
            "vdate" =>$data['date'],
            "chequeno" =>$data['chequeno'],
            "chequedate" =>$data['chequedate'],
			"remarks" =>$nar, 
            "acode" =>$data['acodes'], 
        );
        $table_detail = "tbltrans_detail";
        $add_detail = $this->mod_common->insert_into_table($table_detail, $in_array_detail);

		
		
		
		
		$security_code='1001002001';
				 
         #----------- security Cr ---------------#
        $in_array_detail = array(
            "vno" =>$vno,
            "vtype" =>'SRE',
            "damount" =>'0',
            "camount" =>$data['security_recv'],
            "vdate" =>$data['date'],
            "chequeno" =>$data['chequeno'],
            "chequedate" =>$data['chequedate'],
			"remarks" =>$data['remarks'], 
            "acode" =>$security_code, 
        );
        $table_detail = "tbltrans_detail";
        $add_detail = $this->mod_common->insert_into_table($table_detail, $in_array_detail);

		$acode_bank_cash='';
		
		if($data['pay_mode']=='Cash'){
			 $acode_bank_cash='2003013001'; }
		else{  $acode_bank_cash=$data['bank'];  }
         #----------- Bank/Cash Debit ---------------#
        $in_array_details = array(
            "vno" =>$vno,
            "vtype" =>$vtype,
            "damount" =>$data['security_recv'],
            "camount" =>'0',
            "vdate" =>$data['date'],
            "chequeno" =>$data['chequeno'],
            "chequedate" =>$data['chequedate'],
            "remarks" =>$data['remarks'], 
          
            "acode" =>$acode_bank_cash,
        );
        



        $add_details = $this->mod_common->insert_into_table($table_detail, $in_array_details);
        $this->db->trans_complete();
            if($add && $add_detail){
                return true;
            }else{
                return false;
            }
    }

    public function paymentreceipt_vno($type){

        $result = $this->db->select('vno')
                          ->from('tbltrans_master')
                          ->where('vtype',$type)
                          ->get();

        $Sr = array();
        $num_rows = $result->num_rows();
        if($num_rows!=0)
        {
            $line = $result->result_array();
            foreach ($line as $key => $value)
            {
                $parts = explode("-",$value['vno']);
                $Sr[] = $parts[2];
            }
            $billno=max($Sr)+1;
        }
        else{
            $billno = 1;
        }
        if($billno <=9){
            $billno = "00000" . $billno;
        }
        else if($billno <=99){
            $billno = "0000" . $billno;
        }
        else if($billno <=999){
            $billno = "000" . $billno;
        }
        else if($billno <=9999){
            $billno = "00" . $billno;
        }
        else if($billno <=99999){
            $billno = "0" . $billno;
        }
        return $vno=$this->session->userdata('id') . "-" . $type . "-" . $billno;
 
    }
 
     public function update_transaction($data){
        $id = $data['id'];
        
		 
        $transaction = $id;
       
        $this->db->trans_start();
       
        #----------- add record trans master---------------#
        $in_array_master = array(
           
            "customercode" =>$data['acodes'],
            "itemid" =>$data['itemid'],
            "qty" =>$data['qty'],
            "security_recv" =>$data['security_recv'],
            "remarks" =>$data['remarks'],
         
            "acode" =>$data['bank'],
            "cheque_dt" =>$data['chequedate'],
            "cheque_no" =>$data['chequeno'],
            "dt" =>$data['date'],
            "created_dt" =>date('Y-m-d'),
            "created_by" =>$this->session->userdata('id')    
        );
          
        
		$table = "tbl_security_receipt";
		$where = "trans_id= '$id'";
		$update_goods=$this->mod_common->update_table($table,$where,$in_array_master);
		
	  	$vno=$id.'-SecurityReceipt';
		$vtype='SR';
			$vtyped='SRE';
		
		
		$sqld ="DELETE FROM `tbltrans_detail` WHERE `vno` = '$vno' and `vtype`='$vtype'"; $this->db->query($sqld);
		$sqld ="DELETE FROM `tbltrans_detail` WHERE `vno` = '$vno' and `vtype`='$vtyped'"; $this->db->query($sqld);
		$sqlm ="DELETE FROM `tbltrans_master` WHERE `vno` = '$vno' and `vtype`='$vtype'"; $this->db->query($sqlm);

		
		
        #----------- add record trans master---------------#
        $in_array_master = array(
            "vno" =>$vno,
            "vtype" =>$vtype,
            "camount" =>$data['security_recv'],
            "damount" =>$data['security_recv'],
            "created_date" =>$data['date'],
            "created_by" =>$this->session->userdata('id')    
        );
        
        $table = "tbltrans_master";
        $add = $this->mod_common->insert_into_table($table, $in_array_master);
        //q();

		
		
		
		/////////////////// Customer First Dr then Cr ///////////////////////////////
		 #----------- Customer Cr ---------------#
        $in_array_detail = array(
            "vno" =>$vno,
            "vtype" =>$vtype,
            "damount" =>'0',
            "camount" =>$data['security_recv'],
            "vdate" =>$data['date'],
            "chequeno" =>$data['chequeno'],
            "chequedate" =>$data['chequedate'],
			"remarks" =>$data['remarks'], 
            "acode" =>$data['acodes'], 
        );
        $table_detail = "tbltrans_detail";
        $add_detail = $this->mod_common->insert_into_table($table_detail, $in_array_detail);
		#----------- Customer Dr ---------------#
			$nar="Security Amount Booked Against Cyclinder";
        $in_array_detail = array(
            "vno" =>$vno,
            "vtype" =>'SRE',
            "camount" =>'0',
            "damount" =>$data['security_recv'],
            "vdate" =>$data['date'],
            "chequeno" =>$data['chequeno'],
            "chequedate" =>$data['chequedate'],
			"remarks" =>$nar, 
            "acode" =>$data['acodes'], 
        );
        $table_detail = "tbltrans_detail";
        $add_detail = $this->mod_common->insert_into_table($table_detail, $in_array_detail);

		
		
		
		$security_code='1001002001';
		#----------- security Cr ---------------#
        $in_array_detail = array(
            "vno" =>$vno,
            "vtype" =>'SRE',
            "damount" =>'0',
            "camount" =>$data['security_recv'],
            "vdate" =>$data['date'],
            "chequeno" =>$data['chequeno'],
            "chequedate" =>$data['chequedate'],
			"remarks" =>$data['remarks'], 
            "acode" =>$security_code, 
        );
        $table_detail = "tbltrans_detail";
        $add_detail = $this->mod_common->insert_into_table($table_detail, $in_array_detail);

		$acode_bank_cash='';
		
		if($data['pay_mode']=='Cash'){
			 $acode_bank_cash='2003013001'; }
		else{  $acode_bank_cash=$data['bank'];  }
         #----------- Bank/Cash Debit ---------------#
        $in_array_details = array(
            "vno" =>$vno,
            "vtype" =>$vtype,
            "damount" =>$data['security_recv'],
            "camount" =>'0',
            "vdate" =>$data['date'],
            "chequeno" =>$data['chequeno'],
            "chequedate" =>$data['chequedate'],
            "remarks" =>$data['remarks'], 
          
            "acode" =>$acode_bank_cash,
        );
        



        $add_details = $this->mod_common->insert_into_table($table_detail, $in_array_details);
        $this->db->trans_complete();
            if($add && $add_detail){
                return true;
            }else{
                return false;
            }
     }

     public function acode_forpayment(){

        $rest_creditors_code='2001001';
        $result = $this->db->select('COALESCE(max(right(`acode`,3)),0) as code')
                          ->from('tbltrans_detail')
                          ->where('LEFT(`acode`,7)',$rest_creditors_code)
                          ->get();

        $num_rows = $result->num_rows();
        $Code = 1;

        $line = $result->row_array();

        if(intval($line["code"])==0)
            $Code=1;
        else
            $Code=intval($line["code"])+1;
        if($Code<=99)
            $Code="00".$Code;

        return $rest_creditors_code .= $Code;
        
    }



    function select_trans_print_records($where) {

     	$query = " SELECT t.*,a.aname, m.itemname
 from tbl_security_receipt t, tblmaterial_coding m, tblacode a
 where 
 t.customercode=a.acode and t.itemid=m.materialcode
    and  t.trans_id='$where'  ";  
		  $result = $this->db->query($query); 
          return $result->result_array();
    }



     public function acode_forreceipt(){

            $rest_creditors_code='2001002';
            $result = $this->db->select('COALESCE(max(right(`acode`,3)),0) as code')
                          ->from('tbltrans_detail')
                          ->where('LEFT(`acode`,7)',$rest_creditors_code)
                          ->get();

        $num_rows = $result->num_rows();
        $Code = 1;

        $line = $result->row_array();

        if(intval($line["code"])==0)
            $Code=1;
        else
            $Code=intval($line["code"])+1;
        if($Code<=99)
            $Code="00".$Code;

        return $rest_creditors_code .= $Code;
        
    }
}

?>