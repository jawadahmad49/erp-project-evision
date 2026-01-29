<html>
<title>Order Report</title>

<head>
    <link rel="stylesheet" type="text/css" href="<?php echo SURL ?>assets/css/old_css.css">
</head>

<body style="font-family:Verdana, Arial, Helvetica, sans-serif;">
    <style type="text/css">
        .style1 {
            font-family: Arial, Helvetica, sans-serif;
            font-weight: bold;
        }

        .style7 {
            font-family: Arial, Helvetica, sans-serif;
            font-size: small;
            font-weight: bold;
        }

        .style10 {
            font-size: small;
            font-family: Arial, Helvetica, sans-serif;
        }
    </style>
    <table width="80%" align="center" border="0" class="imagetable">
        <?php
        $hosp_id = '';
        $hosp_name = '';
        $hosp_address_1 = '';
        $hosp_address_2 = '';
        $hosp_nums = '';
        $hosp_fax = '';
        $hosp_email = '';
        $hosp_url = '';
        $hosp_img = '';
        $qresult = $this->db->query("select * from tbl_company where id=1")->row_array();
        $hosp_id = $qresult['id'];
        $hosp_name = $qresult['business_name'];
        $hosp_address_1 = $qresult['address'];
        $hosp_nums = $qresult['phone'];
        $hosp_email = $qresult['email'];
        $hosp_img = $qresult['logo'];
        ?>
        <tr align="center">
            <td width="30%"><img src="<?php echo IMG . "company/" . $hosp_img ?>" width="240" height="97"> </td>
            <td colspan="2" width="40%"> <span align="center" style="color :#153860; font-family: times New Roman;   font-size: 18px;  height: 29px;">Order Report<?php $csv_outputs .= ",,," . "Purchase Report" . "\n"; ?><?php $csv_output .= "\t\t\t" . "Purchase Report" . "\n"; ?></span>
                <br>
                <span align="center" style="color :#153860; font-family: times New Roman;   font-size: 13px;  height: 29px;">
                    <?php print $fromdate . ' To ' . $todate; ?><?php $csv_outputs .= ",,," . $fromdate . ' To ' . $todate . "\n"; ?><?php $csv_output .= "\t\t\t" . $fromdate . ' To ' . $todate . "\n"; ?></span>
                <br>
            </td>
            <td width="30%" style="text-align:right" valign="bottom">
                <span style="font-size:12px; color:#1f2153; font-weight:900;"><i class="fa fa-h-square fa-fw"></i><?php print $hosp_name;
                ?> </span><br>
                <span style="font-size:12px; color:#1f2153; font-weight:900;"><i class="fa fa-h-square fa-fw"></i><?php print $hosp_address_1;
                if ($hosp_address_2) {
                    print '<br>' . $hosp_address_2;
                }
                ?> </span><br>
                <?php if ($hosp_nums) { ?>
                    <span style="font-size:11px; color:#1f2153; font-weight:400;"><i class="fa fa-phone fa-fw"></i><?php print $hosp_nums; ?></span> <br>
                <?php } ?>
                <?php if ($hosp_fax) { ?>
                    <span style="font-size:11px; color:#1f2153; font-weight:400;"><i class="fa fa-fax fa-fw"></i><?php print $hosp_fax; ?></span> <br>
                <?php } ?>
                <?php if ($hosp_email) { ?>
                    <span style="font-size:11px; color:#1f2153; font-weight:400;"><i class="fa fa-envelope-o fa-fw"></i><?php print $hosp_email; ?></span> <br>
                <?php } ?>
                <?php if ($hosp_url) { ?>
                    <span style="font-size:11px; color:#1f2153; font-weight:400;"><i class="fa fa-globe fa-fw"></i><?php print $hosp_url; ?></span> <br>
                <?php } ?>
            </td>
        </tr>
        <?php
        // $csv_hdr .= ",,Purshase order Report&amp; From Date," . $fromdate . " ,To Date," . $todate . "\n";
        // $csv_hdr .= "Order No. ,Supplier Name,Remarks,Book Date,Payment Terms,Partial Delivery\n";
        ?>

    </table>
    <table width="80%" height="30" align="center" class="imagetable">
        <tr class="exist_rec_sb_high_main">
            <td>
                <div align="left">Order ID<?php $csv_outputs .= 'Order ID' . ","; ?><?php $csv_output .= 'Order ID' . "\t"; ?></div>
            </td>
            <td>
                <div align="left">Delivery Type<?php $csv_outputs .= 'Delivery Type' . ","; ?><?php $csv_output .= 'Delivery Type' . "\t"; ?></div>
            </td>
            <td>
                <div align="left">User Name<?php $csv_outputs .= 'User Name' . ","; ?><?php $csv_output .= 'User Name' . "\t"; ?></div>
            </td>
            <td>
                <div align="left">Date<?php $csv_outputs .= 'Date' . ","; ?><?php $csv_output .= 'Date' . "\t"; ?></div>
            </td>
            <td>
                <div>Time<?php $csv_outputs .= 'Time' . ","; ?><?php $csv_output .= 'Time' . "\t"; ?></div>
            </td>
            <td>
                <div align="left">Delivery Status<?php $csv_outputs .= 'Delivery Status' . ","; ?><?php $csv_output .= 'Delivery Status' . "\t"; ?></div>
            </td>
            <td>
                <div align="left">Zone Name<?php $csv_outputs .= 'Zone Name' . ","; ?><?php $csv_output .= 'Zone Name' . "\t"; ?></div>
            </td>
            <td>
                <div align="left">City Name<?php $csv_outputs .= 'City Name' . ","; ?><?php $csv_output .= 'City Name' . "\t"; ?></div>
            </td>
            <td>
                <div align="left">Address<?php $csv_outputs .= 'Address' . ","; ?><?php $csv_output .= 'Address' . "\t"; ?></div>
            </td>
            <td>
                <div align="left">Rider Name<?php $csv_outputs .= 'Rider Name' . ","; ?><?php $csv_output .= 'Rider Name' . "\t"; ?></div>
            </td>
            <td>
                <div>Remarks<?php $csv_outputs .= 'Remarks' . "\n"; ?><?php $csv_output .= 'Remarks' . "\n"; ?></div>
            </td>
        </tr>
        <?php
        foreach ($report as $key => $row) {
            $userid = $row['userid'];
            $name = $this->db->query("select name from tbl_user where id ='$userid'")->row_array()['name'];
            $order_id = $row['id'];
            $rider_id = $row['rider_id'];
            $area_id = $row['area_id'];
            $city_id = $row['city_id'];
            $zone_id = $row['zone_id'];

            $detail = $this->db->query("SELECT * FROM tbl_place_order_detail where order_id= '$order_id'")->result_array();
            $rider_name = $this->db->query("SELECT rider_name FROM tbl_rider_coding where id= '$rider_id'")->row_array()['rider_name'];
            $zone_name = $this->db->query("SELECT zone_name FROM tbl_zone where id= '$zone_id'")->row_array()['zone_name'];
            $city_name = $this->db->query("SELECT city_name FROM tbl_city where city_id= '$city_id'")->row_array()['city_name'];

            ?>
            <tr class="even_frm_top" id="row">
                <td width="10%" align="left" style="cursor: pointer; text-decoration: underline;"><img src="<?php echo SURL ?>assets/images/reports/plus.png" id="<?php echo $order_id . "_" . $donotremove; ?>" onclick="toggle('<?php echo $order_id . "_" . $donotremove; ?>');" /> &nbsp;
                    <?php echo $order_id; ?>
                    <?php $csv_outputs .= trim($order_id) . ","; ?>     <?php $csv_output .= trim($order_id) . "\t"; ?>
                </td>
                <td>
                    <?php echo $row['deliveryType']; ?><?php $csv_outputs .= $row['deliveryType'] . ","; ?>     <?php $csv_output .= $row['deliveryType'] . "\t"; ?>
                </td>
                <td>
                    <?php echo ucwords($name); ?><?php $csv_outputs .= $name . ","; ?>     <?php $csv_output .= $name . "\t"; ?>
                </td>
                <td>
                    <?php echo $row['date']; ?><?php $csv_outputs .= $row['date'] . ","; ?>     <?php $csv_output .= $row['date'] . "\t"; ?>
                </td>
                <td>
                    <?php echo $row['time']; ?>     <?php $csv_outputs .= $row['time'] . ","; ?>     <?php $csv_output .= $row['time'] . "\t"; ?>
                </td>
                <td>
                    <?php echo $row['deliveryStatus']; ?>     <?php $csv_outputs .= $row['deliveryStatus'] . ","; ?>     <?php $csv_output .= $row['deliveryStatus'] . "\t"; ?>
                </td>
                <td>
                    <?php echo $zone_name; ?>     <?php $csv_outputs .= $zone_name . ","; ?>     <?php $csv_output .= $zone_name . "\t"; ?>
                </td>
                <td>
                    <?php echo $row['address']; ?>     <?php $csv_outputs .= $row['address'] . ","; ?>     <?php $csv_output .= $row['address'] . "\t"; ?>
                </td>
                <td>
                    <?php echo $city_name ; ?>     <?php $csv_outputs .= $city_name  . ","; ?>     <?php $csv_output .= $row['street_no'] . "\t"; ?>
                </td>
                <td>
                    <?php echo $rider_name; ?>     <?php $csv_outputs .= $rider_name . ","; ?>     <?php $csv_output .= $rider_name . "\t"; ?>
                </td>
                <td>
                    <?php echo ucwords($row['remarks']); ?>     <?php $csv_outputs .= $row['remarks'] . ","; ?>     <?php $csv_output .= $row['remarks'] . "\t"; ?>
                </td>
            </tr>
            <tr>
                <td colspan="9">
                    <table width="800" align="center" id="<?php echo $order_id; ?>" style="display: none;">
                        <tr class="exist_rec_sb">
                            <td width="10%">
                                <div align="left">Sr No.<?php $csv_outputs .= 'Sr No.' . ","; ?><?php $csv_output .= 'Sr No.' . "\t"; ?></div>
                            </td>
                            <td width="10%">
                                <div align="left">Item Name
                            </td><?php $csv_outputs .= 'Item Name' . ","; ?><?php $csv_output .= 'Item Name' . "\t"; ?></div>

                            <td width="10%">
                                <div align="right">Quantity <?php $csv_outputs .= 'Quantity' . ","; ?><?php $csv_output .= 'Quantity' . "\t"; ?></div>
                            </td>
                            <td width="10%">
                                <div>Type
                            </td><?php $csv_outputs .= 'type' . ","; ?><?php $csv_output .= 'type' . "\t"; ?></div>
                        </tr>
                        <?php
                        $qty_total = 0;
                        $count = 0;

                        foreach ($detail as $key => $row_detail) {
                            $materialcode = $row_detail['materialcode'];
                            $count++;
                            $itemname = $this->db->query("select itemname from tblmaterial_coding where materialcode='$materialcode'")->row_array()['itemname'];
                            // $tbl_acode = $this->db->query("select short_code,aname from tblacode where acode='$itemcode'")->row_array();
                            print '<tr  class="even_frm_top">
                            <td width="10%" align="left">';
                            echo $count;
                            $csv_output .= trim($count) . "\t";
                            $csv_outputs .= trim($count) . ",";
                            print '</td>';

                            print '<td width="10%" align="left">';
                            echo $itemname;
                            $csv_output .= trim($itemname) . "\t";
                            $csv_outputs .= trim($itemname) . ",";
                            print '</td>';

                            print ' <td width="10%" align="right">';
                            $qty_total += $row_detail['quantity'];
                            echo $row_detail['quantity'];
                            $csv_output .= trim($row_detail['quantity']) . "\t";
                            $csv_outputs .= trim($row_detail['quantity']) . ",";
                            print '</td>';

                            print '<td width="10%">';
                            echo $row_detail['type'];
                            $csv_output .= trim($row_detail['type']) . "\t";
                            $csv_outputs .= trim($row_detail['type']) . ",";
                            print '</td>';


                            print '</tr>';
                        }
                        print '<tr  class="exist_rec_sb">
                        <td width="10%" align="center"></td>
                        <td width="10%" align="center">Total</td>
                        ';
                        $csv_output .= "\tTotal\t\t";
                        $csv_outputs .= ",,Total,";
                        print ' <td width="10%" align="right">';
                        echo number_format($qty_total, 2);
                        $csv_output .= trim($qty_total) . "\t\t";
                        $csv_outputs .= trim($qty_total) . ",,";
                        print '</td>';
                        print '</tr>';
                        ?>
                    </table>
                    <?php
        }
        ?>
    </table>
    </div>
    <!-- /.main-container -->
    <script type="text/javascript">
        function exportfile() {
            document.export1.submit();
        }
    </script>
    <script>
        function exportfile1() {
            //alert(document.getElementById("csv_output").value);
            document.export2.submit();
        }

        function show_details() {
            document.getElementById("caption").style.display = 'block';
            document.getElementById("details").style.display = 'block';
        }

        function toggle(cls) {
            var cls1 = cls.split("_");
            if (document.getElementById(cls1[0]).style.display == 'block') {
                document.getElementById(cls1[0]).style.display = 'none';
                document.getElementById(cls).src = '<?php echo SURL ?>assets/images/reports/plus.png';
            } else {
                document.getElementById(cls1[0]).style.display = 'block';
                document.getElementById(cls).src = '<?php echo SURL ?>assets/images/reports/minus.png';
            }
        }
    </script>
    <script src="https://code.jquery.com/jquery-3.6.4.min.js"></script>

    <script>
        function export_File() {
            var csvHdr = <?php echo json_encode($csv_hdr); ?>;
            var csvOutput = <?php echo json_encode($csv_output); ?>;

            $.ajax({
                url: "<?php echo SURL . "Common/export_to_xls" ?>",
                cache: false,
                type: "POST",
                data: {
                    csv_hdr: csvHdr,
                    csv_output: csvOutput
                },
                success: function (data) {
                    var blob = new Blob([data]);
                    var link = document.createElement('a');
                    link.href = window.URL.createObjectURL(blob);
                    link.download = 'Konwa_Report.xls';
                    document.body.appendChild(link);
                    link.click();
                    document.body.removeChild(link);
                }
            });
        }
    </script>

    <script>
        function exportFile() {
            var csvHdrs = <?php echo json_encode($csv_hdrs); ?>;
            var csvOutputs = <?php echo json_encode($csv_outputs); ?>;

            $.ajax({
                url: "<?php echo SURL . "Common/export" ?>",
                cache: false,
                type: "POST",
                data: {
                    csv_hdrs: csvHdrs,
                    csv_outputs: csvOutputs
                },
                success: function (data) {
                    var blob = new Blob([data]);
                    var link = document.createElement('a');
                    link.href = window.URL.createObjectURL(blob);
                    link.download = 'Konwa_Report.csv';
                    document.body.appendChild(link);
                    link.click();
                    document.body.removeChild(link);
                }
            });
        }
    </script>

</body>

</html>