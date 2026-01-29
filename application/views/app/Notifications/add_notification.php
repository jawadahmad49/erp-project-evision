<!DOCTYPE html>
<html lang="en">
<?php
$this->load->view('app/include/head');
$this->load->view('app/include/header');
?>

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
							<a href="<?php echo SURL . "app/Notifications"; ?>">Notification List </a>
						</li>
						<li class="active">Add Notifications </li>
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
							HMS
							<small>
								<i class="ace-icon fa fa-angle-double-right"></i>
								Add Notifications
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
										Oh snap!
									</strong>

									<?php echo $this->session->flashdata('err_message'); ?>
									<br>
								</div>

								<?php
							} ?>

							<form id="formID" class="form-horizontal" role="form" method="post" action="<?php echo SURL . "app/Notifications/send_message" ?>" enctype="multipart/form-data">

								<!-- <div class="form-group">
										<label class="col-sm-3 control-label no-padding-right" for="form-field-1"> Class </label>

										<div class="col-sm-3">
											<select required="required" class="form-control" name="clas" id="clas" data-placeholder="Choose a Class..." >
												<option value="">Choose a Class...</option>
												
												<?php

												foreach ($class_list as $key => $data) {
													# code...
													?>
												<option value="<?php echo $data['classcode']; ?>"><?php echo ucwords($data['classname']); ?></option>
												
												<?php } ?>
										
										</select>
										</div>

									</div> -->
								<input type="hidden" name="clas" value="1">



								<div class="form-group">
									<label class="col-sm-3 control-label no-padding-right" for="form-field-1"> Select Promo </label>

									<div class="col-sm-3">
										<select required="required" class="form-control chosen-select" name="pcode" id="pcode" data-placeholder="Choose a Promo...">
											<option value="0">Notifications Without Promo</option>

											<?php
											foreach ($promo_list as $key => $data) { ?>
												<option value="<?php echo $data['transid']; ?>"><?php echo ucwords($data['promo_code']); ?></option>

											<?php } ?>
										</select>


									</div>
								</div>
								<div class="form-group">
									<label class="col-sm-3 control-label no-padding-right" for="form-field-1"> Notifications Title </label>

									<div class="col-sm-9">
										<input maxlength="50" value="" type="text" id="title" name="title" placeholder="Enter Title" class="col-xs-10 col-sm-5" required="required" />
									</div>
								</div>
								<div class="form-group">
									<label class="col-sm-3 control-label no-padding-right" for="form-field-1">Notifications Image </label>
									<div class="col-sm-9">
										<div id="targetLayer">
											<?php if (isset($company['logo']) && $company['logo'] != '') { ?>
												<img width="200" height="200" id="logo_id" src="<?php echo IMG . 'company/' . $company['logo']; ?>">
											<?php } ?>

										</div>
										<input type="hidden" name="old_image" value="<?php echo $company['logo']; ?>">
										<label class="ace-file-input">
											<input type="file" accept="image/x-png,image/gif,image/jpeg" name="company_image" id="logo" class="col-xs-10 col-sm-5" onChange="showPreview(this);">
											<span class="ace-file-container col-xs-10 col-sm-5" data-title="Choose">
												<span class="ace-file-name" data-title="No File ...">
													<i class=" ace-icon fa fa-upload"></i>
												</span>
											</span>
											<a class="remove" href="#"><i class=" ace-icon fa fa-times"></i></a></label>
									</div>
								</div>




								<div class="form-group">
									<label class="col-sm-3 control-label no-padding-right" for="form-field-1">Start Date</label>



									<div class="col-sm-3">

										<div class="input-group">
											<span class="input-group-addon">
												<i class="fa fa-calendar bigger-110"></i>
											</span>
											<input name="start_date" class="form-control date-picker start_date" id="id-date-picker-1" type="text" data-date-format="yyyy-mm-dd" data-date-start-date="0d" required="" value="<?php echo date('Y-m-d'); ?>">
										</div>
									</div>
								</div>
								<style type="text/css">
									.message_date {
										display: none;
										color: red;
									}
								</style>
								<div style="display: none;" class="form-group message_date">
									<label class="col-sm-3 control-label no-padding-right" for="form-field-1"></label>
									<div class=" col-sm-3">Start date must Not be greater than Expiry date</div>

								</div>
								<div class="form-group">
									<label class="col-sm-3 control-label no-padding-right" for="form-field-1">Expiry Date</label>



									<div class="col-sm-3">

										<div class="input-group">
											<span class="input-group-addon">
												<i class="fa fa-calendar bigger-110"></i>
											</span>
											<input name="end_date" class="form-control date-picker end_date" id="id-date-picker-2" type="text" data-date-format="yyyy-mm-dd" data-date-start-date="0d" required="" value="<?php echo date('Y-m-d'); ?>">
										</div>
									</div>


								</div>
								<div class="form-group">
									<label class="col-sm-3 control-label no-padding-right" for="form-field-1">Short Description </label>

									<div class="col-sm-4">
										<textarea name="short" maxlength="250" rows="3%" class="form-control" id="form-field-8" placeholder="Enter Detail"></textarea>
									</div>
								</div>
								<div class="form-group">
									<label class="col-sm-3 control-label no-padding-right" for="form-field-1">Long Description </label>

									<div class="col-sm-6">
										<textarea name="remarks" maxlength="250" rows="5%" class="form-control" id="form-field-8" placeholder="Enter Detail"></textarea>
									</div>
								</div>
								<div class="form-group">
									<label class="col-sm-3 control-label no-padding-right" for="form-field-1">Select Status </label>

									<div class="col-sm-3">
										<select class="chosen-select form-control" name="status" id="status" data-placeholder="Choose a Status...">

											<option value="Active">Active</option>
											<option value="InActive">InActive</option>

										</select>
									</div>
								</div>
								<div class="row">
									<input type="hidden" id="code_id" name="code_id" value="0" />
									<hr />

									<div class="form-actions center">
										<button class="btn btn-info">
											<i class="ace-icon fa fa-check bigger-110"></i>
											Submit
										</button>
									</div>

								</div>

								<input type="hidden" name="id" value="Senddata" />
							</form>

							<!-- PAGE CONTENT ENDS -->
						</div><!-- /.col -->
					</div><!-- /.row -->
				</div><!-- /.page-content -->
			</div>
		</div><!-- /.main-content -->

	</div><!-- /.main-container -->

	<?php
	$this->load->view('app/include/footer');
	?>

	<script type="text/javascript">
		// $(document).ready(function(){

		// $('#date-picker').datepicker({
		// dateFormat: "yy-mm-dd",
		// minDate: 0

		// });
		// function english_lang() {

		// $('#itemname').setUrduInput({value_in: 15});
		// $('#itemname').removeAttr('dir');

		// }

		// function urdu_lang() {

		// $('#itemname').attr("dir", "rtl");
		// $('#itemname').setUrduInput({value_in: 17});


		// }
		// }
	</script>
	<script type="text/javascript">
		$("#formID").submit(function (e) {

			var start_date = $('.start_date').val();
			var end_date = $('.end_date').val();

			if (new Date(start_date) > new Date(end_date)) {

				$('.message_date').css('display', 'block');
				e.preventDefault();
			} else {
				$('.message_date').css('display', 'none');
			}

		});
	</script>
	<?php
	$this->load->view('app/include/js');
	?>
	<script type="text/javascript">
		$('#category').on('change', function () {

			var cat_id = $(this).val();


			if (cat_id == 'Percentage') {
				var test_final = $.noConflict(jQuery);

				$("#brandname").prop('required', true);
				$('.brand').css('display', 'block');


				$("#amount").prop('required', false);
				$('.amount').css('display', 'none');

			} else if (cat_id == 'Amount') {
				var test_final = $.noConflict(jQuery);

				$("#amount").prop('required', true);
				$('.amount').css('display', 'block');


				$("#brandname").prop('required', false);
				$('.brand').css('display', 'none');

			}

		});

		//document.getElementById("clas").focus();
	</script>
	<?php $this->load->view('app/include/paymentreceipt_js.php'); ?>

	<!-- inline scripts related to this page -->

	<?php $this->load->view('app/include/item_js.php'); ?>

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

		function english_lang() {

			jQuery(".urdu_class").each(function (index) {

				jQuery(this).removeAttr('dir');
				setEnglish(jQuery(this));

			});

		}

		function urdu_lang() {

			jQuery(".urdu_class").each(function (index) {

				jQuery(this).attr("dir", "rtl");

				setUrdu(jQuery(this));

			});

		}
	</script>
	<script type="text/javascript">
		function showPreview(objFileInput) {
			if (objFileInput.files[0]) {
				var fileReader = new FileReader();
				fileReader.onload = function (e) {
					$("#targetLayer").html('<img src="' + e.target.result + '" width="200px" height="200px" class="upload-preview" />');
					$("#targetLayer").css('opacity', '0.7');
					$(".icon-choose-image").css('opacity', '0.5');
				}
				fileReader.readAsDataURL(objFileInput.files[0]);
			}
		}
	</script>


</body>

</html>