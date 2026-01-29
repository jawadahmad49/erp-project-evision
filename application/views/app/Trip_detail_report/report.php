<html>
<title>Trip Detail Report</title>

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
            <td colspan="2" width="40%"> <span align="center" style="color :#153860; font-family: times New Roman;   font-size: 18px;  height: 29px;">Trip Detail Report<?php $csv_outputs .= ",,," . "Purchase Report" . "\n"; ?><?php $csv_output .= "\t\t\t" . "Purchase Report" . "\n"; ?></span>
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
        <?php
        $total_receivable = "0";
        $total_delivered = "0";
        $total_rejected = "0";
        foreach ($report as $key => $row) {
            $trip_id = $row['id'];
            $sale_point_id = $row['sale_point_id'];
            $vehicle_id = $row['vehicle_id'];
            $rider_id = $row['rider_id'];
            $status = $row['status'];
            $created_by = $row['created_by'];
            $created_date = $row['created_date'];

            $total_received = $row['total_received'];
            $order_ids = $row['order_id'];

            $user_name = $this->db->query("select name from tbl_user where id ='$created_by'")->row_array()['name'];
            $sp_name = $this->db->query("SELECT sp_name FROM tbl_sales_point WHERE sale_point_id = '$sale_point_id'")->row_array()['sp_name'];
            $vehicle = $this->db->query("SELECT * FROM tbl_vehicle_coding WHERE id = '$vehicle_id'")->row_array();
            $rider_name = $this->db->query("SELECT * FROM tbl_rider_coding WHERE id = '$rider_id'")->row_array()['rider_name'];
            ?>
            <tr class="exist_rec_sb_high_main" style="background-color: green;">
                <td colspan="11">
                    <div align="center">Trip # <?= $trip_id; ?><?php $csv_outputs .= 'Trip # ' . $trip_id . "\n"; ?><?php $csv_output .= 'Trip # ' . $trip_id . "\n"; ?></div>
                </td>
            </tr>
            <tr class="exist_rec_sb_high_main">
                <td>
                    <div>Sale point <?php $csv_outputs .= 'Sale point ' . "\n"; ?><?php $csv_output .= 'Sale point ' . "\n"; ?></div>
                </td>
                <td>
                    <div>Vehicle <?php $csv_outputs .= 'Vehicle' . "\n"; ?><?php $csv_output .= 'Vehicle' . "\n"; ?></div>
                </td>
                <td>
                    <div>Rider Name <?php $csv_outputs .= 'Rider Name' . "\n"; ?><?php $csv_output .= 'Rider Name' . "\n"; ?></div>
                </td>
                <td>
                    <div>Status <?php $csv_outputs .= 'Status' . "\n"; ?><?php $csv_output .= 'Status' . "\n"; ?></div>
                </td>
                <td>
                    <div>Created By <?php $csv_outputs .= 'Created By' . "\n"; ?><?php $csv_output .= 'Created By' . "\n"; ?></div>
                </td>
                <td>
                    <div>Created date <?php $csv_outputs .= 'Created date' . "\n"; ?><?php $csv_output .= 'Created date' . "\n"; ?></div>
                </td>
                <td>
                    <div>Trip Amount <?php $csv_outputs .= 'Trip Amount' . "\n"; ?><?php $csv_output .= 'Trip Amount' . "\n"; ?></div>
                </td>
                <td>
                    <div>Rider Receiving <?php $csv_outputs .= 'Rider Receiving ' . "\n"; ?><?php $csv_output .= 'Rider Receiving ' . "\n"; ?></div>
                </td>
                <td>
                    <div>Shop Incharge Received <?php $csv_outputs .= 'Shop Incharge Received' . "\n"; ?><?php $csv_output .= 'Shop Incharge Received' . "\n"; ?></div>
                </td>
                <td>
                    <div>Total Delivered Orders <?php $csv_outputs .= 'Total Delivered Orders' . "\n"; ?><?php $csv_output .= 'Total Delivered Orders' . "\n"; ?></div>
                </td>
                <td>
                    <div>Total Rejected Orders <?php $csv_outputs .= 'Total Rejected Orders' . "\n"; ?><?php $csv_output .= 'Total Rejected Orders' . "\n"; ?></div>
                </td>
            </tr>
            <tr class="even_frm_top">
                <td>
                    <?php echo ucwords($sp_name); ?>     <?php $csv_outputs .= ucwords($sp_name) . ","; ?>     <?php $csv_output .= ucwords($sp_name) . "\t"; ?>
                </td>
                <td>
                    <?php echo $vehicle['vehicle_number'] . '-' . ucwords($vehicle['vehicle_type']); ?>     <?php $csv_outputs .= $vehicle['vehicle_number'] . '-' . ucwords($vehicle['vehicle_type']) . ","; ?>     <?php $csv_output .= $value['vehicle_number'] . '-' . ucwords($value['vehicle_type']) . "\t"; ?>
                </td>
                <td>
                    <?php echo ucfirst($rider_name); ?>     <?php $csv_outputs .= ucfirst($rider_name) . ","; ?>     <?php $csv_output .= ucfirst($rider_name) . "\t"; ?>
                </td>
                <td>
                    <?php echo ucfirst($status); ?>     <?php $csv_outputs .= ucfirst($status) . ","; ?>     <?php $csv_output .= ucfirst($status) . "\t"; ?>
                </td>
                <td>
                    <?php echo ucfirst($user_name); ?>     <?php $csv_outputs .= ucfirst($user_name) . ","; ?>     <?php $csv_output .= ucfirst($user_name) . "\t"; ?>
                </td>
                <td>
                    <?php echo $created_date; ?>     <?php $csv_outputs .= $created_date . ","; ?>     <?php $csv_output .= $created_date . "\t"; ?>
                </td>
                <td align='right' id="trip_amount_<?= $trip_id ?>">
                    <?= $total_receivable; ?>
                    <?php $csv_outputs .= $total_receivable . ","; ?>
                    <?php $csv_output .= $total_receivable . "\t"; ?>
                </td>
                <td align='right' id="total_receivable_<?= $trip_id ?>">
                    <?= $total_receivable; ?>
                    <?php $csv_outputs .= $total_receivable . ","; ?>
                    <?php $csv_output .= $total_receivable . "\t"; ?>
                </td>
                <td>
                    <?= $total_received; ?>
                    <?php $csv_outputs .= $total_received . ","; ?>
                    <?php $csv_output .= $total_received . "\t"; ?>
                </td>
                <td id="total_delivered_<?= $trip_id ?>">

                    <?= $total_delivered; ?>
                    <?php $csv_outputs .= $total_delivered . ","; ?>
                    <?php $csv_output .= $total_delivered . "\t"; ?>
                </td>
                <td id="total_rejected_<?= $trip_id ?>">
                    <?= $total_rejected; ?>
                    <?php $csv_outputs .= $total_rejected . ","; ?>
                    <?php $csv_output .= $total_rejected . "\t"; ?>
                </td>
            </tr>
            <tr class="exist_rec_sb_high_main">
                <td>
                    <div align="left">Order ID<?php $csv_outputs .= 'Order ID' . ","; ?><?php $csv_output .= 'Order ID' . "\t"; ?></div>
                </td>
                <td>
                    <div align="left">Delivery Type<?php $csv_outputs .= 'Delivery Type' . ","; ?><?php $csv_output .= 'Delivery Type' . "\t"; ?></div>
                </td>
                <td>
                    <div align="left">Customer Name<?php $csv_outputs .= 'Customer Name' . ","; ?><?php $csv_output .= 'Customer Name' . "\t"; ?></div>
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
                    <div align="left">Delivery Charges<?php $csv_outputs .= 'Delivery Charges' . ","; ?><?php $csv_output .= 'Delivery Charges' . "\t"; ?></div>
                </td>
                <td>
                    <div align="left">GST<?php $csv_outputs .= 'GST' . ","; ?><?php $csv_output .= 'GST' . "\t"; ?></div>
                </td>
            </tr>
            <?php
            $orders = $this->db->query("Select * from tbl_place_order where id in ($order_ids)")->result_array();
            // pm($orders);exit;
            $trip_amount = "0";
            $total_receivable = "0";
            $total_delivered = "0";
            $total_rejected = "0";
            foreach ($orders as $key) {
                $userid = $key['userid'];
                $name = $this->db->query("select name from tbl_user where id ='$userid'")->row_array()['name'];
                $order_id = $key['id'];
                $rider_id = $key['rider_id'];
                $area_id = $key['area_id'];
                $city_id = $key['city_id'];
                $zone_id = $key['zone_id'];

                $detail = $this->db->query("SELECT * FROM tbl_place_order_detail where order_id= '$order_id'")->result_array();
                $rider_name = $this->db->query("SELECT rider_name FROM tbl_rider_coding where id= '$rider_id'")->row_array()['rider_name'];
                $zone_id = $this->db->query("SELECT zone_id FROM `tbl_zone_detail` where id='$area_id'")->row_array()['zone_id'];
                $zone_name = $this->db->query("SELECT zone_name FROM tbl_zone where id= '$zone_id'")->row_array()['zone_name'];
                $city_name = $this->db->query("SELECT city_name FROM tbl_city where city_id= '$city_id'")->row_array()['city_name'];
                ?>
                <tr class="even_frm_top" id="row">
                    <td width="10%" align="left" style="cursor: pointer; text-decoration: underline;"><img src="<?php echo SURL ?>assets/images/reports/plus.png" id="<?php echo $order_id . "_" . $donotremove; ?>" onclick="toggle('<?php echo $order_id . "_" . $donotremove; ?>');" /> &nbsp;
                        <?php echo $order_id; ?>
                        <?php $csv_outputs .= trim($order_id) . ","; ?>         <?php $csv_output .= trim($order_id) . "\t"; ?>
                    </td>
                    <td>
                        <?php echo $key['deliveryType']; ?>         <?php $csv_outputs .= $key['deliveryType'] . ","; ?>         <?php $csv_output .= $key['deliveryType'] . "\t"; ?>
                    </td>
                    <td>
                        <?php echo ucwords($name); ?>         <?php $csv_outputs .= $name . ","; ?>         <?php $csv_output .= $name . "\t"; ?>
                    </td>
                    <td>
                        <?php echo $key['date']; ?>         <?php $csv_outputs .= $key['date'] . ","; ?>         <?php $csv_output .= $key['date'] . "\t"; ?>
                    </td>
                    <td>
                        <?php echo $key['time']; ?>         <?php $csv_outputs .= $key['time'] . ","; ?>         <?php $csv_output .= $key['time'] . "\t"; ?>
                    </td>
                    <td>
                        <?php echo $key['deliveryStatus']; ?>         <?php $csv_outputs .= $key['deliveryStatus'] . ","; ?>         <?php $csv_output .= $key['deliveryStatus'] . "\t"; ?>
                    </td>
                    <td>
                        <?php echo $zone_name; ?>         <?php $csv_outputs .= $zone_name . ","; ?>         <?php $csv_output .= $zone_name . "\t"; ?>
                    </td>
                    <td>
                        <?php echo $key['address']; ?>         <?php $csv_outputs .= $key['address'] . ","; ?>         <?php $csv_output .= $key['address'] . "\t"; ?>
                    </td>
                    <td>
                        <?php echo $city_name; ?>         <?php $csv_outputs .= $city_name . ","; ?>         <?php $csv_output .= $key['street_no'] . "\t"; ?>
                    </td>
                    <td>
                        <?php echo $key['delivery_charges']; ?>         <?php $csv_outputs .= $key['delivery_charges'] . ","; ?>         <?php $csv_output .= $key['delivery_charges'] . "\t"; ?>
                    </td>
                    <td>
                        <?php echo $key['gst'] . '%'; ?>         <?php $csv_outputs .= $key['gst'] . '%' . ","; ?>         <?php $csv_output .= $key['gst'] . '%' . "\t"; ?>
                    </td>
                </tr>
                <tr>
                    <td colspan="11">
                        <table width="800" align="center" id="<?php echo $order_id; ?>" style="display: none;">
                            <tr class="exist_rec_sb">
                                <td width="10%">
                                    <div align="left">Sr No.<?php $csv_outputs .= 'Sr No.' . ","; ?><?php $csv_output .= 'Sr No.' . "\t"; ?></div>
                                </td>
                                <td width="10%">
                                    <div align="left">Item Name
                                </td><?php $csv_outputs .= 'Item Name' . ","; ?><?php $csv_output .= 'Item Name' . "\t"; ?></div>
                                <td width="10%">
                                    <div>Type
                                </td><?php $csv_outputs .= 'type' . ","; ?><?php $csv_output .= 'type' . "\t"; ?></div>
                                <td width="10%">
                                    <div>Unit Price
                                </td><?php $csv_outputs .= 'Unit Price' . ","; ?><?php $csv_output .= 'Unit Price' . "\t"; ?></div>

                                <td width="10%">
                                    <div align="right">Quantity <?php $csv_outputs .= 'Quantity' . ","; ?><?php $csv_output .= 'Quantity' . "\t"; ?></div>
                                </td>
                                <td width="10%">
                                    <div align="right">GST <?php $csv_outputs .= 'GST' . ","; ?><?php $csv_output .= 'GST' . "\t"; ?></div>
                                </td>
                                <td width="10%">
                                    <div align="right">Amount <?php $csv_outputs .= 'Amount' . ","; ?><?php $csv_output .= 'Amount' . "\t"; ?></div>
                                </td>
                            </tr>
                            <?php
                            $qty_total = 0;
                            $count = 0;

                            $total_qty = 0;
                            $lpg_amount = 0;
                            $ttl_accessories = 0;
                            $ttl_security_charges = 0;
                            $ttl_delivery_charge = 0;
                            $ttl_gst = 0;
                            $ttl_swap_charges = 0;

                            foreach ($detail as $row_detail) {
                                $materialcode = $row_detail['materialcode'];
                                $count++;
                                $itemname = $this->db->query("select * from tblmaterial_coding where materialcode='$materialcode'")->row_array();
                                $catcode = $itemname['catcode'];

                                $saleprice = $row_detail['price'];
                                $security_charges = $row_detail['security_charges'];



                                if ($row_detail['type'] != 'Swap') {
                                    $gst = round(($saleprice * $key['gst']) / 100);
                                    $ttl_gst += $row_detail['quantity'] * $gst;
                                    $total_amount = ($saleprice + $gst) * $row_detail['quantity'];
                                }

                                // Calculate total amount (with security charges if type is New)
                                if ($row_detail['type'] == 'New') {
                                    $total_amount += $security_charges * $row_detail['quantity'];  // Add security charges if type is 'New'
                                    $ttl_security_charges += $security_charges * $row_detail['quantity'];  // Add security charges if type is 'New'
                                }

                                $catcode = $item_detail['catcode'];
                                $area_id = $key['area_id'];
                                $zone_id = $this->db->query("SELECT zone_id FROM `tbl_zone_detail` where id='$area_id'")->row_array()['zone_id'];
                                $delivery = $this->db->query("SELECT standard_range,express_range,night_range FROM `tbl_delivery_charges` where sale_point_id='$sale_point_id' and zone='$zone_id' and e_date<='$date'ORDER BY e_date DESC LIMIT 1")->row_array();

                                if ($key['deliveryType'] == 'Standard') {
                                    $delivery_gst = ($delivery['standard_range'] * $key['gst']) / 100;
                                    $delivery_charges = $delivery['standard_range'] + $delivery_gst;
                                }
                                if ($key['deliveryType'] == 'Express') {
                                    $delivery_gst = ($delivery['express_range'] * $key['gst']) / 100;
                                    $delivery_charges = $delivery['express_range'] + $delivery_gst;
                                }
                                if ($key['deliveryType'] == 'Night') {
                                    $delivery_gst = ($delivery['night_range'] * $key['gst']) / 100;
                                    $delivery_charges = $delivery['night_range'] + $delivery_gst;
                                }

                                if ($catcode == 1) {
                                    if ($row_detail['type'] != 'Swap') {
                                        $ttl_delivery_charge += (int) $row_detail['quantity'] * (float) $delivery_charges;
                                        $lpg_amount += $row_detail['quantity'] * $saleprice;
                                    }
                                } else {
                                    $ttl_accessories += $row_detail['quantity'] * $saleprice;
                                }
                                $total_qty += $row_detail['quantity'];

                                if ($row_detail['type'] == 'Swap') {
                                    $ttl_swap_charges += $row_detail['swap_charges'] * $row_detail['quantity'];  // Add security charges if type is 'New'
                                    $total_amount = $row_detail['swap_charges'] * $row_detail['quantity'];
                                }


                                $brand_name = $this->db->query("SELECT brand_name FROM tbl_brand WHERE brand_id = '$row_detail[cylinder_brand]'")->row_array()['brand_name'];

                                // $tbl_acode = $this->db->query("select short_code,aname from tblacode where acode='$itemcode'")->row_array();
                                print '<tr  class="even_frm_top">
                                    <td width="10%" align="left">';
                                echo $count;
                                $csv_output .= trim($count) . "\t";
                                $csv_outputs .= trim($count) . ",";
                                print '</td>';

                                print '<td width="10%" align="left">';
                                echo $itemname['itemname'];
                                $csv_output .= trim($itemname['itemname']) . "\t";
                                $csv_outputs .= trim($itemname['itemname']) . ",";
                                print '</td>';

                                print '<td width="10%">';
                                echo $row_detail['type'];
                                $csv_output .= trim($row_detail['type']) . "\t";
                                $csv_outputs .= trim($row_detail['type']) . ",";
                                print '</td>';

                                print '<td width="10%">';
                                if ($row_detail['type'] == 'New') {
                                    print '<b>LPG Price:</b> <span class="saleprice">' . number_format($saleprice) . '</span><br><b>Security Charges:</b> <span class="securitycharges">' . number_format($security_charges) . '</span>';
                                } elseif ($row_detail['type'] == 'Swap') {
                                    print '<b>Cylinder Brand:</b> <span>' . $brand_name . '</span>
                                                            <br>
                                                            <b>Cylinder Condition:</b> <span>' . $row_detail['cylinder_condition'] . '</span>
                                                            <br>
                                                            <b>Swap Credits:</b> <span class="swapcharges">' . number_format($row_detail['swap_charges']) . '</span>';
                                } elseif ($row_detail['type'] == 'Refill') {
                                    print '<b>LPG Price:</b> <span class="saleprice">' . number_format($saleprice) . '</span>';
                                } elseif ($row_detail['type'] == 'Accessories') {
                                    print '<b>Accessories Price:</b> <span class="saleprice">' . number_format($saleprice) . '</span>';
                                }
                                // if ($row_detail['type'] != 'Swap') {
                                //     print '<br><b>GST:</b> <span>' . number_format($gst) . '</span><input type="hidden" value="' . round($gst) . '" class="gst">';
                                // }
                                print '</td>';




                                print ' <td width="10%" align="right">';
                                $qty_total += $row_detail['quantity'];
                                echo $row_detail['quantity'];
                                $csv_output .= trim($row_detail['quantity']) . "\t";
                                $csv_outputs .= trim($row_detail['quantity']) . ",";
                                print '</td>';
                                
                                print ' <td width="10%" align="right">';
                                //$qty_total += $row_detail['quantity'];
                                echo number_format($gst);
                                $csv_output .= trim(number_format($gst)) . "\t";
                                $csv_outputs .= trim(number_format($gst)) . ",";
                                print '</td>';
                                
                                print ' <td width="10%" align="right">';
                                $qty_total += number_format($total_amount);
                                echo number_format($total_amount);
                                $csv_output .= trim(number_format($total_amount)) . "\t";
                                $csv_outputs .= trim(number_format($total_amount)) . ",";
                                print '</td>';



                                print '</tr>';
                            }
                            print '<tr  class="exist_rec_sb">
                                <td width="10%" align="center"></td>
                                <td width="10%" align="center"></td>
                                <td width="10%" align="center"></td>
                                <td width="10%" align="center"></td>
                                <td width="10%" align="center"></td>
                                <td width="10%" align="center">Grand Total</td>
                                ';
                            $csv_output .= "\tGrand Total\t\t";
                            $csv_outputs .= ",,Grand Total,";
                            print ' <td width="10%" align="right">';
                            $grand_total = $lpg_amount + $ttl_accessories + $ttl_security_charges + $key['delivery_charges'] + $ttl_gst + $ttl_swap_charges;
                            echo number_format($grand_total);
                            $csv_output .= $grand_total . "\t\t";
                            $csv_outputs .= $grand_total . ",,";
                            print '</td>';
                            print '</tr>';

                            ?>
                        </table>
                        <?php
                        if ($key['deliveryStatus'] == "Delivered") {
                            $total_receivable += $grand_total;
                            $total_delivered++;
                        }
                        if ($key['deliveryStatus'] == "Reject") {
                            $total_rejected++;
                        }
                        $trip_amount += $grand_total;
            }
            print "<input type='hidden' data-trip_id = '" . $trip_id . "' data-total_receivable = '" . $total_receivable . "' data-trip_amount = '" . $trip_amount . "' data-total_delivered = '" . $total_delivered . "' data-total_rejected = '" . $total_rejected . "'>";
            ?>
                    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

                    <script type="text/javascript">
                        var sourceInput = $('input[data-trip_id="<?= $trip_id; ?>"][data-total_receivable][data-trip_amount][data-total_delivered][data-total_rejected]');

                        var trip_amount = sourceInput.data('trip_amount');
                        var totalReceivable = sourceInput.data('total_receivable');
                        var totalDelivered = sourceInput.data('total_delivered');
                        var totalRejected = sourceInput.data('total_rejected');
                        // $('input[data-trip_id="<?= $trip_id; ?>"][data-total_receivable]').attr('data-total_receivable', totalReceivable);
                        // $('input[data-trip_id="<?= $trip_id; ?>"][data-total_delivered]').attr('data-total_delivered', totalDelivered);
                        // $('input[data-trip_id="<?= $trip_id; ?>"][data-total_rejected]').attr('data-total_rejected', totalRejected);
                        $('#trip_amount_<?= $trip_id; ?>').html(trip_amount);
                        $('#total_receivable_<?= $trip_id; ?>').html(totalReceivable);
                        $('#total_delivered_<?= $trip_id; ?>').html(totalDelivered);
                        $('#total_rejected_<?= $trip_id; ?>').html(totalRejected);
                    </script>
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