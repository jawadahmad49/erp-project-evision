<!DOCTYPE html>
<html lang="en">
<?php
$this->load->view('app/include/head');
$this->load->view('app/include/header'); ?>

<body class="no-skin">
    <div class="main-container ace-save-state" id="main-container">
        <?php $this->load->view('app/include/sidebar');
        ?>
        <div class="main-content">
            <div class="main-content-inner">
                <div class="breadcrumbs ace-save-state" id="breadcrumbs">
                    <ul class="breadcrumb">
                        <li>
                            <i class="ace-icon fa fa-home home-icon"></i>
                            <a href="<?php echo SURL . "Module/app"; ?>">Home</a>
                        </li>
                        <li>

                            <a href="<?php echo SURL . "app/Walk_in_orders"; ?>">All Orders</a>
                        </li>
                        <li class="active">Walk in orders </li>
                    </ul><!-- /.breadcrumb -->

                    <div class="nav-search" id="nav-search">
                        <form class="form-search">
                            <span class="input-icon">
                                <input type="text" placeholder="Search ..." class="nav-search-input" id="nav-search-input" autocomplete="off" />
                                <i class="ace-icon fa fa-search nav-search-icon"></i>
                            </span>
                        </form>
                    </div><!-- /.nav-search -->
                </div>

                <div class="page-content">
                    <div class="ace-settings-container" id="ace-settings-container">
                        <div class="btn btn-app btn-xs btn-warning ace-settings-btn" id="ace-settings-btn">
                            <i class="ace-icon fa fa-cog bigger-130"></i>
                        </div>

                        <!-- /.ace-settings-box -->
                    </div><!-- /.ace-settings-container -->

                    <div class="page-header">
                        <h1>
                            LPG
                            <small>
                                <i class="ace-icon fa fa-angle-double-right"></i>
                                Walk in orders
                            </small>
                        </h1>
                    </div><!-- /.page-header -->
                    <style>
                        .scheduler-border {
                            border: 1px solid #ccc;
                            padding: 5px 10px;
                            border-radius: 5px;
                            background: #fff;
                        }

                        fieldset.scheduler-border {
                            padding-bottom: 20px;
                        }

                        legend {
                            width: 100%;
                            margin-bottom: 20px;
                            font-size: 21px;
                            line-height: inherit;
                            border-bottom: 1px solid #e5e5e5;
                        }

                        label {
                            font-weight: bold;
                        }
                    </style>
                    <div class="row">
                        <div class="col-xs-12">
                            <!-- PAGE CONTENT BEGINS -->

                            <?php
                            if ($this->session->flashdata('err_message')) {
                                ?>

                                <div class="alert alert-danger">
                                    <button type="button" class="close" data-dismiss="alert">
                                        <i class="ace-icon fa fa-times"></i>
                                    </button>

                                    <strong>
                                        <i class="ace-icon fa fa-times"></i>
                                        Oh snap!
                                    </strong>

                                    <?php echo $this->session->flashdata('err_message'); ?>
                                    <br>
                                </div>

                                <?php
                            } ?>
                            <form id="formID" class="form-horizontal" role="form" method="post" action="<?php echo SURL; ?>app/Walk_in_orders/submit" enctype="multipart/form-data">
                                <fieldset class="scheduler-border">
                                    <legend class="scheduler-border">Order Detail</legend>

                                    <div class="col-xs-8">
                                        <div class="col-xs-12 form-group">
                                            <label class="col-sm-4 control-label no-padding-right" for="form-field-1">Date</label>
                                            <div class="col-sm-6">
                                                <div class="input-group ">
                                                    <input name="date" class="form-control date-picker" id="date" type="text" data-date-end-date="0d" readonly data-date-format="yyyy-mm-dd" required value="<?php
                                                    if ($record['date']) {
                                                        echo $record['date'];
                                                    } else {
                                                        echo date('Y-m-d');
                                                    } ?>" />
                                                    <span class="input-group-addon">
                                                        <i class="fa fa-calendar bigger-110"></i>
                                                    </span>
                                                </div>
                                            </div>

                                        </div>

                                    </div>
                                    <div class="col-xs-8">
                                        <div class="col-xs-12 form-group">
                                            <label class="col-sm-4 control-label no-padding-right" for="form-field-1">Sale Point</label>
                                            <div class="col-sm-6">
                                                <select class="chosen-select form-control" name="salepoint" id="salepoint">
                                                    <?php foreach ($salepoint as $key => $value) { ?>
                                                        <option value="<?php echo $value['sale_point_id']; ?>" <?php if ($record['sale_point_id'] == $value['sale_point_id']) {
                                                               echo "selected";
                                                           } ?>><?php echo $value['sp_name']; ?></option>
                                                    <?php } ?>
                                                </select>
                                            </div>

                                        </div>

                                    </div>

                                    <div class="col-xs-8">
                                        <div class="col-xs-12 form-group">
                                            <label class="col-sm-4 control-label no-padding-right" for="form-field-1">Customer Name</label>
                                            <div class="col-sm-6">
                                                <select name="customer_id" id="customer_id" onchange="get_customer();" class="form-control select-2">
                                                    <?php foreach ($user_list as $key => $value) { ?>
                                                        <option value="<?php echo $value['id']; ?>" <?php if ($record['userid'] == $value['id']) {
                                                               echo "selected";
                                                           } ?>><?php echo $value['name'] . ' ' . $value['phone']; ?></option>
                                                    <?php } ?>

                                                </select>
                                            </div>
                                        </div>



                                    </div>
                                    <div class="col-xs-4" style="
    margin-top: -6%;
