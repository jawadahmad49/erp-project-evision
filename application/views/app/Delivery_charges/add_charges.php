<!DOCTYPE html>
<html lang="en">
<?php
$this->load->view('app/include/head');
$this->load->view('app/include/header');
?>

<body class="no-skin">

	<div class="main-container ace-save-state" id="main-container">

		<?php $this->load->view('app/include/sidebar'); ?>


		<fieldset class="scheduler-border">

			<div class="main-content">
				<div class="main-content-inner">
					<div class="breadcrumbs ace-save-state" id="breadcrumbs">
						<ul class="breadcrumb">
							<li>
								<i class="ace-icon fa fa-home home-icon"></i>
								<a href="<?php echo SURL . "admin"; ?>">Home</a>
							</li>

							<li>
								<a href="<?php echo SURL . "app/Delivery_charges"; ?>">Delivery Charges List <?php if ($arabic_check == 'Yes') { ?>(قائمة العملاء)<?php } ?> </a>
							</li>
							<li class="active"><?php echo ucwords($filter); ?> Delivery Charges<?php if ($arabic_check == 'Yes') { ?>(أضف الزبون)<?php } ?> </li>
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
								LPG <?php if ($arabic_check == 'Yes') { ?>(نقاط البيع)<?php } ?>
								<small>
									<i class="ace-icon fa fa-angle-double-right"></i>
									<?php echo ucwords($filter); ?> Delivery Charges <?php if ($arabic_check == 'Yes') { ?>(أضف الزبون)<?php } ?>
								</small>
							</h1>
						</div><!-- /.page-header -->
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
										Oh snap!
									</strong>

									<?php echo $this->session->flashdata('err_message'); ?>
									<br>
								</div>

								<?php
							} ?>


						</div>

						<form class="form-horizontal" role="form" id="c_form" method="post" action="<?php echo SURL . "app/Delivery_charges/" . $filter ?>" enctype="multipart/form-data">
							<!-- <form class="form-horizontal" role="form" id="c_form" method="post" action="<?php echo SURL . "app/Delivery_charges/getExtra_DevCharges" ?>" enctype="multipart/form-data"> -->
							<?php if ($arabic_check == 'Yes') { ?>
								<div class="form-group">
									<label class="col-sm-4 control-label no-padding-right" for="form-field-1"> Language </label>

									<div class="col-sm-4" style="margin-top: 8px;">
										<input type="radio" onclick="english_lang()" checked="checked" name="lang" id="english">
										English
										<input style="margin-left: 2%;" type="radio" onclick="urdu_lang()" name="lang" id="urdu">Arabic
									</div>
								</div>
							<?php } ?>


							<div class="col-xs-12 col-sm-12">
								<!-- PAGE CONTENT BEGINS-->

								<div class="row">
									<div class="col-xs-12 col-sm-12">

										<div class=" widget-body " style="display: block;">
											<fieldset class="scheduler-border">
												<legend class="scheduler-border">Delivery Charges</legend>
												<div class="widget-main">
													<div class="form-group">
														<label class="col-sm-5 control-label no-padding-right" for="form-field-1"> Location </label>
														<div class="col-sm-3">
															<select class="chosen-select form-control" name="location" id="location" required onchange="get_zone()">
																<option value="">Select Location</option>
																<?php foreach ($salepoint as $key => $value) { ?>
																	<option value="<?php echo $value['sale_point_id']; ?>" <?php if ($record['sale_point_id'] == $value['sale_point_id']) {
																		   echo 'selected';
																	   } ?>><?php echo $value['sp_name']; ?></option>
																<?php } ?>
															</select>
														</div>
													</div>
													<div class="form-group">
														<label class="col-sm-5 control-label no-padding-right" for="form-field-1"> Zone </label>
														<div class="col-sm-3">
															<select class="chosen-select form-control" name="zone" id="zone" required>
															</select>
														</div>
													</div>

													<div class="form-group mt-3">
														<label class="col-sm-5 control-label no-padding-right" for="form-field-1">Effective Date</label>
														<div class="col-sm-3">
															<div class="input-group">
																<input name="e_date" class="form-control" id="id-date-picker-1" type="text" required>
																<span class="input-group-addon">
																	<i class="fa fa-calendar bigger-110"></i>
																</span>
															</div>
														</div>
													</div>

													<?php if ($status['deliver_type'] != 'fixed_charges') { ?>
														<div class="form-group">
															<label class="col-sm-2 control-label range no-padding-right" for="form-field-1">Default Range</label>

															<div class=" col-sm-4 range">
																<input maxlength="5" value="<?php echo $record['kilo_meters']; ?>" type="text" id="kilo_meters" name="kilo_meters" placeholder="Enter Kms " style="width:100%; " onkeypress="return /[0-9]/i.test(event.key)" title="Only Numbers Allowed..." />
															</div>
															<div class="col-sm-1"></div>

															<label class="col-sm-2 control-label range no-padding-right" for="form-field-1" style="left: 33px;">Charges Per Km</label>

															<div class=" col-sm-2 range">
																<input maxlength="5" value="<?php echo $record['charges_outside_range']; ?>" type="text" id="pkm_charges" name="pkm_charges" placeholder="Per Km Charges outside range " style="width: 100%; position: relative; left: 39px;" onkeypress="return /[0-9]/i.test(event.key)" title="Only Numbers Allowed..." />
															</div>
														</div>
													<?php } ?>
													<div class="form-group">
													</div>
													<?php if ($status['standard'] == 'standard') { ?>
														<div class="form-group">
															<label class="col-sm-5 control-label range no-padding-right" for="form-field-1"> Standard Charges </label>

															<div class="col-sm-3">
																<input maxlength="5" value="<?php echo $record['standard_range']; ?>" type="text" id="std_charges" name="std_charges" placeholder="Standard charges  " required style="width:100%;" onkeypress="return /[0-9 . ]/i.test(event.key)" title="Only Numbers Allowed..." />
															</div>




															<!-- <label class="col-sm-1 control-label range no-padding-right" for="form-field-1" style="width: 6%;">Outside Default </label>

														<div class="col-sm-1 range">
															<input maxlength="5" type="text" id="delivery" name="delivery" required style="width:100%;" onkeypress="return /[0-9 . ]/i.test(event.key)" title="Only Numbers Allowed..." />
														</div> -->

															<!-- <label class="col-sm-1 control-label range no-padding-right" for="form-field-1" style="width: 6%;"> Total </label>

														<div class="col-sm-1 range">
															<input maxlength="5" type="text" readonly name="standard" id="standard" required style="width:100%;" onkeypress="return /[0-9 . ]/i.test(event.key)" title="Only Numbers Allowed..." />
														</div> -->

															<!-- <label class="col-sm-3 control-label  no-padding-right" for="hours">Standard Delivery Time</label> -->

															<div class="col-sm-2" >
																<!-- <select id="s_hours" name="s_hours" required style="width:47%; display: inline-block;">
																	<?php for ($i = 0; $i < 24; $i++):
																		$stdrd_time = $record['stdrd_time'];
																		$time = explode(':', $stdrd_time)[0]; ?>

																		<option value="<?php echo str_pad($i, 2, '0', STR_PAD_LEFT); ?>" <?php if ($i == $time) {
																				  echo 'selected';
																			  } ?>>
																			<?php echo str_pad($i, 2, '0', STR_PAD_LEFT); ?>
																		</option>
																	<?php endfor; ?>
																</select> -->
																<!-- <select id="s_minutes" name="s_minutes" required style="width:50%; display: inline-block;">
																	<?php for ($i = 0; $i < 60; $i++):
																		$stdrd_time = $record['stdrd_time'];
																		$time = explode(':', $stdrd_time)[1];
																		?>
																		<option value="<?php echo str_pad($i, 2, '0', STR_PAD_LEFT); ?>" <?php if ($i == $time) {
																				  echo 'selected';
																			  } ?>>
																			<?php echo str_pad($i, 2, '0', STR_PAD_LEFT); ?>
																		</option>
																	<?php endfor; ?>
																</select> -->
															</div>
														</div>
													<?php } ?>
													<?php if ($status['express'] == 'express') { ?>

														<div class="form-group">

															<label class="col-sm-5 range control-label no-padding-right" for="form-field-1"> Express Charges </label>

															<div class="col-sm-3 ">
																<input maxlength="5" required value="<?php echo $record['express_range']; ?>" type="text" id="exp_charges" name="exp_charges" placeholder="Express Charges  " style="width:100%;" onkeypress="return /[0-9 . ]/i.test(event.key)" title="Only Numbers Allowed..." />
															</div>


															<!-- <label class="col-sm-1 control-label range no-padding-right" for="form-field-1" style="width: 6%;">Outside Default </label>

														<div class="col-sm-1 range">
															<input maxlength="5" type="text" id="express_delivery" name="express" required style="width:100%;" onkeypress="return /[0-9 . ]/i.test(event.key)" title="Only Numbers Allowed..." />
														</div> -->


															<!-- <label class="col-sm-1 control-label range no-padding-right" for="form-field-1" style="width: 6%;"> Total </label>

														<div class="col-sm-1 range">
															<input maxlength="5" type="text" id="express" readonly style="width:100%;" onkeypress="return /[0-9 . ]/i.test(event.key)" title="Only Numbers Allowed..." />
														</div> -->

															<!-- <label class="col-sm-3 control-label   no-padding-right" for="hours">Express Delivery Time</label> -->

															<!-- <div class="col-sm-2 ">
																<select id="ex_hours" name="ex_hours" required style="width:47%; display: inline-block;">
																	<?php for ($i = 0; $i < 24; $i++):
																		$expres_time = $record['expres_time'];
																		$time = explode(':', $expres_time)[0];
																		?>
																		<option value="<?php echo str_pad($i, 2, '0', STR_PAD_LEFT); ?>" <?php if ($i == $time) {
																				  echo 'selected';
																			  } ?>>
																			<?php echo str_pad($i, 2, '0', STR_PAD_LEFT); ?>
																		</option>
																	<?php endfor; ?>
																</select>
																<select id="ex_minutes" name="ex_minutes" required style="width:50%; display: inline-block;">
																	<?php for ($i = 0; $i < 60; $i++):
																		$expres_time = $record['expres_time'];
																		$time = explode(':', $expres_time)[1];
																		?>
																		<option value="<?php echo str_pad($i, 2, '0', STR_PAD_LEFT); ?>" <?php if ($i == $time) {
																				  echo 'selected';
																			  } ?>>
																			<?php echo str_pad($i, 2, '0', STR_PAD_LEFT); ?>
																		</option>
																	<?php endfor; ?>
																</select>
															</div>
														</div> -->
													<?php } ?>
													<!-- <?php //if ($status['night'] == 'night') { ?>

														<div class="form-group ">
															<label class="col-sm-2 range control-label no-padding-right" for="form-field-1"> Night Charges </label>

															<div class="col-sm-2">
																<input maxlength="5" minlength="1" placeholder="Night Charges" required value="<?php echo $record['night_range']; ?>" type="text" id="night_charges" name="night_charges" style="width:100%;" onkeypress="return /[0-9 . ]/i.test(event.key)" title="Only Numbers Allowed..." />
															</div> -->


															<!-- <label class="col-sm-1 control-label range no-padding-right" for="form-field-1" style="width: 6%;">Outside Default </label>

														<div class="col-sm-1 range">
															<input maxlength="5" type="text" id="night_delivery" name="nightdelivery" required style="width:100%;" onkeypress="return /[0-9 . ]/i.test(event.key)" title="Only Numbers Allowed..." />
														</div> -->


															<!-- <label class="col-sm-1 control-label range no-padding-right" for="form-field-1" style="width: 6%;"> Total </label>

														<div class="col-sm-1 range">
															<input maxlength="5" type="text" id="night" required style="width:100%;" onkeypress="return /[0-9 . ]/i.test(event.key)" title="Only Numbers Allowed..." />
														</div> -->

															<!-- <label class="col-sm-3 control-label   no-padding-right" for="hours">Night Delivery Time</label>

															<div class="col-sm-2 ">
																<select id="nit_hours" name="nit_hours" required style="width:47%; display: inline-block;">
																	<?php for ($i = 0; $i < 13; $i++):
																		$night_time = $record['night_time'];
																		$time = explode(':', $night_time)[0];
																		?>
																		<option value="<?php echo str_pad($i, 2, '0', STR_PAD_LEFT); ?>" <?php if ($i == $time) {
																				  echo 'selected';
																			  } ?>>
																			<?php echo str_pad($i, 2, '0', STR_PAD_LEFT); ?>
																		</option>
																	<?php endfor; ?>
																</select>
																<select id="nit_minutes" name="nit_minutes" required style="width:50%; display: inline-block;">
																	<?php for ($i = 0; $i < 61; $i++):
																		$night_time = $record['night_time'];
																		$time = explode(':', $night_time)[1]; ?>

																		<option value="<?php echo str_pad($i, 2, '0', STR_PAD_LEFT); ?>" <?php if ($i == $time) {
																				  echo 'selected';
																			  } ?>>
																			<?php echo str_pad($i, 2, '0', STR_PAD_LEFT); ?>
																		</option>
																	<?php endfor; ?>
																</select>
															</div>
														</div> -->
													<?php //} ?>

													<!-- <div class="form-group">
														<label class="col-sm-5 range control-label no-padding-right" for="form-field-1">Order Distance </label>

														<div class="col-sm-5">
															<input maxlength="5" minlength="1" onkeyup="getExtra_DevCharges(); total()" name="order_dist" id="order_dist" placeholder="Order Distance in Kms" required type="text" onkeypress="return /[0-9 . ]/i.test(event.key)" title="Only Numbers Allowed..." />
														</div>
													</div> -->
												</div>
											</fieldset>
										</div>
									</div>
								</div>

								<div class="row">
									<input type="hidden" id="code_id" name="code_id" value="0" />
									<hr />
									<div class="form-actions center">
										<!-- <input type="text" id="extra_del" onclick="getExtra_DevCharges()"> -->

										<button class="btn btn-info" onclick="reload_parent();">
											<i class="ace-icon fa fa-check bigger-110"></i>
											Submit <?php if ($arabic_check == 'Yes') { ?> (إرسال) <?php } ?>
										</button>
									</div>

									<input type="hidden" name="action" value="" />
									<input type="hidden" name="edit" id='edit' value="<?php echo $record['id']; ?>" />
								</div>
							</div>
						</form>
					</div>
				</div>
			</div>
		</fieldset>
	</div><!-- /.main-container -->

	<?php
	$this->load->view('app/include/footer');
	$this->load->view('app/include/js');
	?>

	<!-- inline scripts related to this page -->


	<!-- start editor  -->

	<!-- page specific plugin scripts -->

	<!-- <?php $this->load->view('app/include/customer_js.php'); ?> -->


	<script src="http://ajax.googleapis.com/ajax/libs/jquery/1.2.6/jquery.js" type="text/javascript"></script>

	<script src="<?php echo SURL ?>assets/js/jquery.UrduEditor.js" type="text/javascript"></script>

	<script src="http://ajax.googleapis.com/ajax/libs/jquery/1.2.6/jquery.min.js" type="text/javascript"></script>

	<script type="text/javascript">
		var test_final = jQuery.noConflict($);

		$(document).ready(function ($) {

			jQuery(".urdu_class").each(function (index) {
				jQuery(this).UrduEditor();
				setEnglish($(this));
				jQuery(this).removeAttr('dir');

			});
		});
	</script>
	<!-- jQuery library -->
	<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
	<!-- Flatpickr CSS -->
	<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
	<!-- Flatpickr JS -->
	<script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
	<script>
		document.addEventListener('DOMContentLoaded', function () {
			flatpickr("#id-date-picker-1", {
				dateFormat: "Y-m-d", // Format as yyyy-mm-dd
				defaultDate: "<?php echo $record['e_date'] ? $record['e_date'] : date('Y-m-d'); ?>"
			});
		});
	</script>

	<script>
		$("#id-date-picker-1").blur(function () {
			//alert('asdas');
			// date = $(this).val();
			// alert(date);
			// stock(date);
		});

		function english_lang() {

			jQuery(".urdu_class").each(function (index) {

				jQuery(this).removeAttr('dir');
				setEnglish(jQuery(this));

			});

		}


		function reload_parent() {
			var type = '<?php echo $_GET["type"] ?>';

			if (type == "sale") {
				//self.close();
				window.opener.document.location.reload(true);

			}

		}

		function getExtra_DevCharges() {

			var range = $('#kilo_meters').val()
			var per_km_charges = $('#pkm_charges').val()
			var stan_delivery_charges = $('#std_charges').val()
			var express_delivery_charges = $('#exp_charges').val()
			var night_delivery_charges = $('#night_charges').val()
			var total_dist = $('#order_dist').val()


			$.ajax({
				url: "<?php echo SURL . "app/Delivery_charges/getExtra_DevCharges"; ?>",
				cache: false,
				type: "POST",
				data: {
					range: range,
					per_km_charges: per_km_charges,
					stan_delivery_charges: stan_delivery_charges,
					express_delivery_charges: express_delivery_charges,
					night_delivery_charges: night_delivery_charges,
					total_dist: total_dist
				},
				success: function (response) {
					var data = JSON.parse(response);

					$('#delivery').val(data.delivery);
					$('#express_delivery').val(data.expressdelivery);
					$('#night_delivery').val(data.nightdelivery);

					var standard = parseFloat($('#delivery').val()) + parseFloat($('#std_charges').val());
					$('#standard').val(standard);

					var express = parseFloat($('#express_delivery').val()) + parseFloat($('#exp_charges').val());
					$('#express').val(express);

					var night = parseFloat($('#night_delivery').val()) + parseFloat($('#night_charges').val());
					$('#night').val(night);

					$('#extra_del').attr('readonly', true);
					$('#extra_del').val(response)
				}
			});
		}
		get_zone()
		function get_zone() {
			var location = $('#location').val();
			var edit = "<?php echo $record['zone']; ?>";  // Check if this is correctly set

			// console.log("Location:", location);
			// console.log("Edit:", edit);  // Log to see what is being sent

			$.ajax({
				url: "<?php echo SURL . 'app/Delivery_charges/get_zone'; ?>",
				cache: false,
				type: "POST",
				data: {
					location: location,
					edit: edit
				},
				success: function (html) {
					$('#zone').html(html); // Update the #zone dropdown with the response HTML
				},
				error: function (xhr, status, error) {
					console.error("An error occurred while fetching zones: ", status, error);
				}
			});
		}


		function range_or_per() {
			var type = $("#type").val();

			if (type == 'Manual Range') {
				$(".per").hide();

				$("#std_charges_per_km").removeAttr("required");
				$("#exp_charges_per_km").removeAttr("required");
				$("#night_charges_per_km").removeAttr("required");

				$("#kilo_meters").attr("required", "required");
				$("#std_charges").attr("required", "required");
			} else {
				$(".per").show();

				$("#std_charges_per_km").attr("required", "required");
				$("#exp_charges_per_km").attr("required", "required");
				$("#night_charges_per_km").attr("required", "required");

				$("#kilo_meters").removeAttr("required");
				$("#std_charges").removeAttr("required");
			}
		}
	</script>


	<!-- end editor -->
	<style>
		.scheduler-border {
			border: 1px solid #ccc;
			/* Border style */
			padding: 5px 10px;
			/* Padding to give some space around the text */
			border-radius: 5px;
			/* Optional: Rounded corners */
		}
	</style>
</body>

</html>