<!DOCTYPE html>
<html lang="en">
<?php
$this->load->view('app/include/head');
$this->load->view('app/include/header');
?>

<body class="no-skin">
   <div class="main-container ace-save-state" id="main-container">
      <?php $this->load->view('app/include/sidebar'); ?>

      <div class="main-content">
         <div class="main-content-inner">
            <div class="breadcrumbs ace-save-state" id="breadcrumbs">
               <ul class="breadcrumb">
                  <li>
                     <i class="ace-icon fa fa-home home-icon"></i>
                     <a href="#">Home</a>
                  </li>
                  <li class="active">Dashboard</li>
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


               <div class="page-header">
                  <h1>
                     Dashboard
                     <small>
                        <i class="ace-icon fa fa-angle-double-right"></i>
                        overview &amp; stats
                     </small>
                  </h1>
               </div><!-- /.page-header -->

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

                           </strong>

                           <?php echo $this->session->flashdata('err_message'); ?>
                           <br>
                        </div>

                        <?php
                     }
                     if ($this->session->flashdata('ok_message')) {
                        ?>

                        <div class="alert alert-block alert-success">
                           <button type="button" class="close" data-dismiss="alert">
                              <i class="ace-icon fa fa-times"></i>
                           </button>

                           <p>
                              <strong>
                                 <i class="ace-icon fa fa-check"></i>

                              </strong>
                              <?php echo $this->session->flashdata('ok_message'); ?>
                           </p>
                        </div>

                        <?php
                     }
                     ?>

                     <style type="text/css">
                        fieldset.scheduler-border {
                           border: 1px groove #ddd !important;
                           padding: 0 1.4em 1.4em 1.4em !important;
                           margin: 0 0 1.5em 0 !important;
                           -webkit-box-shadow: 0px 0px 0px 0px #000;
                           box-shadow: 0px 0px 0px 0px #000;
                        }

                        legend.scheduler-border {
                           margin-bottom: 10px;
                           font-size: 1.2em !important;
                           font-weight: bold !important;
                           text-align: left !important;
                           width: auto;
                           padding: 0 10px;
                           border-bottom: none;
                        }

                        hr {
                           border-top: 2px solid #6e7a99;
                        }

                        .infobox {
                           width: 22% !important;
                           margin: -1px 0px 0 8px;

                        }

                        .infobox-icon {
                           vertical-align: top !important;
                           width: 100% !important;
                        }

                        .infobox-data .infobox-data-number {
                           color: #369bd7 !important;
                        }

                        .badge {
                           border-radius: 10px !important;
                           padding: 10px 7px !important;
                           line-height: 0px !important;
                           right: -7px !important;
                           top: -10px !important;
                           border-color: #2FABE9;
                        }

                        fieldset.schedule-border {
                           padding: 0 1.4em 1.4em 1.4em !important;
                           margin: 0 0 1.5em 0 !important;
                           -webkit-box-shadow: 0px 0px 0px 0px #000;
                           box-shadow: 0px 0px 0px 0px #000;
                           border-top: 1px groove !important;
                        }
                     </style>
                     <fieldset id="add_new" class="form-horizontal scheduler-border">
                        <legend class="scheduler-border">Order Details</legend>

                        <fieldset class="form-horizontal schedule-border">
                           <legend class="scheduler-border">SALES</legend>

                           <div class="form-group center">
                              <div>
                                 <a>
                                    <?php
                                    $today = date('Y-m-d');
                                    $login_user = $this->session->userdata('id');

                                    $this->db->select('location');
                                    $this->db->from('tbl_admin');
                                    $this->db->where('id', $login_user);
                                    $sale_point_ids = $this->db->get()->row_array()['location'];

                                    if ($sale_point_ids) {
                                       $sale_point_id_array = explode(',', $sale_point_ids);
                                       $this->db->select('COUNT(*) as count');
                                       $this->db->from('tbl_place_order');
                                       $this->db->where('date', $today);
                                       $this->db->where_in('sale_point_id', $sale_point_id_array);
                                       $count = $this->db->get()->row_array()['count'];
                                    } else {
                                       $count = 0;
                                    }

                                    if ($count > 1) {
                                       $orders = " Orders were";
                                    } else {
                                       $orders = " Order was";
                                    } ?>

                                    <div class="infobox infobox-green center col-xs-12" style="height: 100%;">
                                       <span class="badge" style="background-color: #AFC95B !important;">
                                          <?php echo $count; ?>
                                       </span>
                                       <div class="infobox-data-number infobox-icon">
                                          <i class="ace-icon fa fa-calendar-plus-o"></i>
                                       </div>

                                       <div class="infobox-data center">
                                          <span class="infobox-data-number bolder" style="font-size: 1.5rem;">
                                             Today Orders<br>
                                          </span>
                                          <p class="green"><?= $count . $orders ?> Received</p>

                                       </div>
                                    </div>
                                 </a>
                              </div>
                              <div>
                                 <a>
                                    <?php
                                    $login_user = $this->session->userdata('id');
                                    $this->db->select('location');
                                    $this->db->from('tbl_admin');
                                    $this->db->where('id', $login_user);
                                    $sale_point_ids = $this->db->get()->row_array()['location'];

                                    if ($sale_point_ids) {
                                       $sale_point_id_array = explode(',', $sale_point_ids);
                                       $this->db->select('COUNT(*) as count');
                                       $this->db->from('tbl_place_order');
                                       $this->db->where('status_dt', $today);
                                       $this->db->where('deliveryStatus', 'Dispatch');
                                       $this->db->where_in('sale_point_id', $sale_point_id_array);
                                       $count = $this->db->get()->row_array()['count'];
                                    } else {
                                       $count = 0;
                                    }
                                    if ($count > 1) {
                                       $orders = " Orders were";
                                    } elseif ($count < 1) {
                                       $orders = "No Orders were";
                                    } else {
                                       $orders = " Order was";
                                    } ?>

                                    <div class="infobox infobox-blue center col-xs-12" style="height: 100%;">
                                       <span class="badge" style="background-color: #8DC2E6 !important;">
                                          <?php echo $count ?>
                                       </span>
                                       <div class="infobox-data-number infobox-icon">
                                          <i class="ace-icon fa fa-calendar-check-o"></i>
                                       </div>


                                       <div class="infobox-data center">
                                          <span class="infobox-data-number" style="font-size: 1.5rem;">
                                             Dispatched Orders<br>
                                          </span>
                                          <p class=""><?= $count . $orders ?> Disptached</p>

                                       </div>
                                    </div>
                                 </a>
                              </div>
                              <div>
                                 <a>
                                    <?php
                                    $login_user = $this->session->userdata('id');
                                    $this->db->select('location');
                                    $this->db->from('tbl_admin');
                                    $this->db->where('id', $login_user);
                                    $sale_point_ids = $this->db->get()->row_array()['location'];

                                    if ($sale_point_ids) {
                                       $sale_point_id_array = explode(',', $sale_point_ids);
                                       $this->db->select('COUNT(*) as count');
                                       $this->db->from('tbl_place_order');
                                       $this->db->where('status_dt', $today);
                                       $this->db->where('deliveryStatus', 'Delivered');
                                       $this->db->where_in('sale_point_id', $sale_point_id_array);
                                       $count = $this->db->get()->row_array()['count'];
                                    } else {
                                       $count = 0;
                                    }

                                    if ($count > 1) {
                                       $orders = " Orders were";
                                    } else {
                                       $orders = " Order was";
                                    } ?>

                                    <div class="infobox infobox-orange center col-xs-12" style="height: 100%;">
                                       <span class="badge" style="background-color: #EDC03C !important;">
                                          <?php echo $count ?>
                                       </span>
                                       <div class="infobox-data-number infobox-icon">
                                          <i class="ace-icon fa fa-calendar"></i>
                                       </div>


                                       <div class="infobox-data center">
                                          <span class="infobox-data-number" style="font-size: 1.5rem;">
                                             Completed Orders<br>
                                          </span>
                                          <p class=""><?= $count . $orders ?> Delivered</p>

                                       </div>
                                    </div>
                                 </a>
                              </div>
                              <div>
                                 <a style="text-decoration: none;">
                                    <?php
                                    $login_user = $this->session->userdata('id');

                                    $this->db->select('location');
                                    $this->db->from('tbl_admin');
                                    $this->db->where('id', $login_user);
                                    $sale_point_ids = $this->db->get()->row_array()['location'];

                                    if ($sale_point_ids) {
                                       $sale_point_id_array = explode(',', $sale_point_ids);
                                       $this->db->select('COUNT(*) as count');
                                       $this->db->from('tbl_place_order');
                                       $this->db->where('status_dt', $today);
                                       $this->db->where('deliveryStatus', 'Reject');
                                       $this->db->where_in('sale_point_id', $sale_point_id_array);
                                       $count = $this->db->get()->row_array()['count'];
                                    } else {
                                       $count = 0;
                                    }
                                    if ($count > 1) {
                                       $orders = " Orders were";
                                    } else {
                                       $orders = " Order was";
                                    } ?>

                                    <div class="infobox infobox-red center col-xs-12" style="height: 100%;">
                                       <span class="badge" style="background-color: #D96564 !important;">
                                          <?php echo $count ?>
                                       </span>
                                       <div class="infobox-data-number infobox-icon">
                                          <i class="ace-icon fa fa-calendar-o"></i>
                                       </div>


                                       <div class="infobox-data center">
                                          <span class="infobox-data-number" style="font-size: 1.5rem;">
                                             Canceled Orders<br>
                                          </span>
                                          <p class=""><?= $count . $orders ?> Canceled</p>

                                       </div>
                                    </div>
                                 </a>
                              </div>
                           </div>
                           <hr>
                        </fieldset>
                        <div class="col-xs-12">
                           <div>
                              <div id="user-profile-2" class="user-profile">
                                 <div class="tabbable">
                                    <ul class="nav nav-tabs padding-18">
                                       <li>
                                          <a data-toggle="tab" href="#home" onclick="check4()">
                                             <i class="blue ace-icon fa fa-globe bigger-130"></i>
                                             All
                                          </a>
                                       </li>

                                       <li class="active">
                                          <a data-toggle="tab" href="#feed" onclick="check1()">
                                             <i class="orange ace-icon fa fa-clock-o bigger-130"></i>
                                             Pending
                                          </a>
                                       </li>

                                       <li>
                                          <a data-toggle="tab" href="#friends" onclick="check2()">
                                             <i class="green ace-icon fa fa-th-list bigger-130"></i>
                                             Completed
                                          </a>
                                       </li>
                                       <li>
                                          <a data-toggle="tab" href="#pictures" onclick="check3()">
                                             <i class="pink ace-icon fa fa-times bigger-120"></i>
                                             Cancelled
                                          </a>
                                       </li>
                                    </ul>
                                    <script>
                                       check1()

                                       function check1() {
                                          $("#feed").show('10000');
                                          $("#home").hide('10000');
                                          $("#friends").hide('10000');
                                          $("#pictures").hide('10000');
                                       }

                                       function check2() {
                                          $("#friends").show('10000');
                                          $("#feed").hide('10000');
                                          $("#pictures").hide('10000');
                                          $("#home").hide('10000');
                                       }

                                       function check3() {
                                          $("#pictures").show('10000');
                                          $("#feed").hide('10000');
                                          $("#friends").hide('10000');
                                          $("#home").hide('10000');
                                       }

                                       function check4() {
                                          $("#pictures").hide();
                                          $("#feed").hide();
                                          $("#friends").hide();
                                          $("#home").show('10000');
                                       }
                                    </script>


                                    <div class="tab-content no-border padding-24">

                                       <div id="home" class="tab-pane">
                                          <table id="dynamic-table" class="table table-striped table-bordered table-hover" style="width: 100%;">
                                             <thead>
                                                <tr>
                                                   <th>SR No. </th>
                                                   <th>Order NO</th>
                                                   <th>Order Date </th>
                                                   <th>Delivery Type</th>
                                                   <th>Execution Time </th>
                                                   <th>Status</th>
                                                   <th>Action</th>
                                                </tr>
                                             </thead>
                                             <tbody>
                                                <?php $count = 0;
                                                $today = date('Y-m-d');
                                                $login_user = $this->session->userdata('id');
                                                $this->db->select('location');
                                                $this->db->from('tbl_admin');
                                                $this->db->where('id', $login_user);
                                                $sale_point_ids = $this->db->get()->row_array()['location'];

                                                if ($sale_point_ids) {
                                                   $sale_point_id_array = explode(',', $sale_point_ids);

                                                   $this->db->select('*');
                                                   $this->db->from('tbl_place_order');
                                                   $this->db->where('status_dt', $today);
                                                   $this->db->where_in('sale_point_id', $sale_point_id_array);
                                                   $order_list = $this->db->get()->result_array();
                                                } else {
                                                   $order_list = [];
                                                }
                                                foreach ($order_list as $key => $value) {
                                                   $count++;
                                                   $id = $value['id']; ?>

                                                   <tr>

                                                      <td>
                                                         <?php echo $count ?>
                                                      </td>
                                                      <td> <?php echo $value['id'] ?> </td>
                                                      <td> <?php echo $value['date'] ?> </td>

                                                      <td>
                                                         <?php echo $value['deliveryType'] ?>
                                                      </td>

                                                      <?php if ($value['deliveryStatus'] == 'Delivered' || $value['deliveryStatus'] == 'Reject') { ?>
                                                         <td>
                                                            <?= $value['date'] ?> &nbsp; <?= $value['time'] ?> <br>
                                                            <i class="blue fa fa-long-arrow-right bigger-140" aria-hidden="true"></i>
                                                            <?php echo $value['delivery_date'] ?> &nbsp; <?= $value['delivery_time'] ?>
                                                         </td>
                                                      <?php } else {
                                                         echo "<td>";
                                                         echo $value['date'] ?> &nbsp; <?= $value['time'] ?> <br>
                                                         <i class="blue fa fa-long-arrow-right bigger-140" aria-hidden="true"></i>
                                                         <?php
                                                         echo "</td>";
                                                      } ?>

                                                      <?php if ($value['deliveryStatus'] == 'Delivered') { ?>

                                                         <td class="blue">
                                                            <strong><?php echo $value['deliveryStatus'] ?></strong>
                                                         </td>
                                                      <?php } elseif ($value['deliveryStatus'] == 'Booked') { ?>
                                                         <td class="green">
                                                            <strong><?php echo $value['deliveryStatus'] ?></strong>
                                                         </td>
                                                      <?php } elseif ($value['deliveryStatus'] == 'Reject') { ?>
                                                         <td class="red">
                                                            <strong><?php echo $value['deliveryStatus'] ?></strong>
                                                         </td>
                                                      <?php } elseif ($value['deliveryStatus'] == 'Confirm') { ?>
                                                         <td class="green">
                                                            <strong><?php echo $value['deliveryStatus'] ?></strong>
                                                         </td>
                                                      <?php } else {
                                                         echo " <td></td>  ";
                                                      }
                                                      ?>
                                                      <td>
                                                         <div class="hidden-sm hidden-xs action-buttons">
                                                            <?php if ($value['deliveryStatus'] == 'Complete' || $value['deliveryStatus'] == 'Cancelled') { ?>
                                                               <div class="action-buttons" style="display: flex; align-items: center;">
                                                                  <a class="btn btn-info btn-sm" target="_blank" title="Print Invoice" href="<?= SURL . "app/Today_Order_dispatch/detail_invoice/$id" ?>"> View Detail </a>
                                                                  <a id="firstprint" target="_blank" class="ml-2" title="Print Invoice" href="<?= SURL . "app/Today_Order_dispatch/small_invoice/$id" ?>">
                                                                     <i class="ace-icon fa fa-print bigger-130 green"></i>
                                                                  </a>
                                                               </div>
                                                            <?php } else { ?>
                                                               <div class="action-buttons" style="display: flex; align-items: center;">
                                                                  <a class="btn btn-info btn-sm" target="_blank" title="Print Invoice" href="<?= SURL . "app/Today_Order_dispatch/detail_invoice/$id" ?>"> View Detail </a>
                                                                  <a id="firstprint" target="_blank" class="ml-2" title="Print Invoice" href="<?= SURL . "app/Today_Order_dispatch/small_invoice/$id" ?>">
                                                                     <i class="ace-icon fa fa-print bigger-130 green"></i>
                                                                  </a>
                                                               </div>
                                                            <?php } ?>
                                                         </div>
                                                      </td>
                                                   </tr>
                                                <?php } ?>
                                             </tbody>
                                          </table>
                                       </div>
                                       <div id="feed" class="tab-pane active">
                                          <table id="dynamic-table" class="table table-striped table-bordered table-hover">
                                             <thead>
                                                <tr>
                                                   <th>SR No. </th>
                                                   <th>Order NO</th>
                                                   <th>Order Date </th>
                                                   <th>Delivery Type</th>
                                                   <th>Status</th>
                                                   <th>Action</th>
                                                </tr>
                                             </thead>
                                             <?php $count = 0;
                                             $login_user = $this->session->userdata('id');
                                             $order_list = $this->db->query("SELECT * FROM `tbl_place_order` where deliveryStatus ='Booked' and status_dt = '$today' order by id desc")->result_array();
                                             foreach ($order_list as $key => $value) {
                                                $count++;
                                                $id = $value['id'];
                                                // $order_detail = $this->db->query("SELECT * FROM `tbl_place_order` where  id = $id")->row_array();
                                             
                                                ?>
                                                <tbody>

                                                   <tr>
                                                      <td>
                                                         <?php echo $count ?>
                                                      </td>

                                                      <td>
                                                         <?= $value['id'] ?>
                                                      </td>

                                                      <td>
                                                         <?= $value['date'] ?>
                                                      </td>

                                                      <td>
                                                         <?= $value['deliveryType'] ?>
                                                      </td>

                                                      <td class="green">
                                                         <strong><?php echo $value['deliveryStatus'] ?></strong>
                                                      </td>
                                                      <td>
                                                         <div class="hidden-sm hidden-xs action-buttons">
                                                            <div class="action-buttons" style="display: flex; align-items: center;">
                                                               <a class="btn btn-info btn-sm" target="_blank" title="Print Invoice" href="<?= SURL . "app/Today_Order_dispatch/detail_invoice/$id" ?>"> View Detail </a>
                                                               <a id="firstprint" target="_blank" class="ml-2" title="Print Invoice" href="<?= SURL . "app/Today_Order_dispatch/small_invoice/$id" ?>">
                                                                  <i class="ace-icon fa fa-print bigger-130 green"></i>
                                                               </a>
                                                            </div>
                                                         </div>
                                                      </td>
                                                   </tr>
                                                </tbody>
                                             <?php } ?>
                                          </table>

                                       </div><!-- /#feed -->

                                       <div id="friends" class="tab-pane">
                                          <table id="dynamic-table" class="table table-striped table-bordered table-hover">
                                             <thead>
                                                <tr>
                                                   <th>SR No. </th>
                                                   <th>Order NO</th>
                                                   <th>Order Date </th>
                                                   <th>Delivery Type</th>
                                                   <th>Execution Time </th>
                                                   <th>Status</th>
                                                   <th>Action</th>
                                                </tr>
                                             </thead>
                                             <?php
                                             $count = 0;
                                             $login_user = $this->session->userdata('id');
                                             $order_list = $this->db->query("SELECT * FROM `tbl_place_order` where deliveryStatus ='Delivered' and status_dt = '$today' order by id desc")->result_array();
                                             foreach ($order_list as $key => $value) {
                                                $count++;
                                                $id = $value['id'];
                                                ?>

                                                <tbody>

                                                   <tr>
                                                      <td>
                                                         <?php echo $count ?>
                                                      </td>

                                                      <td>
                                                         <?= $value['id'] ?>
                                                      </td>

                                                      <td>
                                                         <?= $value['date'] ?>
                                                      </td>

                                                      <td>
                                                         <?= $value['deliveryType'] ?>
                                                      </td>

                                                      <?php if ($value['deliveryStatus'] == 'Delivered') { ?>
                                                         <td>
                                                            <?= $value['date'] ?> &nbsp; <?= $value['time'] ?> <br>
                                                            <i class="blue fa fa-long-arrow-right bigger-140" aria-hidden="true"></i>
                                                            <?php echo $value['delivery_date'] ?> &nbsp; <?= $value['delivery_time'] ?>
                                                         </td>
                                                      <?php } else {
                                                         echo "<td>";
                                                         echo $value['date'] ?> &nbsp; <?= $value['time'] ?> <br>
                                                         <i class="blue fa fa-long-arrow-right bigger-140" aria-hidden="true"></i>
                                                         <?php
                                                         echo "</td>";
                                                      } ?>

                                                      <td class="blue">
                                                         <strong><?php echo $value['deliveryStatus'] ?></strong>
                                                      </td>
                                                      <td>
                                                         <div class="hidden-sm hidden-xs action-buttons">
                                                            <div class="action-buttons" style="display: flex; align-items: center;">
                                                               <a class="btn btn-info btn-sm" target="_blank" title="Print Invoice" href="<?= SURL . "app/Today_Order_dispatch/detail_invoice/$id" ?>"> View Detail </a>
                                                               <a id="firstprint" target="_blank" class="ml-2" title="Print Invoice" href="<?= SURL . "app/Today_Order_dispatch/small_invoice/$id" ?>">
                                                                  <i class="ace-icon fa fa-print bigger-130 green"></i>
                                                               </a>
                                                            </div>
                                                         </div>
                                                      </td>
                                                   </tr>
                                                </tbody>
                                             <?php } ?>
                                          </table>
                                       </div><!-- /#friends -->
                                       <div id="pictures" class="tab-pane">
                                          <table id="dynamic-table" class="table table-striped table-bordered table-hover">
                                             <thead>
                                                <tr>
                                                   <th>SR No. </th>
                                                   <th>Order NO</th>
                                                   <th>Order Date </th>
                                                   <th>Delivery Type</th>
                                                   <th>Execution Time </th>
                                                   <th>Status</th>
                                                   <th>Action</th>
                                                </tr>
                                             </thead>
                                             <?php $count = 0;
                                             $login_user = $this->session->userdata('id');
                                             $order_list = $this->db->query("SELECT * FROM `tbl_place_order` where deliveryStatus ='Reject' and status_dt = '$today' order by id desc")->result_array();
                                             foreach ($order_list as $key => $value) {
                                                $count++;
                                                $id = $value['id']; ?>

                                                <tbody>

                                                   <tr>

                                                      <td>
                                                         <?php echo $count ?>
                                                      </td>

                                                      <td>
                                                         <?= $value['id'] ?>
                                                      </td>

                                                      <td>
                                                         <?= $value['date'] ?>
                                                      </td>

                                                      <td>
                                                         <?= $value['deliveryType'] ?>
                                                      </td>

                                                      <td>
                                                         <?= $value['date'] ?> &nbsp; <?= $value['time'] ?> <br>
                                                         <i class="blue fa fa-long-arrow-right bigger-140" aria-hidden="true"></i> <br>
                                                         <?php echo $value['delivery_date'] ?> &nbsp; <?= $value['delivery_time'] ?>
                                                      </td>
                                                      <td class="red">
                                                         <strong><?php echo $value['deliveryStatus'] ?></strong>
                                                      </td>
                                                      <td>
                                                         <div class="hidden-sm hidden-xs action-buttons">
                                                            <div class="action-buttons" style="display: flex; align-items: center;">
                                                               <a class="btn btn-info btn-sm" target="_blank" title="Print Invoice" href="<?= SURL . "app/Today_Order_dispatch/detail_invoice/$id" ?>"> View Detail </a>
                                                               <a id="firstprint" target="_blank" class="ml-2" title="Print Invoice" href="<?= SURL . "app/Today_Order_dispatch/small_invoice/$id" ?>">
                                                                  <i class="ace-icon fa fa-print bigger-130 green"></i>
                                                               </a>
                                                            </div>
                                                         </div>
                                                      </td>
                                                   </tr>
                                                </tbody>
                                             <?php } ?>
                                          </table>
                                       </div><!-- /#pictures -->
                                    </div>
                                 </div>
                              </div>
                           </div>
                        </div>
                        <hr>
                     </fieldset><!-- /.col -->
                     <!-- /.col -->

                     <!-- <div class="col-xs-12 col-sm-4">
                              <div class="widget-box ">
                                 <div class="widget-header widget-header-flat">
                                    <h4 class="widget-title lighter">
                                       <i class="ace-icon fa fa-signal red"></i>
                                       Today's Sale
                                    </h4>


                                 </div>

                                 <div class="widget-body">
                                    <div class="widget-main no-padding">
                                       <table class="table table-bordered table-striped">
                                          <thead class="thin-border-bottom">
                                             <tr>
                                                <th>
                                                   <i class="ace-icon fa fa-caret-right blue"></i>Customer
                                                </th>

                                                <th>
                                                   <i class="ace-icon fa fa-caret-right blue"></i>Date
                                                </th>

                                                <th class="hidden-480">
                                                   <i class="ace-icon fa fa-caret-right blue"></i>Total Amount
                                                </th>
                                             </tr>
                                          </thead>

                                          <tbody>
                                             <?php //$count = 1;
                                             //for ($i = 0; $i < count($salelpg_list); $i++) {
                                             ?>
                                             <tr>
                                                <td><?php //echo  ucwords($salelpg_list[$i]['aname']);
                                                ?></td>
                                                <td><?php //echo  $salelpg_list[$i]['issuedate'];
                                                ?></td>
                                                <td>
                                                   <b class="blue">
                                                      <?php //echo  $salelpg_list[$i]['amounttotal'];
                                                      ?>
                                                   </b>
                                                </td>
                                             </tr>
                                             <?php //}
                                             //if (!$salelpg_list) {
                                             ?>
                                                   <tr>

                                                      <td colspan="3" class="red" style="text-align: center;">No Record Found!</td>

                                                   </tr>

                                             <?php //}
                                             ?>


                                          </tbody>
                                       </table>
                                    </div>
                                 </div>
                              </div>
                           </div> -->



                  </div><!-- /.row -->


                  <div class="row">

                     <div style='display:none;' class="col-sm-12">
                        <div class="widget-box transparent">
                           <div class="widget-header widget-header-flat">
                              <h4 class="widget-title lighter">
                                 <!-- <i class="ace-icon fa fa-signal"></i> -->
                                 Sale in Month
                              </h4>
                              <SELECT onchange="get_chart()" id="chart_month">
                                 <option <?php if (date('F') == 'January')
                                    echo "selected"; ?> value="January">January</option>
                                 <option <?php if (date('F') == 'February')
                                    echo "selected"; ?> value="February">February</option>
                                 <option <?php if (date('F') == 'March')
                                    echo "selected"; ?> value="March">March</option>
                                 <option <?php if (date('F') == 'April')
                                    echo "selected"; ?> value="April">April</option>
                                 <option <?php if (date('F') == 'May')
                                    echo "selected"; ?> value="May">May</option>
                                 <option <?php if (date('F') == 'June')
                                    echo "selected"; ?> value="June">June</option>
                                 <option <?php if (date('F') == 'July')
                                    echo "selected"; ?> value="July">July</option>
                                 <option <?php if (date('F') == 'August')
                                    echo "selected"; ?> value="August">August</option>
                                 <option <?php if (date('F') == 'September')
                                    echo "selected"; ?> value="September">September</option>
                                 <option <?php if (date('F') == 'October')
                                    echo "selected"; ?> value="October">October</option>
                                 <option <?php if (date('F') == 'November')
                                    echo "selected"; ?> value="November ">November </option>
                                 <option <?php if (date('F') == 'December')
                                    echo "selected"; ?> value="December ">December </option>
                              </SELECT>

                              <SELECT onchange="get_chart()" id="chart_year">
                                 <?php
                                 $currently_selected = date('Y');
                                 $earliest_year = 1950;
                                 $latest_year = date('Y');

                                 foreach (range($latest_year, $earliest_year) as $i) {
                                    print '<option value="' . $i . '"' . ($i === $currently_selected ? ' selected="selected"' : '') . '>' . $i . '</option>';
                                 }
                                 ?>
                              </SELECT>

                           </div>

                           <div class="widget-body">
                              <div class="widget-main padding-4">
                                 <div id="chart_ajax">
                                    <div id="chartContainer" style="display:none; height: 300px; width: 100%;"></div>
                                    <div class="over" style=" height: 20px;margin-top: -14px;width: 60px;background-color: white;position: absolute;"></div>
                                 </div>

                              </div>
                           </div>

                        </div>
                     </div>
                  </div>
                  <!-- PAGE CONTENT ENDS -->
               </div><!-- /.col -->
            </div><!-- /.row -->
         </div><!-- /.page-content -->

      </div>
   </div><!-- /.main-content -->

   <?php

   $this->load->view('app/include/footer');
   ?>
   </div><!-- /.main-container -->

   <!-- basic scripts -->

   <!--[if !IE]> -->
   <script src="<?php echo SURL; ?>assets/js/jquery-2.1.4.min.js"></script>

   <!-- <![endif]-->

   <!--[if IE]>
