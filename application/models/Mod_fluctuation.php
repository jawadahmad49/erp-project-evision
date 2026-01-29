<?php

class Mod_fluctuation extends CI_Model
{

    function __construct()
    {

        parent::__construct();
        error_reporting(0);
    }


    function get_stock($id, $date)
    {

        $sqlcot = "SELECT opening_qty as opening_qty FROM `tblmaterial_coding` WHERE materialcode ='$id'";
        $querycot = $this->db->query($sqlcot);
        $open = $querycot->row_array();

        $sqlv_e = "SELECT  COALESCE(SUM(`tbl_goodsreceiving_detail`.`quantity`),0) as quantity  FROM `tbl_goodsreceiving` INNER JOIN `tbl_goodsreceiving_detail` ON `tbl_goodsreceiving`.`receiptnos` = `tbl_goodsreceiving_detail`.`receipt_detail_id` WHERE `receiptdate`<='$date'   AND `tbl_goodsreceiving_detail`.`itemid`='$id'";
        $queryv_e = $this->db->query($sqlv_e);
        $purchase = $queryv_e->row_array();

        $sqlv_e = "SELECT  COALESCE(SUM(`tbltrans_detail`.`qty`),0) as sale  FROM `tbltrans_detail`  WHERE `tbltrans_detail`.`vdate`<='$date'   AND `tbltrans_detail`.`itemcode`='$id'  AND `tbltrans_detail`.`damount`>0 and svtype IN ('SP','CS','GS')";
        $queryv_e = $this->db->query($sqlv_e);
        $sale = $queryv_e->row_array();
        $sqlv_ee = "SELECT  COALESCE(SUM(`tbltrans_detail`.`qty`),0) as gain  FROM `tbltrans_detail`  WHERE `tbltrans_detail`.`vdate`<='$date'   AND `tbltrans_detail`.`itemcode`='$id'  AND `tbltrans_detail`.`damount`>0 and svtype='GL'";
        $queryv_ee = $this->db->query($sqlv_ee);
        $sale_dain = $queryv_ee->row_array();

        return $open['opening_qty'] + $purchase['quantity'] - $sale['sale'] + $sale_dain['gain'];
    }
    function get_stock_closing($id, $date)
    {

        $sqlcot = "SELECT opening_qty as opening_qty FROM `tblmaterial_coding` WHERE materialcode ='$id'";
        $querycot = $this->db->query($sqlcot);
        $open = $querycot->row_array();



        $sqlv_e = "SELECT  COALESCE(SUM(`tbl_goodsreceiving_detail`.`quantity`),0) as quantity  FROM `tbl_goodsreceiving` INNER JOIN `tbl_goodsreceiving_detail` ON `tbl_goodsreceiving`.`receiptnos` = `tbl_goodsreceiving_detail`.`receipt_detail_id` WHERE `receiptdate`<'$date'   AND `tbl_goodsreceiving_detail`.`itemid`='$id'";
        $queryv_e = $this->db->query($sqlv_e);
        $purchase = $queryv_e->row_array();

        $sqlv_e = "SELECT  COALESCE(SUM(`tbltrans_detail`.`qty`),0) as sale  FROM `tbltrans_detail`  WHERE `tbltrans_detail`.`vdate`<'$date'   AND `tbltrans_detail`.`itemcode`='$id'  AND `tbltrans_detail`.`damount`>0 and svtype IN ('SP','CS')";
        $queryv_e = $this->db->query($sqlv_e);
        $sale = $queryv_e->row_array();
        $sqlv_ee = "SELECT  COALESCE(SUM(`tbltrans_detail`.`qty`),0) as gain  FROM `tbltrans_detail`  WHERE `tbltrans_detail`.`vdate`<'$date'   AND `tbltrans_detail`.`itemcode`='$id'  AND `tbltrans_detail`.`damount`>0 and svtype='GL'";
        $queryv_ee = $this->db->query($sqlv_ee);
        $sale_dain = $queryv_ee->row_array();








        return $open['opening_qty'] + $purchase['quantity'] - $sale['sale'] + $sale_dain['gain'];
    }
    function get_stock_closing_report($id, $date)
    {

        $sqlcot = "SELECT opening_qty as opening_qty FROM `tblmaterial_coding` WHERE materialcode ='$id'";
        $querycot = $this->db->query($sqlcot);
        $open = $querycot->row_array();



        $sqlv_e = "SELECT  COALESCE(SUM(`tbl_goodsreceiving_detail`.`quantity`),0) as quantity  FROM `tbl_goodsreceiving` INNER JOIN `tbl_goodsreceiving_detail` ON `tbl_goodsreceiving`.`receiptnos` = `tbl_goodsreceiving_detail`.`receipt_detail_id` WHERE `receiptdate`<'$date'   AND `tbl_goodsreceiving_detail`.`itemid`='$id'";
        $queryv_e = $this->db->query($sqlv_e);
        $purchase = $queryv_e->row_array();

        $sqlv_e = "SELECT  COALESCE(SUM(`tbltrans_detail`.`qty`),0) as sale  FROM `tbltrans_detail`  WHERE `tbltrans_detail`.`vdate`<'$date'   AND `tbltrans_detail`.`itemcode`='$id'  AND `tbltrans_detail`.`damount`>0 and svtype IN ('SP','CS')";
        $queryv_e = $this->db->query($sqlv_e);
        $sale = $queryv_e->row_array();
        $sqlv_ee = "SELECT  COALESCE(SUM(`tbltrans_detail`.`qty`),0) as gain  FROM `tbltrans_detail`  WHERE `tbltrans_detail`.`vdate`<'$date'   AND `tbltrans_detail`.`itemcode`='$id'  AND `tbltrans_detail`.`damount`>0 and svtype='GL'";
        $queryv_ee = $this->db->query($sqlv_ee);
        $sale_dain = $queryv_ee->row_array();








        return $open['opening_qty'] + $purchase['quantity'] - $sale['sale'];
    }
    function shop_opening($id)
    {

        $this->db->select('SELECT COUNT(id) as total');
        $this->db->where('item_id=', $id);
        $get = $this->db->get('tbl_price_fluctuation');
        $sale_filled = $get->row_array();


        return $sale_filled['total'];
    }

    public function manage_item()
    {
        $this->db->select('tbl_price_fluctuation.*,tblmaterial_coding.itemname');
        $this->db->from('tbl_price_fluctuation');
        $this->db->join('tblmaterial_coding', 'tbl_price_fluctuation.item_id = tblmaterial_coding.materialcode');
        $this->db->order_by("id", "desc");
        $query = $this->db->get();
        return $query->result_array();
    }

    public function get_fluctuation($item, $date, $location)
    {
        $sql = "SELECT * from `tbl_price_fluctuation` WHERE `item_id`=$item and `edate`<='$date' and sale_point_id = '$location' order by edate desc limit 1";
        // echo "SELECT * from `tbl_price_fluctuation` WHERE `item_id`=$itemid and `edate`<='$date'";
        $query = $this->db->query($sql);

        if ($query->num_rows() > 0) {
            foreach ($query->result_array() as $key => $value) {
                $rate = $value['new_rate'];
            }
        }
        return $rate;
    }



    public function add_item_price($data)
    {
        $city = $data["city"];
        $area = $data['area'];
        if ($city == 'All' && $area = 'All') {
            $sql_pri = "SELECT city_id FROM  tbl_city ";
            $cityd = mysql_query($sql_pri);
            while ($cityn = mysql_fetch_array($cityd)) {
                $city_id = $cityn['city_id'];
                $sql_pr = "SELECT area_id FROM  tbl_area where city_id='$city_id' ";
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
