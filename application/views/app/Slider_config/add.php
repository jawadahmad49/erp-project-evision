<!DOCTYPE html>
<html lang="en">
<?php
$this->load->view('app/include/head');
$this->load->view('app/include/header');
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
							<a href="<?php echo SURL . "app/Slider_config"; ?>">Manage Slider Configuration</a>
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
							Manage Slider

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
						} ?>
					</div>


					<form class="form-horizontal" role="form" method="post" action="<?php echo SURL . "app/Slider_config/add" ?>" enctype="multipart/form-data">
						<div class="row">
							<div class="col-xs-12 col-sm-12">


								<script type="text/javascript">
									$(document).on('change', '#vendortype', function () {
										var vall = $(this).val();
										if (vall == "Foreigner") {
											$(".convorate").show();
										} else {
											$(".convorate").hide();
										}
									});

									$(document).on('change', '#vendortype', function () {
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
										<label class="col-sm-4 control-label" for="form-field-1"><b>Upload Image</b></label>

										<div class="col-xs-4">
											<input type="hidden" name="old_image" id="old_image" value="<?php echo $record['image']; ?>">
											<label class="ace-file-input"><input type="file" name="image" id="logo" accept="image/x-png,image/gif,image/jpeg" class="col-xs-12" onchange="validateImage(this); showPreview(this);"><span class="ace-file-container col-xs-12" data-title="Choose"><span class="ace-file-name" data-title="No File ..."><i class=" ace-icon fa fa-upload"></i></span></span><a class="remove" href="#"><i class=" ace-icon fa fa-times"></i></a></label>

											<div id="targetLayer">
												<img width="250" height="250" id="target" src="<?php if (isset($record['image'])) {
													echo IMG . 'slider/' . $record['image'];
												} else {
													echo IMG . 'slider/default.png';
												} ?>" style="margin-left: 25%;">
											</div>

										</div>

									</div>


									<div class="form-group">
										<label class="col-sm-4 control-label no-padding-right" for="form-field-1"> Status</label>
										<div class="col-sm-4">
											<select class="form-control chosen-select" name="status" id="status" data-placeholder="">

												<option <?php if ($record['status'] == "Active") {
													echo "selected";
												} ?> value="Active">Active</option>
												<option <?php if ($record['status'] == "InActive") {
													echo "selected";
												} ?> value="InActive">InActive </option>
											</select>
										</div>
									</div>




									<div class="col-xs-12">
										<div class="row">
											<hr />
											<div class="form-actions center">
												<button class="btn btn-info">
													<i class="ace-icon fa fa-check bigger-110"></i>
													Submit
												</button>

											</div>

										</div>
										<input type="hidden" name="id" id="edit" value="<?php echo $id; ?>" />
									</div><!-- /.col -->
								</fieldset>
					</form>

				</div><!-- /.row -->
			</div><!-- /.page-content -->
		</div>
	</div><!-- /.main-content -->

	</div><!-- /.main-container -->

	<?php
	$this->load->view('app/include/footer');
	?>
	<?php
	$this->load->view('app/include/js');
	?>

	<script type="text/javascript">
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
					$("#targetLayer").html('<img src="' + e.target.result + '" width="250px" height="250px" class="upload-preview" style="margin-left: 25%;" />');
					//$("#targetLayer").css('opacity','0.7');
					$(".icon-choose-image").css('opacity', '0.5');
				}
				fileReader.readAsDataURL(objFileInput.files[0]);
			}
		}
	</script>

	<!-- inline scripts related to this page -->


	<script type="text/javascript">
		document.getElementById("sname").focus();
	</script>

	<!-- start editor  -->

	<!-- page specific plugin scripts -->

	<?php $this->load->view('app/include/customer_js.php'); ?>



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