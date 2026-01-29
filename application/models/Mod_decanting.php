<?php

class Mod_decanting extends CI_Model {

    function __construct() {

        parent::__construct();
        error_reporting(0);
    }
	public function get_decanting(){
		$this->db->select('tbl_issue_goods.*,tblmaterial_coding.*');    //,SUM(tbl_issue_goods_detail.total_amount)
		$this->db->from('tbl_issue_goods');
		$this->db->join('tbl_issue_goods_detail', 'tbl_issue_goods_detail.ig_detail_id= tbl_issue_goods.issuenos');
		$this->db->join('tblmaterial_coding', 'tblmaterial_coding.materialcode = tbl_issue_goods_detail.itemid');
		$this->db->where('tbl_issue_goods.decanting_status=','Inprocess');
		$this->db->where('tbl_issue_goods.decanting=','Yes');
		$this->db->group_by('ig_detail_id');
		$this->db->order_by("issuenos", "desc");
		$query = $this->db->get();
		return $query->result_array();
	}

	public function get_single_decanting($where){
		$this->db->select('tbl_issue_goods.*,tblmaterial_coding.*');    //,SUM(tbl_issue_goods_detail.total_amount)
		$this->db->from('tbl_issue_goods');
		$this->db->join('tbl_issue_goods_detail', 'tbl_issue_goods_detail.ig_detail_id= tbl_issue_goods.issuenos');
		$this->db->join('tblmaterial_coding', 'tblmaterial_coding.materialcode = tbl_issue_goods_detail.itemid');
		$this->db->where('tbl_issue_goods.decanting_status=','Inprocess');
		$this->db->where('tbl_issue_goods.decanting=','Yes');
		$this->db->where($where);
		$this->db->group_by('ig_detail_id');
		$this->db->order_by("issuenos", "desc");
		$query = $this->db->get();
		return $query->row_array();
	}

	public function select_one_decanting($where){
		$this->db->select('tbl_decanting.*,tbl_decanting.created_date as c_date,tblmaterial_coding.*');    //,SUM(tbl_issue_goods_detail.total_amount)
		$this->db->from('tbl_decanting');
		$this->db->join('tblmaterial_coding', 'tblmaterial_coding.materialcode = tbl_decanting.itemcode');
		$this->db->where($where);
		$query = $this->db->get();
		return $query->row_array();
	}


	public function get_detail_decanting($issuenos){

		$query = $this->db->query("SELECT sum(qty) as dec_qty , sum(amount) as dec_amount FROM tbl_decanting WHERE issuenos ='$issuenos' ");
		return $query->row_array();
	}

	public function get_detail_decanting_item($issuenos){

		$query = $this->db->query("SELECT * FROM tbl_decanting WHERE issuenos ='$issuenos' ");
		return $query->result_array();
	}


	public function update_transdetail($amount,$goodsid){

			
		/////////////////////////// here is code//////////////////
		 	$receiptdate=date('Y-m-d');
	 
			$vendorname='';
			$netamount=$amount;
			$netamountr=$amount;
			$recv_nar='Decanting';
			$nar='Decanting';

			$decant_sale_code='2004003001';

			$cash_inhand='2003013001';

			$user = $this->session->userdata('id');
			$goodsidt=$goodsid."-Sale decanting";
			 
 			$return_rate=0;
			$return_gas=0;
			$return_amount=0;
			$sr=0;

  
///////////////////////// sale entry for gas

				$sr++;
		        $queryd = "INSERT into `tbltrans_detail` (vno,srno,acode,aname,damount,camount,remarks,vtype,svtype,vdate) 
			   values('$goodsidt','$sr','$decant_sale_code','$vendorname','0','$netamount','$nar','SV','SP','$receiptdate')";
				$this->db->query($queryd);	

				$sr++;
		        $queryd = "INSERT into `tbltrans_detail` (vno,srno,acode,aname,damount,camount,remarks,vtype,svtype,vdate) 
			   values('$goodsidt','$sr','$cash_inhand','','$netamount','0','$nar','SV','SP','$receiptdate')";
				$this->db->query($queryd);	

///////////////////////// recv entry for gas if amount recv>0
			$querys="INSERT into `tbltrans_master` (vno,vtype,damount,camount,authenticate,printed,created_by,svtype,created_date)
			values
			('$goodsidt' , 'SV' , '$netamount' , '$netamount' ,'No' ,'No' ,'$user','SP' ,'$receiptdate')";
			$this->db->query($querys);

  

	}
	public function manage_decanting($from,$to){
		$this->db->select('tbl_issue_goods.*,tblmaterial_coding.*');    //,SUM(tbl_issue_goods_detail.total_amount)
		$this->db->from('tbl_issue_goods');
		$this->db->join('tbl_issue_goods_detail', 'tbl_issue_goods_detail.ig_detail_id= tbl_issue_goods.issuenos');
		$this->db->join('tblmaterial_coding', 'tblmaterial_coding.materialcode = tbl_issue_goods_detail.itemid');
		$this->db->where('tbl_issue_goods.decanting=','Yes');
		
		$this->db->where('tbl_issue_goods.issuedate >=', $from);
		$this->db->where('tbl_issue_goods.issuedate <=', $to);	
		
		$this->db->group_by('ig_detail_id');
		$this->db->order_by("issuenos", "desc");
		$query = $this->db->get();
		return $query->result_array();
	}
	public function edit_decanting($id){
		$this->db->select('tbl_issue_goods.*,tbl_issue_goods_detail.*,tblacode.*');
		$this->db->from('tbl_issue_goods');
		$this->db->join('tbl_issue_goods_detail', 'tbl_issue_goods.issuenos = tbl_issue_goods_detail.ig_detail_id');
		$this->db->join('tblacode', 'tbl_issue_goods.issuedto = tblacode.acode');
		$this->db->where('tbl_issue_goods.issuenos=',$id);
		$this->db->order_by("issuenos", "desc");
		$query = $this->db->get();
		return $query->result_array();
	}
	
	public function add_decanting($id){
		$this->db->select('tbl_issue_goods.*,tbl_issue_goods_detail.*,tblacode.*');
		$this->db->from('tbl_issue_goods');
		$this->db->join('tbl_issue_goods_detail', 'tbl_issue_goods.issuenos = tbl_issue_goods_detail.ig_detail_id');
		$this->db->join('tblacode', 'tbl_issue_goods.issuedto = tblacode.acode');
		$this->db->where('tbl_issue_goods.issuenos=',$id);
		$this->db->order_by("issuenos", "desc");
		$query = $this->db->get();
		return $query->result_array();
	}
	

	public function update_decanting($data){
		$ins_array = array(
		    "issuedto" =>$data['customer'],
		    "issuedate" =>$data['date'],
		    "remarks" =>$data['remarks'],
		    "sale_type" =>$data['saletype'],
		    "return_gas" =>$data['returngas'],
		    "return_rate" =>$data['returnrate'],
		    "return_amount" =>$data['returntotal'],
		    "security_amt" =>$data['securityamt'],
		    "gas_amt" =>$data['gasamt'],
		    "total_received" =>$data['totalrecv'],
		   // "created_date" =>date('Y-m-d'),
		    //"created_by" =>$this->session->userdata('id')      
		);
		#----------- add record---------------#
		$id = $_POST['id'];
		$table = "tbl_issue_goods";
		$where = "issuenos= '$id'";
		$update_goods=$this->mod_common->update_table($table,$where,$ins_array);
		
			if($update_goods){
				return $this->multipleitems_againstid($data,$id,'tbl_issue_goods_detail','34');

			}else{
				return false;
			}
	}
}

?>