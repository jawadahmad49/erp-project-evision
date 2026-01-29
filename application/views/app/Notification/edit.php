<!DOCTYPE html>
<html lang="en">
<?php
$this->load->view('app/include/head');
$this->load->view('app/include/header');
?>
<style>
	.chosen-container-multi .chosen-choices {
		min-height: 34px !important;
		/* matches bootstrap input */
		height: auto !important;
		line-height: normal !important;
		border-radius: 4px;
		overflow-y: auto;
	}

	.chosen-container {
		width: 100% !important;
	}
</style>

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
							<a href="<?php echo SURL . ""; ?>">Home</a>
						</li>

						<li>
							<a href="<?php echo SURL . "app/Notification/"; ?>">Notification List </a>
						</li>
						<!-- <li class="active">Update Notification </li> -->
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
							Book
							<small>
								<i class="ace-icon fa fa-angle-double-right"></i>
								Update Notification
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
							}   ?>

							<form id="formID" class="form-horizontal" role="form" method="post" action="<?php echo SURL . "app/Notification/update" ?>" enctype="multipart/form-data">
								<div class="form-group">
									<label class="col-sm-3 control-label no-padding-right">Target Audience</label>
									<div class="col-sm-9">
										<label class="radio-inline">
											<input type="radio" name="target_mode" value="zone" <?php echo $target_mode == 'zone' ? 'checked' : ''; ?>
												onclick="toggleTargetSelection('zone')"> By Zones
										</label>
										<label class="radio-inline">
											<input type="radio" name="target_mode" value="location" <?php echo $target_mode == 'location' ? 'checked' : ''; ?>
												onclick="toggleTargetSelection('location')"> By Sale Points
										</label>
									</div>
								</div>
								<input type="hidden" name="clas" value="1">
								<div class="form-group">
									<label class="col-sm-3 control-label no-padding-right" for="user-from-date">User Join From Date</label>
									<div class="col-sm-3">
										<div class="input-group">
											<span class="input-group-addon">
												<i class="fa fa-calendar bigger-110"></i>
											</span>
											<input name="from_date" readonly class="form-control date-picker" id="user-from-date-picker" type="text" data-date-format="yyyy-mm-dd" placeholder="Select From Date" value="<?php echo isset($notification['from_date']) ? $notification['from_date'] : ''; ?>">
										</div>
									</div>
								</div>

								<div class="form-group">
									<label class="col-sm-3 control-label no-padding-right" for="user-to-date">User Join To Date</label>
									<div class="col-sm-3">
										<div class="input-group">
											<span class="input-group-addon">
												<i class="fa fa-calendar bigger-110"></i>
											</span>
											<input name="to_date" readonly class="form-control date-picker" id="user-to-date-picker" type="text" data-date-format="yyyy-mm-dd" placeholder="Select To Date" value="<?php echo isset($notification['to_date']) ? $notification['to_date'] : ''; ?>">
										</div>
									</div>
								</div>

								<div class="form-group">
									<label class="col-sm-3 control-label no-padding-right" for="form-field-1"> Notification Title </label>

									<div class="col-sm-9">
										<input value="<?php echo $notification['title'] ?>" type="text" id="title" name="title" placeholder="Enter Title" class="col-xs-10 col-sm-5" required="required" />
									</div>
								</div>


								<div class="form-group">
									<label class="col-sm-3 control-label no-padding-right" for="form-field-1">Notification Image </label>
									<div class="col-sm-9">
										<div id="targetLayer">
											<?php if (isset($notification['logo']) && $notification['logo'] != '') { ?>
												<img width="200" height="200" id="logo_id" src="<?php echo IMG . 'gallery/' . $notification['logo']; ?>">
											<?php } ?>

										</div>
										<input type="hidden" name="old_image" value="<?php echo $notification['logo']; ?>">
										<label class="ace-file-input">
											<input type="file" accept="image/x-png,image/gif,image/jpeg" name="company_image" id="logo" class="col-xs-10 col-sm-5" onChange="showPreview(this);"><span class="ace-file-container col-xs-10 col-sm-5" data-title="Choose"><span class="ace-file-name" data-title="No File ..."><i class=" ace-icon fa fa-upload"></i></span></span><a class="remove" href="#"><i class=" ace-icon fa fa-times"></i></a></label>
									</div>
								</div>
								<div class="form-group" id="salePointSelectBox" style="display: none;">
									<label class="col-sm-3 control-label no-padding-right" for="form-field-1">Location</label>
									<div class="col-sm-3">
										<select class="form-control chosen-select" name="location[]" id="location" multiple>
											<?php foreach ($location as $key => $value) { ?>
												<option value="<?php echo $value['sale_point_id'];  ?>" <?php echo in_array((int)$value['sale_point_id'], $assigned_location_ids, true) ? 'selected' : ''; ?>><?php echo $value['sp_name']; ?></option>
											<?php } ?>
										</select>
									</div>
								</div>
								<div class="form-group" id="zoneSelectBox">
									<label class="col-sm-3 control-label no-padding-right" for="form-field-1">Zone</label>
									<div class="col-sm-3">
										<select class="form-control chosen-select" name="zone_id[]" id="zone_id" multiple>
											<?php foreach ($zone_list as $z): ?>
												<option value="<?php echo (int)$z['id']; ?>"
													<?php echo in_array((int)$z['id'], $assigned_zone_ids, true) ? 'selected' : ''; ?>>
													<?php echo htmlspecialchars($z['zone_name']); ?>
												</option>
											<?php endforeach; ?>
										</select>
									</div>
								</div>


								<div class="form-group">
									<label class="col-sm-3 control-label no-padding-right" for="form-field-1">Start Date</label>



									<div class="col-sm-3">

										<div class="input-group">
											<span class="input-group-addon">
												<i class="fa fa-calendar bigger-110"></i>
											</span>
											<input name="start_date" class="form-control date-picker start_date" id="start_date_picker" type="text" data-date-format="yyyy-mm-dd" required value="<?php echo $notification['start_date'] ?>">
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
									<div class=" col-sm-3">Start date must be greater than Expiry date</div>

								</div>
								<div class="form-group">
									<label class="col-sm-3 control-label no-padding-right" for="form-field-1">Expiry Date</label>



									<div class="col-sm-3">

										<div class="input-group">
											<span class="input-group-addon">
												<i class="fa fa-calendar bigger-110"></i>
											</span>
											<input name="end_date" class="form-control date-picker end_date" id="end_date_picker" type="text" data-date-format="yyyy-mm-dd" required value="<?php echo $notification['expiry_date']  ?>">
										</div>
									</div>


								</div>
								<div class="form-group">
									<label class="col-sm-3 control-label no-padding-right" for="form-field-1">Select Status </label>

									<div class="col-sm-3">
										<select class="chosen-select form-control" name="status" id="status" data-placeholder="Choose a Status...">

											<option value="Active" <?php if ($notification['sts'] == 'Active') { ?> selected <?php } ?>>Active</option>
											<option value="InActive" <?php if ($notification['sts'] == 'InActive') { ?> selected <?php } ?>>InActive</option>

										</select>
									</div>
								</div>
								<div class="form-group">
									<label class="col-sm-3 control-label no-padding-right" for="form-field-1">Short Description </label>

									<div class="col-sm-4">
										<textarea name="short" rows="3%" class="form-control" id="form-field-8" placeholder="Enter Detail"><?php echo $notification['short_detail']; ?></textarea>
									</div>
								</div>
								<div class="form-group">
									<label class="col-sm-3 control-label no-padding-right" for="form-field-1">Long Description </label>

									<div class="col-sm-6">
										<textarea name="remarks" rows="5%" class="form-control" id="form-field-8" placeholder="Enter Detail"> <?php echo $notification['details']; ?></textarea>
									</div>
								</div>
									<div class="form-group">
									<label class="col-sm-3 control-label no-padding-right" for="form-field-1">Send Date Time</label>
									<div class="col-sm-3">
										<input type="date" class="form-control date-picker" name="date_time" id="date_time" value="<?php echo isset($scheduled_date) ? $scheduled_date : date('Y-m-d'); ?>" data-date-format="yyyy-mm-dd">
										<input type="time" class="form-control" name="time" id="time" value="<?php echo isset($scheduled_time) ? $scheduled_time : date('H:i'); ?>">
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

								<input type="hidden" name="id" value="<?php echo $notification['transid'] ?>" />
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
		function english_lang() {

			$('#itemname').setUrduInput({
				value_in: 15
			});
			$('#itemname').removeAttr('dir');

		}

		function urdu_lang() {

			$('#itemname').attr("dir", "rtl");
			$('#itemname').setUrduInput({
				value_in: 17
			});


		}
	</script>
	<script type="text/javascript">
		$("#formID").submit(function(e) {

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
		var targetMode = "<?php echo $target_mode; ?>";

		if (targetMode === "location") {
			document.getElementById("salePointSelectBox").style.display = "block";
			document.getElementById("zoneSelectBox").style.display = "none";
		} else if (targetMode === "zone") {
			document.getElementById("zoneSelectBox").style.display = "block";
			document.getElementById("salePointSelectBox").style.display = "none";
		}

		function toggleTargetSelection(mode) {
			if (mode === 'zone') {
				$('#zoneSelectBox').show();
				$('#salePointSelectBox').hide();
				$('#location').val([]).trigger("chosen:updated");
			} else {
				$('#zoneSelectBox').hide()
				$('#salePointSelectBox').show();
				$('#zone_id').val([]).trigger("chosen:updated");

			}
		}

		$('#category').on('change', function() {

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
		$(document).ready(function() {

			var cat_id = jQuery('#category').val();
			if (cat_id == 'Percentage') {
				var test_final = $.noConflict(jQuery);
				$("#brandname").prop('required', true);
				$('.brand').css('display', 'block');



			}

		});
		$(document).ready(function() {
			var cat_id = jQuery('#category').val();
			if (cat_id == 'Amount') {
				var test_final = $.noConflict(jQuery);
				$("#amount").prop('required', true);
				$('.amount').css('display', 'block');


			}
		});

		//document.getElementById("clas").focus();
	</script>
	<?php $this->load->view('en/include/paymentreceipt_js.php'); ?>

	<!-- inline scripts related to this page -->


	<script src="http://ajax.googleapis.com/ajax/libs/jquery/1.2.6/jquery.js" type="text/javascript"></script>

	<script src="<?php echo SURL ?>assets/js/jquery.UrduEditor.js" type="text/javascript"></script>
	<!-- 
	<script src="http://ajax.googleapis.com/ajax/libs/jquery/1.2.6/jquery.min.js" type="text/javascript"></script> -->

	<script type="text/javascript">
		var test_final = jQuery.noConflict($);

		$(document).ready(function($) {

			jQuery(".urdu_class").each(function(index) {
				jQuery(this).UrduEditor();
				setEnglish($(this));
				jQuery(this).removeAttr('dir');

			});
		});

		function english_lang() {

			jQuery(".urdu_class").each(function(index) {

				jQuery(this).removeAttr('dir');
				setEnglish(jQuery(this));

			});

		}

		function urdu_lang() {

			jQuery(".urdu_class").each(function(index) {

				jQuery(this).attr("dir", "rtl");

				setUrdu(jQuery(this));

			});

		}
	</script>
	<script type="text/javascript">
		function showPreview(objFileInput) {
			if (objFileInput.files[0]) {
				var fileReader = new FileReader();
				fileReader.onload = function(e) {
					$("#targetLayer").html('<img src="' + e.target.result + '" width="200px" height="200px" class="upload-preview" />');
					$("#targetLayer").css('opacity', '0.7');
					$(".icon-choose-image").css('opacity', '0.5');
				}
				fileReader.readAsDataURL(objFileInput.files[0]);
			}
		}
		document.getElementById("pcode").focus();
		// document.getElementById("pcode").select();
	</script>

</body>

</html>