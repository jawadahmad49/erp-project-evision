<!DOCTYPE html>
<html lang="en">
<?php
$this->load->view('app/include/head');
$this->load->view('app/include/header');
?>
<style>
	.btn-success:hover {
		background: green !important;
	}

	.btn-success:focus {
		background: green !important;
	}

	.message_date {

		display: none;

		color: red;

	}
</style>

<body class="no-skin">


	<div class="main-container ace-save-state" id="main-container">
		<?php $this->load->view('app/include/sidebar'); ?>


		<div class="main-content">
			<div class="main-content-inner">
				<div class="breadcrumbs ace-save-state" id="breadcrumbs">
					<ul class="breadcrumb">
						<li>
							<i class="ace-icon fa fa-home home-icon"></i>
							<a href="<?php echo SURL . "Module/app"; ?>">Home</a>
						</li>


						<li class="active">Manage Rejcted Orders</li>
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


					<div class="row">
						<div class="col-xs-12">
							<!-- PAGE CONTENT BEGINS -->


							<div class="row">
								<div class="col-xs-12">
									<h3 class="header smaller lighter blue"><?php echo $title; ?></h3>

									<?php
									if ($this->session->flashdata('err_message')) { ?>

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

									<form id="formID" method="post" action="" style="background-color:#fafafa;">

										<div class="form-group">
											<label class="col-sm-1 control-label no-padding-right" for="form-field-1">From Date</label>

											<div class="col-sm-2">
												<div class="input-group">
													<input name="from" class="form-control date-picker" id="datepicker" type="text" data-date-format="yyyy-mm-dd" required value="<?php if (isset($_POST['from'])) {
														echo $_POST['from'];
													} else {
														echo date('Y-m-d', strtotime('-15 day'));
													} ?>">
													<span class="input-group-addon">
														<i class="fa fa-calendar bigger-110"></i>
													</span>
												</div>
											</div>

										</div>
										<div class="form-group">
											<label class="col-sm-1 control-label no-padding-right" for="form-field-1">To Date</label>

											<div class="col-sm-2">
												<div class="input-group">
													<input name="to" class="form-control date-picker" id="datepicker1" type="text" data-date-format="yyyy-mm-dd" required value="<?php if (isset($_POST['to'])) {
														echo $_POST['to'];
													} else {
														echo date('Y-m-d');
													} ?>">
													<span class="input-group-addon">
														<i class="fa fa-calendar bigger-110"></i>
													</span>
												</div>
											</div>

										</div>



										<!-- <button type="submit" name="submit" class="btn btn-sm btn-info">
											Search
										</button> -->

									</form>

									<div class="clearfix" style="margin-bottom: 1rem;">
										<div class="pull-right tableTools-container">
											<!-- <a>
												<button autofocus="" class="btn btn-success">
													<i class="ace-icon glyphicon glyphicon-plus"></i>
													Add New
												</button>
											</a> -->
										</div>
									</div>

									<div class="table-header">
										Results for "Rejected Orders"
									</div>

									<div>

										<table id="dynamic-table" class="table table-striped table-bordered table-hover">
											<thead>
												<tr>
													<th>Sr No</th>
													<th>Order No</th>
													<th>Order Date</th>
													<th>Delivery Type</th>
													<th>Execution Time</th>
													<th>Status</th>
													<th>Action</th>
												</tr>
											</thead>

											<tbody>

											</tbody>
										</table>
									</div>
								</div>
							</div><!-- PAGE CONTENT ENDS -->
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
		if ('ontouchstart' in document.documentElement) document.write("<script src='<?php echo SURL; ?>assets/js/jquery.mobile.custom.min.js'>" + "<" + "/script>");
	</script>
	<script src="<?php echo SURL; ?>assets/js/bootstrap.min.js"></script>

	<!-- page specific plugin scripts -->
	<script src="<?php echo SURL; ?>assets/js/jquery.dataTables.min.js"></script>
	<script src="<?php echo SURL; ?>assets/js/jquery.dataTables.bootstrap.min.js"></script>
	<!-- <script src="<?php echo SURL; ?>assets/js/dataTables.buttons.min.js"></script> -->
	<script src="<?php echo SURL; ?>assets/js/buttons.flash.min.js"></script>
	<script src="<?php echo SURL; ?>assets/js/buttons.html5.min.js"></script>
	<script src="<?php echo SURL; ?>assets/js/buttons.print.min.js"></script>
	<script src="<?php echo SURL; ?>assets/js/buttons.colVis.min.js"></script>
	<script src="<?php echo SURL; ?>assets/js/dataTables.select.min.js"></script>

	<!-- ace scripts -->
	<script src="<?php echo SURL; ?>assets/js/ace-elements.min.js"></script>
	<script src="<?php echo SURL; ?>assets/js/ace.min.js"></script>
	<script>
		$(document).ready(function () {
			// Trigger table reload on date change
			$('#datepicker, #datepicker1').change(function () {
				$('#dynamic-table').DataTable().ajax.reload(null, false);
			});

			// Initialize DataTable
			var table = $('#dynamic-table').DataTable({
				"processing": false,
				"serverSide": true,
				"ajax": {
					"url": "<?= SURL . "app/Reject_orders/your_ajax_endpoint"; ?>",
					"type": "POST",
					"data": function (d) {
						d.datepicker = $('#datepicker').val();
						d.datepicker1 = $('#datepicker1').val();
					}
				},
				"paging": true,
				"pageLength": 10,
				"order": [
					[1, 'desc']
				],
				"columns": [
					{ "data": "count", "orderable": false },
					{
						"data": "id"
					},
					{
						"data": "date"
					},
					{
						"data": "deliveryType"
					},
					{
						"data": "exec_time",
						"orderable": false
					},
					{
						"data": "deliveryStatus",
						"render": function (data) {
							return data;
						}
					},
					{
						"data": "actions",
						"orderable": true,
						"searching": true,
						"render": function (data) {
							return data;
						}
					},
				]
			});
		});
	</script>
	<script type="text/javascript">
		function confirmDelete(delUrl) {
			bootbox.confirm("Are you sure you want to delete?", function (result) {
				if (result) {
					document.location = delUrl;
				}
			});

		}
	</script>
	<script type="text/javascript">
		// var test = jQuery.noConflict();
		jQuery(function ($) {

			$('.date-picker').datepicker({
				autoclose: true,
				todayHighlight: true
			})
		});

		$("#formID").submit(function (e) {

			var form_date = $('.form_date').val();
			var to_date = $('.to_date').val();

			if (new Date(form_date) > new Date(to_date)) {
				$('.message_date').css('display', 'block');
				e.preventDefault();
			} else {
				$('.message_date').css('display', 'none');
			}


		});
	</script>

</body>

</html>
<!-- <script src="<?php echo SURL ?>assets/js/jquery-2.1.4.min.js"></script> -->
<script src="<?php echo SURL ?>assets/js/bootstrap-datepicker.min.js"></script>
<script src="<?php echo SURL ?>assets/js/moment.min.js"></script>