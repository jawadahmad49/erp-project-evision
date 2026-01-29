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
  }

  @page {
    margin: 0;
  }

  @media print {
    .noPrint {
      display: none;
    }

    table.meta1 {
      float: left;
      width: 60%;
      margin-bottom: 4%;
    }
  }
</style>

<body>
  <?php
  $order = $this->db->query("SELECT * FROM `tbl_place_order` where id ='$id' ")->row_array();
  $order_detail = $this->db->query("SELECT * FROM `tbl_place_order_detail` where order_id ='$id' ")->result_array();


  $user_id = $order['userid'];

  $customer  = $this->db->query("SELECT * FROM `tbl_user` where id ='$user_id' ")->row_array(); ?>

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
                    <td><span content><?php echo $customer['name'] ?></span></td>
                  </tr>
                  <tr>
                    <th><span content>Company Name</span></th>
                    <td><span content><?php echo $customer['company_name'] ?></span></td>
                  </tr>
                  <tr>
                    <th><span content>Phone No.</span></th>
                    <td><span content><?php echo $customer['phone'] ?></span></td>
                  </tr>
                  <tr>
                    <th><span content>E-Mail</span></th>
                    <td><span id="prefix"></span><span content><?php echo $customer['email'] ?></span></td>
                  </tr>
                  <tr>
                    <th><span content>Address</span></th>
                    <td><span id="prefix"></span><span content><?php echo $item['address'] ?></span></td>
                  </tr>
                </table>
                <table class="meta">
                  <tr>
                    <th><span content>Invoice #</span></th>
                    <td><span content><?= $id ?></span></td>
                  </tr>
                  <tr>
                    <th><span content>Order Date</span></th>
                    <td><span content><?php echo $item['date'] ?></span></td>
                  </tr>
                </table>
                <table class="inventory">
                  <thead>
                    <tr>
                      <th><span content>Sr No</span></th>
                      <th><span content>Item</span></th>
                      <th><span content>Quantity</span></th>
                      <th><span content>Type</span></th>
                    </tr>
                  </thead>
                  <tbody>
                    <tr>
                      <td><span content><?= $count ?></span></td>
                      <td><span content><?= $material['itemname'] ?></span></td>
                      <td><span content><?= $value['quantity'] ?></span></td>
                      <!-- <td><span data-prefix></span><span content><?php echo number_format($item['rate']) ?></span></td> -->
                      <td><span data-prefix></span><span content><?= $value['type'] ?></span></td>
                    </tr>
                  </tbody>
                </table>
                <table class="balance">
                  <tr>
                    <th><span content>Total</span></th>
                    <td><span data-prefix></span><span content><?php echo number_format($amount) ?></span></td>
                  </tr>
                </table>
              </article>
              <div class="col-xs-12 center" style="margin-left: 40%;">
                <!-- <a class="btn btn-info btnsubmit" href="<?php echo SURL . "Order_dispatch/confirm/$issuenos" ?>">
                  <i class="ace-icon fa fa-check bigger-110"></i>
                  Dispatch
                </a> -->
                <a class="btn btn-info btnsubmit noPrint" onclick="window.print();">
                  Print
                </a>
                <a class="btn btn-info btnsubmit noPrint" onclick="submit('<?php echo $issuenos ?>')">
                  <i class="ace-icon fa fa-check bigger-110"></i>
                  Dispatch
                </a>
                <a class="btn btn-info btnsubmit noPrint" href="<?php echo SURL . "Order_dispatch/cancel/$issuenos" ?>">
                  <i class="ace-icon fa fa-check bigger-110"></i>
                  Cancel
                </a>
                <a class="btn btn-info btnsubmit noPrint" href="<?php echo SURL . "Order_dispatch" ?>">
                  Back
                </a>
              </div>

            </div>

            <!-- PAGE CONTENT ENDS -->
          </div><!-- /.col -->
        </div><!-- /.row -->
      </div><!-- /.page-content -->
    </div>
  </div><!-- /.main-content -->
  <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
  <script type="text/javascript">
    function submit(issuenos) {
      $.ajax({
        url: "<?php echo SURL . "Order_dispatch/confirm"; ?>",
        cache: false,
        type: "POST",
        data: {
          issuenos: issuenos
        },
        success: function(response) {
          if (response != '') {
            alert("Order Dispatched Successfully !");
            window.location.href = '<?php echo SURL . "Order_dispatch/"; ?>'
          }
        }
      });
      // body...
    }
  </script>
</body>

</html>