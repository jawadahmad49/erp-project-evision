<!DOCTYPE html>
<html lang="en">
<?php
$this->load->view('en/include/head');
$this->load->view('en/include/header');
?>

<body class="no-skin">
	<div class="main-container ace-save-state" id="main-container">
		<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.4.1/jquery.min.js"></script>

		<?php $this->load->view('en/include/sidebar');
		?>
		<div class="main-content">
			<div class="main-content-inner">
				<div class="breadcrumbs ace-save-state" id="breadcrumbs">
					<ul class="breadcrumb">
						<li>
							<i class="ace-icon fa fa-home home-icon"></i>
							<a href="<?php echo SURL . "admin"; ?>">Home</a>
						</li>

						<li>
							<a href="<?php echo SURL . "Order_booking"; ?>">Item List <?php if ($arabic_check == 'Yes') { ?> (قائمة البند) <?php } ?></a>
						</li>
						<li class="active">Add Item <?php if ($arabic_check == 'Yes') { ?>(اضافة عنصر)<?php } ?></li>
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

						<div class="ace-settings-box clearfix" id="ace-settings-box">
							<div class="pull-left width-50">
								<div class="ace-settings-item">
									<div class="pull-left">
										<select id="skin-colorpicker" class="hide">
											<option data-skin="no-skin" value="#438EB9">#438EB9</option>
											<option data-skin="skin-1" value="#222A2D">#222A2D</option>
											<option data-skin="skin-2" value="#C6487E">#C6487E</option>
											<option data-skin="skin-3" value="#D0D0D0">#D0D0D0</option>
										</select>
									</div>
									<span>&nbsp; Choose Skin</span>
								</div>

								<div class="ace-settings-item">
									<input type="checkbox" class="ace ace-checkbox-2 ace-save-state" id="ace-settings-navbar" autocomplete="off" />
									<label class="lbl" for="ace-settings-navbar"> Fixed Navbar</label>
								</div>

								<div class="ace-settings-item">
									<input type="checkbox" class="ace ace-checkbox-2 ace-save-state" id="ace-settings-sidebar" autocomplete="off" />
									<label class="lbl" for="ace-settings-sidebar"> Fixed Sidebar</label>
								</div>

								<div class="ace-settings-item">
									<input type="checkbox" class="ace ace-checkbox-2 ace-save-state" id="ace-settings-breadcrumbs" autocomplete="off" />
									<label class="lbl" for="ace-settings-breadcrumbs"> Fixed Breadcrumbs</label>
								</div>

								<div class="ace-settings-item">
									<input type="checkbox" class="ace ace-checkbox-2" id="ace-settings-rtl" autocomplete="off" />
									<label class="lbl" for="ace-settings-rtl"> Right To Left (rtl)</label>
								</div>

								<div class="ace-settings-item">
									<input type="checkbox" class="ace ace-checkbox-2 ace-save-state" id="ace-settings-add-container" autocomplete="off" />
									<label class="lbl" for="ace-settings-add-container">
										Inside
										<b>.container</b>
									</label>
								</div>
							</div><!-- /.pull-left -->

							<div class="pull-left width-50">
								<div class="ace-settings-item">
									<input type="checkbox" class="ace ace-checkbox-2" id="ace-settings-hover" autocomplete="off" />
									<label class="lbl" for="ace-settings-hover"> Submenu on Hover</label>
								</div>

								<div class="ace-settings-item">
									<input type="checkbox" class="ace ace-checkbox-2" id="ace-settings-compact" autocomplete="off" />
									<label class="lbl" for="ace-settings-compact"> Compact Sidebar</label>
								</div>

								<div class="ace-settings-item">
									<input type="checkbox" class="ace ace-checkbox-2" id="ace-settings-highlight" autocomplete="off" />
									<label class="lbl" for="ace-settings-highlight"> Alt. Active Item</label>
								</div>
							</div><!-- /.pull-left -->
						</div><!-- /.ace-settings-box -->
					</div><!-- /.ace-settings-container -->
					<!-- <div class="page-header">
							<h1>
								POS <?php if ($arabic_check == 'Yes') { ?>(نقاط البيع
)<?php } ?>		
								<small>
									<i class="ace-icon fa fa-angle-double-right"></i>
									Add Item <?php if ($arabic_check == 'Yes') { ?>(اضافة عنصر)<?php } ?>
								</small>
							</h1>
						</div>/.page-header -->


					<!-- PAGE CONTENT BEGINS -->
					<div class="row">
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
										Well done!
									</strong>
									<?php echo $this->session->flashdata('ok_message'); ?>
								</p>
							</div>

						<?php
						}
						?>
					</div>

					<div class="row">
						<div class="col-xs-12 col-sm-12">
							<style type="text/css">
								fieldset.scheduler-border {
									border: 1px groove #ddd !important;
									padding: 0 1.4em 1.4em 1.4em !important;
									margin: 0 0 1.5em 0 !important;
									-webkit-box-shadow: 0px 0px 0px 0px #000;
									box-shadow: 0px 0px 0px 0px #000;
								}

								legend.scheduler-border {
									font-size: 1.2em !important;
									font-weight: bold !important;
									text-align: left !important;
									width: auto;
									padding: 0 10px;
									border-bottom: none;
								}
							</style>

							<fieldset class="scheduler-border">
								<legend class="scheduler-border">Required Fields !</legend>
								<input hidden type='text' name='item[]' value='<?php echo $value['id']; ?>' />

								<div class="form-group col-xs-12">
									<label class="col-sm-2 control-label align-left" for="form-field-1"><strong>Item</strong></label>
									<div class="col-sm-3">
										<select class="chosen-select2 form-control" name="item_code" onchange="get_stock(this.value)" required id="item" data-placeholder="Choose Item...">
											<option value="">Choose a Item..</option>
											<?php
											foreach ($item as $key => $data) { ?>
												<option value="<?php echo $data['materialcode']; ?>"><?php echo ucwords($data['itemname']); ?></option>
											<?php } ?>
										</select>
									</div>
									<label class="col-sm-2 control-label align-left" for="form-field-1"><strong>Enter Quantity</strong></label>
									<div class="col-sm-3">
										<input type="text" maxlength="2" name="num_of_days" readonly style="width:100%" id="num_of_days" value="2.0kg" placeholder="Enter Quantity (Kgs/Tons)" class="text-input">
									</div>
								</div>

								<div class="form-group col-xs-12">

									<label class="col-sm-2 control-label align-left" for="form-field-1"><strong>Sale Rate</strong></label>
									<div class="col-sm-3">
										<input type="text" maxlength="2" name="num_of_days" readonly style="width:100%" id="num_of_days" value="1500.00" placeholder="Sale Rate" class="text-input">
									</div>

									<label class="col-sm-2 control-label align-left" for="form-field-1"><strong>Total Amount</strong></label>
									<div class="col-sm-3">
										<input type="text" maxlength="2" name="num_of_days" readonly style="width:100%" id="num_of_days" value="1500.00" placeholder="Total Amount" class="text-input">
									</div>

								</div>
								<div class="form-group col-xs-12">

									<label class="col-sm-2 control-label align-left" for="form-field-1"><strong>Delivery Date</strong></label>
									<div class="col-sm-3">

										<div class="input-group">
											<input name="from_date" class="form-control date-picker form_date" id="id-date-picker-1" type="text" data-date-format="yyyy-mm-dd" required="" value="<?php echo date('Y-m-d'); ?>">
											<span class="input-group-addon">
												<i class="fa fa-calendar bigger-110"></i>
											</span>
										</div>
									</div>

									<label class="col-sm-2 control-label align-left" for="form-field-1"><strong>Location</strong></label>
									<div class="col-sm-3">
										<input type="text" maxlength="2" name="num_of_days" readonly style="width:100%" id="num_of_days" value="Barka Sanyia, Sultanate of Oman" placeholder="Total Amount" class="text-input">
									</div>

								</div>


								<div class="col-xs-12">

									<div class="row">

										<hr />
										<div class="form-actions center">
											<a class="btn btn-info btnsubmit" target="blank" class="" title="Print Invoice" href="<?php echo SURL . "Order_booking/detail_invoice/" ?>">
											Order Summary Details 
																</a>
											<button class="btn btn-info btnsubmit" onclick="check_already()">
												<i class="ace-icon fa fa-check bigger-110"></i>
												Submit
											</button>
										</div>

									</div>

									<input type="hidden" name="id" value="<?php echo $record['id']; ?>" />

								</div>
							</fieldset>
						</div><!-- /.col -->
					</div><!-- /.row -->
				</div><!-- /.page-content -->
			</div><!-- /.main-content-inner -->
		</div><!-- /.main-content -->
	</div><!-- /.main-container -->
	<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>

	<?php
	$this->load->view('en/include/footer');
	?>
	<?php
	$this->load->view('en/include/js');
	?>

	<script type="text/javascript">
		$(document).ready(function() {
			$("#loccode").focus();
		});

		function check_already() {
			var loccode = document.getElementById("loccode").value;
			var deptcode = document.getElementById("deptcode").value;
			var leave_type = document.getElementById("leave_type").value;
			var leave_period = document.getElementById("leave_period").value;
			var num_of_days = document.getElementById("num_of_days").value;

			$.ajax({
				url: "<?php echo SURL . "crm/Leave_entitlement/check_already"; ?>",
				cache: false,
				type: "POST",
				data: {
					loccode: loccode,
					deptcode: deptcode,
					leave_type: leave_type,
					leave_period: leave_period,
					num_of_days: num_of_days
				},
				success: function(html) {
					if (html == "insert_") {
						insert_ind();

					}
					if (html == "error_") {
						if (confirm("Leave entitlement is already recorded for selected Leave Type! \n\nAre you sure to update existing entitlement ! ")) {
							insert_ind();
						} else {
							return false;
						}
					}
				}
			});

		}

		function insert_ind() {
			var loccode = document.getElementById("loccode").value;
			var deptcode = document.getElementById("deptcode").value;
			var leave_type = document.getElementById("leave_type").value;
			var leave_period = document.getElementById("leave_period").value;
			var num_of_days = document.getElementById("num_of_days").value;
			// alert(loccode)
			$.ajax({
				url: "<?php echo SURL . "crm/Leave_entitlement/insert_ind"; ?>",
				cache: false,
				type: "POST",
				data: {
					loccode: loccode,
					deptcode: deptcode,
					leave_type: leave_type,
					leave_period: leave_period,
					num_of_days: num_of_days
				},
				success: function(html) {
					// alert(html)
					if (html == "ok_") {
						alert("Leave entitlement is recorded for selected Leave Type!");
						return false;
					}
				}
			});

		}
	</script>

	<!-- inline scripts related to this page -->


	<!-- page specific plugin scripts -->

	<?php $this->load->view('en/include/customer_js.php'); ?>







</body>

</html>