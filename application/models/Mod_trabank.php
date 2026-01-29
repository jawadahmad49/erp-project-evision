<?php

class Mod_trabank extends CI_Model {

    function __construct() {

        parent::__construct();
        error_reporting(E_ALL);
    
    }


    public function all_bank_transaction($where,$from,$to,$sum){
		  // $query = "  SELECT *, (select tblacode.aname from tblacode, tbltrans_detail where tblacode.acode=tbltrans_detail.acode
// and tbltrans_detail.acode not in(select acode from tblacode where general='2004002000')
// and tbltrans_detail.vno=tbltrans_master.vno) as account_name FROM `tbltrans_master` 
// WHERE $where and created_date >='$from' and created_date <='$to' ORDER BY `vno` DESC
// ";



	  	$query = " SELECT t.*,a.aname as account_name 
 from tbltrans_master t, tbltrans_detail m, tblacode a
 where t.vno = m.vno and m.acode not like '2004002%' and 
 m.acode=a.acode  
    and  t.created_date >='$from' and t.created_date <='$to'  and t.vtype ='BP' ORDER BY t.vno DESC
	  ";  
	  

//$query = "Select $sum,d.vno,d.vtype, d.vdate as created_date,a.aname as account_name from tbltrans_detail d, tblacode a, tbltrans_master m where $where AND m.vno=d.vno and m.vtype=d.vtype and d.acode!='2003013001' and d.vdate >='$from' and d.vdate <='$to' and a.acode=d.acode GROUP BY d.vno ORDER BY d.vno DESC";
			$result = $this->db->query($query); 
 
        return $result->result_array();

        }

    public function all_bank_transaction_receipt($where,$from,$to,$sum){
		  // $query = "  SELECT *, (select tblacode.aname from tblacode, tbltrans_detail where tblacode.acode=tbltrans_detail.acode
// and tbltrans_detail.acode not in(select acode from tblacode where general='2004002000')
// and tbltrans_detail.vno=tbltrans_master.vno) as account_name FROM `tbltrans_master` 
// WHERE $where and created_date >='$from' and created_date <='$to' ORDER BY `vno` DESC
// ";



	  	$query = " SELECT t.*,a.aname as account_name 
 from tbltrans_master t, tbltrans_detail m, tblacode a
 where t.vno = m.vno and m.acode not like '2004002%' and 
 m.acode=a.acode  
    and  t.created_date >='$from' and t.created_date <='$to'  and t.vtype='BR' ORDER BY t.vno DESC
	  ";  
	  

//$query = "Select $sum,d.vno,d.vtype, d.vdate as created_date,a.aname as account_name from tbltrans_detail d, tblacode a, tbltrans_master m where $where AND m.vno=d.vno and m.vtype=d.vtype and d.acode!='2003013001' and d.vdate >='$from' and d.vdate <='$to' and a.acode=d.acode GROUP BY d.vno ORDER BY d.vno DESC";
			$result = $this->db->query($query); 
 
        return $result->result_array();

        }
    
