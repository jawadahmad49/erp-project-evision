<?php

class Mod_vendorledger extends CI_Model
{

    function __construct()
    {

        parent::__construct();
        error_reporting(0);
    }

    public function get_bankreport($bank_id)
    {
        $date = date('Y-m-d');


        $this->db->select('trcode.damount as dd ,trcode.camount as cc,tcode.aname,tcode.acode,trcode.vno,trcode.vdate,trcode.remarks');
        $this->db->join('tblacode as tcode', 'trcode.acode = tcode.acode');

        $this->db->where('trcode.acode', $bank_id);
        $this->db->where('trcode.vdate<=', $date);
        $this->db->from('tbltrans_detail as trcode');

        $query = $this->db->get();



        return $query->result_array();
    }

    public function get_report_dollar($data, $id)
    {
        // $daterange= $data['daterange'];
        // $sr=explode("/",($daterange));
        // $fdate=trim($sr[0]);
        // $tdate=trim($sr[1]);

        //echo "<pre>"; var_dump($this->input->post()); exit();



        $fdate = $data['from_date'];
        $tdate = $data['to_date'];

        if ($id != '') {
            $fdate = '2000-01-01';
            $tdate = date('Y-m-d');
        }
        //error_reporting(E_ALL);





        $sort = $_POST['sort'];
        $query1 = "";
        $query2 = "";
        $query3 = "";
        $filter = $_POST['filter'];
        $filter1 = $_POST['filter1'];
        $filter2 = $_POST['filter2'];


        if (($_POST['filter'] == "party") and ($_POST['filter1'] == "amount") and ($_POST['filter2'] == "nar")) {
            $acode = $_POST['acode'];
            $aname = $_POST['aname'];
            $amount = $_POST['amo'];
            $nar = $_POST['narr'];

            $query1 = "SELECT opngbl,optype,aname FROM `tblacode` WHERE acode ='$acode'";
            $query2 = "SELECT sum(damount) as op_damount ,sum(camount) as op_camount FROM `tbltrans_detail` WHERE acode ='$acode' and vdate < '$fdate'";
            if ($sort == "date") {
                // $query3 = "SELECT * FROM `tbltrans_detail` WHERE acode ='$acode' and damount ='$amount'  OR camount ='$amount' and   remarks LIKE '%$nar%' and
                //          vdate between '$fdate' AND '$tdate' order by vdate desc ";
            } else if ($sort == "vno") {
                $query3 = "SELECT * FROM `tbltrans_detail` WHERE acode ='$acode' and damount ='$amount'  OR camount ='$amount' and remarks LIKE '%$nar%' and
              vdate between '$fdate' AND '$tdate'  order by vdate asc ";
            } else if ($sort == 'debit') {
                $query3 = "SELECT * FROM `tbltrans_detail` WHERE acode ='$acode' and damount ='$amount'  OR camount ='$amount' and remarks LIKE '%$nar%' and
              vdate between '$fdate' AND '$tdate'  order by vdate asc ";
            } else if ($sort == 'credit') {
                $query3 = "SELECT * FROM `tbltrans_detail` WHERE acode ='$acode' and damount ='$amount'  OR camount ='$amount' and remarks LIKE '%$nar%' and
              vdate between '$fdate' AND '$tdate'  order by vdate asc ";
            }
        }

        if (($_POST['filter'] == "party") and ($_POST['filter1'] == "amount") and ($_POST['filter2'] == "")) {
            $acode = $_POST['acode'];
            $aname = $_POST['aname'];
            $amount = $_POST['amo'];
            // $nar = $_POST['narr'];

            $query1 = "SELECT opngbl,optype,aname FROM `tblacode` WHERE acode ='$acode' ";
            $query2 = "SELECT sum(damount) as op_damount ,sum(camount) as op_camount FROM `tbltrans_detail`
            WHERE acode ='$acode' and vdate < '$fdate'";
            $query3 = "SELECT * FROM `tbltrans_detail` WHERE acode ='$acode' and (damount ='$amount'  OR camount ='$amount') and
            vdate between '$fdate' AND '$tdate'  order by vdate asc ";
        }

        if (($_POST['filter'] == "party") and ($_POST['filter1'] == "") and ($_POST['filter2'] == "")) {

            $acode = $_POST['acode'];
            $aname = $_POST['aname'];

            $query1 = "SELECT opngbl,optype,aname,opngbl_dollars FROM `tblacode` WHERE acode ='$acode' ";

            $query2 = "SELECT sum(damount_dollar) as op_damount ,sum(camount_dollar) as op_camount FROM `tbltrans_detail` WHERE acode ='$acode' and vdate < '$fdate'";

            if ($sort == 'date') {
                $query3 = "SELECT * FROM `tbltrans_detail` WHERE acode ='$acode' and vdate between '$fdate' AND '$tdate' order by vdate asc ";
            } else if ($sort == "vno") {
                $query3 = "SELECT * FROM `tbltrans_detail` WHERE acode ='$acode' and vdate between '$fdate' AND '$tdate' order by vdate asc";
            } else if ($sort == 'debit') {
                $query3 = "SELECT * FROM `tbltrans_detail` WHERE acode ='$acode' and vdate between '$fdate' AND '$tdate'  order by vdate asc ";
            } else if ($sort == 'credit') {
                $query3 = "SELECT * FROM `tbltrans_detail` WHERE acode ='$acode' and vdate between '$fdate' AND '$tdate'  order by vdate asc  ";
            }

            //vno in (  SELECT vno FROM `tbltrans_master` WHERE created_date BETWEEN '$fdate' AND '$tdate' ) ";
        }

        // if(($_POST['filter']=="party")and ($_POST['filter1']=="") and ($_POST['filter2']==""))
        // {




        if (($_POST['filter'] == "") and ($_POST['filter1'] == "amount") and ($_POST['filter2'] == "")) {
            $acode = $_POST['acode'];
            $aname = $_POST['aname'];
            $amount = $_POST['amo'];
            // $nar = $_POST['narr'];

            $query1 = "SELECT opngbl,optype,aname FROM `tblacode` WHERE acode ='$acode' ";
            $query2 = "SELECT sum(damount) as op_damount ,sum(camount) as op_camount FROM `tbltrans_detail`
            WHERE acode ='$acode' and vdate < '$fdate'";
            //$query3 = "SELECT * FROM `tbltrans_detail` WHERE (damount ='$amount'  OR camount ='$amount') and
            //      vdate between '$fdate' AND '$tdate' ";

            if ($sort == "date") {
                $query3 = "SELECT * FROM `tbltrans_detail` WHERE (damount ='$amount'  OR camount ='$amount') and vdate between '$fdate' AND'$tdate'  order by vdate asc ";
            } else if ($sort == "vno") {
                $query3 = "SELECT * FROM `tbltrans_detail` WHERE (damount ='$amount'  OR camount ='$amount') and vdate between '$fdate' AND'$tdate'  order by vdate asc ";
            } else if ($sort == 'debit') {
                $query3 = "SELECT * FROM `tbltrans_detail` WHERE (damount ='$amount'  OR camount ='$amount') and vdate between '$fdate'AND '$tdate'  order by vdate asc ";
            } else if ($sort == 'credit') {
                $query3 = "SELECT * FROM `tbltrans_detail` WHERE (damount ='$amount'  OR camount ='$amount') and vdate between '$fdate'AND '$tdate'  order by vdate asc ";
            }
        }
        if (($_POST['filter'] == "") and ($_POST['filter1'] == "") and ($_POST['filter2'] == "nar")) {
            $acode = $_POST['acode'];
            $aname = $_POST['aname'];
            $amount = $_POST['amo'];
            $nar = $_POST['narr'];

            $query1 = "SELECT opngbl,optype,aname FROM `tblacode` WHERE acode ='$acode' ";
            $query2 = "SELECT sum(damount) as op_damount ,sum(camount) as op_camount FROM `tbltrans_detail` WHERE acode ='$acode' and vdate < '$fdate'";

            // $query3 = "SELECT * FROM `tbltrans_detail` WHERE remarks LIKE '%$nar%' and
            //s    vdate between '$fdate' AND '$tdate' ";

            if ($sort == "date") {
                $query3 = "SELECT * FROM `tbltrans_detail` WHERE remarks LIKE '%$nar%' and vdate between '$fdate' AND '$tdate'  order by vdate asc  ";
            } else if ($sort == "vno") {
                $query3 = "SELECT * FROM `tbltrans_detail` WHERE remarks LIKE '%$nar%' and vdate between '$fdate' AND '$tdate'  order by vdate asc ";
            } else if ($sort == 'debit') {
                $query3 = "SELECT * FROM `tbltrans_detail` WHERE remarks LIKE '%$nar%' and vdate between '$fdate' AND '$tdate'  order by vdate asc ";
            } else if ($sort == 'credit') {
                $query3 = "SELECT * FROM `tbltrans_detail` WHERE remarks LIKE '%$nar%' and vdate between '$fdate' AND '$tdate'  order by vdate asc ";
            }
        }


        if (($_POST['filter'] == "party") and ($_POST['filter1'] == "") and ($_POST['filter2'] == "nar")) {
            $acode = $_POST['acode'];
            $aname = $_POST['aname'];
            $amount = $_POST['amo'];
            $nar = $_POST['narr'];

            $query1 = "SELECT opngbl,optype,aname FROM `tblacode` WHERE acode ='$acode' ";
            $query2 = "SELECT sum(damount) as op_damount ,sum(camount) as op_camount FROM `tbltrans_detail` WHERE acode ='$acode' and vdate < '$fdate'";

            //  $query3 = "SELECT * FROM `tbltrans_detail` WHERE remarks LIKE '%$nar%' and
            //         vdate between '$fdate' AND '$tdate' and  acode ='$acode'  ";

            if ($sort == "date") {
                $query3 = "SELECT * FROM `tbltrans_detail` WHERE remarks LIKE '%$nar%' and vdate between '$fdate' AND '$tdate' and acode ='$acode'  order by vdate asc  ";
            } else if ($sort == "vno") {
                $query3 = "SELECT * FROM `tbltrans_detail` WHERE remarks LIKE '%$nar%' and vdate between '$fdate' AND '$tdate' and acode ='$acode'  order by vdate asc ";
            } else if ($sort == 'debit') {
                $query3 = "SELECT * FROM `tbltrans_detail` WHERE remarks LIKE '%$nar%' and vdate between '$fdate' AND '$tdate' and acode ='$acode'  order by vdate asc) ";
            } else if ($sort == 'credit') {
                $query3 = "SELECT * FROM `tbltrans_detail` WHERE remarks LIKE '%$nar%' and vdate between '$fdate' AND '$tdate' and acode ='$acode'  order by vdate asc ";
            }
        }


        if (($_POST['filter'] == "") and ($_POST['filter1'] == "amount") and ($_POST['filter2'] == "nar")) {
            $acode = $_POST['acode'];
            $aname = $_POST['aname'];
            $amount = $_POST['amo'];
            $nar = $_POST['narr'];

            $query1 = "SELECT opngbl,optype,aname FROM `tblacode` WHERE acode ='$acode' ";
            $query2 = "SELECT sum(damount) as op_damount ,sum(camount) as op_camount FROM `tbltrans_detail` WHERE acode ='$acode' and vdate < '$fdate'";
            $query3 = "SELECT * FROM `tbltrans_detail` WHERE acode ='$acode' and damount ='$amount'  OR camount ='$amount' and remarks LIKE '%$nar%' and vdate between '$fdate' AND '$tdate'  order by vdate asc ";

            if ($sort == "date") {
                $query3 = "SELECT * FROM `tbltrans_detail` WHERE acode ='$acode' and damount ='$amount'  OR camount ='$amount' and remarks LIKE '%$nar%' and vdate between '$fdate' AND '$tdate'   order by vdate asc  ";
            } else if ($sort == "vno") {
                $query3 = "SELECT * FROM `tbltrans_detail` WHERE acode ='$acode' and damount ='$amount'  OR camount ='$amount' and remarks LIKE '%$nar%' and
            vdate between '$fdate' AND '$tdate'   order by vdate asc ";
            } else if ($sort == 'debit') {
                $query3 = "SELECT * FROM `tbltrans_detail` WHERE acode ='$acode' and damount ='$amount'  OR camount ='$amount' and remarks LIKE '%$nar%' and vdate between '$fdate' AND '$tdate'   order by vdate asc ";
            } else if ($sort == 'credit') {
                $query3 = "SELECT * FROM `tbltrans_detail` WHERE acode ='$acode' and damount ='$amount'  OR camount ='$amount' and remarks LIKE '%$nar%' and vdate between '$fdate' AND '$tdate'  order by vdate asc ";
            }
        }

        if ($id != '') {

            $acode = $id;
            $sort = 'date';

            $query1 = "SELECT opngbl,optype,aname FROM `tblacode` WHERE acode ='$acode' ";


            $query2 = "SELECT sum(damount) as op_damount ,sum(camount) as op_camount FROM `tbltrans_detail` WHERE acode ='$acode' and vdate < '$fdate'";

            $query3 = "SELECT * FROM `tbltrans_detail` WHERE acode ='$acode' and vdate between '$fdate' AND '$tdate'  order by vdate asc  ";
        }

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

        $opngbl = $line['opngbl_dollars'];
        $optype = $line['optype'];
        $aname = $line['aname'];

        $result = $this->db->query($query2);
        foreach ($result->result_array() as $key => $line) {
            $op_damount = $line['op_damount'];
            $op_camount = $line['op_camount'];
        }
        $total_opngbl = '';
        if ($optype == 'Credit') {
            $opngbl = -1 * ($opngbl);
        }
        $total_opngbl =    ($op_damount    - $op_camount) + ($opngbl);

        if (($_POST['filter1'] == "amount") or ($_POST['filter2'] == "nar")) {
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
                $damount = $line['damount_dollar'];

                $total_damount =  $damount + $total_damount;
                // if($filter == "party"){
                if ($damount != 0) {
                    $total_opngbl = $damount + $total_opngbl;
                }
                //}
                $camount = $line['camount_dollar'];
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
                    'openingbalbal' => ($total_opngbl_new > 0 ? $total_opngbl_new . ' Dr' : $total_opngbl_new . ' Cr'),
                    "voucherno" => $vno,
                    "voucherdate" => $vdate,
                    "description" => $remarks,
                    "vtype" => $vtype,

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
                "vtype" => $vtype,
                'openingbalbal' => ($total_opngbl_new > 0 ? $total_opngbl_new . ' Dr' : $total_opngbl_new . ' Cr'),

                "tdebit" => round($total_damount),
                "tcredit" => round($total_camount),
                "tbalance" => round($total_opngbl)
            );
        }

