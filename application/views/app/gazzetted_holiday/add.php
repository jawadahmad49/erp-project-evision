<!DOCTYPE html>
<html lang="en">
<?php
$this->load->view('en/include/head');
$this->load->view('en/include/header');
?>

<body class="no-skin">
	<div class="main-container ace-save-state" id="main-container">
		<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.4.1/jquery.min.js"></script>

		<?php $this->load->view('app/include/sidebar');
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
							<a href="<?php echo SURL . "app/Gazzetted_holidays"; ?>">Manage Holidays</a>
						</li>
						<li class="active"><?php echo $title; ?></li>
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

					<div class="page-header">
						<h1>
							Manage Holidays
							<small>
								<i class="ace-icon fa fa-angle-double-right"></i>
								<?php echo $title; ?>
							</small>
						</h1>
					</div>

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
						}   ?>
					</div>


					<form class="form-horizontal" role="form" method="post" action="" enctype="multipart/form-data">
						<div class="row">
							<div class="col-xs-12 col-sm-12">


								<script type="text/javascript">
									$(document).on('change', '#vendortype', function() {
										var vall = $(this).val();
										if (vall == "Foreigner") {
											$(".convorate").show();
										} else {
											$(".convorate").hide();
										}
									});

									$(document).on('change', '#vendortype', function() {
										var vall = $(this).val();
										if (vall == "Foreigner") {
											$(".convorate").show();
										} else {
											$(".convorate").hide();
										}
									});
								</script>
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
									<div class="form-group">
										<label class="col-sm-2 control-label no-padding-right" for="form-field-1"> Holiday Description * </label>

										<div class="col-sm-3">
											<input maxlength="20" title="Enter Advance Type !" value="<?php echo ucwords($record['holiday_name']); ?>" type="text" id="holiday_name" name="holiday_name" Required class="validate[required,custom[firstCharacter] text-input" style="width:357px;">
										</div>
									</div>

									<div class="form-group">
										<div class="col-xs-2" style="text-align: right;margin-left: -4px; padding-top: 7px;">From Date</div>
										<div class="col-sm-3">
											<div class="input-group">
												<input name="from_date" id="from_date" autofocus type="text" data-date-format="yyyy-mm-dd" class="form-control date_picker date" required value="<?php echo isset($record['from_date']) ? $record['from_date'] : date('Y-m-d'); ?>" onchange="calculateDays()">
												<span class="input-group-addon">
													<i class="fa fa-calendar bigger-110"></i>
												</span>
											</div>
										</div>
									</div>

									<div class="form-group">
										<div class="col-xs-2" style="text-align: right;margin-left: -4px; padding-top: 7px;">To Date</div>
										<div class="col-sm-3">
											<div class="input-group">
												<input name="to_date" id="to_date" autofocus type="text" data-date-format="yyyy-mm-dd" class="form-control date_picker date" required value="<?php echo isset($record['to_date']) ? $record['to_date'] : date('Y-m-d'); ?>" onchange="calculateDays()">
												<span class="input-group-addon">
													<i class="fa fa-calendar bigger-110"></i>
												</span>
											</div>
										</div>
									</div>


									<div class="form-group">

										<label class="col-sm-2 control-label no-padding-right" for="form-field-1"> Repeats Annually</label>
										<div class="col-sm-3">
											<select class="chosen-select form-control" name="repeats_annualy" id="repeats_annualy" data-placeholder="Choose a Item..." Required>

												<option <?php if ($record['repeats_annualy'] == "Yes") {
															echo "selected";
														} ?> value="Yes">Yes</option>
												<option <?php if ($record['repeats_annualy'] == "No") {
															echo "selected";
														} ?> value="No">No </option>
											</select>
										</div>
									</div>
									<div class="form-group">
										<label class="col-sm-2 control-label no-padding-right" for="form-field-1"> Number of Days </label>
										<div class="col-sm-3">
											<input maxlength="20" title="" value="<?php echo ucwords($record['duration']); ?>" type="text" id="full_half" name="full_half" class="validate[required,custom[firstCharacter]] text-input" style="width:357px;" required readonly>
										</div>
									</div>

									<script>
										function calculateDays() {
											var fromDate = document.getElementById("from_date").value;
											var toDate = document.getElementById("to_date").value;

											if (fromDate && toDate) {
												var start = new Date(fromDate);
												var end = new Date(toDate);

												// Check if From Date is greater than To Date
												// if (start > end) {
												// 	alert("From Date cannot be later than To Date.");
												// 	document.getElementById("from_date").value = ""; // Optionally reset the From Date
												// 	document.getElementById("full_half").value = 0; // Reset the Number of Days field
												// 	return;
												// }

												// Calculate the difference in milliseconds
												var diff = end - start;

												// Convert milliseconds to days
												var days = diff / (1000 * 60 * 60 * 24);
												var days = days + 1;
												// Update the Number of Days field
												document.getElementById("full_half").value = days >= 0 ? days : 0;
											}
										}
									</script>
									<div class="col-xs-12">
										<div class="row">
											<input type="hidden" id="code_id" name="code_id" value="0" />
											<hr />

											<div class="form-actions center">
												<button onclick="sub_form()" class="btn btn-info">
													<i class="ace-icon fa fa-check bigger-110"></i>
													Submit
												</button>

											</div>

										</div>
										<input type="hidden" name="duration" id="duration" />
										<input type="hidden" name="id" id="code" value="<?php echo $record['holiday_code']; ?>" />
									</div><!-- /.col -->
								</fieldset>
					</form>

				</div><!-- /.row -->
			</div><!-- /.page-content -->
		</div>
	</div><!-- /.main-content -->

	</div><!-- /.main-container -->

	<?php
	$this->load->view('en/include/footer');
	?>
	<?php
	$this->load->view('en/include/js');
	?>

	<script type="text/javascript">
		flatpickr('.date_picker', {
			dateFormat: "Y-m-d",
			// maxDate: "today",
		});
		var code = document.getElementById("code").value;

		function sub_form() {

			// if (confirm("Are you sure to add holiday ! ")) {
				if (code !== "") {
					update();
				} else {
					insert();
				}
			// } else {
			// 	return false;
			// }
		}

		function insert() {

			var holiday_name = document.getElementById("holiday_name").value;
			var from_date = document.getElementById("from_date").value;
			var to_date = document.getElementById("to_date").value;
			var full_half = document.getElementById("full_half").value;
			var repeats_annualy = document.getElementById("repeats_annualy").value;
			var duration = document.getElementById("duration").value;
			var fromDate = document.getElementById("from_date").value;
			var toDate = document.getElementById("to_date").value;
			if (fromDate == '' || fromDate == 'NULL') {
				alert("Please Select From Date!");
				return false;
			}
			if (toDate == '' || toDate == 'NULL') {
				alert("Please Select To Date!");
				return false;
			}
			var start = new Date(fromDate);
			var end = new Date(toDate);
			// Check if From Date is greater than To Date
			if (start > end) {
				alert("From Date cannot be later than To Date.");
				document.getElementById("from_date").value = ""; // Optionally reset the From Date
				document.getElementById("full_half").value = 0; // Reset the Number of Days field
				return false;
			}

			$.ajax({
				url: "<?php echo SURL . "app/Gazzetted_holidays/insert"; ?>",
				cache: false,
				type: "POST",
				data: {
					holiday_name: holiday_name,
					from_date: from_date,
					to_date: to_date,
					full_half: full_half,
					repeats_annualy: repeats_annualy,
					duration: duration,
				},
				success: function(html) {
					if (html == 'already_on_same') {
						alert("Operation Failed !\n\nHoliday already record for same period  'From date' and 'To Date' \n\nPlease select valid period.");
						return false;
					}
					if (html == 'already_on_period') {
						alert("Operation Failed !\n\nHoliday period overlaped, already recorded in same period 'From date' or 'To Date' \n\nPlease select valid period.");
						return false;
					}
					if (html == "success") {
						alert("Holiday Add/Update Successfully !");
						window.location.href = "<?php echo SURL ?>app/Gazzetted_holidays";
						return false;
					}






				}

			});

		}

		function update() {

			var holiday_name = document.getElementById("holiday_name").value;
			var from_date = document.getElementById("from_date").value;
			var to_date = document.getElementById("to_date").value;
			var full_half = document.getElementById("full_half").value;
			var repeats_annualy = document.getElementById("repeats_annualy").value;
			var duration = document.getElementById("duration").value;
			var code = document.getElementById("code").value;

			$.ajax({
				url: "<?php echo SURL . "app/Gazzetted_holidays/update"; ?>",
				cache: false,
				type: "POST",
				data: {
					holiday_name: holiday_name,
					from_date: from_date,
					to_date: to_date,
					full_half: full_half,
					repeats_annualy: repeats_annualy,
					duration: duration,
					code: code,
				},
				success: function(html) {
					if (html !== 'already') {
						alert("Holiday Add/Update Successfully !");
						return false;
					} else {
						alert("Holiday has child ! Operation Failed.");
					}

				}

			});

		}
	</script>

	<!-- inline scripts related to this page -->


	<script type="text/javascript">
		document.getElementById("holiday_name").focus();
	</script>

	<!-- start editor  -->

	<!-- page specific plugin scripts -->

	<?php $this->load->view('en/include/customer_js.php'); ?>



	<script src="http://ajax.googleapis.com/ajax/libs/jquery/1.2.6/jquery.js" type="text/javascript"></script>

	<script src="<?php echo SURL ?>assets/js/jquery.UrduEditor.js" type="text/javascript"></script>

	<script src="http://ajax.googleapis.com/ajax/libs/jquery/1.2.6/jquery.min.js" type="text/javascript"></script>

	<script type="text/javascript">

	</script>

	<style type="text/css">
		.chosen-container-multi {
			width: 282px !important;
		}

		.default {
			padding: 14px 6px 13px !important;
		}
	</style>

	<!-- end editor -->
</body>

</html>