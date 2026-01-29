<!DOCTYPE html>
<html lang="en">
<?php
$this->load->view('app/include/head');
$this->load->view('app/include/header');
?>

<body class="no-skin">

	<div class="main-container ace-save-state" id="main-container">

		<?php $this->load->view('app/include/sidebar'); ?>


		<!-- <fieldset class="scheduler-border"> -->

		<div class="main-content">
			<div class="main-content-inner">
				<div class="breadcrumbs ace-save-state" id="breadcrumbs">
					<ul class="breadcrumb">
						<li>
							<i class="ace-icon fa fa-home home-icon"></i>
							<a href="<?php echo SURL . "admin"; ?>">Home</a>
						</li>

						<li>
							<a href="<?php echo SURL . "app/Vehicle_coding"; ?>">Vehicle Coding List <?php if ($arabic_check == 'Yes') { ?>(قائمة العملاء)<?php } ?> </a>
						</li>
						<li class="active"><?php echo ucwords($filter); ?> Vehicle Coding<?php if ($arabic_check == 'Yes') { ?>(أضف الزبون)<?php } ?> </li>
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
								<?php echo ucwords($filter); ?> Vehicle Coding <?php if ($arabic_check == 'Yes') { ?>(أضف الزبون)<?php } ?>
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

					<form class="form-horizontal" role="form" method="post" action="<?php echo SURL . "app/Vehicle_coding/" . $filter ?>" enctype="multipart/form-data">
						<!-- Language Selection -->
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
							<div class="row">
								<div class="col-xs-12 col-sm-12">
									<div class="widget-body" style="display: block;">
										<fieldset class="scheduler-border">
											<legend class="scheduler-border">Vehicle Coding</legend>
											<div class="widget-main">
												<div class="form-group">
													<label class="col-sm-2 control-label no-padding-right" for="form-field-1"> Location </label>
													<div class="col-sm-3">
														<select class="form-control" name="location" id="location" required>
															<?php foreach ($salepoint as $key => $value) { ?>
																<option value="<?php echo $value['sale_point_id']; ?>" <?php if ($record['sale_point_id'] == $value['sale_point_id']) {
																	   echo 'selected';
																   } ?>><?php echo $value['sp_name']; ?></option>
															<?php } ?>
														</select>
													</div>
													<label class="col-sm-3 control-label no-padding-right" for="vehicle_type">Vehicle Type</label>
													<div class="col-sm-3">
														<select class="form-control" id="vehicle_type" name="vehicle_type" required>
															<option value="motorcycle" <?php if ($record['vehicle_type'] == "motorcycle") { ?>selected<?php } ?>>Motor Cycle</option>
															<option value="motorcar" <?php if ($record['vehicle_type'] == "motorcar") { ?>selected<?php } ?>>Motor Car</option>
															<option value="LTV" <?php if ($record['vehicle_type'] == "LTV") { ?>selected<?php } ?>>LTV</option>
															<option value="HTV" <?php if ($record['vehicle_type'] == "HTV") { ?>selected<?php } ?>>HTV</option>
														</select>
													</div>
												</div>
												<!-- Registration Number -->
												<div class="form-group">
													<label class="col-sm-2 control-label no-padding-right" for="vehicle_number"> Vehicle Number </label>
													<div class="col-sm-3">
														<input class="form-control" type="text" id="vehicle_number" name="vehicle_number" required title="Letters, numbers, and spaces allowed" value="<?php echo $record['vehicle_number']; ?>">
													</div>
													<label class="col-sm-3 control-label no-padding-right" for="vehicle_capacity"> Vehicle Capacity </label>
													<div class="col-sm-3">
														<input class="form-control" type="number" id="vehicle_capacity" name="vehicle_capacity" required min="0" title="Enter a valid Vehicle Capacity" value="<?php echo $record['vehicle_capacity']; ?>">
													</div>
												</div>

												<!-- Purchase Date -->
												<div class="form-group">
													<label class="col-sm-2 control-label no-padding-right" for="registration_date"> Registration Date </label>
													<div class="col-sm-3">
														<div class="input-group">
															<input name="registration_date" class="form-control date-picker" id="registration_date" value="<?php if ($record['registration_date']) {
																echo $record['registration_date'];
															} else {
																echo date('Y-m-d');
															} ?>" type="text" data-date-format="yyyy-mm-dd" required>
															<span class="input-group-addon">
																<i class="fa fa-calendar bigger-110"></i>
															</span>
														</div>
													</div>
												</div>
												<div class="form-group">
													<label class="col-sm-3 control-label no-padding-right" for="form-field-1"> Description </label>


													<div class="col-sm-6">
														<textarea name="description" rows="5%" class="form-control" id="form-field-8" placeholder="Description"><?= $record['description']; ?></textarea>

													</div>
												</div>
												<!-- Submit Button -->
												<div class="row">
													<div class="center">
														<button class="btn btn-info" type="submit">
															<i class="ace-icon fa fa-check bigger-110"></i>
															Submit <?php if ($arabic_check == 'Yes') { ?> (إرسال) <?php } ?>
														</button>
													</div>
													<input type="hidden" name="action" value="" />
													<input type="hidden" name="edit" id='edit' value="<?php echo $record['id']; ?>" />
												</div>
											</div>
										</fieldset>
									</div>
								</div>
							</div>
						</div>
					</form>
				</div>
			</div>
		</div>
		<!-- </fieldset> -->
	</div><!-- /.main-container -->

	<?php
	$this->load->view('app/include/footer');
	$this->load->view('app/include/js');
	?>


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

	<!-- Bootstrap Datepicker -->
	<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.9.0/css/bootstrap-datepicker.min.css">
	<script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.9.0/js/bootstrap-datepicker.min.js"></script>
	<script src="https://cdnjs.cloudflare.com/ajax/libs/chosen/1.8.7/chosen.jquery.min.js"></script>

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
		jQuery(function ($) {
			$('#location').trigger("chosen:updated");
			var $mySelect = $('#location');
			$mySelect.chosen();
			$mySelect.trigger('chosen:activate');
		});
		jQuery(function ($) {
			$('#vehicle_type').trigger("chosen:updated");
			var $mySelect = $('#vehicle_type');
			$mySelect.chosen();
			// $mySelect.trigger('chosen:activate');
		});
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
	<script src="<?php echo SURL ?>assets/js/jquery-2.1.4.min.js"></script>
	<script src="<?php echo SURL ?>assets/js/bootstrap-datepicker.min.js"></script>
	<script src="<?php echo SURL ?>assets/js/moment.min.js"></script>
	<script type="text/javascript">
		var test = jQuery.noConflict();
		jQuery(function ($) {
			//datepicker plugin
			//link
			$('.date-picker').datepicker({
				autoclose: true,
				todayHighlight: true
			})
		});
	</script>
</body>

</html>