<?php



class Mod_customerledger extends CI_Model

{



    function __construct()

    {



        parent::__construct();

        error_reporting(0);
    }



    public function get_issue_goods_items($id, $itemid, $sale_point_id)

    {

        $this->db->where(array('trans_id' => $id, 'sale_point_id' => $sale_point_id));

        $this->db->where(array('itemid' => $itemid));

        return $this->db->get('tbl_issue_goods_detail')->row();
    }

    public function get_return_items($id, $itemid)

    {

        $this->db->where(array('irnos' => $id));

        $this->db->where(array('itemid' => $itemid));

        return $this->db->get('tbl_issue_return_detail')->row();
    }



    public function get_issue_return_items($id, $itemid)

    {

        $this->db->where(array('irnos' => $id, 'itemid' => $itemid));

        return $this->db->get('tbl_issue_return_detail')->row();
    }





    public function get_report($data)

    {



        $fdate = $data['from_date'];

        $tdate = $data['to_date'];

        $scode = $data['scode'];

        if ($scode != '0') {

            $where_scode = "and tbltrans_detail.scode='$scode'  ";
        } else {

            $where_scode = "";
        }



        $sort = $data['sort'];

        $query1 = "";

        $query2 = "";

        $query3 = "";

        $filter = $data['filter'];

        $filter1 = $data['filter1'];

        $filter2 = $data['filter2'];



        if (($data['filter'] == "party") and ($data['filter1'] == "") and ($data['filter2'] == "")) {

            $acode = $data['acode'];

            $aname = $data['aname'];



            $query1 = "SELECT opngbl,optype,aname FROM `tblacode` WHERE acode ='$acode' ";



            $query2 = "SELECT sum(damount) as op_damount ,sum(camount) as op_camount FROM `tbltrans_detail` WHERE acode ='$acode'  and vdate < '$fdate' $where_scode";



            $query3 = "SELECT * FROM `tbltrans_detail` WHERE acode ='$acode' and vdate between '$fdate' AND '$tdate' $where_scode order by vdate asc ";
        }







        $total_opngbl_new = 0;





        $fdate;

        $tdate;



        //Opening Balance

        $opngbl = '';

        $optype = '';

        $result = $this->db->query($query1);

        $line = $result->row_array();



        $optype = $line['optype'];



        $aname = $line['aname'];

        $opngbl = $line['opngbl'];



        $result = $this->db->query($query2);



        foreach ($result->result_array() as $key => $line) {

            $op_damount = $line['op_damount'];

            $op_camount = $line['op_camount'];



            // echo "<pre>";

            // print_r($line);

            // echo "<br>";

        }





        $total_opngbl = '';

        if ($optype == 'Credit') {

            $opngbl = -1 * ($opngbl);
        }



        $total_opngbl =    ($op_damount - $op_camount) + ($opngbl);





        if (($data['filter1'] == "amount") or ($data['filter2'] == "nar")) {

            /// not display opening set 0

            $total_opngbl_new = $total_opngbl = 0;
        } else {

            $total_opngbl_new = $total_opngbl;
        }



        $total_bal = $total_damount = $total_camount = $i = 0;

        $result = $this->db->query($query3);



        //pm($this->db->last_query());



        if ($result->num_rows() > 0) {

            foreach ($result->result_array() as $key => $line) {



                $vno = $line['vno'];

                $vdate = $line['vdate'];

                $vtype = $line['vtype'];

                $remarks = $line['remarks'];

                $damount = $line['damount'];



                $total_damount =  $damount + $total_damount;

                // if($filter == "party"){

                if ($damount != 0) {

                    $total_opngbl = $damount + $total_opngbl;
                }

                //}

                $camount = $line['camount'];

                $total_camount =  $camount + $total_camount;

                // if($filter == "party"){

                if ($camount != 0) {

                    $total_opngbl = $camount - ($total_opngbl);

                    $total_opngbl = -1 * ($total_opngbl);
                }

                //}

                $i++;

                $car_code = $line['car_code'];



                $vdate;

                $remarks;

                round($damount);

                round($camount);

                //if($filter == "party"){ 

                round($total_opngbl);



                //}



                $g_total = 0;





                if ($total_opngbl_new > 0) {

                    $g_total = ($total_damount - $total_camount) + $total_opngbl_new;
                } else {

                    $g_total = ($total_damount - $total_camount) - $total_opngbl_new;
                }

                round($total_opngbl);

                $datas[] = array(

                    "accountcode" => $acode,

                    "accountname" => $aname,

                    "fromdate" => $fdate,

                    "todate" => $tdate,

                    'openingbalbal' => ($total_opngbl_new > 0 ? $total_opngbl_new . ' Dr' : $total_opngbl_new . 'Cr'),

                    "voucherno" => $vno,

                    "voucherdate" => $vdate,

                    "description" => $remarks,

                    "debit" => $damount,

                    "credit" => $camount,

                    "balance" => round($total_opngbl),

                    "tdebit" => round($total_damount),

                    "tcredit" => round($total_camount),

                    "tbalance" => round($total_opngbl),

                );
            }
        } else {





            $datas[] = array(

                "accountcode" => $acode,

                "accountname" => $aname,

                "fromdate" => $fdate,

                "todate" => $tdate,

                'openingbalbal' => ($total_opngbl_new > 0 ? $total_opngbl_new . ' Dr' : $total_opngbl_new . ' Cr'),



                "tdebit" => round($total_damount),

                "tcredit" => round($total_camount),

                "tbalance" => round($total_opngbl)

            );
        }



        // pm($datas);

        return $datas;
    }

