<?php

class Mod_restaurant extends CI_Model {

    function __construct() {

        parent::__construct();
        error_reporting(0);
    
    }
    function get_all_restaurants() {

        $this->db->select('tbl_resturant_reg.*,tbl_city.city_name,tbl_country.country_name');
       	$this->db->join('tbl_country', 'tbl_resturant_reg.restaurant_country = tbl_country.country_id'); 
       	$this->db->join('tbl_city', 'tbl_resturant_reg.restaurant_city = tbl_city.city_id'); 
     	$get = $this->db->get('tbl_resturant_reg');
        return $get->result_array();
    }

    function select_single_restaurant($where) {

        $this->db->select('tbl_resturant_reg.*,tbl_city.city_name,tbl_country.country_name');
       	$this->db->join('tbl_country', 'tbl_resturant_reg.restaurant_country = tbl_country.country_id'); 
       	$this->db->join('tbl_city', 'tbl_resturant_reg.restaurant_city = tbl_city.city_id'); 
       	$this->db->where($where);
     	$get = $this->db->get('tbl_resturant_reg');
        return $get->row_array();
    }

    public function add_restaurant($data){


		//if(isset($data["website_name"]))
		mkdir(trim('assets/'.$data["website_name"]));

	    $table = "tbl_resturant_reg";

			$filename = "";

            if ($_FILES['restaurant_image']['name'] != "") {

                $projects_folder_path = './assets/images/restaurant/';


                $orignal_file_name = $_FILES['restaurant_image']['name'];

                $file_ext = ltrim(strtolower(strrchr($_FILES['restaurant_image']['name'], '.')), '.');

                $rand_num = rand(1, 1000);

                $config['upload_path'] = $projects_folder_path;
                $config['allowed_types'] = 'jpg|jpeg|gif|tiff|tif|png';
                $config['overwrite'] = false;
                $config['encrypt_name'] = TRUE;
                //$config['file_name'] = $file_name;

                $this->load->library('upload', $config);
                $this->upload->initialize($config);

                if (!$this->upload->do_upload('restaurant_image')) {

                    $error_file_arr = array('error' => $this->upload->display_errors());
                	//print_r($error_file_arr); exit;
                    //return $error_file_arr;
                } else {
				
                    $data_image_upload = array('upload_image_data' => $this->upload->data());
                    $filename = $data_image_upload['upload_image_data']['file_name'];
					$full_path =   $data_image_upload['upload_image_data']['full_path'];
                }
            }

		$home_delivery=trim($data["home_delivery"]);


     	if($home_delivery=='on')
		{
			$home_delivery='Yes';
		}
		else {
			$home_delivery='No';
		}

		$eat_in=trim($data["eat_in"]);

		if($eat_in=='on')
		{
			$eat_in='Yes';
		}
		else {
			$eat_in='No';
		}

		$take_away=trim($data["take_away"]);

		if($take_away=='on')
		{
			$take_away='Yes';
		}
		else {
			$take_away='No';
		}

		$collection =trim($data["collection"]);
	
		if($collection=='on')
		{
			$collection='Yes';
		}
		else {
			$collection='No';
		}       




/* DAys */
		$monday_working=trim($data["monday_working"]);
		if($monday_working=='on')
		{
			$monday_working='Yes';
		}
		else {
			$monday_working='No';
		}
		$tuesday_working=trim($data["tuesday_working"]);
		if($tuesday_working=='on')
		{
			$tuesday_working='Yes';
		}
		else {
			$tuesday_working='No';
		}
		$wednesday_working=trim($data["wednesday_working"]);
		if($wednesday_working=='on')
		{
			$wednesday_working='Yes';
		}
		else {
			$wednesday_working='No';
		}
		$thursday_working=trim($data["thursday_working"]);
		if($thursday_working=='on')
		{
			$thursday_working='Yes';
		}
		else {
			$thursday_working='No';
		}
		$friday_working=trim($data["friday_working"]);
		if($friday_working=='on')
		{
			$friday_working='Yes';
		}
		else {
			$friday_working='No';
		}
		$saturday_working=trim($data["saturday_working"]);
		if($saturday_working=='on')
		{
			$saturday_working='Yes';
		}
		else {
			$saturday_working='No';
		}
		$sunday_working=trim($data["sunday_working"]);
		if($sunday_working=='on')
		{
			$sunday_working='Yes';
		}
		else {
			$sunday_working='No';
		}
            $ins_array = array(
                "restaurant_name" =>$data['restaurant_name'],
                "restaurant_logo" =>$filename,
                "restaurant_owner_name" =>$data['owner_name'],
                "contact_person" =>$data['contact_person'],
                "restaurant_registration" =>$data['registration'],
                "restaurant_vat" =>$data['vat'],
                "restaurant_mobile1" =>$data['mobile_1'],
                "restaurant_mobile2" =>$data['mobile_2'],
                "restaurant_mobile3" =>$data['mobile_3'],
                "restaurant_mobile4" =>$data['mobile_4'],
                "restaurant_phone1" =>$data['phone_1'],
                "restaurant_phone2" =>$data['phone_2'],
                "restaurant_phone3" =>$data['phone_3'],
                "restaurant_phone4" =>$data['phone_4'],
                "restaurant_email" =>$data['email'],
                "restaurant_fax" =>$data['fax'],
                "restaurant_address" =>$data['address'],
                "restaurant_country" =>$data['country'],
                "restaurant_city" =>$data['city'],
                "restaurant_area" =>$data['area'],
                "restaurant_postalcode" =>$data['postal_code'],
                "restaurant_lat" =>$data['lat_cordinates'],
                "restaurant_long" =>$data['long_cordinates'],
                "restaurant_home_delivery" =>$home_delivery,
                "restaurant_eatin" =>$eat_in,
                "restaurant_take_away" =>$take_away,
                "restaurant_collection" =>$collection,
                "restaurant_delivery_radius" =>$data['home_radius'],
                "restaurant_delivery_amount" =>$data['mimimum_amount'],
                "restaurant_website_name" =>$data['website_name'],
                "monday_working" =>$monday_working,
                "thuesday_working" =>$tuesday_working,
                "wednesday_working" =>$wednesday_working,
                "thursday_working" =>$thursday_working,
                "friday_working" =>$friday_working,
                "saturday_working" =>$saturday_working,
                "sunday_working" =>$sunday_working,
                "monday_starttime" =>$data['monday_starttime'],
                "monday_endtime" =>$data['monday_endtime'],
                "tuesday_starttime" =>$data['tuesday_starttime'],
                "tuesday_endtime" =>$data['tuesday_endtime'],
                "wednesday_starttime" =>$data['wednesday_starttime'],
                "wednesday_endtime" =>$data['wednesday_endtime'],
                "thursday_starttime" =>$data['thursday_starttime'],
                "thursday_endtime" =>$data['thursday_endtime'],
                "friday_starttime" =>$data['friday_starttime'],
                "friday_endtime" =>$data['friday_endtime'],
                "saturday_starttime" =>$data['saturday_starttime'],
                "saturday_endtime" =>$data['saturday_endtime'],
                "sunday_starttime" =>$data['sunday_starttime'],
                "sunday_endtime" =>$data['sunday_endtime'],
                "created_date" =>date('Y-m-d'),
                "created_by" =>$this->session->userdata('id')      
				);
				#----------- add record---------------#
				$table = "tbl_resturant_reg";
				$add_restaurant = $this->mod_common->insert_into_table($table, $ins_array);
				if($add_restaurant){
					return true;
					}else{
						return false;
			}
    }
	