    public function add_transaction($data){
        //pm($data);
        $transaction = $data['transactions'];
        if($transaction=="Payment"){
            $vtype="BP";
            $damount = $data['amount'];
            $vno = $this->paymentreceipt_vno($vtype);
            //$acode_payment = $this->acode_forpayment();
            /* add record for CR as well */
            $vtype2="BP";
            $camount2 = $data['amount'];
            $vno2 = $this->paymentreceipt_vno($vtype2);
            //$acode_receipt = $this->acode_forreceipt();

        }else{
            $vtype="BR";
            $camount = $data['amount'];
            $vno = $this->paymentreceipt_vno($vtype);
            //$acode_receipt = $this->acode_forreceipt();
            /* add record for CP as well */
            $vtype2="BR";
            $damount2 = $data['amount'];
            $vno2 = $this->paymentreceipt_vno($vtype2);
            //$acode_payment = $this->acode_forpayment();
        }
        $this->db->trans_start();
       
        #----------- add record trans master---------------#
        $in_array_master = array(
            "vno" =>$vno,
            "vtype" =>$vtype,
            "camount" =>$data['amount'],
            "damount" =>$data['amount'],
            "created_date" =>$data['date'],
            "created_by" =>$this->session->userdata('id')    
        );
        
        $table = "tbltrans_master";
        $add = $this->mod_common->insert_into_table($table, $in_array_master);
        //q();

         #----------- add record trans detail ---------------#
        $in_array_detail = array(
            "vno" =>$vno,
            "vtype" =>$vtype,
            "camount" =>$damount,
            "damount" =>$camount,
            "vdate" =>$data['date'],
            "chequeno" =>$data['chequeno'],
            "chequedate" =>$data['chequedate'],
           // "bank" =>$data['bank'],
            "remarks" =>$data['remarks'], 
            //"aname" =>$data['name'], 
            "expense_type" =>$data['acodes'], 
            "acode" =>$data['bank'], 
        );
        $table_detail = "tbltrans_detail";
        $add_detail = $this->mod_common->insert_into_table($table_detail, $in_array_detail);


         #----------- if CR add another entry for CP (vice versa) ---------------#
        $in_array_details = array(
            "vno" =>$vno2,
            "vtype" =>$vtype2,
            "camount" =>$damount2,
            "damount" =>$camount2,
            "vdate" =>$data['date'],
            "chequeno" =>$data['chequeno'],
            "chequedate" =>$data['chequedate'],
            "remarks" =>$data['remarks'], 
            //"aname" =>$data['name'], 
            "expense_type" =>$data['acodes'],
            "acode" =>$data['acodes'],
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
        $vno =$data['id'];

$vno_array=explode('-',$vno);

if($vno_array[1]=='BP'){$transaction ='Payment';}else{$transaction ='Receipt'; }
 
        if($transaction=="Payment"){
            $vtype="BP";
            $damount = $data['amount'];
            $vno = $this->paymentreceipt_vno($vtype); 
            $vtype2="BP";
            $camount2 = $data['amount'];
            $vno2 = $this->paymentreceipt_vno($vtype2); 

        }else{
            $vtype="BR";
            $camount = $data['amount'];
            $vno = $this->paymentreceipt_vno($vtype); 
            $vtype2="BR";
            $damount2 = $data['amount'];
            $vno2 = $this->paymentreceipt_vno($vtype2); 
        }

$this->db->trans_start();
        #----------- update record trans master---------------#
        $in_array_master = array(
            "vtype" =>$vtype,
            "camount" =>$data['amount'],
            "damount" =>$data['amount'],
            "created_date" =>$data['date'],
            "created_by" =>$this->session->userdata('id'),
            "modify_date" =>date("Y-m-d")  
        );
        
        $table = "tbltrans_master";
        $where = "vno='$id'";
        $update=$this->mod_common->update_table($table,$where,$in_array_master);

        $bank_code=$data['bank'];

         $detail_data=$this->mod_common->select_array_records('tbltrans_detail',"*",$where);
 
         $first_id_bank=$detail_data[0]['testid'];
       

        $first_id=$detail_data[1]['testid'];
      

         #----------- update record trans detail ---------------#
        $in_array_detail = array(
             "vtype" =>$vtype,
            "camount" =>$damount,
            "damount" =>$camount,
            "vdate" =>$data['date'],
            "chequeno" =>$data['chequeno'],
            "chequedate" =>$data['chequedate'],
            "remarks" =>$data['remarks'],  
            "expense_type" =>$data['acodes'], 
            "acode" =>$data['bank'],  
        );
        $table_detail = "tbltrans_detail";
        $where = "testid='$first_id_bank'";
        $update_detail = $this->mod_common->update_table($table_detail,$where,$in_array_detail);
        
         #----------- if CR update another entry for CP (vice versa) ---------------#
        $in_array_details = array(
            "vtype" =>$vtype2,
            "camount" =>$damount2,
            "damount" =>$camount2,
            "vdate" =>$data['date'],
            "chequeno" =>$data['chequeno'],
            "chequedate" =>$data['chequedate'],
            "remarks" =>$data['remarks'],  
            "expense_type" =>$data['acodes'],
            "acode" =>$data['acodes'],
        );
        
        $where = "testid='$first_id'";
        $update_details = $this->mod_common->update_table($table_detail,$where,$in_array_details);
        $this->db->trans_complete();
            if($update_detail){
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

        $this->db->select('tbltrans_detail.*,tblacode.aname as final_name');
        $this->db->join('tblacode', 'tbltrans_detail.acode = tblacode.acode'); 
         $this->db->group_by('tbltrans_detail.testid');
        $this->db->where($where);

        $get = $this->db->get('tbltrans_detail');
        return $get->result_array();
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