    public function get_report_last($data)

    {







        // $daterange= $data['daterange'];

        // $sr=explode("/",($daterange));

        // $fdate=trim($sr[0]);

        // $tdate=trim($sr[1]);







        $fdate = $data['from_date'];

        $tdate = $data['to_date'];



        $sort = $data['sort'];

        $query1 = "";

        $query2 = "";

        $query3 = "";

        $filter = $data['filter'];

        $filter1 = $data['filter1'];

        $filter2 = $data['filter2'];







        if (($data['filter'] == "party") and ($data['filter1'] == "amount") and ($data['filter2'] == "nar")) {

            $acode = $data['acode'];

            $aname = $data['aname'];

            $amount = $data['amo'];

            $nar = $data['narr'];







            $query1 = "SELECT opngbl,optype,aname FROM `tblacode` WHERE acode ='$acode'";

            $query2 = "SELECT sum(damount) as op_damount ,sum(camount) as op_camount FROM `tbltrans_detail` WHERE acode ='$acode' and vdate < '$fdate'";

            if ($sort == "date") {

                // $query3 = "SELECT * FROM `tbltrans_detail` WHERE acode ='$acode' and damount ='$amount'  OR camount ='$amount' and   remarks LIKE '%$nar%' and

                //          vdate between '$fdate' AND '$tdate' order by vdate desc ";

            } else if ($sort == "vno") {

                $query3 = "SELECT * FROM `tbltrans_detail` WHERE acode ='$acode' and damount ='$amount'  OR camount ='$amount' and remarks LIKE '%$nar%' and

              vdate between '$fdate' AND '$tdate' order by vdate,vno  desc ";
            } else if ($sort == 'debit') {

                $query3 = "SELECT * FROM `tbltrans_detail` WHERE acode ='$acode' and damount ='$amount'  OR camount ='$amount' and remarks LIKE '%$nar%' and

              vdate between '$fdate' AND '$tdate' order by vdate,vno  desc ";
            } else if ($sort == 'credit') {

                $query3 = "SELECT * FROM `tbltrans_detail` WHERE acode ='$acode' and damount ='$amount'  OR camount ='$amount' and remarks LIKE '%$nar%' and

              vdate between '$fdate' AND '$tdate' order by vdate,vno  desc ";
            }
        }



        if (($data['filter'] == "party") and ($data['filter1'] == "amount") and ($data['filter2'] == "")) {

            $acode = $data['acode'];

            $aname = $data['aname'];

            $amount = $data['amo'];

            // $nar = $data['narr'];



            $query1 = "SELECT opngbl,optype,aname FROM `tblacode` WHERE acode ='$acode' ";

            $query2 = "SELECT sum(damount) as op_damount ,sum(camount) as op_camount FROM `tbltrans_detail`

            WHERE acode ='$acode' and vdate < '$fdate'";

            $query3 = "SELECT * FROM `tbltrans_detail` WHERE acode ='$acode' and (damount ='$amount'  OR camount ='$amount') and

            vdate between '$fdate' AND '$tdate' order by vdate,vno  desc ";
        }



        if (($data['filter'] == "party") and ($data['filter1'] == "") and ($data['filter2'] == "")) {

            // echo "string";exit;





            $acode = $data['acode'];

            $aname = $data['aname'];



            $query1 = "SELECT opngbl,optype,aname FROM `tblacode` WHERE acode ='$acode' ";



            $query2 = "SELECT sum(damount) as op_damount ,sum(camount) as op_camount FROM `tbltrans_detail` WHERE acode ='$acode' and vdate < '$fdate'";



            if ($sort == 'date') {

                $query3 = "SELECT * FROM `tbltrans_detail` WHERE acode ='$acode' and vdate between '$fdate' AND '$tdate' order by vdate,vno   desc ";
            } else if ($sort == "vno") {

                $query3 = "SELECT * FROM `tbltrans_detail` WHERE acode ='$acode' and vdate between '$fdate' AND '$tdate' order by vdate,vno  desc ";
            } else if ($sort == 'debit') {

                $query3 = "SELECT * FROM `tbltrans_detail` WHERE acode ='$acode' and vdate between '$fdate' AND '$tdate' order by vdate,vno  desc ";
            } else if ($sort == 'credit') {

                $query3 = "SELECT * FROM `tbltrans_detail` WHERE acode ='$acode' and vdate between '$fdate' AND '$tdate' order by vdate,vno   desc ";
            }



            //vno in (  SELECT vno FROM `tbltrans_master` WHERE created_date BETWEEN '$fdate' AND '$tdate' ) ";

        }



        if (($data['filter'] == "") and ($data['filter1'] == "amount") and ($data['filter2'] == "")) {

            $acode = $data['acode'];

            $aname = $data['aname'];

            $amount = $data['amo'];

            // $nar = $data['narr'];



            $query1 = "SELECT opngbl,optype,aname FROM `tblacode` WHERE acode ='$acode' ";

            $query2 = "SELECT sum(damount) as op_damount ,sum(camount) as op_camount FROM `tbltrans_detail`

            WHERE acode ='$acode' and vdate < '$fdate'";

            //$query3 = "SELECT * FROM `tbltrans_detail` WHERE (damount ='$amount'  OR camount ='$amount') and

            //      vdate between '$fdate' AND '$tdate' ";



            if ($sort == "date") {

                $query3 = "SELECT * FROM `tbltrans_detail` WHERE (damount ='$amount'  OR camount ='$amount') and vdate between '$fdate' AND'$tdate' order by vdate,vno  desc ";
            } else if ($sort == "vno") {

                $query3 = "SELECT * FROM `tbltrans_detail` WHERE (damount ='$amount'  OR camount ='$amount') and vdate between '$fdate' AND'$tdate' order by vdate,vno  desc ";
            } else if ($sort == 'debit') {

                $query3 = "SELECT * FROM `tbltrans_detail` WHERE (damount ='$amount'  OR camount ='$amount') and vdate between '$fdate'AND '$tdate' order by vdate,vno  desc ";
            } else if ($sort == 'credit') {

                $query3 = "SELECT * FROM `tbltrans_detail` WHERE (damount ='$amount'  OR camount ='$amount') and vdate between '$fdate'AND '$tdate' order by vdate,vno  desc ";
            }
        }

        if (($data['filter'] == "") and ($data['filter1'] == "") and ($data['filter2'] == "nar")) {

            $acode = $data['acode'];

            $aname = $data['aname'];

            $amount = $data['amo'];

            $nar = $data['narr'];



            $query1 = "SELECT opngbl,optype,aname FROM `tblacode` WHERE acode ='$acode' ";

            $query2 = "SELECT sum(damount) as op_damount ,sum(camount) as op_camount FROM `tbltrans_detail` WHERE acode ='$acode' and vdate < '$fdate'";



            // $query3 = "SELECT * FROM `tbltrans_detail` WHERE remarks LIKE '%$nar%' and

            //s    vdate between '$fdate' AND '$tdate' ";



            if ($sort == "date") {

                $query3 = "SELECT * FROM `tbltrans_detail` WHERE remarks LIKE '%$nar%' and vdate between '$fdate' AND '$tdate' order by vdate,vno   desc ";
            } else if ($sort == "vno") {

                $query3 = "SELECT * FROM `tbltrans_detail` WHERE remarks LIKE '%$nar%' and vdate between '$fdate' AND '$tdate' order by vdate,vno  desc ";
            } else if ($sort == 'debit') {

                $query3 = "SELECT * FROM `tbltrans_detail` WHERE remarks LIKE '%$nar%' and vdate between '$fdate' AND '$tdate' order by vdate,vno  desc ";
            } else if ($sort == 'credit') {

                $query3 = "SELECT * FROM `tbltrans_detail` WHERE remarks LIKE '%$nar%' and vdate between '$fdate' AND '$tdate' order by vdate,vno  desc ";
            }
        }





        if (($data['filter'] == "party") and ($data['filter1'] == "") and ($data['filter2'] == "nar")) {

            $acode = $data['acode'];

            $aname = $data['aname'];

            $amount = $data['amo'];

            $nar = $data['narr'];



            $query1 = "SELECT opngbl,optype,aname FROM `tblacode` WHERE acode ='$acode' ";

            $query2 = "SELECT sum(damount) as op_damount ,sum(camount) as op_camount FROM `tbltrans_detail` WHERE acode ='$acode' and vdate < '$fdate'";



            //  $query3 = "SELECT * FROM `tbltrans_detail` WHERE remarks LIKE '%$nar%' and

            //         vdate between '$fdate' AND '$tdate' and  acode ='$acode'  ";



            if ($sort == "date") {

                $query3 = "SELECT * FROM `tbltrans_detail` WHERE remarks LIKE '%$nar%' and vdate between '$fdate' AND '$tdate' and acode ='$acode' order by vdate,vno  desc  ";
            } else if ($sort == "vno") {

                $query3 = "SELECT * FROM `tbltrans_detail` WHERE remarks LIKE '%$nar%' and vdate between '$fdate' AND '$tdate' and acode ='$acode' order by vdate,vno  desc ";
            } else if ($sort == 'debit') {

                $query3 = "SELECT * FROM `tbltrans_detail` WHERE remarks LIKE '%$nar%' and vdate between '$fdate' AND '$tdate' and acode ='$acode' order by vdate,vno  desc  ";
            } else if ($sort == 'credit') {

                $query3 = "SELECT * FROM `tbltrans_detail` WHERE remarks LIKE '%$nar%' and vdate between '$fdate' AND '$tdate' and acode ='$acode' order by vdate,vno  desc ";
            }
        }





        if (($data['filter'] == "") and ($data['filter1'] == "amount") and ($data['filter2'] == "nar")) {

            $acode = $data['acode'];

            $aname = $data['aname'];

            $amount = $data['amo'];

            $nar = $data['narr'];



            $query1 = "SELECT opngbl,optype,aname FROM `tblacode` WHERE acode ='$acode' ";

            $query2 = "SELECT sum(damount) as op_damount ,sum(camount) as op_camount FROM `tbltrans_detail` WHERE acode ='$acode' and vdate < '$fdate'";

            $query3 = "SELECT * FROM `tbltrans_detail` WHERE acode ='$acode' and damount ='$amount'  OR camount ='$amount' and remarks LIKE '%$nar%' and vdate between '$fdate' AND '$tdate' order by vdate,vno  desc ";



            if ($sort == "date") {

                $query3 = "SELECT * FROM `tbltrans_detail` WHERE acode ='$acode' and damount ='$amount'  OR camount ='$amount' and remarks LIKE '%$nar%' and vdate between '$fdate' AND '$tdate'  order by vdate,vno   desc ";
            } else if ($sort == "vno") {

                $query3 = "SELECT * FROM `tbltrans_detail` WHERE acode ='$acode' and damount ='$amount'  OR camount ='$amount' and remarks LIKE '%$nar%' and

            vdate between '$fdate' AND '$tdate'  order by vdate,vno  desc ";
            } else if ($sort == 'debit') {

                $query3 = "SELECT * FROM `tbltrans_detail` WHERE acode ='$acode' and damount ='$amount'  OR camount ='$amount' and remarks LIKE '%$nar%' and vdate between '$fdate' AND '$tdate'  order by vdate,vno  desc ";
            } else if ($sort == 'credit') {

                $query3 = "SELECT * FROM `tbltrans_detail` WHERE acode ='$acode' and damount ='$amount'  OR camount ='$amount' and remarks LIKE '%$nar%' and vdate between '$fdate' AND '$tdate' order by vdate,vno  desc ";
            }
        }





        // print $query1;

        // print $query3;

        // print $query2;

        // exit;



        $total_opngbl_new = 0;



        if ($filter == "party") {

            $acode;

            echo $aname;
        }

        $fdate;

        $tdate;



        //Opening Balance

        $opngbl = '';

        $optype = '';

        $result = $this->db->query($query1);

        $line = $result->row_array();



        $optype = $line['optype'];



        $aname = $line['aname'];

        $opngbl = $line['opngbl'];



        $result = $this->db->query($query2);



        foreach ($result->result_array() as $key => $line) {

            $op_damount = $line['op_damount'];

            $op_camount = $line['op_camount'];



            // echo "<pre>";

            // print_r($line);

            // echo "<br>";

        }





        $total_opngbl = '';

        if ($optype == 'Credit') {

            $opngbl = -1 * ($opngbl);
        }



        $total_opngbl =    ($op_damount - $op_camount) + ($opngbl);





        if (($data['filter1'] == "amount") or ($data['filter2'] == "nar")) {

            /// not display opening set 0

            $total_opngbl_new = $total_opngbl = 0;
        } else {

            $total_opngbl_new = $total_opngbl;
        }



        $total_bal = $total_damount = $total_camount = $i = 0;

        $result = $this->db->query($query3);



        //pm($this->db->last_query());



        if ($result->num_rows() > 0) {

            foreach ($result->result_array() as $key => $line) {



                $vno = $line['vno'];

                $vdate = $line['vdate'];

                $vtype = $line['vtype'];

                $remarks = $line['remarks'];

                $damount = $line['damount'];



                $total_damount =  $damount + $total_damount;

                // if($filter == "party"){

                if ($damount != 0) {

                    $total_opngbl = $damount + $total_opngbl;
                }

                //}

                $camount = $line['camount'];

                $total_camount =  $camount + $total_camount;

                // if($filter == "party"){

                if ($camount != 0) {

                    $total_opngbl = $camount - ($total_opngbl);

                    $total_opngbl = -1 * ($total_opngbl);
                }

                //}

                $i++;



                // $string = htmlentities($remarks, null, 'utf-8');

                // $remarks = str_replace(" ", "&nbsp;", $string);

                // $remarks = html_entity_decode($remarks);

                $car_code = $line['car_code'];

                if ($line['vtype'] == 'SV' ||  $line['svtype'] == 'FS') {

                    $vno;
                } else if ($line['vtype'] == 'CP' ||  $line['vtype'] == 'JV' ||  $line['vtype'] == 'CR' ||  $line['vtype'] == 'BP' ||  $line['vtype'] == 'BR'  ||  $line['vtype'] == 'RC') {

                    $vno;
                } else if ($line['vtype'] == 'SA') {

                    $vno;
                } else if ($line['vtype'] == 'PV') {

                    $vno;
                } else {

                    $vno;
                }

                $vdate;

                $remarks;

                round($damount);

                round($camount);

                //if($filter == "party"){ 

                round($total_opngbl);



                //}



                //Total 

                $g_total = 0;

                // echo $total_opngbl_new;

                // echo "<br>";

                // exit;\



                // echo $total_opngbl_new;

                // exit;







                if ($total_opngbl_new > 0) {

                    $g_total = ($total_damount - $total_camount) + $total_opngbl_new;
                } else {

                    $g_total = ($total_damount - $total_camount) - $total_opngbl_new;
                }

                round($total_opngbl);

                // echo "string";

                // echo $total_opngbl_new;

                // exit;







                $datas[] = array(

                    "accountcode" => $acode,

                    "accountname" => $aname,

                    "fromdate" => $fdate,

                    "todate" => $tdate,

                    'openingbalbal' => ($total_opngbl_new > 0 ? $total_opngbl_new . ' Dr' : $total_opngbl_new . 'Cr'),

                    "voucherno" => $vno,

                    "voucherdate" => $vdate,

                    "description" => $remarks,

                    "debit" => $damount,

                    "credit" => $camount,

                    "balance" => round($total_opngbl),

                    "tdebit" => round($total_damount),

                    "tcredit" => round($total_camount),

                    "tbalance" => round($total_opngbl),

                );
            }
        } else {

            // echo "string";

            // echo $total_opngbl_new;



            $datas[] = array(

                "accountcode" => $acode,

                "accountname" => $aname,

                "fromdate" => $fdate,

                "todate" => $tdate,

                'openingbalbal' => ($total_opngbl_new > 0 ? $total_opngbl_new . ' Dr' : $total_opngbl_new . ' Cr'),



                "tdebit" => round($total_damount),

                "tcredit" => round($total_camount),

                "tbalance" => round($total_opngbl)

            );
        }



        // pm($datas);

        return $datas;
    }