	public function edit_record($rid){		
		#------------ get record------------#
        $table = "tbl_resturant_reg";
        $where = "restaurant_id='" . $rid . "'";
        $result = $this->mod_common->select_single_records($table, $where);
		return $result;
	}
    
   public function update_restaurant($data){  



//		mkdir(trim($data["website_name"]));

	$restaurant_id = $this->input->post('restaurant_id');
	$filename = $this->input->post('old_image');

	    $table = "tbl_resturant_reg";

            if ($_FILES['restaurant_image']['name'] != "") {

                $projects_folder_path = './assets/images/restaurant/';


                $orignal_file_name = $_FILES['restaurant_image']['name'];

                $file_ext = ltrim(strtolower(strrchr($_FILES['restaurant_image']['name'], '.')), '.');

                $rand_num = rand(1, 1000);

                $config['upload_path'] = $projects_folder_path;
                $config['allowed_types'] = 'jpg|jpeg|gif|tiff|tif|png';
                $config['overwrite'] = false;
                $config['encrypt_name'] = TRUE;
                //$config['file_name'] = $file_name;

                $this->load->library('upload', $config);
                $this->upload->initialize($config);

                if (!$this->upload->do_upload('restaurant_image')) {

                    $error_file_arr = array('error' => $this->upload->display_errors());
                	//print_r($error_file_arr); exit;
                    //return $error_file_arr;
                } else {
				
                    $data_image_upload = array('upload_image_data' => $this->upload->data());
                    $filename = $data_image_upload['upload_image_data']['file_name'];
					$full_path =   $data_image_upload['upload_image_data']['full_path'];
                }
            }

		$home_delivery=trim($data["home_delivery"]);

     	if($home_delivery=='on')
		{
			$home_delivery='Yes';
		}
		else {
			$home_delivery='No';
		}

		$eat_in=trim($data["eat_in"]);

		if($eat_in=='on')
		{
			$eat_in='Yes';
		}
		else {
			$eat_in='No';
		}

		$take_away=trim($data["take_away"]);

		if($take_away=='on')
		{
			$take_away='Yes';
		}
		else {
			$take_away='No';
		}

		$collection =trim($data["collection"]);
	
		if($collection=='on')
		{
			$collection='Yes';
		}
		else {
			$collection='No';
		}       


/* DAys */
		$monday_working=trim($data["monday_working"]);
		if($monday_working=='on')
		{
			$monday_working='Yes';
		}
		else {
			$monday_working='No';
		}
		$tuesday_working=trim($data["tuesday_working"]);
		if($tuesday_working=='on')
		{
			$tuesday_working='Yes';
		}
		else {
			$tuesday_working='No';
		}
		$wednesday_working=trim($data["wednesday_working"]);
		if($wednesday_working=='on')
		{
			$wednesday_working='Yes';
		}
		else {
			$wednesday_working='No';
		}
		$thursday_working=trim($data["thursday_working"]);
		if($thursday_working=='on')
		{
			$thursday_working='Yes';
		}
		else {
			$thursday_working='No';
		}
		$friday_working=trim($data["friday_working"]);
		if($friday_working=='on')
		{
			$friday_working='Yes';
		}
		else {
			$friday_working='No';
		}
		$saturday_working=trim($data["saturday_working"]);
		if($saturday_working=='on')
		{
			$saturday_working='Yes';
		}
		else {
			$saturday_working='No';
		}
		$sunday_working=trim($data["sunday_working"]);
		if($sunday_working=='on')
		{
			$sunday_working='Yes';
		}
		else {
			$sunday_working='No';
		}
            $ins_array = array(
                "restaurant_name" =>$data['restaurant_name'],
                "restaurant_logo" =>$filename,
                "restaurant_owner_name" =>$data['owner_name'],
                "contact_person" =>$data['contact_person'],
                "restaurant_registration" =>$data['registration'],
                "restaurant_vat" =>$data['vat'],
                "restaurant_mobile1" =>$data['mobile_1'],
                "restaurant_mobile2" =>$data['mobile_2'],
                "restaurant_mobile3" =>$data['mobile_3'],
                "restaurant_mobile4" =>$data['mobile_4'],
                "restaurant_phone1" =>$data['phone_1'],
                "restaurant_phone2" =>$data['phone_2'],
                "restaurant_phone3" =>$data['phone_3'],
                "restaurant_phone4" =>$data['phone_4'],
                "restaurant_fax" =>$data['fax'],
                "restaurant_address" =>$data['address'],
                "restaurant_country" =>$data['country'],
                "restaurant_city" =>$data['city'],
                "restaurant_area" =>$data['area'],
                "restaurant_postalcode" =>$data['postal_code'],
                "restaurant_lat" =>$data['lat_cordinates'],
                "restaurant_long" =>$data['long_cordinates'],
                "restaurant_home_delivery" =>$home_delivery,
                "restaurant_eatin" =>$eat_in,
                "restaurant_take_away" =>$take_away,
                "restaurant_collection" =>$collection,
                "restaurant_delivery_radius" =>$data['home_radius'],
                "restaurant_delivery_amount" =>$data['mimimum_amount'],
                "monday_working" =>$monday_working,
                "thuesday_working" =>$tuesday_working,
                "wednesday_working" =>$wednesday_working,
                "thursday_working" =>$thursday_working,
                "friday_working" =>$friday_working,
                "saturday_working" =>$saturday_working,
                "sunday_working" =>$sunday_working,
                "monday_starttime" =>$data['monday_starttime'],
                "monday_endtime" =>$data['monday_endtime'],
                "tuesday_starttime" =>$data['tuesday_starttime'],
                "tuesday_endtime" =>$data['tuesday_endtime'],
                "wednesday_starttime" =>$data['wednesday_starttime'],
                "wednesday_endtime" =>$data['wednesday_endtime'],
                "thursday_starttime" =>$data['thursday_starttime'],
                "thursday_endtime" =>$data['thursday_endtime'],
                "friday_starttime" =>$data['friday_starttime'],
                "friday_endtime" =>$data['friday_endtime'],
                "saturday_starttime" =>$data['saturday_starttime'],
                "saturday_endtime" =>$data['saturday_endtime'],
                "sunday_starttime" =>$data['sunday_starttime'],
                "sunday_endtime" =>$data['sunday_endtime'],
                "updated_date" =>date('Y-m-d'),
                "updated_by" =>$this->session->userdata('id')     
				);
			
				#----------- add record---------------#
			$table = "tbl_resturant_reg";
            $where = "`restaurant_id`='" . $restaurant_id . "'";
            $update_resturant = $this->mod_common->update_table($table, $where, $ins_array);
				if($update_resturant)
				{
					return true;
				}
				else
				{
					return false;
				}
    }
	
}

?>