<?php

class Mod_cylinderconversion extends CI_Model {

    function __construct() {

        parent::__construct();
        error_reporting(0);
    
    }

	
	public function add_cylinder_conversion($data){
		

		error_reporting(E_ALL);

		//pm($data['makenew']);
		$login_user=$this->session->userdata('id');
        $sale_point_id = $this->db->query("select location from tbl_admin where id='$login_user'")->row_array()['location'];
		$ins_array = array(
		    "trans_date" =>$data['date'],
		    "trans_remarks" =>$data['remarks'],
		   	"created_date" =>date('Y-m-d'),
		   	"sale_point_id" =>$sale_point_id,
		    "created_by" =>$this->session->userdata('id')      
		);
		//pm($this->input->post());
		#----------- add record---------------#
		$table = "tbl_cylinderconversion_master";
		$add_conversion = $this->mod_common->insert_into_table($table, $ins_array);
		$insert_id = $add_conversion;
			if($add_conversion){
	
			return $this->multipleitems_againstid($data,$insert_id,'tbl_cylinderconversion_detail');

			}else{
				return false;
		}
	}

	public function multipleitems_againstid($data,$masterid,$table,$updated_value=''){
        $login_user=$this->session->userdata('id');
        $sale_point_id = $this->db->query("select location from tbl_admin where id='$login_user'")->row_array()['location'];
		if($updated_value=='')
		{
			$from_ins_array = array(
		   	"trans_id" =>$masterid,
		    "itemcode" =>$data['hidden_item'],
		    "type" =>'from',
		    "qty" =>$data['hidden_qty'],
		    "sale_point_id" =>$sale_point_id,
		   	"created_date" =>date('Y-m-d'),
		    "created_by" =>$this->session->userdata('id') 
		);

// echo $data['item'][0];
// exit();

			$this->mod_common->insert_into_table('tbl_cylinderconversion_detail', $from_ins_array);

					$datas = array();
					foreach($data['item'] as $key=>$value) {
					$datas[] = array(
						'trans_id' => $masterid,
					    'itemcode' => $data['item'][$key],
					    "type" =>'to',
					    'qty' => $data['to_qty'][$key],
					    "created_date" =>date('Y-m-d'),
					    "sale_point_id" =>$sale_point_id,
					    "created_by" =>$this->session->userdata('id')
					    
					   );
					}
		
		 	$this->db->insert_batch($table, $datas);
		 	return true;
		 }
		 else
		 {
// 		 	echo $data['from_qty'];
		 	//pm($data);
		 	$from_qty=$data['from_qty'];
		 	if($data['from_qty']=='')
		 	{
		 		$from_qty=$data['hidden_qty'];
		 	}

// exit();
		 	$masterid=$data['id'];	
		 	$where_from = "trans_id='$masterid' AND type='from'";
		 	$from_uodate_array = array(
		    "type" =>'from',
		    "qty" =>$from_qty,
		   	"updated_date" =>date('Y-m-d'),
		   	"sale_point_id" =>$sale_point_id,
		    "updated_by" =>$this->session->userdata('id') 
		);
			$table_from='tbl_cylinderconversion_detail';
			$res=$this->mod_common->update_table($table_from,$where_from,$from_uodate_array);
			
		 	 //pm($data['item']);

			$datas = array();
			$datai = array();
			foreach($data['item'] as $key=>$value) {
				$datas[] = array(
					'detail_id' => $data['detail_id'][$key],
				    'itemcode' => $data['item'][$key],
				    'trans_id' => $data['id'],
				    "type" =>'to',
				    'qty' => $data['to_qty'][$key],
				    "sale_point_id" =>$sale_point_id,
				    "created_date" =>date('Y-m-d'),
				    "created_by" =>$this->session->userdata('id')
				   );
			}
			//	print $sale_security;

			//pm($datas);



			foreach($datas as $key=>$value) {
				if($value['detail_id']){
					$datau[] = $value;
				}else{
					$datai[] = $value;
				}
			}
				if($datau){ $this->db->update_batch($table, $datau,'detail_id');}
				if($datai){ $this->db->insert_batch($table, $datai);}


		 }
	}
// SELECT `tbl_issue_goods`.*, `tblacode`.*, SUM(`tbl_issue_goods_detail`.`total_amount`) FROM `tbl_issue_goods` JOIN `tblacode` ON `tbl_issue_goods`.`issuedto` = `tblacode`.`acode` JOIN `tbl_issue_goods_detail` ON `tbl_issue_goods_detail`.`ig_detail_id`= `tbl_issue_goods`.`issuenos` GROUP BY `ig_detail_id` ORDER BY `issuenos` DESC
	public function manage_cylinderconversion($from,$to,$sale_point_id){
		$this->db->select('tbl_cylinderconversion_master.*,tblmaterial_coding.*,tbl_cylinderconversion_detail.qty');  
		$this->db->from('tbl_cylinderconversion_master');
		$this->db->join('tbl_cylinderconversion_detail', ' tbl_cylinderconversion_detail.trans_id= tbl_cylinderconversion_master.trans_id');
		$this->db->join('tblmaterial_coding', 'tbl_cylinderconversion_detail.itemcode = tblmaterial_coding.materialcode');
		$this->db->where('tbl_cylinderconversion_detail.type=','from');
		
		$this->db->where('tbl_cylinderconversion_master.trans_date >=', $from);
		$this->db->where('tbl_cylinderconversion_master.trans_date <=', $to);
		$this->db->where('tbl_cylinderconversion_master.sale_point_id =', $sale_point_id);	
		
		$this->db->group_by('trans_id');
		$query = $this->db->get();
		return $query->result_array();
	}

