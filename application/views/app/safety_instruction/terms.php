<!DOCTYPE html>
<html lang="en">
<?php
$this->load->view('en/include/head');
$this->load->view('en/include/header');
?>
<link href="https://cdn.jsdelivr.net/npm/summernote@0.8.18/dist/summernote-lite.min.css" rel="stylesheet">


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
						<li class="active"><a href="<?php echo SURL . "Safety_instruction"; ?>">Manage Safety Instruction</a></li>
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
							<style type="text/css">
								fieldset.scheduler-border {
									border: 1px groove #ddd !important;
									padding: 0 1.4em 1.4em 1.4em !important;
									margin: 0 0 1.5em 0 !important;
									-webkit-box-shadow: 0px 0px 0px 0px #000;
									box-shadow: 0px 0px 0px 0px #000;
								}

								.btn:focus {
									outline: none !important;
								}

								legend.scheduler-border {
									font-size: 1.2em !important;
									font-weight: bold !important;
									text-align: left !important;
									width: auto;
									padding: 0 10px;
									border-bottom: none;
								}

								@media only screen and (max-width: 768px) {

									/* For mobile phones: */
									#box {
										display: flex !important;
										flex-direction: column;
									}

									#tbody_add_expense,
									#tbody_add_diesel {
										width: 100% !important;
									}

									#expense_amount {
										margin-top: 3px !important;
										width: 100% !important;
										margin-left: 0px !important;
									}

									#expense_status,
									#diesel_status {
										width: 100% !important;
									}

									.add_expense,
									#diesel_qty {
										margin-top: 5px;
									}

									#expense_text,
									#diesel_qty {
										width: 100% !important;
										margin-left: 0px !important;
									}

									.add_diesel {
										width: 50% !important;
										text-align: center !important;
										margin-top: 5px;
									}

									#buttons {
										width: 100% !important;
									}

									#simple-table {
										width: 100% !important;
										margin: 0 auto;
									}
								}
							</style>
							<form class="form-horizontal" role="form" method="post" action="<?php echo SURL . "app/safety_instruction/add" ?>" enctype="multipart/form-data">
								<fieldset class="scheduler-border">
									<legend class="scheduler-border">Safety Instruction</legend>

									<textarea id="summernote" name="safety_instruction"><?php echo $record['safety_instruction']; ?></textarea>
									<div class="form-group">
										<label class="col-sm-2 control-label no-padding-right" for="form-field-1"><strong>Description</strong> </label>
										<div class="col-sm-10">
											<textarea name="description" id="summernote1" rows="20"><?php echo $record['description']; ?></textarea>
										</div>
									</div>
								</fieldset>
								<div class="row">
									<hr />
									<div class="form-actions center">
										<button class="btn btn-info btnsubmit">
											<i class="ace-icon fa fa-check bigger-110"></i>
											Submit
										</button>
									</div>
								</div>
								<input type="hidden" name="id" id='id' value="<?php echo $record['id']; ?>" />
							</form>
							<!-- PAGE CONTENT ENDS -->
						</div><!-- /.col -->
					</div><!-- /.row -->
				</div><!-- /.page-content -->
			</div>
		</div><!-- /.main-content -->
	</div><!-- /.main-container -->
	<?php $this->load->view('en/include/footer'); ?>
	<script src="https://cdn.jsdelivr.net/npm/summernote@0.8.18/dist/summernote-lite.min.js"></script>
	<?php $this->load->view('en/include/js'); ?>
	<script>
		$(document).ready(function() {
			$('#summernote').summernote({
				height: 300,
				toolbar: [
					['style', ['style']],
					['font', ['bold', 'italic', 'underline', 'clear']],
					['fontname', ['fontname']],
					['color', ['color']],
					['para', ['ul', 'ol', 'paragraph']],
					['table', ['table']],
					['insert', ['link', 'picture', 'video']],
					['view', ['fullscreen', 'codeview', 'help']]
				]
			});
			$('#summernote1').summernote({
				height: 300,
				toolbar: [
					['style', ['style']],
					['font', ['bold', 'italic', 'underline', 'clear']],
					['fontname', ['fontname']],
					['color', ['color']],
					['para', ['ul', 'ol', 'paragraph']],
					['table', ['table']],
					['insert', ['link', 'picture', 'video']],
					['view', ['fullscreen', 'codeview', 'help']]
				]
			});
		});
	</script>




</body>

</html>