<script src="assets/js/jquery-1.11.3.min.js"></script>
<![endif]-->
   <script type="text/javascript">
      if ('ontouchstart' in document.documentElement) document.write("<script src='<?php echo SURL; ?>assets/js/jquery.mobile.custom.min.js'>" + "<" + "/script>");
   </script>
   <script src="<?php echo SURL; ?>assets/js/bootstrap.min.js"></script>

   <!-- page specific plugin scripts -->
   <script src="<?php echo SURL; ?>assets/js/jquery.dataTables.min.js"></script>
   <script src="<?php echo SURL; ?>assets/js/jquery.dataTables.bootstrap.min.js"></script>
   <script src="<?php echo SURL; ?>assets/js/dataTables.buttons.min.js"></script>
   <script src="<?php echo SURL; ?>assets/js/buttons.flash.min.js"></script>
   <script src="<?php echo SURL; ?>assets/js/buttons.html5.min.js"></script>
   <script src="<?php echo SURL; ?>assets/js/buttons.print.min.js"></script>
   <script src="<?php echo SURL; ?>assets/js/buttons.colVis.min.js"></script>
   <script src="<?php echo SURL; ?>assets/js/dataTables.select.min.js"></script>

   <!-- ace scripts -->
   <script src="<?php echo SURL; ?>assets/js/ace-elements.min.js"></script>
   <script src="<?php echo SURL; ?>assets/js/ace.min.js"></script>

   <!-- inline scripts related to this page -->
   <script type="text/javascript">
      jQuery(function ($) {
         //initiate dataTables plugin
         var myTable =
            $('#dynamic-table')
               //.wrap("<div class='dataTables_borderWrap' />")   //if you are applying horizontal scrolling (sScrollX)
               .DataTable({
                  // bAutoWidth: false,
                  // "aoColumns": [
                  //   { "bSortable": true },
                  //   null, null,
                  //   { "bSortable": false }
                  // ],
                  // "aaSorting": [],


                  //"bProcessing": true,
                  //"bServerSide": true,
                  //"sAjaxSource": "http://127.0.0.1/table.php"	,

                  //,
                  //"sScrollY": "200px",
                  //"bPaginate": false,

                  //"sScrollX": "100%",
                  //"sScrollXInner": "120%",
                  //"bScrollCollapse": true,
                  //Note: if you are applying horizontal scrolling (sScrollX) on a ".table-bordered"
                  //you may want to wrap the table inside a "div.dataTables_borderWrap" element

                  //"iDisplayLength": 50


                  select: {
                     style: 'multi'
                  }
               });



         $.fn.dataTable.Buttons.defaults.dom.container.className = 'dt-buttons btn-overlap btn-group btn-overlap';

         new $.fn.dataTable.Buttons(myTable, {
            buttons: [{
               "extend": "colvis",
               "text": "<i class='fa fa-search bigger-110 blue'></i> <span class='hidden'>Show/hide columns</span>",
               "className": "btn btn-white btn-primary btn-bold",
               columns: ':not(:first):not(:last)'
            },
            {
               "extend": "copy",
               "text": "<i class='fa fa-copy bigger-110 pink'></i> <span class='hidden'>Copy to clipboard</span>",
               "className": "btn btn-white btn-primary btn-bold"
            },
            {
               "extend": "csv",
               "text": "<i class='fa fa-database bigger-110 orange'></i> <span class='hidden'>Export to CSV</span>",
               "className": "btn btn-white btn-primary btn-bold"
            },
            {
               "extend": "excel",
               "text": "<i class='fa fa-file-excel-o bigger-110 green'></i> <span class='hidden'>Export to Excel</span>",
               "className": "btn btn-white btn-primary btn-bold"
            },
            {
               "extend": "pdf",
               "text": "<i class='fa fa-file-pdf-o bigger-110 red'></i> <span class='hidden'>Export to PDF</span>",
               "className": "btn btn-white btn-primary btn-bold"
            },
            {
               "extend": "print",
               "text": "<i class='fa fa-print bigger-110 grey'></i> <span class='hidden'>Print</span>",
               "className": "btn btn-white btn-primary btn-bold",
               autoPrint: false,
               //message: 'This print was produced using the Print button for DataTables'
               exportOptions: {
                  columns: [0, 1, 2]
               }
            }
            ]
         });
         myTable.buttons().container().appendTo($('.tableTools-container'));

         //style the message box
         var defaultCopyAction = myTable.button(1).action();
         myTable.button(1).action(function (e, dt, button, config) {
            defaultCopyAction(e, dt, button, config);
            $('.dt-button-info').addClass('gritter-item-wrapper gritter-info gritter-center white');
         });


         var defaultColvisAction = myTable.button(0).action();
         myTable.button(0).action(function (e, dt, button, config) {

            defaultColvisAction(e, dt, button, config);


            if ($('.dt-button-collection > .dropdown-menu').length == 0) {
               $('.dt-button-collection')
                  .wrapInner('<ul class="dropdown-menu dropdown-light dropdown-caret dropdown-caret" />')
                  .find('a').attr('href', '#').wrap("<li />")
            }
            $('.dt-button-collection').appendTo('.tableTools-container .dt-buttons')
         });

         ////

         setTimeout(function () {
            $($('.tableTools-container')).find('a.dt-button').each(function () {
               var div = $(this).find(' > div').first();
               if (div.length == 1) div.tooltip({
                  container: 'body',
                  title: div.parent().text()
               });
               else $(this).tooltip({
                  container: 'body',
                  title: $(this).text()
               });
            });
         }, 500);





         myTable.on('select', function (e, dt, type, index) {
            if (type === 'row') {
               $(myTable.row(index).node()).find('input:checkbox').prop('checked', true);
            }
         });
         myTable.on('deselect', function (e, dt, type, index) {
            if (type === 'row') {
               $(myTable.row(index).node()).find('input:checkbox').prop('checked', false);
            }
         });




         /////////////////////////////////
         //table checkboxes
         $('th input[type=checkbox], td input[type=checkbox]').prop('checked', false);

         //select/deselect all rows according to table header checkbox
         $('#dynamic-table > thead > tr > th input[type=checkbox], #dynamic-table_wrapper input[type=checkbox]').eq(0).on('click', function () {
            var th_checked = this.checked; //checkbox inside "TH" table header

            $('#dynamic-table').find('tbody > tr').each(function () {
               var row = this;
               if (th_checked) myTable.row(row).select();
               else myTable.row(row).deselect();
            });
         });

         //select/deselect a row when the checkbox is checked/unchecked
         $('#dynamic-table').on('click', 'td input[type=checkbox]', function () {
            var row = $(this).closest('tr').get(0);
            if (this.checked) myTable.row(row).deselect();
            else myTable.row(row).select();
         });



         $(document).on('click', '#dynamic-table .dropdown-toggle', function (e) {
            e.stopImmediatePropagation();
            e.stopPropagation();
            e.preventDefault();
         });



         //And for the first simple table, which doesn't have TableTools or dataTables
         //select/deselect all rows according to table header checkbox
         var active_class = 'active';
         $('#simple-table > thead > tr > th input[type=checkbox]').eq(0).on('click', function () {
            var th_checked = this.checked; //checkbox inside "TH" table header

            $(this).closest('table').find('tbody > tr').each(function () {
               var row = this;
               if (th_checked) $(row).addClass(active_class).find('input[type=checkbox]').eq(0).prop('checked', true);
               else $(row).removeClass(active_class).find('input[type=checkbox]').eq(0).prop('checked', false);
            });
         });

         //select/deselect a row when the checkbox is checked/unchecked
         $('#simple-table').on('click', 'td input[type=checkbox]', function () {
            var $row = $(this).closest('tr');
            if ($row.is('.detail-row ')) return;
            if (this.checked) $row.addClass(active_class);
            else $row.removeClass(active_class);
         });



         /********************************/
         //add tooltip for small view action buttons in dropdown menu
         $('[data-rel="tooltip"]').tooltip({
            placement: tooltip_placement
         });

         //tooltip placement on right or left
         function tooltip_placement(context, source) {
            var $source = $(source);
            var $parent = $source.closest('table')
            var off1 = $parent.offset();
            var w1 = $parent.width();

            var off2 = $source.offset();
            //var w2 = $source.width();

            if (parseInt(off2.left) < parseInt(off1.left) + parseInt(w1 / 2)) return 'right';
            return 'left';
         }




         /***************/
         $('.show-details-btn').on('click', function (e) {
            e.preventDefault();
            $(this).closest('tr').next().toggleClass('open');
            $(this).find(ace.vars['.icon']).toggleClass('fa-angle-double-down').toggleClass('fa-angle-double-up');
         });
         /***************/


      })
   </script>
   <script src="<?php echo SURL; ?>assets/js/bootbox.js"></script>
   <script type="text/javascript">
      function confirmDelete(delUrl) {
         bootbox.confirm("Are you sure you want to delete?", function (result) {
            if (result) {
               document.location = delUrl;
            }
         });

      }
   </script>

</body>

</html>