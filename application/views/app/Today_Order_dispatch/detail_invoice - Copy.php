<html>

<head>
  <meta charset="utf-8">
  <title>Invoice</title>
  <link rel="stylesheet" href="style.css">
  <link rel="stylesheet" href="<?php echo SURL ?>assets/css/custom_bootstrap.css" />
</head>
<style>
  /* reset */

  * {
    border: 0;
    box-sizing: content-box;
    color: inherit;
    font-family: inherit;
    font-size: inherit;
    font-style: inherit;
    font-weight: inherit;
    line-height: inherit;
    list-style: none;
    margin: 0;
    padding: 0;
    text-decoration: none;
    vertical-align: top;
  }

  *[content]:hover,
  *[content]:focus,
  td:hover *[content],
  td:focus *[content],
  img.hover {
    background: #def;
    box-shadow: 0 0 1em 0.5em #def;
  }

  /* content editable */

  /* *[content] {
    border-radius: 0.25em;
    min-width: 1em;
    outline: 0;
  }

  *[content] {
    cursor: pointer;
  }

  *[content]:hover,
  *[content]:focus,
  td:hover *[content],
  td:focus *[content],
  img.hover {
    background: #def;
    box-shadow: 0 0 1em 0.5em #def;
  }

  span[content] {
    display: inline-block;
  } */

  /* heading */

  h1 {
    font: bold 100% sans-serif;
    letter-spacing: 0.5em;
    text-align: center;
    text-transform: uppercase;
  }

  /* table */

  table {
    font-size: 75%;
    table-layout: fixed;
    width: 100%;
  }

  table {
    border-collapse: separate;
    border-spacing: 2px;
  }

  th,
  td {
    border-width: 1px;
    padding: 0.5em;
    position: relative;
    text-align: left;
  }

  th,
  td {
    border-radius: 0.25em;
    border-style: solid;
  }

  th {
    background: #eee;
    border-color: #bbb;
  }

  td {
    border-color: #ddd;
  }

  /* page */

  html {
    font: 20px "Open Sans", sans-serif;
    font-weight: bold;
    overflow: auto;
    padding: 0.1in;
  }

  html {
    background: #999;
    cursor: default;
  }

  body {
    box-sizing: border-box;
    height: 11in;
    margin: 0 auto;
    overflow: hidden;
    padding: 0.5in;
    width: 90%;
    font-size: 16px;
  }

  body {
    background: #fff;
    border-radius: 1px;
    box-shadow: 0 0 1in -0.25in rgba(0, 0, 0, 0.5);
  }

  /* header */

  header {
    margin: 0 0 3em;
  }

  header:after {
    clear: both;
    content: "";
    display: table;
  }

  header h1 {
    background: #000;
    border-radius: 0.25em;
    color: #fff;
    margin: 0 0 1em;
    padding: 0.5em 0;
  }

  header address {
    float: left;
    font-size: 75%;
    font-style: normal;
    line-height: 1.25;
    margin: 0 1em 1em 0;
  }

  header address p {
    margin: 0 0 0.25em;
  }

  header span,
  header img {
    display: block;
    float: right;
  }

  header span {
    margin: 0 0 1em 1em;
    max-height: 25%;
    max-width: 60%;
    position: relative;
  }

  header img {
    max-height: 100%;
    max-width: 100%;
  }

  header input {
    cursor: pointer;
    /* -ms-filter: "progid:DXImageTransform.Microsoft.Alpha(Opacity=0)"; */
    height: 100%;
    left: 0;
    opacity: 0;
    position: absolute;
    top: 0;
    width: 100%;
  }

  /* article */

  article,
  article address,
  table.meta,
  table.inventory {
    margin: 0 0 3em;
  }

  article:after {
    clear: both;
    content: "";
    display: table;
  }

  article h1 {
    clip: rect(0 0 0 0);
    position: absolute;
  }

  article address {
    float: left;
    font-size: 125%;
    font-weight: bold;
  }

  /* table meta & balance */

  table.meta,
  table.balance {
    float: right;
    width: 36%;
  }

  table.meta1 {
    float: left;
    width: 36%;
    margin-bottom: 4%;
  }

  table.meta:after,
  table.balance:after {
    clear: both;
    content: "";
    display: table;
  }

  /* table meta */

  table.meta th {
    width: 40%;
  }

  table.meta td {
    width: 60%;
  }

  /* table items */

  table.inventory {
    clear: both;
    width: 100%;
  }

  table.inventory th {
    font-weight: bold;
    text-align: center;
  }

  table.inventory td:nth-child(1) {
    width: 26%;
  }

  table.inventory td:nth-child(2) {
    /* text-align: right; */
    width: 38%;
  }

  table.inventory td:nth-child(3) {
    text-align: right;
    width: 12%;
  }

  table.inventory td:nth-child(4) {
    text-align: right;
    width: 12%;
  }

  table.inventory td:nth-child(5) {
    text-align: right;
    width: 12%;
  }

  /* table balance */

  table.balance th,
  table.balance td {
    width: 50%;
  }

  table.balance td {
    text-align: right;
  }

  /* aside */

  aside h1 {
    border: none;
    border-width: 0 0 1px;
    margin: 0 0 1em;
  }

  aside h1 {
    border-color: #999;
    border-bottom-style: solid;
  }

  /* javascript */

  @media print {
    * {
      -webkit-print-color-adjust: exact;
    }

    html {
      background: none;
      padding: 0;
    }

    body {
      box-shadow: none;
      margin: 0;
    }

    span:empty {
      display: none;
    }

    table.meta1 {
      float: left;
      width: 60%;
      margin-bottom: 4%;
    }
  }

  @page {
    margin: 0;
  }