        //pm($datas);
        return $datas;
    }


    public function get_report($data, $id)
    {
        // $daterange= $data['daterange'];
        // $sr=explode("/",($daterange));
        // $fdate=trim($sr[0]);
        // $tdate=trim($sr[1]);

        //pm($data);


        $fdate = $data['from_date'];
        $tdate = $data['to_date'];

        if ($id != '') {
            $fdate = '2000-01-01';
            $tdate = date('Y-m-d');
        }
        //error_reporting(E_ALL);


        $sort = $_POST['sort'];
        $query1 = "";
        $query2 = "";
        $query3 = "";
        $filter = $_POST['filter'];
        $filter1 = $_POST['filter1'];
        $filter2 = $_POST['filter2'];


        if (($_POST['filter'] == "party") and ($_POST['filter1'] == "amount") and ($_POST['filter2'] == "nar")) {
            $acode = $_POST['acode'];
            $aname = $_POST['aname'];
            $amount = $_POST['amo'];
            $nar = $_POST['narr'];

            // $query1 = "SELECT opngbl,optype,aname FROM `tblacode` WHERE acode ='$acode'";
            // $query2 = "SELECT sum(damount) as op_damount ,sum(camount) as op_camount FROM `tbltrans_detail` WHERE acode ='$acode' and vdate < '$fdate'";
            if ($sort == "date") {
                // $query3 = "SELECT * FROM `tbltrans_detail` WHERE acode ='$acode' and damount ='$amount'  OR camount ='$amount' and   remarks LIKE '%$nar%' and
                //          vdate between '$fdate' AND '$tdate' order by vdate desc ";
            } else if ($sort == "vno") {
                //    $query3 = "SELECT * FROM `tbltrans_detail` WHERE acode ='$acode' and damount ='$amount'  OR camount ='$amount' and remarks LIKE '%$nar%' and
                //vdate between '$fdate' AND '$tdate'  order by vdate asc ";
            } else if ($sort == 'debit') {
                //  $query3 = "SELECT * FROM `tbltrans_detail` WHERE acode ='$acode' and damount ='$amount'  OR camount ='$amount' and remarks LIKE '%$nar%' and
                // vdate between '$fdate' AND '$tdate' order by vdate asc ";
            } else if ($sort == 'credit') {   //$query3 = "SELECT * FROM `tbltrans_detail` WHERE acode ='$acode' and damount ='$amount'  OR camount ='$amount' and remarks LIKE '%$nar%' and
                //vdate between '$fdate' AND '$tdate' order by vdate asc";

            }
        }

        if (($_POST['filter'] == "party") and ($_POST['filter1'] == "amount") and ($_POST['filter2'] == "")) {
            $acode = $_POST['acode'];
            $aname = $_POST['aname'];
            $amount = $_POST['amo'];
            // $nar = $_POST['narr'];

            //   $query1 = "SELECT opngbl,optype,aname FROM `tblacode` WHERE acode ='$acode' ";
            // $query2 = "SELECT sum(damount) as op_damount ,sum(camount) as op_camount FROM `tbltrans_detail`
            //WHERE acode ='$acode' and vdate < '$fdate'";
            //$query3 = "SELECT * FROM `tbltrans_detail` WHERE acode ='$acode' and (damount ='$amount'  OR camount ='$amount') and
            //vdate between '$fdate' AND '$tdate' order by vdate asc";
        }

        if (($_POST['filter'] == "party") and ($_POST['filter1'] == "") and ($_POST['filter2'] == "")) {

            $acode = $_POST['acode'];
            $aname = $_POST['aname'];

            $query1 = "SELECT opngbl,optype,aname FROM `tblacode` WHERE acode ='$acode' ";

            $query2 = "SELECT sum(damount) as op_damount ,sum(camount) as op_camount FROM `tbltrans_detail` WHERE acode ='$acode' and vdate < '$fdate'";

            if ($sort == 'date') {
                $query3 = "SELECT * FROM `tbltrans_detail` WHERE acode ='$acode' and vdate between '$fdate' AND '$tdate'  order by vdate asc  ";
            } else if ($sort == "vno") { // $query3 = "SELECT * FROM `tbltrans_detail` WHERE acode ='$acode' and vdate between '$fdate' AND '$tdate' order by vdate asc ";
            } else if ($sort == 'debit') {
                //$query3 = "SELECT * FROM `tbltrans_detail` WHERE acode ='$acode' and vdate between '$fdate' AND '$tdate' order by vdate asc";
            } else if ($sort == 'credit') {  // $query3 = "SELECT * FROM `tbltrans_detail` WHERE acode ='$acode' and vdate between '$fdate' AND '$tdate' order by vdate asc ";

            }

            //vno in (  SELECT vno FROM `tbltrans_master` WHERE created_date BETWEEN '$fdate' AND '$tdate' ) ";
        }

        // if(($_POST['filter']=="party")and ($_POST['filter1']=="") and ($_POST['filter2']==""))
        // {




        if (($_POST['filter'] == "") and ($_POST['filter1'] == "amount") and ($_POST['filter2'] == "")) {
            $acode = $_POST['acode'];
            $aname = $_POST['aname'];
            $amount = $_POST['amo'];
            // $nar = $_POST['narr'];

            //$query1 = "SELECT opngbl,optype,aname FROM `tblacode` WHERE acode ='$acode' ";
            // $query2 = "SELECT sum(damount) as op_damount ,sum(camount) as op_camount FROM `tbltrans_detail`
            //WHERE acode ='$acode' and vdate < '$fdate'";
            //$query3 = "SELECT * FROM `tbltrans_detail` WHERE (damount ='$amount'  OR camount ='$amount') and
            //      vdate between '$fdate' AND '$tdate' ";

            if ($sort == "date") {
                //  $query3 = "SELECT * FROM `tbltrans_detail` WHERE (damount ='$amount'  OR camount ='$amount') and vdate between '$fdate' AND'$tdate' order by vdate asc";
            } else if ($sort == "vno") {
                // $query3 = "SELECT * FROM `tbltrans_detail` WHERE (damount ='$amount'  OR camount ='$amount') and vdate between '$fdate' AND'$tdate' order by vdate asc ";
            } else if ($sort == 'debit') {
                //$query3 = "SELECT * FROM `tbltrans_detail` WHERE (damount ='$amount'  OR camount ='$amount') and vdate between '$fdate'AND '$tdate' order by vdate asc ";
            } else if ($sort == 'credit') {
                //$query3 = "SELECT * FROM `tbltrans_detail` WHERE (damount ='$amount'  OR camount ='$amount') and vdate between '$fdate'AND '$tdate' order by vdate asc";

            }
        }
        if (($_POST['filter'] == "") and ($_POST['filter1'] == "") and ($_POST['filter2'] == "nar")) {
            $acode = $_POST['acode'];
            $aname = $_POST['aname'];
            $amount = $_POST['amo'];
            $nar = $_POST['narr'];

            //  $query1 = "SELECT opngbl,optype,aname FROM `tblacode` WHERE acode ='$acode' ";
            //$query2 = "SELECT sum(damount) as op_damount ,sum(camount) as op_camount FROM `tbltrans_detail` WHERE acode ='$acode' and vdate < '$fdate'";

            // $query3 = "SELECT * FROM `tbltrans_detail` WHERE remarks LIKE '%$nar%' and
            //s    vdate between '$fdate' AND '$tdate' ";

            if ($sort == "date") {
                //  $query3 = "SELECT * FROM `tbltrans_detail` WHERE remarks LIKE '%$nar%' and vdate between '$fdate' AND '$tdate' order by vdate asc ";
            } else if ($sort == "vno") {
                //$query3 = "SELECT * FROM `tbltrans_detail` WHERE remarks LIKE '%$nar%' and vdate between '$fdate' AND '$tdate' order by vdate asc ";
            } else if ($sort == 'debit') {
                //$query3 = "SELECT * FROM `tbltrans_detail` WHERE remarks LIKE '%$nar%' and vdate between '$fdate' AND '$tdate' order by vdate asc";
            } else if ($sort == 'credit') {
                //$query3 = "SELECT * FROM `tbltrans_detail` WHERE remarks LIKE '%$nar%' and vdate between '$fdate' AND '$tdate' order by vdate asc";

            }
        }


        if (($_POST['filter'] == "party") and ($_POST['filter1'] == "") and ($_POST['filter2'] == "nar")) {
            $acode = $_POST['acode'];
            $aname = $_POST['aname'];
            $amount = $_POST['amo'];
            $nar = $_POST['narr'];

            // $query1 = "SELECT opngbl,optype,aname FROM `tblacode` WHERE acode ='$acode' ";
            //$query2 = "SELECT sum(damount) as op_damount ,sum(camount) as op_camount FROM `tbltrans_detail` WHERE acode ='$acode' and vdate < '$fdate'";

            //  $query3 = "SELECT * FROM `tbltrans_detail` WHERE remarks LIKE '%$nar%' and
            //         vdate between '$fdate' AND '$tdate' and  acode ='$acode'  ";

            if ($sort == "date") {
                //  $query3 = "SELECT * FROM `tbltrans_detail` WHERE remarks LIKE '%$nar%' and vdate between '$fdate' AND '$tdate' and acode ='$acode' order by vdate asc ";
            } else if ($sort == "vno") {
                //  $query3 = "SELECT * FROM `tbltrans_detail` WHERE remarks LIKE '%$nar%' and vdate between '$fdate' AND '$tdate' and acode ='$acode' order by vdate asc ";
            } else if ($sort == 'debit') {
                //$query3 = "SELECT * FROM `tbltrans_detail` WHERE remarks LIKE '%$nar%' and vdate between '$fdate' AND '$tdate' and acode ='$acode' order by vdate asc) ";
            } else if ($sort == 'credit') {
                //$query3 = "SELECT * FROM `tbltrans_detail` WHERE remarks LIKE '%$nar%' and vdate between '$fdate' AND '$tdate' and acode ='$acode' order by vdate asc ";

            }
        }


        if (($_POST['filter'] == "") and ($_POST['filter1'] == "amount") and ($_POST['filter2'] == "nar")) {
            $acode = $_POST['acode'];
            $aname = $_POST['aname'];
            $amount = $_POST['amo'];
            $nar = $_POST['narr'];

            // $query1 = "SELECT opngbl,optype,aname FROM `tblacode` WHERE acode ='$acode' ";
            //$query2 = "SELECT sum(damount) as op_damount ,sum(camount) as op_camount FROM `tbltrans_detail` WHERE acode ='$acode' and vdate < '$fdate'";
            //$query3 = "SELECT * FROM `tbltrans_detail` WHERE acode ='$acode' and damount ='$amount'  OR camount ='$amount' and remarks LIKE '%$nar%' and vdate between '$fdate' AND '$tdate' order by vdate asc ";

            if ($sort == "date") {
                //  $query3 = "SELECT * FROM `tbltrans_detail` WHERE acode ='$acode' and damount ='$amount'  OR camount ='$amount' and remarks LIKE '%$nar%' and vdate between '$fdate' AND '$tdate'  order by vdate asc ";
            } else if ($sort == "vno") {
                //   $query3 = "SELECT * FROM `tbltrans_detail` WHERE acode ='$acode' and damount ='$amount'  OR camount ='$amount' and remarks LIKE '%$nar%' and
                //vdate between '$fdate' AND '$tdate'  order by vdate asc ";
            } else if ($sort == 'debit') {
                //  $query3 = "SELECT * FROM `tbltrans_detail` WHERE acode ='$acode' and damount ='$amount'  OR camount ='$amount' and remarks LIKE '%$nar%' and vdate between '$fdate' AND '$tdate'  order by vdate asc ";
            } else if ($sort == 'credit') {
                //$query3 = "SELECT * FROM `tbltrans_detail` WHERE acode ='$acode' and damount ='$amount'  OR camount ='$amount' and remarks LIKE '%$nar%' and vdate between '$fdate' AND '$tdate' order by vdate asc";
            }
        }

        if ($id != '') {

            $acode = $id;
            $sort = 'date';

            $query1 = "SELECT opngbl,optype,aname FROM `tblacode` WHERE acode ='$acode' ";


            $query2 = "SELECT sum(damount) as op_damount ,sum(camount) as op_camount FROM `tbltrans_detail` WHERE acode ='$acode' and vdate < '$fdate'";

            $query3 = "SELECT * FROM `tbltrans_detail` WHERE acode ='$acode' and vdate between '$fdate' AND '$tdate' and rate > 0 order by vdate asc  ";
        }

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

        $opngbl = $line['opngbl'];
        $optype = $line['optype'];
        $aname = $line['aname'];

        $result = $this->db->query($query2);
        foreach ($result->result_array() as $key => $line) {
            $op_damount = $line['op_damount'];
            $op_camount = $line['op_camount'];
        }
        $total_opngbl = '';
        if ($optype == 'Credit') {
            $opngbl = -1 * ($opngbl);
        }
        $total_opngbl =    ($op_damount    - $op_camount) + ($opngbl);

        if (($_POST['filter1'] == "amount") or ($_POST['filter2'] == "nar")) {
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
                $chequeno = $line['chequeno'];

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
                    "chequeno" => $chequeno,
                    "vtype" => $vtype,
                    'openingbalbal' => ($total_opngbl_new > 0 ? $total_opngbl_new . ' Dr' : $total_opngbl_new . ' Cr'),
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
                "chequeno" => $chequeno,
                "vtype" => $vtype,
                'openingbalbal' => ($total_opngbl_new > 0 ? $total_opngbl_new . ' Dr' : $total_opngbl_new . ' Cr'),

                "tdebit" => round($total_damount),
                "tcredit" => round($total_camount),
                "tbalance" => round($total_opngbl)
            );
        }

        //pm($datas);
        return $datas;
    }



    public function get_dcustomer_report($data, $id)
    {
        // $daterange= $data['daterange'];
        // $sr=explode("/",($daterange));
        // $fdate=trim($sr[0]);
        // $tdate=trim($sr[1]);

        //pm($data);


        $fdate = $data['from_date'];
        $tdate = $data['to_date'];
        $direct_customer=$data['direct_customer'];
       
        if ($id != '') {
            $fdate = '2000-01-01';
            $tdate = date('Y-m-d');
        }
        //error_reporting(E_ALL);


        $sort = $_POST['sort'];
        $query1 = "";
        $query2 = "";
        $query3 = "";
        $filter = $_POST['filter'];
        $filter1 = $_POST['filter1'];
        $filter2 = $_POST['filter2'];

        $name = $this->db->query("SELECT name FROM tbl_direct_customer WHERE id=$direct_customer")->row_array()['name'];
//echo $name;exit;
        if (($_POST['filter'] == "party") and ($_POST['filter1'] == "") and ($_POST['filter2'] == "")) {

            $acode = $_POST['acode'];
            $aname = $_POST['aname'];

            $query1 = "SELECT opngbl,optype,aname FROM `tblacode` WHERE acode ='$acode' ";

            $query2 = "SELECT sum(damount) as op_damount ,sum(camount) as op_camount FROM `tbltrans_detail` WHERE direct_customer ='$direct_customer' and vdate < '$fdate'";

            if ($sort == 'date') {
                $query3 = "SELECT * FROM `tbltrans_detail` WHERE direct_customer ='$direct_customer' and vdate between '$fdate' AND '$tdate'  order by vdate asc  ";    // val of
            } else if ($sort == "vno") { // $query3 = "SELECT * FROM `tbltrans_detail` WHERE acode ='$acode' and vdate between '$fdate' AND '$tdate' order by vdate asc ";
            } else if ($sort == 'debit') {
                //$query3 = "SELECT * FROM `tbltrans_detail` WHERE acode ='$acode' and vdate between '$fdate' AND '$tdate' order by vdate asc";
            } else if ($sort == 'credit') {  // $query3 = "SELECT * FROM `tbltrans_detail` WHERE acode ='$acode' and vdate between '$fdate' AND '$tdate' order by vdate asc ";

            }

            //vno in (  SELECT vno FROM `tbltrans_master` WHERE created_date BETWEEN '$fdate' AND '$tdate' ) ";
        }
  
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

        $opngbl = $line['opngbl'];
        $optype = $line['optype'];
        $aname = $line['aname'];

        $result = $this->db->query($query2);
        foreach ($result->result_array() as $key => $line) {
            $op_damount = $line['op_damount'];
            $op_camount = $line['op_camount'];
        }
        $total_opngbl = '';
        if ($optype == 'Credit') {
            $opngbl = -1 * ($opngbl);
        }
        $total_opngbl =    ($op_damount    - $op_camount) + ($opngbl);

        if (($_POST['filter1'] == "amount") or ($_POST['filter2'] == "nar")) {
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
                $chequeno = $line['chequeno'];

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
                    "chequeno" => $chequeno,
                    "vtype" => $vtype,
                    'openingbalbal' => ($total_opngbl_new > 0 ? $total_opngbl_new . ' Dr' : $total_opngbl_new . ' Cr'),
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
                "chequeno" => $chequeno,
                "vtype" => $vtype,
                'openingbalbal' => ($total_opngbl_new > 0 ? $total_opngbl_new . ' Dr' : $total_opngbl_new . ' Cr'),

                "tdebit" => round($total_damount),
                "tcredit" => round($total_camount),
                "tbalance" => round($total_opngbl)
            );
        }

        //pm($datas);
        return $datas;
    }
}