	public function select_from_records($wheredr){
		$this->db->select('tbl_cylinderconversion_master.*,tblmaterial_coding.*,tbl_cylinderconversion_detail.qty');  
		$this->db->from('tbl_cylinderconversion_master');
		$this->db->join('tbl_cylinderconversion_detail', ' tbl_cylinderconversion_detail.trans_id= tbl_cylinderconversion_master.trans_id');
		$this->db->join('tblmaterial_coding', 'tbl_cylinderconversion_detail.itemcode = tblmaterial_coding.materialcode');
		$this->db->where($wheredr);
		
		$query = $this->db->get();
		return $query->result_array();
	}
	public function select_to_records($wheredr){
		$this->db->select('tbl_cylinderconversion_master.*,tblmaterial_coding.*,tbl_cylinderconversion_detail.qty');  
		$this->db->from('tbl_cylinderconversion_master');
		$this->db->join('tbl_cylinderconversion_detail', ' tbl_cylinderconversion_detail.trans_id= tbl_cylinderconversion_master.trans_id');
		$this->db->join('tblmaterial_coding', 'tbl_cylinderconversion_detail.itemcode = tblmaterial_coding.materialcode');
		$this->db->where($wheredr);
		$query = $this->db->get();
		return $query->result_array();
	}

	public function edit_salelpg($id){
		$this->db->select('tbl_issue_goods.*,tbl_issue_goods_detail.*,tblacode.*');
		$this->db->from('tbl_issue_goods');
		$this->db->join('tbl_issue_goods_detail', 'tbl_issue_goods.issuenos = tbl_issue_goods_detail.ig_detail_id');
		$this->db->join('tblacode', 'tbl_issue_goods.issuedto = tblacode.acode');
		$this->db->where('tbl_issue_goods.issuenos=',$id);
		$this->db->order_by("issuenos", "desc");
		$query = $this->db->get();
		return $query->result_array();
	}
	public function edit_makeneworder($id){
		$this->db->select('tbl_orderbooking.*,tbl_orderbooking_detail.*,tblacode.*');
		$this->db->from('tbl_orderbooking');
		$this->db->join('tbl_orderbooking_detail', 'tbl_orderbooking.id = tbl_orderbooking_detail.orderid');
		$this->db->join('tblacode', 'tbl_orderbooking.acode = tblacode.acode');
		$this->db->where('tbl_orderbooking.id=',$id);
		$this->db->order_by("tbl_orderbooking.id", "desc");
		$query = $this->db->get();
		return $query->result_array();
	}

	public function update_cylinder_conversion($data){
		$login_user=$this->session->userdata('id');
        $sale_point_id = $this->db->query("select location from tbl_admin where id='$login_user'")->row_array()['location'];
		$ins_array = array(
		    "trans_date" =>$data['date'],
		    "trans_remarks" =>$data['remarks'],
		    "updated_date" =>date('Y-m-d'),
		    "sale_point_id" =>$sale_point_id,
		    "updated_by" =>$this->session->userdata('id')      
		);
		#----------- add record---------------#
		$id = $_POST['id'];
		$table = "tbl_cylinderconversion_master";
		$where = "trans_id= '$id'";
		$update_conversion=$this->mod_common->update_table($table,$where,$ins_array);
		
			if($update_conversion){
				return $this->multipleitems_againstid($data,$id,'tbl_cylinderconversion_detail','34');

			}else{
				return false;
			}
	}

	public function select_from_records_1($wheredr){
		$this->db->select('tbl_cylinderconversion_master.*,tblmaterial_coding.*,tbl_cylinderconversion_detail.qty');  
		$this->db->from('tbl_cylinderconversion_master');
		$this->db->join('tbl_cylinderconversion_detail', ' tbl_cylinderconversion_detail.trans_id= tbl_cylinderconversion_master.trans_id');
		$this->db->join('tblmaterial_coding', 'tbl_cylinderconversion_detail.itemcode = tblmaterial_coding.materialcode');
		$this->db->where($wheredr);
		
		$query = $this->db->get();
		return $query->result_array();
	}
	public function select_to_records_2($wheredr){
		$this->db->select('tbl_cylinderconversion_master.*,tblmaterial_coding.*,tbl_cylinderconversion_detail.qty');  
		$this->db->from('tbl_cylinderconversion_master');
		$this->db->join('tbl_cylinderconversion_detail', ' tbl_cylinderconversion_detail.trans_id= tbl_cylinderconversion_master.trans_id');
		$this->db->join('tblmaterial_coding', 'tbl_cylinderconversion_detail.itemcode = tblmaterial_coding.materialcode');
		$this->db->where($wheredr);
		$query = $this->db->get();
		return $query->result_array();
	}

}

?>