</style>

<body style="height: auto;">
  <?php

  $order_detail = $this->db->query("SELECT userid,address,area_id,area_name,city_id,deliveryType,date,sale_point_id,deliveryStatus,gst,reject_reason FROM `tbl_place_order` where id='$id'")->row_array();
  $userid = $order_detail['userid'];
  $address = $order_detail['address'];
  $area_name = $order_detail['area_name'];
  $area_id = $order_detail['area_id'];
  $city_id = $order_detail['city_id'];
  $date = $order_detail['date'];
  $sale_point_id = $order_detail['sale_point_id'];
  $zone_id = $this->db->query("SELECT zone_id FROM `tbl_zone_detail` where id='$area_id'")->row_array()['zone_id'];
  $zone_name = $this->db->query("SELECT zone_name FROM `tbl_zone` where id='$zone_id'")->row_array()['zone_name'];

  $user_detail = $this->db->query("SELECT * FROM `tbl_user` where id='$userid'")->row_array();
  $city_name = $this->db->query("SELECT city_name FROM `tbl_city` where city_id='$city_id'")->row_array()['city_name'];
  if ($user_detail['dp']) {
    $dp = $user_detail['dp'];
  } else {
    $dp = "default.JPG";
  }
  $delivery = $this->db->query("SELECT standard_range,express_range,night_range FROM `tbl_delivery_charges` where sale_point_id='$sale_point_id' and zone='$zone_id' and e_date<='$date'ORDER BY e_date DESC LIMIT 1")->row_array();
  $gst = $order_detail['gst'];
  if ($order_detail['deliveryType'] == 'Standard') {
    $delivery_gst = ($delivery['standard_range'] * $gst) / 100;
    $delivery_charges = $delivery['standard_range'] + $delivery_gst;
  }
  if ($order_detail['deliveryType'] == 'Express') {
    $delivery_gst = ($delivery['express_range'] * $gst) / 100;
    $delivery_charges = $delivery['express_range'] + $delivery_gst;
  }
  if ($order_detail['deliveryType'] == 'Night') {
    $delivery_gst = ($delivery['night_range'] * $gst) / 100;
    $delivery_charges = $delivery['night_range'] + $delivery_gst;
  }
  ?>
  <div class="main-container ace-save-state col-xs-12" id="main-container">


    <div class="main-content">
      <div class="main-content-inner">


        <div class="page-content">

          <div class="row">
            <div class="col-xs-12">
              <!-- PAGE CONTENT BEGINS -->


              <header>
                <h1>ORDER DETAIL</h1>
              </header>
              <article>
                <table class="meta1">
                  <tr>
                    <th><span content>Customer Name</span></th>
                    <td><span content><?php echo $user_detail['name']  ?></span></td>
                  </tr>

                  <tr>
                    <th><span content>Phone No.</span></th>
                    <td><span content><?php echo $user_detail['phone'] ?></span></td>
                  </tr>
                  <tr>
                    <th><span content>E-Mail</span></th>
                    <td><span id="prefix"></span><span content><?php echo $user_detail['email'] ?></span></td>
                  </tr>
                  <tr>
                    <th><span content>City</span></th>
                    <td><span id="prefix"></span><span content><?php echo $city_name ?></span></td>
                  </tr>
                  <tr>
                    <th><span content>Area</span></th>
                    <td><span id="prefix"></span><span content><?php echo $zone_name ?></span></td>
                  </tr>
                  <tr>
                    <th><span content>Delivery Type</span></th>
                    <td><span id="prefix"></span><span content><?php echo $order_detail['deliveryType'] ?></span></td>
                  </tr>
                  <tr>
                    <th><span content>Delivery Address</span></th>
                    <td><span id="prefix"></span><span content><?php echo $address ?></span></td>
                  </tr>
                  <tr>
                    <th><span content>Order Status</span></th>
                    <td><span id="prefix"></span><span content><?php echo $order_detail['deliveryStatus'] ?></span></td>
                  </tr>
                  <?php if ($order_detail['reject_reason']) { ?>
                    <tr>
                      <th><span content>Reject Reason</span></th>
                      <td><span id="prefix"></span><span content><?php echo $order_detail['reject_reason'] ?></span></td>
                    </tr>
                  <?php } ?>
                </table>
                <table class="meta">
                  <tr>
                    <th><span content>Order #</span></th>
                    <td><span content><?= $id ?></span></td>
                  </tr>
                  <tr>
                    <th><span content>Order Date</span></th>
                    <td><span content><?= $order_detail['date'] ?></span></td>
                  </tr>
                  <tr style="border: none;">
                    <td style="border: none;"><img width="268" height="210" id="target" src="<?php echo IMG . 'profile/' . $dp; ?>" style="margin-left: 0%;"></td>
                  </tr>
                </table>
                <table class="inventory">
                  <thead>
                    <tr>
                      <th style="width: 5%;">Sr No</th>
                      <th>Item</th>
                      <th style="width: 8%;">Type</th>
                      <th>Unit Price</th>
                      <th style="width: 8%;">Quantity</th>
                      <th style="width: 8%;">Amount</th>
                    </tr>
                  </thead>
                  <tbody>
                    <?php
                    // Fetch order details
                    $order_list_details = $this->db->query("SELECT * FROM tbl_place_order_detail WHERE order_id = '$id'")->result_array();
                    $order_status = $order_detail['deliveryStatus'];

                    $count = 0;
                    $total_qty = 0;
                    $lpg_amount = 0;
                    $ttl_accessories = 0;
                    $ttl_security_charges = 0;
                    $ttl_delivery_charge = 0;
                    $ttl_gst = 0;

                    foreach ($order_list_details as $value) {
                      $count++;
                      $materialcode = $value['materialcode'];

                      // Fetch item details
                      $item_detail = $this->db->query("SELECT itemname, CONCAT('https://lpginsight.com/GasablePK/assets/images/items/', image_path) AS image_path, catcode, itemnameint, security_price FROM tblmaterial_coding WHERE materialcode = '$materialcode'")->row_array();
                      $date = $order_detail['date'];

                      // Fetch sale price and security charges
                      // $price = $this->db->query("SELECT saleprice, security_charges FROM tbl_price_fluctuation WHERE edate <= '$date' and item_id='$materialcode' and sale_point_id='$sale_point_id' order by id desc")->row_array();
                      $saleprice = $value['price'];
                      $security_charges = $value['security_charges'];


                      if ($value['type'] != 'Swap') {
                        $gst = ($saleprice * $order_detail['gst']) / 100;
                        $ttl_gst += $value['quantity'] * $gst;
                        $total_amount = ($saleprice + $gst) * $value['quantity'];
                      }

                      // Calculate total amount (with security charges if type is New)
                      if ($value['type'] == 'New') {
                        $total_amount += $security_charges * $value['quantity'];  // Add security charges if type is 'New'
                        $ttl_security_charges += $security_charges * $value['quantity'];  // Add security charges if type is 'New'
                      }

                      $catcode = $item_detail['catcode'];
                      $area_id = $order_detail['area_id'];
                      $zone_id = $this->db->query("SELECT zone_id FROM `tbl_zone_detail` where id='$area_id'")->row_array()['zone_id'];
                      $delivery = $this->db->query("SELECT standard_range,express_range,night_range FROM `tbl_delivery_charges` where sale_point_id='$sale_point_id' and zone='$zone_id' and e_date<='$date'ORDER BY e_date DESC LIMIT 1")->row_array();

                      if ($order_detail['deliveryType'] == 'Standard') {
                        $delivery_gst = ($delivery['standard_range'] * $order_detail['gst']) / 100;
                        $delivery_charges = $delivery['standard_range'] + $delivery_gst;
                      }
                      if ($order_detail['deliveryType'] == 'Express') {
                        $delivery_gst = ($delivery['express_range'] * $order_detail['gst']) / 100;
                        $delivery_charges = $delivery['express_range'] + $delivery_gst;
                      }
                      if ($order_detail['deliveryType'] == 'Night') {
                        $delivery_gst = ($delivery['night_range'] * $order_detail['gst']) / 100;
                        $delivery_charges = $delivery['night_range'] + $delivery_gst;
                      }

                      if ($catcode == 1) {
                        if ($value['type'] != 'Swap') {
                          $ttl_delivery_charge += (int)$value['quantity'] * (float)$delivery_charges;
                          $lpg_amount += $value['quantity'] * $saleprice;
                        }
                      } else {
                        $ttl_accessories += $value['quantity'] * $saleprice;
                      }
                      $total_qty += $value['quantity'];

                      if (
                        $value['type'] == 'Swap'
                      ) {
                        $ttl_swap_charges += $value['swap_charges'] * $value['quantity'];  // Add security charges if type is 'New'
                        $total_amount = $value['swap_charges'] * $value['quantity'];
                      }
                      $brand_name = $this->db->query("SELECT brand_name FROM tbl_brand WHERE brand_id = '$value[cylinder_brand]'")->row_array()['brand_name'];

                    ?>
                      <tr id="row_<?php echo $value['id'] ?>">
                        <td><?php echo $count ?></td>
                        <td>
                          <img src="<?php echo $item_detail['image_path'] ?>" alt="Item Image" width="50" height="50" />
                          <?php echo $item_detail['itemname'] ?>
                        </td>
                        <td style="text-align-last: start;"><?php echo $value['type'] ?></td>
                        <td style="text-align-last: start;">
                          <?php if ($value['type'] == 'New') { ?>
                            <b>LPG Price:</b> <span class="saleprice"><?php echo number_format($saleprice) ?></span>
                            <br>
                            <b>Security Charges:</b> <span class="securitycharges"><?php echo number_format($security_charges) ?></span>
                          <?php } elseif ($value['type'] == 'Refill') { ?>
                            <b>LPG Price:</b> <span class="saleprice"><?php echo number_format($saleprice) ?></span>
                          <?php } elseif ($value['type'] == 'Swap') { ?>
                            <b>Cylinder Brand:</b> <span class="saleprice"><?php echo $brand_name ?></span>
                            <br>
                            <b>Condition:</b> <span class="securitycharges"><?php echo $value['cylinder_condition'] ?></span>
                            <br>
                            <b>Swap Credits:</b> <span class="securitycharges"><?php echo number_format($value['swap_charges']) ?></span>
                          <?php } elseif ($value['type'] == 'Accessories') { ?>
                            <b>Accessories Price:</b> <span class="saleprice"><?php echo number_format($saleprice) ?></span>
                          <?php }
                          if ($value['type'] != 'Swap') { ?>
                            <br>
                            <b>GST:</b> <span><?php echo number_format($gst) ?></span>
                          <?php } ?>
                        </td>
                        <td style="text-align-last: end;">
                          <?php echo $value['quantity'] ?>
                        </td>
                        <td style="text-align-last: end;">
                          <?php echo number_format($total_amount) ?>
                        </td>
                      </tr>
                    <?php
                    }
                    ?>
                  </tbody>
                </table>
                <table class="balance">
                  <tr>
                    <th><span content>Total Quantity </span></th>
                    <td><span data-prefix></span><span content><?php echo number_format($total_qty) ?></span></td>
                  </tr>
                  <tr>
                    <th><span content>LPG Amount </span></th>
                    <td><span data-prefix></span><span content><?php echo number_format($lpg_amount) ?></span></td>
                  </tr>
                  <tr>
                    <th><span content>GST Percentage </span></th>
                    <td><span data-prefix></span><span content><?php echo number_format($order_detail['gst']) . " %" ?></span></td>
                  </tr>
                  <tr>
                    <th><span content>GST Amount </span></th>
                    <td><span data-prefix></span><span content><?php echo number_format($ttl_gst) ?></span></td>
                  </tr>
                  <tr>
                    <th><span content>Total Security Charges </span></th>
                    <td><span data-prefix></span><span content><?php echo number_format($ttl_security_charges) ?></span></td>
                  </tr>
                  <tr>
                    <th><span content>Accessories Amount </span></th>
                    <td><span data-prefix></span><span content><?php echo number_format($ttl_accessories) ?></span></td>
                  </tr>
                  <tr>
                    <th><span content>Total Delivery Charges </span></th>
                    <td><span data-prefix></span><span content><?php echo number_format($ttl_delivery_charge) ?></span></td>
                  </tr>
                  <tr>
                    <th><span content>Total Swap Credits</span></th>
                    <td><span data-prefix></span><span content><?php echo number_format($ttl_swap_charges) ?></span></td>
                  </tr>
                  <tr>
                    <th><span content>Grand Total </span></th>
                    <td><span data-prefix></span><span content><?php echo number_format($lpg_amount + $ttl_accessories + $ttl_security_charges + $ttl_delivery_charge + $ttl_gst + $ttl_swap_charges) ?></span></td>
                  </tr>
                </table>
              </article>
            </div>
            <!-- PAGE CONTENT ENDS -->
          </div><!-- /.col -->
        </div><!-- /.row -->
      </div><!-- /.page-content -->
    </div>
  </div><!-- /.main-content -->
  
</body>

</html>