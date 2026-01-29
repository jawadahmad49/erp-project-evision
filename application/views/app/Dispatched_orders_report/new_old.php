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
            $order_id = $row['order_id'];  // Use order_id from the temporary table
            $item_count++;
            $order_item_counts = $this->db->query("SELECT count(*) as count from tbl_place_order_detail where order_id='$order_id'")->row_array()['count'];
            $item_gst = 0;
            $userid = $row['userid']; // Ensure that userid is included as part of the report
            $tbl_user = $this->db->query("select ntn, cnic, tex_type, name from tbl_user where id ='$userid'")->row_array();
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
            $item_detail = $this->db->query("SELECT catcode, itemname, itemnameint FROM tblmaterial_coding where materialcode= '$materialcode'")->row_array();
            $itemnameint = $item_detail['itemnameint'];
            $itemname = $item_detail['itemname'];
            $catcode = $item_detail['catcode'];
            $mt = $itemnameint / 1000;
            // Continue the logic for processing the data
            if ($row['item_type'] != 'Swap') {
                $gst = round(($saleprice * $row['gst']) / 100);
                $ttl_gst += $row['quantity'] * $gst;
                $item_gst = $row['quantity'] * $gst;
                $total_amount = ($saleprice + $gst) * $row['quantity'];
            }
            if ($row['item_type'] == 'New') {
                $total_amount += $security_charges * $row['quantity'];  // Add security charges if type is 'New'
                $ttl_security_charges += $security_charges * $row['quantity'];  // Add security charges if type is 'New'
            }
            // Continue with the logic for calculating totals for delivery charges, GST, etc.
            $grand_total_quantity += $row['quantity'];
            if ($catcode == 1) {
                if ($row['item_type'] != 'Swap') {
                    $item_qty += $row['quantity'];
                }
            }
            if ($row['item_type'] == 'Swap') {
                $ttl_swap_charges += $row['swap_charges'] * $row['quantity'];  // Add swap charges
                $total_amount = $row['swap_charges'] * $row['quantity'];
            }
            $per_item_amount = $row['quantity'] * $saleprice;
            if ($row['item_type'] == 'New') {
                $per_item_amount = ($row['quantity'] * $saleprice) + ($row['quantity'] * $security_charges);
            }
        ?>
            <tr class="even_frm_top" id="row">
                <td>
                    <?= $count; ?>
                </td>
                <td>
                    <?= ucwords($name); ?>
                </td>
                <td align="right">
                    <?= $cnic; ?>
                </td>
                <td align="right">
                    <?= $ntn; ?>
                </td>
                <td>
                    <?= ucwords($tex_type); ?>
                </td>
                <td align="right">
                    <?= $row['order_date']; ?>
                </td>
                <td align="right">
                    <?= $row['dispatch_date']; ?>
                </td>
                <td align="right">
                    <?= $order_id; ?>
                </td>
                <td align="right">
                    <?= $item_count; ?>
                </td>
                <td>
                    <?= $itemname; ?>
                </td>
                <td>
                    <?= $row['item_type']; ?>
                </td>
                <td align="right">
                    <?= $mt; ?>
                </td>
                <td align="right">
                    <?= $row['quantity']; ?>
                </td>
                <td align="right">
                    <?= $row['price']; ?>
                </td>
                <td align="right">
                    <?php if ($row['item_type'] == "New") {
                        echo number_format($row['security_charges'] * $row['quantity']);
                    } ?>
                </td>
                <td align="right">
                    <?= number_format($per_item_amount); ?>
                </td>
                <td align="right">
                    <?= $row['gst'] . "%"; ?>
                </td>
                <td align="right">
                    <?= number_format($item_gst); ?>
                </td>
                <td align="right">
                    <?= number_format($per_item_amount + $item_gst); ?>
                </td>
                <td align="right">
                    <?= $row['trip_id']; ?>
                </td>
                <td>
                    <?= $rider_name; ?>
                </td>
            </tr>
            <?php
            if ($order_item_counts == $item_count) {
                $item_count++;
            ?>
                <tr class="even_frm_top" id="row">
                    <td colspan="12" align="right">
                        <strong>Delivery Charges</strong>
                    </td>
                    <td align="right"><?= $item_qty; ?></td>
                    <td align="right"><?= $per_delivery_charges; ?></td>
                    <td align="right"></td>
                    <td align="right"><?= number_format($item_qty * $per_delivery_charges); ?></td>
                    <td align="right"><?= $delivery_gst . '%'; ?></td>
                    <td align="right">
                        <?php
                        $item_gst = ($item_qty * $per_delivery_charges) * $delivery_gst / 100;
                        echo number_format($item_gst);
                        ?>
                    </td>
                    <td align="right">
                        <?php
                        $delivery_Inclusive_GST = $item_gst + ($item_qty * $per_delivery_charges);
                        $grand_delivery_Inclusive_GST += $delivery_Inclusive_GST;
                        echo number_format($delivery_Inclusive_GST);
                        ?>
                    </td>
                    <td align="right"></td>
                    <td></td>
                </tr>
        <?php
                $item_count = 0;
                $lpg_amount = 0;
                $ttl_accessories = 0;
                $ttl_security_charges = 0;
                $ttl_delivery_charge = 0;
                $ttl_gst = 0;
                $ttl_swap_charges = 0;
                $item_qty = 0;
            }
        }
        ?>
        <tr class="even_frm_top" id="row">
            <td colspan="12" align="right">Grand Total</td>
            <td align="right"><?= number_format($grand_total_quantity); ?></td>
            <td colspan="6" align="right"><?= number_format($grand_total_amount + $grand_delivery_Inclusive_GST); ?></td>
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