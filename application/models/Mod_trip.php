<?php

class Mod_trip extends CI_Model {

    function __construct() {

        parent::__construct();
    }
	
	public function add_trip($data){

		$get_user_loc = $this->db->get_where('tbl_admin',array('id'=>$this->session->userdata('id')))->row();

		$ins_array = array(
		    "trip_dt" =>$data['date'],
		    "trip_time" =>$data['time'],
		    'vehicle_id'=>$data['vehicle'],
		    'salesman_id'=>$data['saleman'],
		    'sts' => $data['status'],
		    "remarks" =>$data['remarks'],
		    'loccode'=>$get_user_loc->loccode,
		    "created_dt" =>date('Y-m-d'),
		    'created_time' => date('H:i:s'),
		    "created_by" =>$this->session->userdata('id')      
		);

		$table = "tbl_trip";
		$add_trip = $this->mod_common->insert_into_table($table, $ins_array);
		$insert_id = $add_trip;
			if($add_trip){
				return $this->multipleitems_againstid($data,$insert_id,'tbl_trip_items_detail');
			}else{
				return false;
		}
	}
	public function multipleitems_againstid($data,$trip_id,$table){

		$get_user_loc = $this->db->get_where('tbl_admin',array('id'=>$this->session->userdata('id')))->row(); 
	 
		$datas = array();
		foreach($data['item'] as $key=>$value) {
		$datas[] = array(
			'trip_id'=>$trip_id,
		    'itemcode' => $data['item'][$key],
		    'qty_allocated' => $data['qty'][$key],
		    'qty_sold' => 0,
		    'balance_qty' => $data['qty'][$key]
		   );

		}
		$this->db->insert_batch($table, $datas);		
	}

	//Trips
	public function manage_trips(){
		$this->db->select('tbl_trip.*,tbl_admin.admin_name,tbl_vehicles.reg_no');
		$this->db->from('tbl_trip');
		$this->db->join('tbl_admin', 'tbl_trip.salesman_id = tbl_admin.id');
		$this->db->join('tbl_vehicles', ' tbl_vehicles.vehicle_id= tbl_trip.vehicle_id');
		$this->db->where('tbl_trip.loccode',$this->session->userdata('loccode'));
		$this->db->where('tbl_trip.sts',"Inprocess");
		//$this->db->order_by("trip_id", "desc");
		$query = $this->db->get();
		return $query->result_array();
	}

	public function all_trips(){
		$this->db->select('tbl_trip.*,tbl_admin.admin_name');
		$this->db->from('tbl_trip');
		$this->db->join('tbl_admin', 'tbl_trip.salesman_id = tbl_admin.id');
		$this->db->where('tbl_trip.sts',"Inprocess");
		$query = $this->db->get();
		return $query->result();
	}

	//Orders
	public function manage_user_new_orders($location){
		$this->db->select('tbl_orderbooking.*,tblacode.aname');
		$this->db->from('tbl_orderbooking');
		$this->db->join('tblacode', ' tbl_orderbooking.acode= tblacode.acode');
		$this->db->where(array('tbl_orderbooking.salepoint'=>$location,'tbl_orderbooking.status'=>"New"));
		$query = $this->db->get();
		return $query->result();
	}

	//Allocated Orders
	public function manage_user_allocated_orders($location,$id){
		$this->db->select('tbl_orderbooking.*,tblacode.aname');
		$this->db->from('tbl_orderbooking');
		$this->db->join('tblacode', ' tbl_orderbooking.acode= tblacode.acode');
		$this->db->where(array('tbl_orderbooking.salepoint'=>$location,'tbl_orderbooking.status!='=>"New",'tbl_orderbooking.trip_id'=>$id));
		$query = $this->db->get();
		return $query->result();
	}

	//All Orders
	public function manage_user_all_orders($location){
		$this->db->select('tbl_orderbooking.*,tblacode.aname');
		$this->db->from('tbl_orderbooking');
		$this->db->join('tblacode', ' tbl_orderbooking.acode= tblacode.acode');
		$this->db->where('tbl_orderbooking.salepoint',$location);
		$this->db->where('tbl_orderbooking.status!=',"New");
		$this->db->where_not_in('tbl_orderbooking.status',"Allocated");
		$query = $this->db->get();
		return $query->result();
	}

	//All Orders of TRIP
	public function manage_user_all_orders_trip($location,$trip_id){
		$this->db->select('tbl_orderbooking.*,tblacode.aname');
		$this->db->from('tbl_orderbooking');
		$this->db->join('tblacode', ' tbl_orderbooking.acode= tblacode.acode');
		$this->db->where('tbl_orderbooking.salepoint',$location);
		$this->db->where('tbl_orderbooking.trip_id',$trip_id);
		$this->db->where('tbl_orderbooking.status!=',"New");
		$this->db->where_not_in('tbl_orderbooking.status',"Allocated");
		$query = $this->db->get();
		return $query->result();
	}

	//All Orders items
	public function manage_user_all_orders_items($id){
		$this->db->select('tbl_trip_items_detail.*,tblmaterial_coding.itemname');
		$this->db->from('tbl_trip_items_detail');
		$this->db->join('tblmaterial_coding', ' tbl_trip_items_detail.itemcode= tblmaterial_coding.materialcode');
		$this->db->where('tbl_trip_items_detail.trip_id',$id);
		$query = $this->db->get();
		return $query->result();
	}

	public function edit_trip($id){
		$this->db->select('tbl_trip_items_detail.*,tblmaterial_coding.itemname');
		$this->db->from('tbl_trip_items_detail');
		$this->db->join('tblmaterial_coding', 'tbl_trip_items_detail.itemcode = tblmaterial_coding.materialcode');
		$this->db->where('tbl_trip_items_detail.trip_id',$id);
		$query = $this->db->get();
		return $query->result_array();
	}

	public function update_trip($data){

		$this->db->where('trip_id',$data['trip_id']);
		$this->db->delete('tbl_trip_items_detail');

		$datas = array();
		foreach($data['item'] as $key=>$value){
		$datas[] = array(
			'trip_id'=>$data['trip_id'],
		    'itemcode' => $data['item'][$key],
		    'qty_allocated' => $data['qty'][$key],
		    'qty_sold' => 0,
		    'balance_qty' => $data['qty'][$key]
		   );

		}
		return $this->db->insert_batch('tbl_trip_items_detail', $datas);		
	}
}
?>