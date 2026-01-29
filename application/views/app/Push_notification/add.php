<!DOCTYPE html>
<html lang="en">
<html lang="en">
<?php
$this->load->view('app/include/head');
$this->load->view('app/include/header');

//include("restaurant_ajax.php");

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
							<a href="<?php echo SURL . "Module/home"; ?>">Home</a>
						</li>
						<li>
							<a href="<?php echo SURL . "app/Push_notification"; ?>" class="bolder">Push Notification</a>
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
							<b>Push Notification</b>
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


					<form class="form-horizontal" role="form" method="post" action="<?php echo SURL . "app/Push_notification/add_location" ?>" enctype="multipart/form-data">
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
									<div class="form-group">
										<label class="col-sm-4 control-label no-padding-right" for="form-field-1"><strong>Select Notification</strong></label>
										<div class="col-sm-3">
											<select name="notification" class="form-control chosen-select">
												<option value="">Select Notification</option>
												<?php foreach ($notification_list as $key => $value) { ?>
													<option value="<?php echo $value['transid'] ?>" <?php if ($record['notification_id'] == $value['transid']) { ?> selected <?php } ?>><?php echo $value['title'] ?></option>
												<?php } ?>
											</select>
										</div>
									</div>
									<div class="form-group">
										<label class="col-sm-4 control-label no-padding-right" for="form-field-1"><strong>Select User</strong></label>
										<?php
										// $user = $this->db->query("SELECT * FROM `tbl_user` where status='Active' order by id")->result_array();
										// pm($user);
										?>
										<div class="col-sm-3">
											<select name="user[]" multiple class="form-control chosen-select">
												<?php
												$user = $this->db->query("SELECT * FROM `tbl_user` where status='Active' order by id")->result_array();
												$get_users = $this->db->query("SELECT * FROM `tbl_push_notification` where notification_id='" . $record['notification_id'] . "'")->result_array();

												foreach ($user as $key => $value) {
													$selected = '';
													foreach ($get_users as $user_key => $user_val) {
														if ($value['phone'] == $user_val['user_id']) {
															$selected = 'selected';
														}
													}
												?>
													<option value="<?php echo $value['phone'] ?>" <?php echo $selected ?>><?php echo $value['name'] ?></option>
												<?php } ?>


											</select>
										</div>
									</div>
									<div class="form-group">
										<label class="col-sm-4 control-label no-padding-right" for="form-field-1"><strong>Select Status</strong></label>

										<div class="col-sm-3">
											<select name="status" class="form-control chosen-select">
												<option value="Active" <?php if ($record['status'] == 'Active') { ?>selected <?php } ?>>Active</option>
												<option value="InActive" <?php if ($record['status'] == 'InActive') { ?>selected <?php } ?>>InActive</option>
											</select>
										</div>
									</div>
								</fieldset>
							</div>
						</div>






						<div class="col-xs-12">
							<div class="row">
								<hr />

								<div class="form-actions center">
									<button class="btn btn-info btnsub">
										<i class="ace-icon fa fa-check bigger-110"></i>
										Submit
									</button>

								</div>

							</div>
							<input type="hidden" name="edit" id="edit" value="<?php echo $record['id']; ?>" />
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



	<!-- inline scripts related to this page -->


	<script type="text/javascript">
		document.getElementById("name").focus();
	</script>

	<!-- start editor  -->

	<!-- page specific plugin scripts -->

	<?php $this->load->view('app/include/customer_js.php'); ?>



	<script src="http://ajax.googleapis.com/ajax/libs/jquery/1.2.6/jquery.js" type="text/javascript"></script>

	<script src="<?php echo SURL ?>assets/js/jquery.UrduEditor.js" type="text/javascript"></script>

	<script src="http://ajax.googleapis.com/ajax/libs/jquery/1.2.6/jquery.min.js" type="text/javascript"></script>

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
	<script>
		$(document).on('click', '.btnsub', function() {

			var floor_name = $("#floor_name").val();

			if (floor_name == "") {
				alert("Please Enter Floor Name");
				floor_name.focus();
				return false;
			}
		});
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