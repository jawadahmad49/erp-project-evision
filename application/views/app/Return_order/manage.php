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


						<li class="active">Manage All Return Orders</li>
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

									<form id="formID" role="form" method="post" action="">

										<div class="form-group">
											<label class="col-sm-1 control-label no-padding-right" for="form-field-1" style="padding-top:6px;">From Date</label>
											<div class="col-sm-2">
												<div class="input-group">
													<input name="from" class="form-control date-picker" id="datepicker" type="text" data-date-format="yyyy-mm-dd" value="<?php echo date("Y-m-d", strtotime('-15 days')); ?>">
													<span class="input-group-addon">
														<i class="fa fa-calendar bigger-110"></i>
													</span>
												</div>
											</div>

											<div class="form-group message_date">

												<label class="col-sm-5 control-label no-padding-right" for="form-field-1"></label>
												<div class=" col-sm-3">To date must be greater than from date</div>
											</div>

											<label class="col-sm-1 control-label no-padding-right" for="form-field-1" style="padding-top:6px;">To Date</label>

											<div class="col-sm-2">
												<div class="input-group">
													<input name="to" class="form-control date-picker form_date" id="datepicker1" type="text" data-date-format="yyyy-mm-dd" value="<?php echo date('Y-m-d'); ?>">
													<span class="input-group-addon">
														<i class="fa fa-calendar bigger-110"></i>
													</span>
												</div>
											</div>

											<button type="submit" value="submit" name="submit" class="btn btn-sm btn-info">
												Search
											</button>
										</div>
									</form>


									<div class="clearfix">
										<div class="pull-right tableTools-container">
											<a href="<?php echo SURL ?>app/Return_order/add">
												<button autofocus class="btn btn-success">
													<i class="ace-icon glyphicon glyphicon-plus"></i>
													Add New <?php if ($arabic_check == 'Yes') { ?>(اضف جديد)<?php } ?>
												</button>
											</a>
										</div>
									</div>
									<div class="table-header">
										Results for "All Return Orders"
									</div>

									<div>

										<table id="dynamic-table" class="table table-striped table-bordered table-hover">
											<thead>
												<tr>
													<th>Sr No</th>
													<th>Order No</th>
													<th>Return Date</th>
													<th>Total Quantity</th>
													<th>Total Amount</th>
													<th>Action</th>
												</tr>
											</thead>

											<tbody>
												<!-- Data will be dynamically populated here -->
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
	</div><!-- /.main-container -->

	<!-- basic scripts -->

	<!--[if !IE]> -->
	<script src="<?php echo SURL; ?>assets/js/jquery-2.1.4.min.js"></script>

	<!-- <![endif]-->

	<!--[if IE]>
<script src="assets/js/jquery-1.11.3.min.js"></script>
<![endif]-->
	<script type="text/javascript">
		if (' ontouchstart' in document.documentElement) document.write("<script src='<?php echo SURL; ?>assets/js/jquery.mobile.custom.min.js'>" + "<" + "/script>");
	</script>
	<script src="<?php echo SURL; ?>assets/js/bootstrap.min.js"></script>

	<!-- page specific plugin scripts -->
	<script src="<?php echo SURL; ?>assets/js/jquery.dataTables.min.js"></script>
	<script src="<?php echo SURL; ?>assets/js/jquery.dataTables.bootstrap.min.js"></script>
	<script src="<?php echo SURL; ?>assets/js/dataTables.buttons.min.js"></script>
	<script src="<?php echo SURL; ?>assets/js/buttons.flash.min.js"></script>
	<script src="<?php echo SURL; ?>assets/js/buttons.html5.min.js"></script>
	<script src="<?php echo SURL; ?>assets/js/buttons.print.min.js"></script>
	<script src="<?php echo SURL; ?>assets/js/buttons.colVis.min.js"></script>
	<script src="<?php echo SURL; ?>assets/js/dataTables.select.min.js"></script>

	<!-- ace scripts -->
	<script src="<?php echo SURL; ?>assets/js/ace-elements.min.js"></script>
	<script src="<?php echo SURL; ?>assets/js/ace.min.js"></script>


	<script>
		$(document).ready(function() {
			$('#datepicker, #datepicker1').change(function() {
				showmange();
			});
			var table = $('#dynamic-table').DataTable({
				"processing": false,
				"serverSide": true,
				"ajax": {
					"url": "<?= SURL . "app/Return_order/your_ajax_endpoint"; ?>",
					"type": "POST",
					"data": function(d) {
						d.datepicker = $('#datepicker').val();
						d.datepicker1 = $('#datepicker1').val();
					}
				},
				"paging": true,
				"pageLength": 10,
				"order": [
					[1, 'desc']
				],
				"columns": [{
						"data": "count",
						"orderable": false
					},
					{
						"data": "order_id"
					},
					{
						"data": "date"
					},
					{
						"data": "total_qty"
					}, {
						"data": "total_amount"
					},
					{
						"data": "actions",
						"orderable": true,
						"searching": true,
						"render": function(data) {
							return data;
						}
					}
				]
			});
		});

		function showmange() {
			$('#dynamic-table').DataTable().ajax.reload(null, false);
		}
	</script>

	<script src="<?php echo SURL; ?>assets/js/bootbox.js"></script>
	<script type="text/javascript">
		function confirmDelete(delUrl) {
			bootbox.confirm("Are you sure you want to delete?", function(result) {
				if (result) {
					document.location = delUrl;
				}
			});

		}
	</script>
	</script>

</body>

</html>