    public function get_report_small_dcustomer($data)

    {

        $tdate = $data['to_date'];

        $d_customer = $data['direct_customer'];
        $transid = $data['trans_id'];
        $vno = $data['location'] . '-Sale-' . $transid;
        $Previous_Balance = $this->db->query("SELECT sum(damount-camount) as Previous_Balance FROM tbltrans_detail WHERE direct_customer= $d_customer and vdate<='$tdate' and vno != '$vno'")->row_array()['Previous_Balance'];




        $datas[] = array( 
            "tbalance" => $Previous_Balance 
        );



        //echo $Previous_Balance;exit;

        return $datas;
    }





    public function get_report_small($data)

    {







        // $daterange= $data['daterange'];

        // $sr=explode("/",($daterange));

        // $fdate=trim($sr[0]);

        // $tdate=trim($sr[1]);





        //        pm($data);





        $id = $data['id'];

        $lenght = strlen($id);

        $id_sale = $id . '-Sale';

        $fdate = $data['from_date'];

        $tdate = $data['to_date'];



        $sort = $data['sort'];

        $query1 = "";

        $query2 = "";

        $query3 = "";

        $filter = $data['filter'];

        $filter1 = $data['filter1'];

        $filter2 = $data['filter2'];



        $acode = $data['acode'];







        $query1 = "SELECT opngbl,optype,aname ,acode FROM `tblacode` WHERE  acode='$acode'";



        $result_main = $this->db->query($query1);

        $result_main->result_array();





        foreach ($result_main->result_array() as $key => $line) {



            $opngbl = '';

            $optype = '';



            $opngbl = $line['opngbl'];

            $optype = $line['optype'];

            $aname = $line['aname'];

            $acode = $line['acode'];



            // $query2="SELECT sum(damount) as op_damount ,sum(camount) as op_camount FROM `tbltrans_detail` WHERE acode ='$acode' and vdate<='$tdate' and left(vno,3) != '$id_sale'"





            $query2 = "SELECT sum(damount) as op_damount ,sum(camount) as op_camount FROM `tbltrans_detail` WHERE acode ='$acode' and vdate<='$tdate' and left(vno,'$lenght') != '$id'  ";





            $result = $this->db->query($query2);

            foreach ($result->result_array() as $key => $line) {

                $op_damount = $line['op_damount'];

                $op_camount = $line['op_camount'];
            }

            $total_opngbl = '';

            if ($optype == 'Credit') {

                $opngbl = -1 * ($opngbl);
            }

            $total_opngbl =    ($op_damount - $op_camount) + ($opngbl);





            round($total_opngbl);









            ////////////////////////////// PURCHASE Filled /////////////////////////////////



            $id_sale = $id . '-Purchase';

            $query = "SELECT COALESCE(SUM(damount),0) AS total_amount

                FROM tbltrans_detail where vno='$id_sale'";

            $result = $this->db->query($query);

            $recv_from_vendor_f_row = $result->row_array();

            $final = $recv_from_vendor_f_row['total_amount'];

            $id_recv = $id . '-Purchase Payment';

            $query = "SELECT COALESCE(SUM(damount),0) AS total_rec

                FROM tbltrans_detail where vno='$id_recv'";

            $result = $this->db->query($query);

            $recv_from_vendor_f_row = $result->row_array();

            $total_rec = $recv_from_vendor_f_row['total_rec'];



            // print $total_rec.'<br>';

            // print $final.'<br>';

            // print $total_opngbl.'<br>';

            // exit;



            $now_bal = $total_rec - $final + $total_opngbl;



            $datas[] = array(

                "accountcode" => $acode,

                "accountname" => $aname,



                "tbalance" => $now_bal,



            );
        }



        return $datas;

        //pm($datas);

    }
}
