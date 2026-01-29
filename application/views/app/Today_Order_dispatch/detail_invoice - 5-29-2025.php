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
    letter-spacing: 0.2em;
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
    border-collapse: collapse;
    border-spacing: 0px;
  }

  th,
  td {
    border-width: 1px;
    padding: 0.5em;
    /* position: relative; */
    text-align: left;
  }

  th,
  td {
    border-radius: 0.25em;
    border-style: solid;
  }

  th {
    background: #ccc;
    border-color: #000000;
  }

  td {
    border-color: #000000;
    font-weight: 400;
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
    padding: 2rem;
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
    margin: 0;
  }

  header:after {
    clear: both;
    content: "";
    display: table;
  }

  header h1 {
    background: #fff !important;
    border-radius: 0.25em;
    color: #000;
    margin: 0 0 1em;
    padding: 0.5em 0;
    font-size: x-large;
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
    /* margin: 0 0 1rem; */
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
    width: 50%;
    margin-bottom: 1rem;
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
      width: 100%;
    }

    span:empty {
      display: none;
    }

    table.meta1 {
      float: left;
      width: 60%;
      margin-bottom: 1rem;
    }
  }

  @page {
    margin: 0;
  }
</style>

<body style="height: auto;">
  <?php
  $company = $this->db->query("select * from tbl_company where id=1")->row_array();

  $order_detail = $this->db->query("SELECT per_delivery_charges,delivery_gst,delivery_charges,userid,address,area_id,area_name,city_id,deliveryType,date,sale_point_id,deliveryStatus,gst,reject_reason FROM `tbl_place_order` where id='$id'")->row_array();
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
  // if ($order_detail['deliveryType'] == 'Standard') {
  //   $delivery_gst = ($delivery['standard_range'] * $gst) / 100;
  //   $delivery_charges = $delivery['standard_range'] + $delivery_gst;
  // }
  // if ($order_detail['deliveryType'] == 'Express') {
  //   $delivery_gst = ($delivery['express_range'] * $gst) / 100;
  //   $delivery_charges = $delivery['express_range'] + $delivery_gst;
  // }
  // if ($order_detail['deliveryType'] == 'Night') {
  //   $delivery_gst = ($delivery['night_range'] * $gst) / 100;
  //   $delivery_charges = $delivery['night_range'] + $delivery_gst;
  // }
  ?>
  <div class="main-container ace-save-state col-xs-12" id="main-container" style="border: 1px solid;">


    <div class="main-content">
      <div class="main-content-inner">


        <div class="page-content">

          <div class="row">
            <div class="col-xs-12">
              <!-- PAGE CONTENT BEGINS -->

              <header style="display: flex; align-items: center; justify-content: space-between;">
                <?php if (isset($company['logo']) && $company['logo'] != '') { ?>
                  <img style="padding: 0.5rem;" width="100" height="80" id="logo_id" src="<?php echo IMG . 'company/' . $company['logo']; ?>">
                <?php } ?>
                <h1>SALE TAX INVOICE</h1>
              </header>
              <article>
                <table class="meta1 table table-striped table-bordered">
                  <tr>
                    <th><span content>Customer Name</span></th>
                    <td><span content><?php echo $user_detail['name'] ?></span></td>
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
                    <th><span content>Invoice #</span></th>
                    <td><span content><?= $id ?></span></td>
                  </tr>
                  <tr>
                    <th><span content>Order Date</span></th>
                    <td><span content><?= $order_detail['date'] ?></span></td>
                  </tr>
                  <tr>
                    <th><span content>NTN #</span></th>
                    <td><span content><?= $company['ntn'] ?></span></td>
                  </tr>
                  <tr>
                    <th><span content>GST #</span></th>
                    <td><span content><?= $company['gst'] ?></span></td>
                  </tr>
                  <!-- <tr style="border: none;">
                    <td style="border: none;"><img width="268" height="210" id="target" src="<?php echo IMG . 'profile/' . $dp; ?>" style="margin-left: 0%;"></td>
                  </tr> -->
                </table>
                <table class="inventory">
                  <thead>
                    <tr>
                      <th style="width: 20%;">Item</th>
                      <th style="width: 8%;">Qty</th>
                      <th style="width: 10%;">Rate</th>
                      <th style="width: 10%;">Type</th>
                      <th style="width: 10%;">Security</th>
                      <th style="width: 8%;">GST</th>
                      <th style="width: 8%;">Amount Incl.GST</th>
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
                    $ttl_swap_charges = 0;

                    foreach ($order_list_details as $value) {
                      $count++;
                      $materialcode = $value['materialcode'];

                      $item_detail = $this->db->query("SELECT itemname, CONCAT('https://lpginsight.com/GasablePK/assets/images/items/', image_path) AS image_path, catcode, itemnameint, security_price FROM tblmaterial_coding WHERE materialcode = '$materialcode'")->row_array();
                      $date = $order_detail['date'];

                      $saleprice = $value['price'];
                      $security_charges = $value['security_charges'];

                      if ($value['type'] != 'Swap') {
                        $gst = round(($saleprice * $order_detail['gst']) / 100);
                        $ttl_gst += $value['quantity'] * $gst;
                        $total_amount = ($saleprice + $gst) * $value['quantity'];
                      }

                      if ($value['type'] == 'New') {
                        $total_amount += $security_charges * $value['quantity'];  // Add security charges if type is 'New'
                        $ttl_security_charges += $security_charges * $value['quantity'];  // Add security charges if type is 'New'
                      }

                      $catcode = $item_detail['catcode'];
                      $area_id = $order_detail['area_id'];


                      if ($catcode == 1) {
                        if ($value['type'] != 'Swap') {
                          $ttl_delivery_charge += (int) $value['quantity'] * (float) $delivery_charges;
                          $lpg_amount += $value['quantity'] * $saleprice;
                        }
                      } else {
                        $ttl_accessories += $value['quantity'] * $saleprice;
                      }
                      $total_qty += $value['quantity'];

                      if ($value['type'] == 'Swap') {
                        $ttl_swap_charges += $value['swap_charges'] * $value['quantity'];  // Add security charges if type is 'New'
                        $total_amount = $value['swap_charges'] * $value['quantity'];
                      }
                      $brand_name = $this->db->query("SELECT brand_name FROM tbl_brand WHERE brand_id = '$value[cylinder_brand]'")->row_array()['brand_name'];

                      ?>
                      <tr id="row_<?php echo $value['id'] ?>">
                        <td>
                          <?php echo explode("-", $item_detail['itemname'])[0]; ?>
                        </td>
                        <td style="text-align-last: end;">
                          <?php echo $value['quantity'] ?>
                        </td>
                        <td style="text-align-last: end;">
                          <?php if ($value['type'] == 'New') { ?>
                            <span class="saleprice"><?php echo number_format($saleprice) ?></span>
                          <?php } elseif ($value['type'] == 'Refill') { ?>
                            <span class="saleprice"><?php echo number_format($saleprice) ?></span>
                          <?php } elseif ($value['type'] == 'Swap') { ?>
                            <?php echo number_format($value['swap_charges']) ?>
                          <?php } elseif ($value['type'] == 'Accessories') { ?>
                            <b></b> <span class="saleprice"><?php echo number_format($saleprice) ?></span>
                          <?php } ?>
                        </td>
                        <td style="text-align-last: start;"><?php if ($value['type'] !== 'Accessories') {
                          echo $value['type'];
                        } else {
                          echo 'Access';
                        } ?></td>

                        <td style="text-align-last: end;">
                          <?php if ($value['type'] == 'New') { ?>
                            <?php echo number_format($security_charges) ?>
                          <?php } ?>
                        </td>
                        <td style="text-align-last: end;">
                          <?php if ($value['type'] !== 'Swap') { ?>
                            <?php echo number_format($gst) ?>
                          <?php } ?>
                        </td>
                        <td style="text-align-last: end;">
                          <?php echo number_format($total_amount); ?>
                        </td>
                      </tr>
                    <?php } ?>
                    <tr>
                      <th>Delivery Charges</th>
                      <th colspan='2' style="text-align: right;"><?php echo number_format($order_detail['per_delivery_charges']) ?></th>
                      <th colspan='2'></th>
                      <th><?php echo number_format($order_detail['delivery_gst']) . " %" ?>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<?php $delivery_gst_amount = $order_detail['delivery_gst'] * $order_detail['per_delivery_charges'] / 100;
                           echo number_format($delivery_gst_amount); ?></th>
                      <th style="text-align: right;"><?php echo number_format($order_detail['delivery_charges']); ?></th>
                    </tr>
                    <tr>
                      <th>Grand Total</th>
                      <th style="text-align: right;"><?php echo number_format($total_qty) ?></th>
                      <th colspan="2"></th>
                      <th style="text-align: right;"><?php echo number_format($ttl_security_charges); ?></th>
                      <th style="text-align: right;" for="<? echo $delivery_gst_amount . '+' . $ttl_gst; ?>"><?php echo number_format($delivery_gst_amount + $ttl_gst) ?></th>
                      <th style="text-align: right;" ><?php echo number_format($lpg_amount + $ttl_accessories + $ttl_security_charges + $order_detail['delivery_charges'] + $ttl_gst + $ttl_swap_charges); ?></th>
                    </tr>
                  </tbody>
                </table>
                <p style="text-align: right; direction: rtl; line-height: 1; font-family: 'Noto Nastaliq Urdu', 'Arial', sans-serif; padding: 1rem;">
                  ۱۔ سلنڈر وصول کرتے وقت سیل اور نمائندے کی موجودگی میں چیک کر لیں، بعد میں کوئی کلیم قابل قبول نہ ہوگا۔<br>
                  ۲۔ معیاری ریگولیٹر اور پائپ استعمال کریں۔<br>
                  ۳۔ ہر ۳ ماہ بعد چولہے کی سروس کروالیں کیونکہ زنگ اور کاربن کی وجہ سے نالیاں بند اور لیک ہو سکتی ہیں۔<br>
                  ۴۔ سلنڈر ہرگز اُلٹا نہ کریں۔<br>
                  ۵۔ استعمال کے بعد ریگولیٹر بند کر دیں۔<br>
                  ۶۔ لیکیج چیک کرنے کے لیئے صابن ملا پانی استعمال کریں۔<br>
                  ۷۔ سلنڈر اور چولہے کے درمیان کم از کم ۱۰ فٹ کا فاصلہ رکھیں۔<br>
                  ۸۔ سلنڈر ہوادار جگہ پر رکھیں۔<br>
                  ۹۔ بچوں کی پہنچ سے دور رکھیں۔<br>
                  ١٠۔ ایل پی جی سلنڈر کو آگ سے ہرگز گرم نہ کریں، بھاپ اور آگ سے دور رکھیں۔<br><br>

                  کمپنی کے نمائندے کی ہدایت کے مطابق میں نے سلنڈر چیک کر لیا ہے۔ اس میں لیکیج یا خرابی نہیں ہے، اور وزن درست ہے۔<br>
                  نمائندے نے مجھے تمام احتیاطی تدابیر سے آگاہ کر دیا ہے۔ میں ان پر عمل پیرا رہوں گا۔<br><br>

                  ایل پی جی انتہائی آتش گیر مادہ ہے جو آپ کے تحفظ کی ضامن ہے۔<br>
                  او۔ پی۔ آئی گیس پرائیویٹ لمیٹڈ آپ کے کسی بھی نقصان کا ذمہ دار نہیں ہے۔<br><br>

                  بر وقت سپلائی کیلئے ایک دن پہلے ڈیمانڈ دینی ہوگی، اور اس کے ۴۸ گھنٹے کے اندر آپ کو سپلائی ہو جائے گی۔
                </p>

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