<html>
<title>Dispatched Orders Report</title>

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
            <td colspan="2" width="40%"> <span align="center" style="color :#153860; font-family: times New Roman;   font-size: 18px;  height: 29px;">Dispatched Orders Report<?php $csv_outputs .= ",,," . "Dispatched Orders Report" . "\n"; ?><?php $csv_output .= "\t\t\t" . "Dispatched Orders Report" . "\n"; ?></span>
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
    </table>
    <table width="80%" height="30" align="center" class="imagetable">
        <tr class="exist_rec_sb_high_main">
            <td>
                <div align="left">Sr #<?php $csv_outputs .= 'Sr #' . ","; ?><?php $csv_output .= 'Sr #' . "\t"; ?></div>
            </td>
            <td>
                <div align="left">Customer Name<?php $csv_outputs .= 'Customer Name' . ","; ?><?php $csv_output .= 'Customer Name' . "\t"; ?></div>
            </td>
            <td>
                <div align="right">CNIC<?php $csv_outputs .= 'CNIC' . ","; ?><?php $csv_output .= 'CNIC' . "\t"; ?></div>
            </td>
            <td>
                <div align="right">NTN<?php $csv_outputs .= 'NTN' . ","; ?><?php $csv_output .= 'NTN' . "\t"; ?></div>
            </td>
            <td>
                <div align="left">Filer Status<?php $csv_outputs .= 'Filer Status' . ","; ?><?php $csv_output .= 'Filer Status' . "\t"; ?></div>
            </td>
            <td>
                <div style="text-align: right; white-space: nowrap;">Order Date<?php $csv_outputs .= 'Order Date' . ","; ?><?php $csv_output .= 'Order Date' . "\t"; ?></div>
            </td>
            <td>
                <div align="right">Dispatch Date<?php $csv_outputs .= 'Dispatch Date' . ","; ?><?php $csv_output .= 'Dispatch Date' . "\t"; ?></div>
            </td>
            <td>
                <div align="right">Order No<?php $csv_outputs .= 'Order No' . ","; ?><?php $csv_output .= 'Order No' . "\t"; ?></div>
            </td>
            <td>
                <div align="right">Item No<?php $csv_outputs .= 'Item No' . ","; ?><?php $csv_output .= 'Item No' . "\t"; ?></div>
            </td>
            <td>
                <div align="left">Item Name<?php $csv_outputs .= 'Item Name' . ","; ?><?php $csv_output .= 'Item Name' . "\t"; ?></div>
            </td>
            <td>
                <div align="left">Type<?php $csv_outputs .= 'Type' . ","; ?><?php $csv_output .= 'Type' . "\t"; ?></div>
            </td>
            <td>
                <div align="right">MT<?php $csv_outputs .= 'MT' . ","; ?><?php $csv_output .= 'MT' . "\t"; ?></div>
            </td>
            <td>
                <div align="right">Quantity<?php $csv_outputs .= 'Quantity' . ","; ?><?php $csv_output .= 'Quantity' . "\t"; ?></div>
            </td>
            <td>
                <div align="right">Unit Price<?php $csv_outputs .= 'Unit Price' . ","; ?><?php $csv_output .= 'Unit Price' . "\t"; ?></div>
            </td>
            <td>
                <div align="right">Security Charges<?php $csv_outputs .= 'Security Charges' . ","; ?><?php $csv_output .= 'Security Charges' . "\t"; ?></div>
            </td>
            <td>
                <div align="right">Value Excluding GST<?php $csv_outputs .= 'Value Excluding GST' . ","; ?><?php $csv_output .= 'Value Excluding GST' . "\t"; ?></div>
            </td>
            <td>
                <div align="right">Tax Rate<?php $csv_outputs .= 'Tax Rate' . ","; ?><?php $csv_output .= 'Tax Rate' . "\t"; ?></div>
            </td>
            <td>
                <div align="right">GST<?php $csv_outputs .= 'GST' . ","; ?><?php $csv_output .= 'GST' . "\t"; ?></div>
            </td>
            <td>
                <div align="right">Value Inclusive GST<?php $csv_outputs .= 'Value Inclusive GST' . ","; ?><?php $csv_output .= 'Value Inclusive GST' . "\t"; ?></div>
            </td>
            <td>
                <div align="right">Trip No<?php $csv_outputs .= 'Trip No' . ","; ?><?php $csv_output .= 'Trip No' . "\t"; ?></div>
            </td>
            <td>
                <div align="left">Rider Name<?php $csv_outputs .= 'Rider Name' . "\r\n"; ?><?php $csv_output .= 'Rider Name' . "\n"; ?></div>
            </td>
        </tr>
        <?php
        $grand_total_quantity = 0;
        $grand_total_amount = 0;
        $grand_delivery_Inclusive_GST = 0;
        $count = 0;
        $prev_order_id = null;
        $item_count = 0;
        $item_qty = 0;
        foreach ($report as $key => $row) {
            $count++;
            $userid = $row['userid'];
            if (!empty($userid)) {
                $order_id = $row['order_id'];

                $item_count++;
                $order_item_counts = $this->db->query("SELECT count(*) as count from tbl_place_order_detail where order_id='$order_id'")->row_array()['count'];
                $item_gst = 0;

                $tbl_user = $this->db->query("select ntn,cnic,tex_type,name from tbl_user where id ='$userid'")->row_array();
                $name = $tbl_user['name'];
                $ntn = $tbl_user['ntn'];
                $cnic = $tbl_user['cnic'];
                $tex_type = $tbl_user['tex_type'];
                $trip_id = $row['trip_id'];
                $materialcode = $row['materialcode'];
                $delivery_gst = $row['delivery_gst'];
                $per_delivery_charges = $row['per_delivery_charges'];
                $delivery = ($per_delivery_charges * $delivery_gst) / 100;
                $delivery_charges = round($per_delivery_charges + $delivery, 0);

                $saleprice = $row['price'];
                $security_charges = $row['security_charges'];

                $rider_id = $this->db->query("SELECT rider_id FROM tbl_trip_coding where id= '$trip_id'")->row_array()['rider_id'];
                $rider_name = $this->db->query("SELECT rider_name FROM tbl_rider_coding where id= '$rider_id'")->row_array()['rider_name'];
                if ($row['type'] == 'walkin') {
                    $rider_name = 'Walkin Order';
                }
                $zone_name = $this->db->query("SELECT zone_name FROM tbl_zone where id= '$zone_id'")->row_array()['zone_name'];
                $city_name = $this->db->query("SELECT city_name FROM tbl_city where city_id= '$city_id'")->row_array()['city_name'];
                $item_detail = $this->db->query("SELECT catcode,itemname,itemnameint FROM tblmaterial_coding where materialcode= '$materialcode'")->row_array();

                $itemnameint = $item_detail['itemnameint'];
                $itemname = $item_detail['itemname'];
                $catcode = $item_detail['catcode'];
                $mt = $itemnameint / 1000;
                $item_type = $row['item_type'];
                $quantity = $row['quantity'];
                if ($item_type != 'Swap') {
                    $gst = round(($saleprice * $row['gst']) / 100);
                    $ttl_gst += $quantity * $gst;
                    $item_gst = $quantity * $gst;
                    $total_amount = ($saleprice + $gst) * $quantity;
                }
                if ($item_type == 'New') {
                    $total_amount += $security_charges * $quantity;  // Add security charges if type is 'New'
                    $ttl_security_charges += $security_charges * $quantity;  // Add security charges if type is 'New'
                }

                if ($catcode == 1) {
                    if ($item_type != 'Swap') {
                        $ttl_delivery_charge += (int) $quantity * (float) $delivery_charges;
                        $lpg_amount += $quantity * $saleprice;
                    }
                } else {
                    $ttl_accessories += $quantity * $saleprice;
                }
                // $total_qty += $quantity;
                $grand_total_quantity += $quantity;
                if ($catcode == 1) {
                    if ($item_type != 'Swap') {
                        $item_qty += $quantity;
                    }
                }
                if ($item_type == 'Swap') {
                    $ttl_swap_charges += $row['swap_charges'] * $quantity;  // Add security charges if type is 'New'
                    $total_amount = $row['swap_charges'] * $quantity;
                }

                $per_item_amount = $quantity * $saleprice;
                if ($item_type == 'New') {
                    $per_item_amount = ($quantity * $saleprice) + ($quantity * $security_charges);
                }
            } else {
                $order_id = $row['order_id'];

                $item_count++;
                $userid = $this->db->query("SELECT userid from tbl_place_order where id='$order_id'")->row_array()['userid'];
                $order_item_counts = $this->db->query("SELECT count(*) as count from tbl_cylinder_return_detail where order_id='$order_id'")->row_array()['count'];
                $item_gst = 0;

                $tbl_user = $this->db->query("select ntn,cnic,tex_type,name from tbl_user where id ='$userid'")->row_array();
                $name = $tbl_user['name'];
                $ntn = $tbl_user['ntn'];
                $cnic = $tbl_user['cnic'];
                $tex_type = $tbl_user['tex_type'];
                $trip_id = $row['trip_id'];
                $materialcode = $row['materialcode'];
                $delivery_gst = $row['delivery_gst'];
                $per_delivery_charges = $row['per_delivery_charges'];
                $delivery = ($per_delivery_charges * $delivery_gst) / 100;
                $delivery_charges = round($per_delivery_charges + $delivery, 0);

                $saleprice = $row['price'];
                $security_charges = $row['security_charges'];

                $item_type = 'Cylinder Return';
                $item_detail = $this->db->query("SELECT catcode,itemname,itemnameint FROM tblmaterial_coding where materialcode= '$materialcode'")->row_array();
                $rider_name = '';
                $itemnameint = $item_detail['itemnameint'];
                $itemname = $item_detail['itemname'];
                $catcode = $item_detail['catcode'];
                $mt = $itemnameint / 1000;
                $quantity = (-1) * $row['quantity'];
                $per_item_amount = $quantity * $security_charges;
                $grand_total_quantity += $quantity;
            }

        ?>
            <tr class="even_frm_top" id="row">
                <td>
                    <?= $count; ?> <?php $csv_outputs .= $count . ","; ?> <?php $csv_output .= $count . "\t"; ?>
                </td>
                <td>
                    <?php echo ucwords($name); ?> <?php $csv_outputs .= $name . ","; ?> <?php $csv_output .= $name . "\t"; ?>
                </td>
                <td align="right">
                    <?php echo $cnic; ?> <?php $csv_outputs .= $cnic . ","; ?> <?php $csv_output .= $cnic . "\t"; ?>
                </td>
                <td align="right">
                    <?php echo $ntn; ?> <?php $csv_outputs .= $ntn . ","; ?> <?php $csv_output .= $ntn . "\t"; ?>
                </td>
                <td>
                    <?php echo ucwords($tex_type); ?> <?php $csv_outputs .= $tex_type . ","; ?> <?php $csv_output .= $tex_type . "\t"; ?>
                </td>
                <td align="right">
                    <?php echo $row['order_date']; ?> <?php $csv_outputs .= $row['order_date'] . ","; ?> <?php $csv_output .= $row['order_date'] . "\t"; ?>
                </td>
                <td align="right">
                    <?php echo $row['dispatch_date']; ?> <?php $csv_outputs .= $row['dispatch_date'] . ","; ?> <?php $csv_output .= $row['dispatch_date'] . "\t"; ?>
                </td>
                <td align="right">
                    <?php echo $order_id; ?> <?php $csv_outputs .= trim($order_id) . ","; ?> <?php $csv_output .= trim($order_id) . "\t"; ?>
                </td>
                <td align="right">
                    <?php echo $item_count; ?> <?php $csv_outputs .= $item_count . ","; ?> <?php $csv_output .= $item_count . "\t"; ?>
                </td>
                <td>
                    <?php echo $itemname; ?> <?php $csv_outputs .= $itemname . ","; ?> <?php $csv_output .= $itemname . "\t"; ?>
                </td>
                <td>
                    <?php echo $item_type; ?> <?php $csv_outputs .= $item_type . ","; ?> <?php $csv_output .= $item_type . "\t"; ?>
                </td>
                <td align="right">
                    <?php if ($catcode == 1) {
                        echo $mt;
                    } ?> <?php $csv_outputs .= $mt . ","; ?> <?php $csv_output .= $mt . "\t"; ?>
                </td>
                <td align="right">
                    <?php echo $quantity; ?> <?php $csv_outputs .= $quantity . ","; ?> <?php $csv_output .= $quantity . "\t"; ?>
                </td>
                <td align="right">
                    <?php echo $row['price']; ?> <?php $csv_outputs .= $row['price'] . ","; ?> <?php $csv_output .= $row['price'] . "\t"; ?>
                </td>
                <td align="right">
                    <?php if ($item_type == "New") {
                        echo number_format($row['security_charges'] * $quantity);
                    } ?> <?php $csv_outputs .= $row['security_charges'] * $quantity . ","; ?> <?php $csv_output .= $row['security_charges'] * $quantity . "\t"; ?>
                </td>
                <td align="right">
                    <?php echo number_format($per_item_amount); ?> <?php $csv_outputs .= $per_item_amount . ","; ?> <?php $csv_output .= $per_item_amount . "\t"; ?>
                </td>
                <td align="right">
                    <?php echo $row['gst'] . "%"; ?> <?php $csv_outputs .= $row['gst'] . "%" . ","; ?> <?php $csv_output .= $row['gst'] . "%" . "\t"; ?>
                </td>
                <td align="right">
                    <?php echo number_format($item_gst); ?> <?php $csv_outputs .= $item_gst . ","; ?> <?php $csv_output .= $item_gst . "\t"; ?>
                </td>

                <td align="right">
                    <?php $grand_total_amount += $per_item_amount + $item_gst;
                    echo number_format($per_item_amount + $item_gst); ?> <?php $csv_outputs .= $per_item_amount + $item_gst . ","; ?> <?php $csv_output .= $per_item_amount + $item_gst . "\t"; ?>
                </td>
                <td align="right">
                    <?php echo $row['trip_id']; ?> <?php $csv_outputs .= $row['trip_id'] . ","; ?> <?php $csv_output .= $row['trip_id'] . "\t"; ?>
                </td>
                <td>
                    <?php echo $rider_name; ?> <?php $csv_outputs .= $rider_name . "\r\n"; ?> <?php $csv_output .= $rider_name . "\n"; ?>
                </td>
            </tr>
            <?php if ($order_item_counts == $item_count) {
                $item_count++;
            ?>
                <tr class="even_frm_top" id="row">
                    <td><?php $csv_outputs .= ","; ?> <?php $csv_output .= "\t"; ?></td>
                    <td><?php echo ucwords($name); ?> <?php $csv_outputs .= $name . ","; ?> <?php $csv_output .= $name . "\t"; ?></td>
                    <td align="right">
                        <?php echo $cnic; ?> <?php $csv_outputs .= $cnic . ","; ?> <?php $csv_output .= $cnic . "\t"; ?>
                    </td>
                    <td align="right">
                        <?php echo $ntn; ?> <?php $csv_outputs .= $ntn . ","; ?> <?php $csv_output .= $ntn . "\t"; ?>
                    </td>
                    <td>
                        <?php echo ucwords($tex_type); ?> <?php $csv_outputs .= $tex_type . ","; ?> <?php $csv_output .= $tex_type . "\t"; ?>
                    </td>
                    <td align="right">
                        <?php echo $row['order_date']; ?> <?php $csv_outputs .= $row['order_date'] . ","; ?> <?php $csv_output .= $row['order_date'] . "\t"; ?>
                    </td>
                    <td align="right">
                        <?php echo $row['dispatch_date']; ?> <?php $csv_outputs .= $row['dispatch_date'] . ","; ?> <?php $csv_output .= $row['dispatch_date'] . "\t"; ?>
                    </td>
                    <td align="right">
                        <?php echo $order_id; ?> <?php $csv_outputs .= trim($order_id) . ","; ?> <?php $csv_output .= trim($order_id) . "\t"; ?>
                    </td>
                    <td align="right">
                        <?php echo $item_count; ?> <?php $csv_outputs .= $item_count . ","; ?> <?php $csv_output .= $item_count . "\t"; ?>
                    </td>
                    <td>Delivery Charges <?php $csv_outputs .= 'Delivery Charges' . ","; ?> <?php $csv_output .= 'Delivery Charges' . "\t"; ?></td>
                    <td><?php $csv_outputs .= ","; ?> <?php $csv_output .= "\t"; ?></td>
                    <td align="right"><?php $csv_outputs .= ","; ?> <?php $csv_output .= "\t"; ?></td>
                    <td align="right"><?php echo $item_qty; ?> <?php $csv_outputs .= $item_qty . ","; ?> <?php $csv_output .= $item_qty . "\t"; ?></td>
                    <td align="right"><?php echo $per_delivery_charges; ?> <?php $csv_outputs .= $per_delivery_charges . ","; ?> <?php $csv_output .= $per_delivery_charges . "\t"; ?></td>
                    <td align="right"><?php $csv_outputs .= ","; ?> <?php $csv_output .= "\t"; ?></td>
                    <td align="right"><?php echo number_format($item_qty * $per_delivery_charges); ?> <?php $csv_outputs .= $item_qty * $per_delivery_charges . ","; ?> <?php $csv_output .= $item_qty * $per_delivery_charges . "\t"; ?></td>
                    <td align="right">
                        <?php echo $delivery_gst . '%'; ?> <?php $csv_outputs .= $delivery_gst . '%' . ","; ?> <?php $csv_output .= $delivery_gst . '%' . "\t"; ?>
                    </td>
                    <td align="right"><?php
                                        $item_gst = ($item_qty * $per_delivery_charges) * $delivery_gst / 100;
                                        echo number_format($item_gst); ?> <?php $csv_outputs .= $item_gst . ","; ?> <?php $csv_output .= $item_gst . "\t"; ?></td>

                    <td align="right"><?php $delivery_Inclusive_GST = $item_gst + ($item_qty * $per_delivery_charges);
                                        $grand_delivery_Inclusive_GST += $delivery_Inclusive_GST;
                                        echo number_format($delivery_Inclusive_GST); ?> <?php $csv_outputs .= $delivery_Inclusive_GST . "\r\n"; ?> <?php $csv_output .= $delivery_Inclusive_GST . "\n"; ?></td>
                    <td align="right"></td>
                    <td></td>
                </tr>
            <?php $item_count = 0;

                $lpg_amount = 0;
                $ttl_accessories = 0;
                $ttl_security_charges = 0;
                $ttl_delivery_charge = 0;
                $ttl_gst = 0;
                $ttl_swap_charges = 0;
                $item_qty = 0;
            } ?>
        <?php } ?>

        <tr class="even_frm_top" id="row">
            <td colspan="12" align="right">Grand Total <?php $csv_outputs .= 'Grand Total' . ",,,,,,,,,,,,"; ?> <?php $csv_output .= 'Grand Total' . "\t\t\t\t\t\t\t\t\t\t\t\t"; ?></td>
            </td>
            <td align="right"><?php echo number_format($grand_total_quantity); ?><?php $csv_outputs .= $grand_total_quantity . ","; ?> <?php $csv_output .= $grand_total_quantity . "\t"; ?></td>
            <td colspan="6" align="right"><?php echo number_format($grand_total_amount + $grand_delivery_Inclusive_GST); ?><?php $csv_outputs .= $grand_total_amount + $grand_delivery_Inclusive_GST . ",,,,,,,"; ?> <?php $csv_output .= $grand_total_amount + $grand_delivery_Inclusive_GST . "\t"; ?></td>
            <td colspan="2"></td>
        </tr>
    </table>
    <!-- Hidden fields to hold CSV data -->
    <input type="hidden" id="csv_hdr" value="<?php echo $csv_hdr; ?>">
    <input type="hidden" id="csv_output" value="<?php echo $csv_output; ?>">

    <!-- Export Button -->
    <button onclick="export_File()">Export to Excel</button>
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
    </script>
    <script src="https://code.jquery.com/jquery-3.6.4.min.js"></script>

    <script>
        function export_File() {
            // Get header and output data
            var csvHdr = document.getElementById('csv_hdr').value;
            var csvOutput = document.getElementById('csv_output').value;

            // AJAX request to export data
            $.ajax({
                url: "<?php echo SURL . 'Common/export_to_xls'; ?>",
                cache: false,
                type: "POST",
                data: {
                    csv_hdr: csvHdr,
                    csv_output: csvOutput,
                },
                success: function(data) {
                    var blob = new Blob([data], {
                        type: 'application/vnd.ms-excel'
                    });
                    var link = document.createElement('a');
                    link.href = window.URL.createObjectURL(blob);
                    link.download = 'Dispatched_Orders_Report.xls';
                    document.body.appendChild(link);
                    link.click();
                    document.body.removeChild(link);
                },
            });
        }
    </script>

    <script>
        function exportFile() {
            var csvHdrs = <?php echo json_encode($csv_output); ?>;
            var csvOutputs = <?php echo json_encode($csv_outputs); ?>;

            $.ajax({
                url: "<?php echo SURL . "Common/export" ?>",
                cache: false,
                type: "POST",
                data: {
                    csv_hdrs: csvHdrs,
                    csv_outputs: csvOutputs
                },
                success: function(data) {
                    var blob = new Blob([data]);
                    var link = document.createElement('a');
                    link.href = window.URL.createObjectURL(blob);
                    link.download = 'Dispatched_Orders_Report.csv';
                    document.body.appendChild(link);
                    link.click();
                    document.body.removeChild(link);
                }
            });
        }
    </script>

</body>

</html>