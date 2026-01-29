<!DOCTYPE html>
<html lang="en">
<?php
$this->load->view('en/include/head');
$csv_hdr = "";
$csv_output = "";
?>
<link rel="stylesheet" type="text/css" href="<?php echo SURL ?>assets/css/old_css.css">

<body class="no-skin">


	<div class="main-container ace-save-state" id="main-container">


		<div class="main-content">
			<div class="main-content-inner">

				<div class="page-content">
					<div class="row">
						<div class="col-xs-12">
							<div class="row">
								<div class="col-xs-12">
									<div class="widget-box transparent">
										<div class="widget-header widget-header-large">
											<h3 class="widget-title grey">

												<?php
												foreach ($company as $key => $data) {
													if ($data['logo']) {
												?>
														<img width="50" height="50" id="logo_id" src="<?php echo IMG . 'company/' . $data['logo']; ?>">
													<?php } else { ?>
														<i class="ace-icon fa fa-leaf green"></i>
													<?php }
													echo ucwords($data['business_name']); ?>
												<?php } ?>
											</h3>


											<div class="widget-toolbar no-border invoice-info" style="    margin-top: -8px;">
												<span class="invoice-info-label"></span>
												<h5 class="blue"><?php echo $title;
																	$csv_hdr .= ",,," . $title . "\n"; ?></h5>
											</div>
											<br>
											<div class="widget-toolbar no-border invoice-info">
												<a href="#" onclick="javascript:window.print();">
													<i class="ace-icon fa fa-print"></i>
												</a> &emsp;
												<!-- <span class="invoice-info-label">Order #: <?php echo $order; ?></span> -->


											</div>

										</div>
									</div>


									<div class="clearfix">

										<div class="pull-right tableTools-container">

										</div>
									</div>
									<div class="table-header" style="text-align: center;">

										Order# <?php echo $order; ?>
									</div>
									<div style="display: flex;justify-content:center;">
										<table id="dynamic-tables" class="table table-striped table-bordered table-hover" style="font-size: 10px;">
											<thead>
												<tr style="width: 100%;">
													<th colspan="10" style="background-color: #0eb16d;text-align: center;color:white;">New Order Entries</th>
												</tr>


												<tr>


													<th>Customer</th>
													<th>Order&nbsp;Date</th>
													<th>Delivery Type</th>
													<th>Delivery Charges</th>
													<th>City Name</th>
													<th>Area</th>
													<th>Zone</th>
													<th>Delivery Address</th> 
													<th>Action</th>
												</tr>
											</thead>

											<tbody>

												<tr class="even_frm_top" id="row">
													<?php
													$orderdetail = $this->db->query("SELECT userid,address,area_id,area_name,city_id,deliveryType,date,sale_point_id,delivery_charges FROM `tbl_place_order` where id='$order'")->row_array();
													$userid = $orderdetail['userid'];
													$address = $orderdetail['address'];
													$area_name = $orderdetail['area_name'];
													$area_id = $orderdetail['area_id'];
													$city_id = $orderdetail['city_id'];
													$date = $orderdetail['date'];
													$sale_point_id = $orderdetail['sale_point_id'];
														$zone_id = $this->db->query("SELECT zone_id FROM `tbl_zone_detail` where id='$area_id'")->row_array()['zone_id'];
														$zone_name = $this->db->query("SELECT zone_name FROM `tbl_zone` where id='$zone_id'")->row_array()['zone_name'];

													$user_detail = $this->db->query("SELECT * FROM `tbl_user` where id='$userid'")->row_array();
													$city_name = $this->db->query("SELECT city_name FROM `tbl_city` where city_id='$city_id'")->row_array()['city_name'];
													?>
													<td><?php echo $user_detail['name']; ?></b></td>
													<td><?php echo $date; ?> </td>
													<td><?php echo $orderdetail['deliveryType']; ?></b></td>
													<td><?php echo number_format($orderdetail['delivery_charges'], 2); ?></b></td>
													<td><?php echo $city_name; ?></b></td>
													<td><?php echo $area_name; ?></b></td>
													<td><?php echo $zone_name; ?></b></td>
													<td><?php echo $address; ?></b></td>

													<td align="left" style="cursor: pointer; text-decoration: underline;"><img src="<?php echo SURL ?>assets/images/reports/plus.png" id="<?php echo $order . "_" . $donotremove; ?>" onclick="toggle('<?php echo $order . "_" . $donotremove; ?>');" />
													</td>

												</tr>
											</tbody>
											<tbody>

												<tr>

													<td colspan="5">
														<table width="1000" id="<?php echo $order; ?>" style="display: none;font-size: 10px;" class="table table-striped table-bordered table-hover">

															<tr class="exist_rec_sb" style="font-size: 10px;">
																<td style="width: 3%">#<?php $csv_output .= trim('Sr No.') . ","; ?></td>
																<td style="width: 10%">Item&nbsp;Name <?php $csv_output .= trim('Item Name') . ","; ?></td>
																<td style="width: 10%">Category <?php $csv_output .= trim('Category') . ","; ?></td>
																<td style="width: 15%" align="right">Quantity<?php $csv_output .= trim('Quantity') . ","; ?></td>
																<td style="width: 10%">Type <?php $csv_output .= trim('Type') . ","; ?></td>
															</tr>



															<?php
															$count = 0;
															$total_amount = 0;

															$result_detail2 = $this->db->query("SELECT * FROM `tbl_place_order_detail` where order_id='$order'")->result_array();

															foreach ($result_detail2 as $key => $row_detail2) {
																$materialcode = $row_detail2['materialcode'];
																$item_detail = $this->db->query("SELECT itemname, CONCAT('https://lpginsight.com/GasablePK/assets/images/items/', image_path) AS image_path, catcode, itemnameint, security_price FROM tblmaterial_coding WHERE materialcode = '$materialcode'")->row_array();
																$catcode = $item_detail['catcode'];

																// Fetch category name
																$catname = $this->db->query("SELECT catname FROM tblcategory WHERE id = '$catcode'")->row_array()['catname'];
																$count++;
															?>
																<tr class="even_frm_top" style="font-size: 10px;">
																	<td align="left">
																		<?php echo $count; ?>
																	</td>
																	<td align="left">
																		<?php echo $item_detail['itemname']; ?>
																	</td>
																	<td align="left">
																		<?php echo $catname; ?>
																	</td>

																	<td align="right">
																		<?php
																		$qty_total += $row_detail2['quantity'];

																		echo $row_detail2['quantity']; ?>
																	</td>
																	<td align="left">
																		<?php echo $row_detail2['type']; ?>
																	</td>
																</tr>

															<?php } ?>

														</table>
													</td>




											</tbody>
										</table>


									</div>
									<table id="dynamic-tables" class="table table-striped table-bordered table-hover" style="font-size: 10px;">
										<thead>
											<tr style="width: 100%;">
												<th colspan="12" style="background-color: #b10e0e;text-align: center;color:white;">Old Order Entries</th>
											</tr>
											<tr>
												<th>Customer</th>
												<th>Order&nbsp;Date</th>
												<th>Delivery Type</th>
												<th>Delivery Charges</th>
												<th>City Name</th>
												<th>Area</th>
												<th>Zone</th>
												<th>Delivery Address</th>
												<th>Updated&nbsp;By</th>
												<th>Updated&nbsp;Date</th>
												<th>Updated&nbsp;Time</th>
												<th>Action</th>
											</tr>
										</thead>

										<tbody>



											<?php

											$count = 0;
											$total_amount = 0;
											foreach ($old_result as $key => $value) {
												$count++;
												$order_id = $value['order_id'];
												$order_detail = $this->db->query("SELECT userid,address,area_id,area_name,city_id,deliveryType,date,sale_point_id,delivery_charges FROM `tbl_place_order` where id='$order_id'")->row_array();
												$userid = $order_detail['userid'];
												$address = $value['address'];
												$area_name = $order_detail['area_name'];
												$area_id = $order_detail['area_id'];
												$city_id = $order_detail['city_id'];
												$date = $order_detail['date'];
												$sale_point_id = $order_detail['sale_point_id'];
												$zone_id = $this->db->query("SELECT zone_id FROM `tbl_zone_detail` where id='$area_id'")->row_array()['zone_id'];
										$zone_name = $this->db->query("SELECT zone_name FROM `tbl_zone` where id='$zone_id'")->row_array()['zone_name'];

												$user_detail = $this->db->query("SELECT * FROM `tbl_user` where id='$userid'")->row_array();
												$city_name = $this->db->query("SELECT city_name FROM `tbl_city` where city_id='$city_id'")->row_array()['city_name'];
												$created_by = $value['created_by'];
												$created_time = $value['created_time'];
												$created_date = $value['created_date'];
												$admin_name = $this->db->query("SELECT admin_name FROM `tbl_admin` where id='$created_by'")->row_array()['admin_name'];
												$log_id = $value['id'];
											?>

												<tr class="even_frm_top">


													<td><?php echo $user_detail['name']; ?></b></td>
													<td><?php echo $date; ?> </td>
													<td><?php echo $order_detail['deliveryType']; ?></b></td>
													<td><?php echo number_format($order_detail['delivery_charges'], 2); ?></b></td>
													<td><?php echo $city_name; ?></b></td>
													<td><?php echo $area_name; ?></b></td>
													<td><?php echo $zone_name; ?></b></td>
													<td><?php echo $address; ?></b></td>
													<td><?php echo $admin_name; ?></b></td>
													<td><?php echo $created_date; ?></b></td>
													<td><?php echo $created_time; ?></b></td>

													<td align="left" style="cursor: pointer; text-decoration: underline;"><img src="<?php echo SURL ?>assets/images/reports/plus.png" id="<?php echo '_' . $donotremove; ?>" onclick="toggle1('<?php echo '_' . $count; ?>');" />
													</td>
												</tr>

										</tbody>
										<tbody>

											<tr>

												<td colspan="5">
													<table width="1000" id="<?php echo "_" . $count; ?>" style="display: none;font-size: 10px;" class="table table-striped table-bordered table-hover">
														<tr class="exist_rec_sb" style="font-size: 10px;">
															<td style="width: 3%">#<?php $csv_output .= trim('Sr No.') . ","; ?></td>
															<td style="width: 10%">Item&nbsp;Name <?php $csv_output .= trim('Item Name') . ","; ?></td>
															<td style="width: 10%">Category <?php $csv_output .= trim('Category') . ","; ?></td>
															<td style="width: 15%" align="right">Quantity<?php $csv_output .= trim('Quantity') . ","; ?></td>
															<td style="width: 10%">Type <?php $csv_output .= trim('Type') . ","; ?></td>
														</tr>



														<?php
														$count = 0;
														$total_amount = 0;

														$result_detail2 = $this->db->query("SELECT * FROM `tbl_order_log_detail` where log_id='$log_id'")->result_array();

														foreach ($result_detail2 as $key => $row_detail2) {
															$materialcode = $row_detail2['materialcode'];
															$item_detail = $this->db->query("SELECT itemname, CONCAT('https://lpginsight.com/GasablePK/assets/images/items/', image_path) AS image_path, catcode, itemnameint, security_price FROM tblmaterial_coding WHERE materialcode = '$materialcode'")->row_array();
															$catcode = $item_detail['catcode'];

															// Fetch category name
															$catname = $this->db->query("SELECT catname FROM tblcategory WHERE id = '$catcode'")->row_array()['catname'];
															$count++;
														?>
															<tr class="even_frm_top" style="font-size: 10px;">
																<td align="left">
																	<?php echo $count; ?>
																</td>
																<td align="left">
																	<?php echo $item_detail['itemname']; ?>
																</td>
																<td align="left">
																	<?php echo $catname; ?>
																</td>

																<td align="right">
																	<?php
																	$qty_total += $row_detail2['quantity'];

																	echo $row_detail2['quantity']; ?>
																</td>
																<td align="left">
																	<?php echo $row_detail2['type']; ?>
																</td>
															</tr>

														<?php } ?>

													</table>
												</td>




										</tbody>

									<?php }
									?>
									</table>
								</div>
							</div>



							<!-- PAGE CONTENT ENDS -->
						</div><!-- /.col -->
					</div><!-- /.row -->
				</div><!-- /.page-content -->
			</div>
			<div>



			</div>
		</div><!-- /.main-content -->


	</div><!-- /.main-container -->

	<!-- basic scripts -->

	<!--[if !IE]> -->
	<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
	<script>
		function toggle(id) {
			// alert(id);
			$(".show_detail_" + id).toggle();
			// body...
		}
	</script>
	<script src="<?php echo SURL; ?>assets/js/jquery-2.1.4.min.js"></script>

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

		function toggle1(cls) {

			// var cls1 = cls.split(",");

			if (document.getElementById(cls).style.display == 'block') {
				document.getElementById(cls).style.display = 'none';
				document.getElementById(cls1[1]).src = '<?php echo SURL ?>assets/images/reports/plus.png';
			} else {
				document.getElementById(cls).style.display = 'block';
				document.getElementById(cls1[1]).src = '<?php echo SURL ?>assets/images/reports/minus.png';
			}
		}
	</script>

</body>

</html>