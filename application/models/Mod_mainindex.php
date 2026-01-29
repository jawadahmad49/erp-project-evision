<?php

class Mod_mainindex extends CI_Model {

    function __construct() {

        parent::__construct();
    }

   public function orders_notification($today){
 
                $sqlsccc = "select count(*) as nos  from tbl_orderbooking where date='$today' and status in ('new','allocated')";
                $querysccc = $this->db->query($sqlsccc);
                $booked_orders = $querysccc->row_array();

 
                $sqlsccc = "select count(*) as nos  from tbl_orderbooking where date='$today' and status in ('Canceled')";
                $querysccc = $this->db->query($sqlsccc);
                $canceled_orders = $querysccc->row_array();

 
                $sqlsccc = "select count(*) as nos  from tbl_orderbooking where date='$today' and status in ('Delivered')";
                $querysccc = $this->db->query($sqlsccc);
                $delivered_orders = $querysccc->row_array();

 
 
                $datas[] = array( 
                    'booked_orders'=>$booked_orders['nos'],
                    'delivered_orders'=>$delivered_orders['nos'],
                    'canceled_orders'=>$canceled_orders['nos'] 
              
                );
        return $datas;
		 
    }
 
   public function query_data_notification($today){
 
                $sqlsccc = "select count(*) as nos  from tbl_complaint_registration where query_complaint='Query' and  reg_dt='$today'";
                $querysccc = $this->db->query($sqlsccc);
                $registered_query = $querysccc->row_array();

 
                $sqlsccc = "select count(*) as nos  from tbl_complaint_registration where query_complaint='Query' and forwarded_dt='$today' and sts in ('Closed','Resolved')";
                $querysccc = $this->db->query($sqlsccc);
                $pending_query = $querysccc->row_array();

 
                $sqlsccc = "select count(*) as nos  from tbl_complaint_registration where query_complaint='Query' and forwarded_dt='$today' and sts in ('Onhold','Forwarded','Inprocess')";
                $querysccc = $this->db->query($sqlsccc);
                $resolved_query = $querysccc->row_array();

 
 
                $datas[] = array( 
                    'registered_query'=>$registered_query['nos'],
                    'resolved_query'=>$resolved_query['nos'],
                    'pending_query'=>$pending_query['nos'] 
              
                );
        return $datas;
		 
    }
	
	 
   public function complaints_data_notification($today){
  
 
                $sqlsccc = "select count(*) as nos  from tbl_complaint_registration where query_complaint='Complaint' and  reg_dt='$today'";
                $querysccc = $this->db->query($sqlsccc);
                $registered_complaints = $querysccc->row_array();

                $sqlsccc = "select count(*) as nos  from tbl_complaint_registration where query_complaint='Complaint' and forwarded_dt='$today' and sts in  ('Closed','Resolved')";
                $querysccc = $this->db->query($sqlsccc);
                $resolved_complaints = $querysccc->row_array();

 
                $sqlsccc = "select count(*) as nos  from tbl_complaint_registration where query_complaint='Complaint' and forwarded_dt='$today' and sts in ('Onhold','Forwarded','Inprocess')";
                $querysccc = $this->db->query($sqlsccc);
                $pending_complaints = $querysccc->row_array();

 
 
                $datas[] = array( 
                    'registered_complaints'=>$registered_complaints['nos'],
                    'pending_complaints'=>$pending_complaints['nos'],
                    'resolved_complaints'=>$resolved_complaints['nos'] 
              
                );
        return $datas;
		 
    }
 
 
 
 
 

}

?>