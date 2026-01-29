<!DOCTYPE html>
<html lang="en">
<?php
$this->load->view('en/include/head');
$this->load->view('en/include/header');

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
							<a href="<?php echo SURL . "admin"; ?>">Home</a>
						</li>

						<li>
							<a href="<?php echo SURL . "app/category"; ?>">Category List </a>
						</li>
						<li class="active">Update Category </li>
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
							LPG
							<small>
								<i class="ace-icon fa fa-angle-double-right"></i>
								Update Category
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



							<form id="formID" class="form-horizontal" role="form" method="post" action="<?php echo SURL . "app/category/update" ?>" enctype="multipart/form-data">

								<!-- <div class="form-group">
										<label class="col-sm-3 control-label no-padding-right" for="form-field-1">Class</label>

										<div class="col-sm-3">
											<select required="required" class="form-control" name="classcode" id="classcode" data-placeholder="Choose a Country..." >
												<option value="">Choose a Class...</option>
												
												<?php

												foreach ($class_list as $key => $data) {
													# code...
												?>
												<option value="<?php echo $data['classcode']; ?>" <?php if ($data['classcode'] == $category['classcode']) { ?> selected <?php } ?>><?php echo ucwords($data['classname']); ?></option>
												
												<?php } ?>
										
										</select>
										</div>
									</div>
 -->
								<input type="hidden" value="1" name="classcode">
								<div class="form-group">
									<label class="col-sm-3 control-label no-padding-right" for="form-field-1"> Category Name </label>

									<div class="col-sm-9">
										<input maxlength="50" value="<?php echo $category['catname']; ?>" type="text" id="catname" name="catname" placeholder="Category Name" class="col-xs-12 col-sm-5" required="required" autofocus />
									</div>
								</div>
								<div class="form-group">
									<label class="col-sm-3 control-label no-padding-right" for="form-field-1">Select Status </label>

									<div class="col-sm-3">
										<select class="chosen-select form-control" name="status" id="status" data-placeholder="Choose a Status...">

											<option value="Active" <?php if ($category['status'] == 'Active') { ?> selected <?php } ?>>Active</option>
											<option value="InActive" <?php if ($category['status'] == 'InActive') { ?> selected <?php } ?>>InActive</option>

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
								<input type="hidden" name="id" value="<?php echo $category['id'] ?>" />

							</form>

							<!-- PAGE CONTENT ENDS -->
						</div><!-- /.col -->
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

	<!-- inline scripts related to this page -->
	<script type="text/javascript">
		document.getElementById("classcode").focus();
	</script>

</body>

</html>