">
                                        <img width="268" height="210" id="target" src="<?php if (isset($record['dp'])) {
                                            echo IMG . 'profile/' . $record['dp'];
                                        } else {
                                            echo IMG . 'profile/default.JPG';
                                        } ?>" style="margin-left: 0%;">
                                    </div>

                                </fieldset>
                                <div class="row col-md-12">
                                    <div class="col-xs-12 col-sm-12 pricing-span-body" style="margin-left: 1%; display: flex;">
                                        <div class="pricing-span6">
                                            <div class="widget-box pricing-box-small widget-color-blue2">
                                                <div class="widget-header">
                                                    <h6 class="widget-title smaller lighter">Item <?php if ($arabic_check == 'Yes') { ?> (بند) <?php } ?></h6>
                                                </div>

                                                <div class="widget-body">
                                                    <select class="chosen-select form-control" id="materialcode" onchange="get_item_detail()" data-placeholder="Choose a Item...">
                                                        <option value="">Select Item</option>
                                                        <?php
                                                        $item_list = $this->db->query("SELECT * FROM `tblmaterial_coding` where status='Active'")->result_array();
                                                        foreach ($item_list as $key => $value) { ?>
                                                            <option value="<?php echo $value['materialcode']; ?>"><?php echo ucwords($value['itemname']); ?> </option>
                                                        <?php } ?>
                                                    </select>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="pricing-span5">
                                            <div class="widget-box pricing-box-small widget-color-blue2">
                                                <div class="widget-header">
                                                    <h6 class="widget-title smaller lighter">Category</h6>
                                                </div>

                                                <div class="widget-body">
                                                    <input type="text" class="form-control" id="category" tabindex="-1" readonly>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="pricing-span5">
                                            <div class="widget-box pricing-box-small widget-color-blue2">
                                                <div class="widget-header">
                                                    <h6 class="widget-title smaller lighter">Item Type</h6>
                                                </div>

                                                <div class="widget-body">
                                                    <select class="form-control" id="item_type" onchange="get_item_detail()" data-placeholder="Choose a Item...">
                                                    </select>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="pricing-span4 brands" id="brandContainer" style="display: none;">
                                            <div class="widget-box pricing-box-small widget-color-blue2">
                                                <div class="widget-header">
                                                    <h6 class="widget-title smaller lighter">Cylinder Brand</h6>
                                                </div>
                                                <div class="widget-body">
                                                    <select class="form-control chosen-select" id="cylinder_brand" onchange="get_swap_charges()" data-placeholder="Choose a Brand...">
                                                        <?php $brands = $this->db->query("SELECT * FROM `tbl_brand`")->result_array();
                                                        foreach ($brands as $key => $value) { ?>
                                                            <option value="<?php echo $value['brand_id'] ?>"><?php echo $value['brand_name'] ?></option>
                                                        <?php } ?>
                                                    </select>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="pricing-span5" id="cylinderConditionContainer" style="display: none;">
                                            <div class="widget-box pricing-box-small widget-color-blue2">
                                                <div class="widget-header">
                                                    <h6 class="widget-title smaller lighter">Cylinder Condition</h6>
                                                </div>
                                                <div class="widget-body">
                                                    <select class="form-control chosen-select" id="cylinder_condition" onchange="get_item_detail()" data-placeholder="Choose Condition...">
                                                        <option value="New/Good Condition">New/Good Condition</option>
                                                        <option value="Average Condition">Average Condition</option>
                                                    </select>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="pricing-span5" id="swapChargesContainer" style="display: none;">
                                            <div class="widget-box pricing-box-small widget-color-blue2">
                                                <div class="widget-header">
                                                    <h6 class="widget-title smaller lighter">Swap Credits</h6>
                                                </div>
                                                <div class="widget-body">
                                                    <input type="text" class="form-control" onkeypress="return /[0-9-]/i.test(event.key)" onkeyup="CalAmount()" id="swap_charges">
                                                </div>
                                            </div>
                                        </div>
                                        <div class="pricing-span5" id="unitPriceContainer">
                                            <div class="widget-box pricing-box-small widget-color-blue2">
                                                <div class="widget-header">
                                                    <h6 class="widget-title smaller lighter">Unit Price </h6>
                                                </div>

                                                <div class="widget-body">
                                                    <input type="text" class="form-control" id="price" tabindex="-1" readonly>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="pricing-span5" id="securityChargesContainer">
                                            <div class="widget-box pricing-box-small widget-color-blue2">
                                                <div class="widget-header">
                                                    <h6 class="widget-title smaller lighter">Security Charges</h6>
                                                </div>

                                                <div class="widget-body">
                                                    <input type="text" class="form-control" id="security_charges" tabindex="-1" readonly>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="pricing-span3">
                                            <div class="widget-box pricing-box-small widget-color-blue2">
                                                <div class="widget-header">
                                                    <b>
                                                        <h6 class="widget-title smaller lighter" style="font-size: 10px;">Quantity <?php if ($arabic_check == 'Yes') { ?> (كمية)<?php } ?></h6>
                                                    </b>
                                                </div>

                                                <div class="widget-body">
                                                    <input class="form-control" type="text" id="qty" maxlength="6" onkeypress="return /[0-9 . ]/i.test(event.key)" onkeyup="CalAmount()" pattern="^[0-9]+$" title="Only Numbers Allowed...">

                                                </div>
                                            </div>
                                        </div>

                                        <div class="pricing-span3">
                                            <div class="widget-box pricing-box-small widget-color-grey">
                                                <div class="widget-header">
                                                    <h6 class="widget-title smaller lighter">Amount <?php if ($arabic_check == 'Yes') { ?> (كمية)<?php } ?></h6>
                                                </div>

                                                <div class="widget-body">
                                                    <input class="form-control" type="text" name="amount" id="amount" disabled="disabled" tabindex="-1">
                                                </div>
                                            </div>
                                        </div>
                                        <div class="pricing-span3">
                                            <div class="widget-box pricing-box-small widget-color-green">
                                                <div class="widget-header">
                                                    <h6 class="widget-title smaller lighter" style="margin-left: 25%;"> Action <?php if ($arabic_check == 'Yes') { ?> (عمل) <?php } ?></h6>
                                                </div>
                                                <div class="widget-body" align="center">
                                                    <input style=" height:34px;width: 40% !important;" id="addremove" class="btn btn-xs btn-info" type="button" onclick="temp_product();" value="Add">
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-12">
                                    <div class="table-header">
                                        Order Detail
                                    </div>

                                    <div>
                                        <table id="dynamic-table" class="table table-striped table-bordered table-hover">
                                            <thead>
                                                <tr>
                                                    <th>Sr No</th>
                                                    <th>Item</th>
                                                    <th>Type</th>
                                                    <th>Unit Price</th>
                                                    <th style="width: 20%;">Quantity</th>
                                                    <th>Amount</th>
                                                    <th>Action</th>
                                                </tr>
                                            </thead>
                                            <tbody id="order_items">
                                                <!-- Order items will be populated here -->
                                            </tbody>
                                        </table>

                                        <table style="width:35%; float: right;" id="simple-table" class="table  table-bordered table-hover fc_currency">
                                            <thead>
                                                <tr>
                                                    <th colspan="2">Bill Details <span class="currency"></span> </th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <tr>
                                                    <td style="background:#848484; color:#fff">Total Quantity</td>
                                                    <td><input class="form-control" type="text" tabindex="-1" readonly="" id="total_qty" name="total_qty" value=""></td>
                                                </tr>
                                                <tr>
                                                    <td style="background:#848484; color:#fff">LPG Amount</td>
                                                    <td><input class="form-control" type="text" tabindex="-1" readonly="" id="lpg_amount" name="lpg_amount" value=""></td>
                                                </tr>
                                                <tr>
                                                    <td style="background:#848484; color:#fff">GST Percentage</td>
                                                    <td><input class="form-control" type="text" tabindex="-1" readonly="" id="gst_perc" name="gst_perc" value=""></td>
                                                </tr>
                                                <tr>
                                                    <td style="background:#848484; color:#fff">GST Amount</td>
                                                    <td><input class="form-control" type="text" tabindex="-1" readonly="" id="ttl_gst" name="ttl_gst" value=""></td>
                                                </tr>
                                                <tr>
                                                    <td style="background:#848484; color:#fff">Total Security Charges</td>
                                                    <td><input class="form-control" type="text" tabindex="-1" readonly="" id="ttl_security_charges" name="ttl_security_charges" value=""></td>
                                                </tr>
                                                <tr>
                                                    <td style="background:#848484; color:#fff">Accessories Amount</td>
                                                    <td><input class="form-control" type="text" tabindex="-1" readonly="" id="ttl_accessories" name="ttl_accessories" value=""></td>
                                                </tr>
                                                <tr>
                                                    <td style="background:#848484; color:#fff">Delivery Charges</td>
                                                    <td><input class="form-control" type="text" tabindex="-1" id="ttl_delivery_charge" onkeyup="cal_grand_total()" name="delivery_charges" value="<?php echo $record['delivery_charges'] ?>"></td>
                                                </tr>
                                                <tr>
                                                    <td style="background:#848484; color:#fff">Total Swap Credits</td>
                                                    <td><input class="form-control" type="text" tabindex="-1" readonly="" id="ttl_swap_charges" name="ttl_swap_charges" value=""></td>
                                                </tr>
                                                <tr>
                                                    <td style="background:#848484; color:#fff">Grand Total</td>
                                                    <td><input class="form-control" type="text" tabindex="-1" readonly="" id="grand_total" name="grand_total" value=""></td>
                                                </tr>
                                                <tr>
                                                    <td style="background:#848484; color:#fff">Order Status</td>
                                                    <td>
                                                        <select name="order_status" id="order_status" class="chosen-select form-control">
                                                            <option value="Delivered">Delivered</option>
                                                        </select>
                                                    </td>
                                                </tr>
                                                <tr id="rider_row" style="display:none;">
                                                    <td style="background:#848484; color:#fff">Rider</td>
                                                    <td>
                                                        <select name="rider_id" id="rider_id" class="chosen-select form-control">
                                                            <?php
                                                            $rider_list = $this->db->query("SELECT * FROM `tbl_rider_coding`")->result_array();
                                                            foreach ($rider_list as $key => $value) { ?>
                                                                <option value="<?php echo $value['id']; ?>"><?php echo ucwords($value['rider_name']); ?> </option>
                                                            <?php } ?>
                                                        </select>
                                                    </td>
                                                </tr>
                                                <tr id="reason_row" style="display:none;">
                                                    <td style="background:#848484; color:#fff">Reject Reason</td>
                                                    <td>
                                                        <textarea style="width: 100%;" maxlength="250" name="reject_reason" id="reject_reason" cols="5" rows="5"></textarea>
                                                    </td>
                                                </tr>


                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                                <div class="form-action row center">
                                    <button type="submit" class="btn btn-sm btn-primary"><i class="fa fa-floppy-o" aria-hidden="true"></i> Submit</button>
                                </div>
                                <input type="hidden" name="edit" value="<?php echo $record['id'] ?>" id="edit">
                            </form>
                        </div><!-- /.col -->
                    </div><!-- /.row -->
                </div><!-- /.page-content -->
            </div>
        </div><!-- /.main-content -->

    </div><!-- /.main-container -->
    <script src="https://code.jquery.com/jquery-3.7.1.js" integrity="sha256-eKhayi8LEQwp4NKxN+CfCh+3qOVUtJn3QNZ0TciWLP4=" crossorigin="anonymous"></script>
    <?php
    $this->load->view('app/include/footer');
    $this->load->view('app/include/js');
    ?>


    <script type="text/javascript">
        jQuery(function ($) {
            $('#salepoint').trigger("chosen:updated");
            var $mySelect = $('#salepoint');
            $mySelect.chosen();
            $mySelect.trigger('chosen:activate');
        });
        get_customer();

        function get_customer() {
            var customer_id = $('#customer_id').val();
            $.ajax({
                url: '<?php echo SURL; ?>app/Walk_in_orders/get_customer',
                type: 'POST',
                data: {
                    customer_id: customer_id
                },
                success: function (response) {
                    $("#target").attr('src', "<?php echo IMG . "profile/" ?>" + response);
                    get_order_detail();
                },
                error: function (xhr, status, error) {
                    console.error('AJAX Error while fetching vehicles:', status, error);
                }
            });
        }

        function temp_product() {
            var materialcode = $('#materialcode').val();
            var qty = $('#qty').val();
            var item_type = $('#item_type').val();
            var swap_charges = $('#swap_charges').val();
            var cylinder_brand = $('#cylinder_brand').val();
            var cylinder_condition = $('#cylinder_condition').val();
            var sale_point_id = $('#salepoint').val();

            if (materialcode == '' || materialcode <= 0) {
                alert("Please Select Item !");
                return false;
            }
            if (item_type == '' || item_type <= 0) {
                alert("Please Select Ttem Type !");
                return false;
            }
            if (qty == '' || qty <= 0) {
                alert("Please Enter Quantity !");
                return false;
            }
            if (item_type == 'Swap' && (swap_charges >= 0 || swap_charges == '-' || swap_charges > -1)) {
                alert("Please Enter Correct Swap Credits !");
                return false;
            }
            var edit = $('#edit').val();

            $.ajax({
                url: '<?php echo SURL; ?>app/Walk_in_orders/temp_product',
                type: 'POST',
                data: {
                    materialcode: materialcode,
                    qty: qty,
                    item_type: item_type,
                    edit: edit,
                    sale_point_id: sale_point_id,
                    swap_charges: swap_charges,
                    cylinder_condition: cylinder_condition,
                    cylinder_brand: cylinder_brand,
                },
                success: function (response) {
                    if (response == 'success') {
                        $('#materialcode').val('')
                        $("#category").val('');
                        $("#price").val('');
                        $("#qty").val(0);
                        $("#security_charges").val('');
                        $('#item_type').html(''); // Assuming your select element ID
                        get_order_detail();
                    } else {
                        alert(response);
                    }
                    $('#materialcode').focus()

                },
                error: function (xhr, status, error) {
                    console.error('AJAX Error while fetching item details:', status, error);
                }
            });
        }

        function del_row(id) {
            $.ajax({
                url: '<?php echo SURL; ?>app/Walk_in_orders/del_row',
                type: 'POST',
                data: {
                    id: id,
                },
                success: function (response) {
                    $('#row_' + id).remove();
                    update_totals();
                },
                error: function (xhr, status, error) {
                    console.error('AJAX Error while fetching item details:', status, error);
                }
            });
        }

        function get_item_detail() {
            var materialcode = $('#materialcode').val();
            var sale_point_id = $('#salepoint').val(); // Assuming this field exists
            var item_type = $('#item_type').val(); // Assuming this field exists
            var date = $('#date').val(); // Assuming this field exists

            $.ajax({
                url: '<?php echo SURL; ?>app/Walk_in_orders/get_item_detail',
                type: 'POST',
                data: {
                    materialcode: materialcode,
                    sale_point_id: sale_point_id,
                    item_type: item_type,
                    date: date
                },
                success: function (response) {
                    var res = response.split('|');
                    $("#category").val(res[0]);
                    $("#price").val(res[1]);
                    $("#security_charges").val(res[2]);
                    $('#item_type').html(res[3]);
                    if (item_type === 'Swap') {
                        $('#unitPriceContainer').hide();
                        $('#securityChargesContainer').hide();
                        $('#swapChargesContainer').show();
                        $('#brandContainer').show();
                        $('#cylinderConditionContainer').show();
                    } else {
                        $('#unitPriceContainer').show();
                        $('#securityChargesContainer').show();
                        $('#swapChargesContainer').hide();
                        $('#brandContainer').hide();
                        $('#cylinderConditionContainer').hide();
                    }
                    get_swap_charges()
                    CalAmount()
                },
                error: function (xhr, status, error) {
                    console.error('AJAX Error while fetching item details:', status, error);
                }
            });
        }
        function get_swap_charges() {
            var brand_id = $('#cylinder_brand').val();
            var materialcode = $('#materialcode').val();
            var sale_point_id = $('#salepoint').val();
            var cylinder_condition = $('#cylinder_condition').val();
            var date = $('#date').val(); // Assuming this field exists
            $.ajax({
                url: '<?php echo SURL; ?>app/Walk_in_orders/get_swap_charges',
                type: 'POST',
                data: {
                    materialcode: materialcode,
                    date: date,
                    sale_point_id: sale_point_id,
                    cylinder_condition: cylinder_condition,
                    brand_id: brand_id
                },
                success: function (response) {
                    $("#swap_charges").val(response); // Assuming this is part of your response for swap charges

                    CalAmount();
                },
                error: function (xhr, status, error) {
                    console.error('AJAX Error while fetching item details:', status, error);
                }
            });
        }

        function get_order_detail() {
            var edit = $('#edit').val();
            var date = $('#date').val();
            if (edit > 0) {
                var order = edit;
            } else {
                var order = 0;
            }

            var userid = $('#customer_id').val();
            var sale_point_id = $('#salepoint').val();
            var delivery_charges = $('#ttl_delivery_charge').val();
            var customer_id = $('#customer_id').val();

            $.ajax({
                url: '<?php echo SURL; ?>app/Walk_in_orders/get_order_detail',
                type: 'POST',
                data: {
                    order: order,
                    sale_point_id: sale_point_id,
                    delivery_charges: delivery_charges,
                    userid: userid,
                    edit: edit,
                    date: date,
                    customer_id: customer_id,
                },
                dataType: 'json',
                success: function (response) {
                    console.log(response)
                    $("#order_items").html(response.rows);
                    // Update totals
                    $('#total_qty').val(response.total_qty);
                    $('#lpg_amount').val(response.lpg_amount);
                    $('#ttl_gst').val(response.ttl_gst);
                    $('#gst_perc').val(response.gst_perc);
                    $('#ttl_accessories').val(response.ttl_accessories);
                    $('#ttl_security_charges').val(response.ttl_security_charges);
                    $('#ttl_swap_charges').val(response.ttl_swap_charges);

                    // $('#ttl_delivery_charge').val(response.ttl_delivery_charge);
                    $('#grand_total').val(response.grand_total);
                    $('#reject_reason').val(response.reject_reason);
                    // Decrease quantity
                    $('.spinbox-down').click(function (e) {
                        e.preventDefault();
                        var input = $(this).closest('.input-group').find('.quantity-input');
                        var value = parseInt(input.val());
                        var min = parseInt(input.attr('min')) || 1;

                        if (value > min) {
                            input.val(value - 1).change();
                        }
                    });

                    // Increase quantity
                    $('.spinbox-up').click(function (e) {
                        e.preventDefault();
                        var input = $(this).closest('.input-group').find('.quantity-input');
                        var value = parseInt(input.val());
                        var max = parseInt(input.attr('max')) || 100;

                        if (value < max) {
                            input.val(value + 1).change();
                        }
                    });

                    // Update total amount and color change on quantity change
                    $('.quantity-input').on('change', function () {
                        var $input = $(this);
                        var quantity = parseInt($input.val());
                        var originalQuantity = parseInt($input.data('original'));
                        var $row = $input.closest('tr');

                        // Fetch the saleprice and securitycharges properly
                        var gst = parseFloat($row.find('.gst').text().replace(/,/g, '')) || 0;
                        var price = parseFloat($row.find('.saleprice').text().replace(/,/g, '')) || 0;
                        var security_charges = parseFloat($row.find('.securitycharges').text().replace(/,/g, '')) || 0;
                        var swap_charges = parseFloat($row.find('.swapcharges').text().replace(/,/g, '')) || 0;
                        var type = $row.find('td').eq(2).text().trim();

                        // Calculate the total amount based on the type (check for security charges)
                        if (type === 'Swap') {
                            var totalAmount = swap_charges * quantity;
                        } else {
                            var totalAmount = (price + gst) * quantity;
                        }
                        if (type === 'New') {
                            totalAmount += security_charges * quantity;
                        }

                        // Update the amount column
                        $row.find('.amount').text(totalAmount.toLocaleString());

                        // Update totals
                        update_totals();

                        // Change color if the quantity is modified
                        if (quantity !== originalQuantity) {
                            $input.css('background-color', '#f0ad4e'); // Highlight the field if quantity changed
                        } else {
                            $input.css('background-color', ''); // Reset to original if reverted
                        }
                    });

                    // Reset quantity to original value
                    $('.btn-reset').click(function (e) {
                        e.preventDefault();
                        var $input = $(this).closest('td').find('.quantity-input');
                        var originalQuantity = $input.data('original');
                        $input.val(originalQuantity).change(); // Reset the value and trigger change event
                    });
                },
                error: function (xhr, status, error) {
                    console.error('AJAX Error while fetching order details:', status, error);
                }
            });
        }

        function update_totals() {
            var total_qty = 0;
            var lpg_amount = 0;
            var ttl_gst = 0;
            var ttl_accessories = 0;
            var ttl_security_charges = 0;
            var ttl_delivery_charge = 0; // Ensure this is initialized
            var ttl_swap_charges = 0;

            $('#order_items tr').each(function () {
                var $row = $(this);
                var quantity = parseInt($row.find('.quantity-input').val()) || 0;
                var saleprice = parseFloat($row.find('.saleprice').text().replace(/,/g, '')) || 0;
                var gst = parseFloat($row.find('.gst').text().replace(/,/g, '')) || 0;
                var security_charges = parseFloat($row.find('.securitycharges').text().replace(/,/g, '')) || 0;
                var type = $row.find('td').eq(2).text().trim();
                var swap_charges = parseFloat($row.find('.swapcharges').text().replace(/,/g, '')) || 0;

                total_qty += quantity;

                if (type !== 'Swap') {
                    ttl_gst += gst * quantity;
                }
                if (type === 'Swap') {
                    ttl_swap_charges += swap_charges * quantity;
                } else if (type === 'New') {
                    lpg_amount += saleprice * quantity;
                    ttl_security_charges += security_charges * quantity;
                    ttl_delivery_charge += quantity * parseFloat($('#delivery_charges').val() || 0);
                } else if (type === 'Refill') {
                    lpg_amount += saleprice * quantity;
                    ttl_delivery_charge += quantity * parseFloat($('#delivery_charges').val() || 0);
                } else if (type === 'Accessories') {
                    ttl_accessories += saleprice * quantity;
                }
            });

            $('#total_qty').val(total_qty);
            $('#lpg_amount').val(lpg_amount);
            $('#ttl_gst').val(ttl_gst);
            $('#ttl_accessories').val(ttl_accessories);
            $('#ttl_security_charges').val(ttl_security_charges);
            $('#ttl_swap_charges').val(ttl_swap_charges);
            $('#ttl_delivery_charge').val(ttl_delivery_charge);

            var grandTotal = (Number(lpg_amount) + Number(ttl_accessories) + Number(ttl_security_charges) + Number(ttl_delivery_charge) + Number(ttl_gst) + Number(ttl_swap_charges));
            $('#grand_total').val(grandTotal); // Ensure toFixed is called on a number
        }

        function cal_grand_total() {
            var lpg_amount = parseFloat($('#lpg_amount').val());
            var ttl_gst = parseFloat($('#ttl_gst').val());
            var ttl_accessories = parseFloat($('#ttl_accessories').val());
            var ttl_security_charges = parseFloat($('#ttl_security_charges').val());
            var ttl_delivery_charge = parseFloat($('#ttl_delivery_charge').val()) || 0;
            var ttl_swap_charges = parseFloat($('#ttl_swap_charges').val()) || 0;
            var grand_total = lpg_amount + ttl_accessories + ttl_security_charges + ttl_delivery_charge + ttl_gst + ttl_swap_charges;
            $('#grand_total').val(grand_total);
        }

        function CalAmount() {
            var qty = $("#qty").val();
            var price = $("#price").val();
            var security_charges = $("#security_charges").val();
            var item_type = $("#item_type").val();
            if (security_charges > 0) {
                var amount = (qty * price) + (qty * security_charges);
            } else {
                var amount = (qty * price);
            }
            if (item_type == 'Swap') {
                var amount = (qty * $("#swap_charges").val());
            }
            $("#amount").val(amount);
        }
    </script>
</body>

</html>