<?php 

class Mod_user extends CI_Model {

    function __construct() {

        parent::__construct();
        error_reporting(0);
    
    }
	
	public function get_by_title($title) {
        $query = $this->db->select('*')
        				->from('tbl_admin')
        				->where('loginid', $title)
            			->get();
        //return $query->num_rows > 0 ? $query->row_array() : FALSE;
		//return $query->result_array();
        return $query->num_rows();
    }

    public function user_rights($where) {

        $this->db->select('tbl_admin.*,tbl_user_rights.uid,,tbl_user_rights.pageid');
        $this->db->join('tbl_user_rights', 'tbl_admin.id = tbl_user_rights.uid'); 
        $this->db->group_by('tbl_user_rights.id'); 
        $this->db->where($where); 
        $get = $this->db->get('tbl_admin');
        return $get->result_array();

    }

    public function get_language($comap_id='') {
        $where_user= "id=".$this->session->userdata('comp_id');

        if($comap_id!='')
        {
             $where_user= "id=".$comap_id;
        }

        $this->db->select('lang_opt');
        $this->db->where($where_user); 
        $get = $this->db->get('tbl_company');

        return $get->row_array();
    }

    public function get_menu() {
        $where_user= "uid=".$this->session->userdata('id');
        $where_active= "tbl_menu.sts='Active'";
        $this->db->select('tbl_menu.*,tbl_user_rights.uid,tbl_user_rights.id,tbl_user_rights.pageid');
        $this->db->join('tbl_user_rights', 'tbl_menu.pageid = tbl_user_rights.pageid'); 
        $this->db->where($where_user); 
        $this->db->where($where_active); 
        $this->db->group_by('tbl_user_rights.pageid'); 
        $this->db->order_by("tbl_user_rights.pageid", "ASC");
        $get = $this->db->get('tbl_menu');
        return $get->result_array();
    }
	 public function map_area() {
        $where_user= "user_id=".$this->session->userdata('id');
       // $where_active= "tbl_menu.sts='Active'";
        $this->db->select('tbl_area.*,tbl_user_area.user_id,tbl_user_area.area_id');
        $this->db->join('tbl_user_area', 'tbl_area.area_id = tbl_user_area.area_id'); 
        $this->db->where($where_user); 
       // $this->db->where($where_active); 
        $this->db->group_by('tbl_user_area.area_id'); 
        $this->db->order_by("tbl_user_area.area_id", "ASC");
        $get = $this->db->get('tbl_area');
        return $get->result_array();
    }
    public function get_last_posted() {
  
		$this->db->select('*');    
		$this->db->from('tbl_posting_stock');
		$this->db->order_by("post_date", "desc");
		$this->db->limit(1);
		$query = $this->db->get();
	 
		return $query->result_array();
    }
    public function get_last_backupdate() {
  
		$this->db->select('*');    
		$this->db->from('tbl_db_log');
		$this->db->order_by("dt", "desc");
		$this->db->limit(1);
		$query = $this->db->get();
	 
		return $query->result_array();
    }

	public function manage_users(){
		$this->db->select('*');    
		$this->db->from('tbl_admin');
    //    $this->db->where("id !=", 1);
		$this->db->order_by("id", "desc");
		$query = $this->db->get();
		return $query->result_array();
	}

  	public function edit_by_title($title,$id) {
        $query = $this->db->select('*')
                        ->from('tbl_admin')
                        ->where('loginid', $title)
                        ->where('id!=', $id)
                        ->get();
        //return $query->num_rows > 0 ? $query->row_array() : FALSE;
        return $query->num_rows();
    }

}

?>