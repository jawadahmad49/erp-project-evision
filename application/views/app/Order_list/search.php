<!DOCTYPE html>

<html lang="en">

<?php

$this->load->view('app/include/head');

$this->load->view('app/include/header');

$comp = $this->db->query("SELECT * FROM `tbl_company`")->row_array(); ?>



<body class="no-skin">



	<div class="main-container ace-save-state" id="main-container">



		<?php $this->load->view('app/include/sidebar'); ?>



		<div class="main-content">

			<div class="main-content-inner">

				<div class="breadcrumbs ace-save-state" id="breadcrumbs" style=" font-weight: bold;">

					<ul class="breadcrumb">

						<li>

							<i class="ace-icon fa fa-home home-icon"></i>

							<a href="<?php echo SURL . "Module/app"; ?>">Home</a>

						</li>



						<li class="active"><?php echo $title; ?><?php if ($arabic_check == 'Yes') { ?>(تقرير البائع الحكيم

							)<?php } ?> </li>

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

							ERP <?php echo $this->session->flashdata('nature'); ?>

							<small>

								<i class="ace-icon fa fa-angle-double-right"></i>

								<?php echo $title; ?><?php if ($arabic_check == 'Yes') { ?>(تقرير البائع الحكيم

								)<?php } ?>

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



									</strong>



									<?php echo $this->session->flashdata('err_message'); ?>

									<br>

								</div>



							<?php

							}   ?>



							<form id="formID" target="blank_" class="form-horizontal" role="form" method="post" action="<?php echo SURL; ?>app/Order_list/details" enctype="multipart/form-data">



								<style type="text/css">
									.message_date {

										display: none;

										color: red;

									}
								</style>



								<?php

								$companyData = $this->db->get("tbl_company")->row();



								?>

								<div class="form-group">

									<label class="col-sm-5 control-label no-padding-right" for="form-field-1">From Date<?php if ($arabic_check == 'Yes') { ?>(من التاريخ

										)<?php } ?> </label>


									<div class="col-sm-3">



										<div class="input-group">

											<span class="input-group-addon">

												<i class="fa fa-calendar bigger-110"></i>

											</span>

											<input name="from_date" class="form-control date-picker form_date" readonly id="id-date-picker-1" type="text" data-date-format="yyyy-mm-dd" required="" value="<?php echo date('Y-m-d'); ?>">

										</div>

									</div>

								</div>

								<div class="form-group message_date">

									<label class="col-sm-5 control-label no-padding-right" for="form-field-1"></label>

									<div class=" col-sm-3">To date must be greater than from date</div>

								</div>





								<div class="form-group">

									<label class="col-sm-5 control-label no-padding-right" for="form-field-1">To Date</label>







									<div class="col-sm-3">



										<div class="input-group">

											<span class="input-group-addon">

												<i class="fa fa-calendar bigger-110"></i>

											</span>

											<input name="to_date" class="form-control date-picker to_date" id="id-date-picker-1" readonly type="text" data-date-format="yyyy-mm-dd" required="" value="<?php echo date('Y-m-d'); ?>">

										</div>

									</div>





								</div>

								<div class="form-group">

									<label class="col-sm-5 control-label no-padding-right" for="form-field-1">Select User</label>



									<div class="col-sm-3">

										<select class="chosen-select form-control" name="user" id="user">

											<option value="All">All</option>

											<?php foreach ($users as $key => $data) { ?>

												<option value="<?php echo $data['id']; ?>"><?php echo $data['name']; ?></option>

											<?php } ?>



										</select>

									</div>

								</div>



								<div class="form-group">

									<label class="col-sm-5 control-label no-padding-right" for="form-field-1">Select Sale Point</label>



									<div class="col-sm-3">

										<select class="chosen-select form-control" onchange="get_zone()" name="salepoint" id="salepoint">
											<?php
											if ($salepoint) {
												foreach ($salepoint as $key => $value) { ?>

													<option value="<?php echo  $value['sale_point_id']; ?>" <?php if ($sale_Point == $value['sale_point_id']) {
																												echo "selected";
																											} ?>><?php echo  $value['sp_name']; ?></option>

												<?php }
											} else { ?>
												<option value="All">All Locations</option>
											<?php }
											?>


										</select>

										<input type="hidden" id="sale_Point" value="<?php echo $report['sale_Point'] ?>">



									</div>


								</div>
								<div class="form-group">
								<label class="col-sm-5 control-label no-padding-right" for="form-field-1">Select Zone</label>

								<div class="col-sm-3">
										<select class="chosen-select form-control" name="zone_id" id="zone_id">
										<option value="All">All Zone</option>
											<!-- <?php
											if ($zone_lists) {
												foreach ($zone_lists as $key => $value) { ?>

													<option value="<?php echo  $value['detail_id']; ?>" <?php if ($zone_id == $value['detail_id']) {
																												echo "selected";
																											} ?>><?php echo  $value['zone_name']; ?></option>

												<?php }
											} else { ?>
												<option value="All">All Zone</option>
											<?php }
											?> -->


										</select>

										<input type="hidden" id="sale_Point" value="<?php echo $report['sale_Point'] ?>">



										</div>

										</div>


								<div class="row">



									<div class="form-actions center">

										<button class="btn btn-info">

											<i class="ace-icon fa fa-check bigger-110"></i>

											Preview

										</button>

									</div>



								</div>



								<input type="hidden" name="id" value="<?php echo $payemetreceipt['vno'] ?>" />

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

	</div>

	<?php

	$this->load->view('app/include/js');

	?>

<script type="text/javascript">

get_zone()
		function get_zone() {
			var salepoint = $('#salepoint').val();

			var edit = "<?php echo $record['zone']; ?>";  // Check if this is correctly set

			// console.log("Location:", location);
			// console.log("Edit:", edit);  // Log to see what is being sent

			$.ajax({
				url: "<?php echo SURL . 'app/Order_list/get_zone'; ?>",
				cache: false,
				type: "POST",
				data: {
					salepoint: salepoint,
					edit: edit
				},
				success: function (html) {

					$("#zone_id").html(html);
					$("#zone_id").attr("class", "chosen-select");
					jQuery(function($) {
						$('#zone_id').trigger("chosen:updated");
						var $mySelect = $('#zone_id');
						$mySelect.chosen();
						//$mySelect.trigger('chosen:activate');

					});
				},
				error: function (xhr, status, error) {
					console.error("An error occurred while fetching zones: ", status, error);
				}
			});
		}
</script>

	<script type="text/javascript">



		get_sale_point();



		// function get_sale_point() {



		// 	var sale_Point = $("#sale_Point").val();

		// 	$.ajax({

		// 		url: "<?php echo SURL . "app/Order_list/get_sale_point"; ?>",

		// 		cache: false,

		// 		type: "POST",

		// 		data: {



		// 			sale_Point: sale_Point,

		// 		},

		// 		success: function(html) {

		// 			$("#salepoint").html(html);

</script>
	<?php $this->load->view('app/include/paymentreceipt_js.php'); ?>

</body>



</html>