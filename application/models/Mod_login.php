<?php



class Mod_login extends CI_Model
{



    function __construct()
    {



        parent::__construct();

        error_reporting(0);
    }

    function check_login($table = "", $data = "", $fields = "*")
    {



        $where = array(
            'loginid' => $data['email'],
            'admin_pwd' => base64_encode($data['password'])
        );

        $this->db->select($fields);

        $this->db->where($where);

        $get = $this->db->get($table);

        return $get->row_array();

        //echo $this->db->last_query();

    }
}
