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

							LPG

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



							<form id="formID" target="blank_" class="form-horizontal" role="form" method="post" action="<?php echo SURL; ?>app/App_feedback/details" enctype="multipart/form-data">



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

										<select class="chosen-select form-control" onchange="user_zones()" name="user" id="user">

											<option value="All">All</option>

											<?php foreach ($users as $key => $data) { ?>

												<option value="<?php echo $data['id']; ?>"><?php echo $data['name']; ?></option>

											<?php } ?>



										</select>

									</div>

								</div>




								<!-- <div class="form-group">
								<label class="col-sm-5 control-label no-padding-right" for="form-field-1">Select Zone</label>

								<div class="col-sm-3">
										<select class="chosen-select form-control" name="zone_id" id="zone_id">



										</select>


										</div>

										</div> -->


								<div class="row">



									<div class="form-actions center">

										<button class="btn btn-info">

											<i class="ace-icon fa fa-check bigger-110"></i>

											Preview

										</button>

									</div>



								</div>



								<input type="hidden" name="id" value="<?php echo $payemetreceipt['vno'] ?>" />
								<input name="from_date" class="form-control date-picker form_date" readonly id="id-date-picker-1" type="hidden" data-date-format="yyyy-mm-dd" value="<?php echo date('Y-m-d', strtotime('-15 days')); ?>">

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
		// user_zones()
		function user_zones() {
			var user = $("#user").val();
			// alert(user);

			$.ajax({
				url: "<?php echo SURL . 'app/App_feedback/user_zones'; ?>",
				cache: false,
				type: "POST",
				data: {
					user: user
				},
				success: function(html) {
					// $("#zone_id").html(html);
					$("#zone_id").html(html);
					$("#zone_id").attr("class", "chosen-select");
					jQuery(function($) {
						$('#zone_id').trigger("chosen:updated");
						var $mySelect = $('#zone_id');
						$mySelect.chosen();
						//$mySelect.trigger('chosen:activate');

					});
				},
				error: function(xhr, status, error) {
					console.error("AJAX Error: " + status + ": " + error);
					alert("An error occurred while fetching zone points.");
				}
			});
		}
	</script>

	<?php $this->load->view('app/include/paymentreceipt_js.php'); ?>

</body>



</html>