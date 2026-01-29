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
							<a href="<?php echo SURL . "app/Rider_coding"; ?>">Rider Coding List <?php if ($arabic_check == 'Yes') { ?>(قائمة العملاء)<?php } ?> </a>
						</li>
						<li class="active"><?php echo ucwords($filter); ?> Rider Coding<?php if ($arabic_check == 'Yes') { ?>(أضف الزبون)<?php } ?> </li>
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
								<?php echo ucwords($filter); ?> Rider Coding <?php if ($arabic_check == 'Yes') { ?>(أضف الزبون)<?php } ?>
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

					<form class="form-horizontal" role="form" id="c_form" method="post" action="<?php echo SURL . "app/Rider_coding/" . $filter ?>" enctype="multipart/form-data">
						<!-- <form class="form-horizontal" role="form" id="c_form" method="post" action="<?php echo SURL . "app/Rider_coding/getExtra_DevCharges" ?>" enctype="multipart/form-data"> -->
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
											<legend class="scheduler-border">Rider Coding</legend>
											<div class="widget-main">
												<div class="form-group">
													<label class="col-sm-5 control-label no-padding-right" for="form-field-1"> Location </label>
													<div class="col-sm-3">
														<select class="chosen-select form-control" name="location" id="location" required>
															<?php foreach ($salepoint as $key => $value) { ?>
																<option value="<?php echo $value['sale_point_id']; ?>" <?php if ($record['sale_point_id'] == $value['sale_point_id']) {
																	   echo 'selected';
																   } ?>><?php echo $value['sp_name']; ?></option>
															<?php } ?>
														</select>
													</div>
												</div>
												<div class="form-group">
													<label class="col-sm-5 control-label" for="form-field-1"><b>Upload Image</b></label>

													<div class="col-xs-3">
														<input type="hidden" name="old_image" id="old_image" value="<?php echo $record['image']; ?>">
														<label class="ace-file-input"><input type="file" name="image" id="logo" accept="image/x-png,image/gif,image/jpeg" class="col-xs-12" onchange="validateImage(this); showPreview(this);"><span class="ace-file-container col-xs-12" data-title="Choose"><span class="ace-file-name" data-title="No File ..."><i class=" ace-icon fa fa-upload"></i></span></span><a class="remove" href="#"><i class=" ace-icon fa fa-times"></i></a></label>

														<div id="targetLayer">
															<img width="250" height="250" id="target" src="<?php if (isset($record['image'])) {
																echo IMG . 'rider/' . $record['image'];
															} else {
																echo IMG . 'rider/default.png';
															} ?>">
														</div>

													</div>

												</div>
												<div class="form-group mt-3">
													<label class="col-sm-5 control-label no-padding-right" for="form-field-1">Rider Name</label>
													<div class="col-sm-3">
														<input class="form-control" type="text" id="rider_name" name="rider_name" onkeypress="return (event.charCode > 64 && event.charCode < 91) || (event.charCode > 96 && event.charCode < 123) || (event.charCode==32)" required value="<?php echo $record['rider_name']; ?>">
														</span>
													</div>
												</div>
												<div class="form-group mt-3">
													<label class="col-sm-5 control-label no-padding-right" for="form-field-1">Phone Number</label>
													<div class="col-sm-3">
														<input class="form-control" onkeypress="return /[0-9 . ]/i.test(event.key)" maxlength="15" type="text" id="phone_number" name="phone_number" required value="<?php echo $record['phone_number']; ?>">
														</span>
													</div>
												</div>
												<div class="form-group mt-3">
													<label class="col-sm-5 control-label no-padding-right" for="form-field-1">Rider CNIC</label>
													<div class="col-sm-3">
														<input class="form-control" onkeypress="return /[0-9 . ]/i.test(event.key)" maxlength="15" type="text" id="cnic" name="cnic" required value="<?php echo $record['cnic']; ?>">
													</div>
												</div>
											</div>
											<div class="form-group mt-3">
												<label class="col-sm-5 control-label no-padding-right" for="form-field-1">Driving License Type</label>
												<div class="col-sm-3">
													<select class="chosen-select form-control" id="license_type" name="license_type" required>
														<option value="Learner" <?php if ($record['license_type'] == "Learner") { ?>selected<?php } ?>>Learner</option>
														<option value="motorcycle" <?php if ($record['license_type'] == "motorcycle") { ?>selected<?php } ?>>Motor Cycle</option>
														<option value="motorcar" <?php if ($record['license_type'] == "motorcar") { ?>selected<?php } ?>>Motor Car</option>
														<option value="LTV" <?php if ($record['license_type'] == "LTV") { ?>selected<?php } ?>>LTV</option>
														<option value="HTV" <?php if ($record['license_type'] == "HTV") { ?>selected<?php } ?>>HTV</option>
													</select>
													</span>
												</div>
											</div>
											<div class="form-group mt-3">
												<label class="col-sm-5 control-label no-padding-right" for="form-field-1">Registration Date</label>
												<div class="col-sm-3">
													<div class="input-group">

														<input name="date" class="form-control date-picker" id="id-date-picker-1" value="<?php if ($record['e_date']) {
															echo $record['date'];
														} else {
															echo date('Y-m-d');
														} ?>" type="text" data-date-format="yyyy-mm-dd" required value="<?php echo date('Y-m-d'); ?>">
														<span class="input-group-addon">
															<i class="fa fa-calendar bigger-110"></i>
														</span>
													</div>
												</div>
											</div>
											<div class="form-group mt-3">
												<label class="col-sm-5 control-label no-padding-right" for="form-field-1">Additional Notes</label>
												<div class="col-sm-3">
													<textarea class="form-control" maxlength="200" id="notes" name="notes" rows="4"><?php echo $record['notes']; ?></textarea>
												</div>
											</div>
											<div class="form-group">
												<label class="col-sm-5 control-label no-padding-right" for="form-field-1">Login Id</label>
												<div class="col-sm-3">
													<input type="text" required id='loginid' class="form-control" name="loginid" value="<?php echo $record['loginid'] ?>" maxlength="25">
												</div>
											</div>
											<div class="form-group">
												<label class="col-sm-5 control-label no-padding-right" for="form-field-1">Password</label>
												<div class="col-sm-3">
													<input type="password" required id='password' class="form-control" name="password" value="<?php echo base64_decode($record['password']) ?>" maxlength="15">
												</div>
											</div>
											<div class="row">
												<div class="center">
													<button class="btn btn-info" onclick="reload_parent();">
														<i class="ace-icon fa fa-check bigger-110"></i>
														Submit <?php if ($arabic_check == 'Yes') { ?> (إرسال) <?php } ?>
													</button>
												</div>

												<input type="hidden" name="action" value="" />
												<input type="hidden" name="edit" id='edit' value="<?php echo $record['id']; ?>" />
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
		function validateImage(input) {
			const file = input.files[0];
			const maxSize = 500 * 1024; // 500KB in bytes

			if (file && file.size > maxSize) {
				alert("The selected image exceeds the maximum size of 500KB. Please select a smaller image.");
				input.value = ''; // Clear the file input
			}
		}
		function showPreview(objFileInput) {
			if (objFileInput.files[0]) {
				var fileReader = new FileReader();
				fileReader.onload = function (e) {
					$("#targetLayer").html('<img src="' + e.target.result + '" width="250px" height="250px" class="upload-preview" />');
					//$("#targetLayer").css('opacity','0.7');
					$(".icon-choose-image").css('opacity', '0.5');
				}
				fileReader.readAsDataURL(objFileInput.files[0]);
			}
		}
	</script>
</body>

</html>