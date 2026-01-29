<?php

class Mod_item extends CI_Model
{

    function __construct()
    {
        parent::__construct();
        error_reporting(0);
    }


    public function manage_item()
    {
        $login_user = $this->session->userdata('id');
        $this->db->select('tblmaterial_coding.*,tblcategory.catname,tblclass.classname ');
        $this->db->from('tblmaterial_coding');
        $this->db->join('tblcategory', 'tblmaterial_coding.catcode = tblcategory.id');
        $this->db->join('tblclass', 'tblmaterial_coding.classcode = tblclass.classcode');
        $this->db->join('tbl_admin', 'tbl_admin.id = ' . $login_user);
        $this->db->join('tbl_sales_point', 'tbl_sales_point.sale_point_id = tbl_admin.id');
        $this->db->order_by("materialcode", "desc");
        $query = $this->db->get();
        return $query->result_array();
    }

    public function add_item_price($data)
    {
        $city = $data["city"];
        $area = $data['area'];
        if ($city == 'All' && $area = 'All') {
            $sql_pri = "SELECT * FROM  tbl_city ";
            $cityd = mysql_query($sql_pri);
            while ($cityn = mysql_fetch_array($cityd)) {
                $city_id = $cityn['city_id'];
                $sql_pr = "SELECT * FROM  tbl_area where city_id='$city_id' ";
                $Aread = mysql_query($sql_pr);
                while ($arean = mysql_fetch_array($Aread)) {
                    $area_id = $arean['area_id'];


                    $array_insert = array(
                        'city' => $city_id,
                        'area' => $area_id,
                        'itemcode' => $data['itemid'],
                        'sale_price' => $data['price'],
                        'effective_date' => $data['date'],
                        'created_by' => $this->session->userdata('id'),
                        'created_dt' => date('Y-m-d')
                    );

                    $table = "tbl_price";
                    $add = $this->mod_common->insert_into_table($table, $array_insert);

                    if ($add) {
                        return $add;
                    } else {
                        return false;
                    }
                }
            }
        }
    }

    public function get_prices_item($id, $itemid)
    {
        $this->db->select('*');
        $this->db->from('tbl_price');
        $this->db->where(array('itemcode' => $itemid, 'city' => $id));
        $this->db->order_by('effective_date', 'DESC');
        $query = $this->db->get();
        //return $query->num_rows > 0 ? $query->row_array() : FALSE;
        //return $query->result_array();
        return $query->result_array();
    }

    public function get_issue($id)
    {
        $query = $this->db->select('*')
            ->from('tbl_issue_goods_detail')
            ->where('itemid=', $id)
            ->get();
        //return $query->num_rows > 0 ? $query->row_array() : FALSE;
        return $query->num_rows();
    }
    public function get_sold($id)
    {
        $query = $this->db->select('*')
            ->from('tbl_goodsreceiving_detail')
            ->where('itemid=', $id)
            ->get();
        //return $query->num_rows > 0 ? $query->row_array() : FALSE;
        return $query->num_rows();
    }
    public function get_shopopening($id)
    {
        $query = $this->db->select('*')
            ->from('tbl_shop_opening')
            ->where('materialcode=', $id)
            ->get();
        //return $query->num_rows > 0 ? $query->row_array() : FALSE;
        return $query->num_rows();
    }

    public function get_by_title($title)
    {
        $query = $this->db->select('*')
            ->from('tblmaterial_coding')
            ->where('itemname', $title)
            ->get();
        //return $query->num_rows > 0 ? $query->row_array() : FALSE;
        return $query->num_rows();
    }

    public function edit_by_title($title, $id)
    {
        $query = $this->db->select('*')
            ->from('tblmaterial_coding')
            ->where('itemname', $title)
            ->where('materialcode!=', $id)
            ->get();
        //return $query->num_rows > 0 ? $query->row_array() : FALSE;
        return $query->num_rows();
    }
}
