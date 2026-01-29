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


						<li class="active"><?php echo $title; ?> </li>
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
								<?php echo $title; ?>
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

							<form target="_blank" id="formID" class="form-horizontal" role="form" method="post" action="<?php echo SURL; ?>app/Order_log/details" enctype="multipart/form-data">

								<style type="text/css">
									.message_date {
										display: none;
										color: red;
									}

									.chosen-container {
										width: 100% !important;
									}
								</style>
								<?php

								?><?php
									$companyData = $this->db->get("tbl_company")->row();

									?>
								<div class="form-group">
									<label class="col-sm-3 control-label no-padding-right" for="form-field-1">Select Form</label>

									<div class="col-sm-3">
										<select required="required" class="chosen-select form-control" onchange="fetchData()" name="salepoint" id="salepoint" data-placeholder="Choose a User..." onchange="showhide();">
											<?php foreach ($salepoint as $key => $value) { ?>
												<option value="<?php echo $value['sale_point_id']; ?>" <?php if ($sale_Point == $value['sale_point_id']) {
																											echo "selected";
																										} ?>><?php echo $value['sp_name']; ?></option>
											<?php } ?>

										</select>
									</div>
								</div>

								<div class="form-group">
									<label class="col-sm-3 control-label no-padding-right" for="form-field-1">Sale Order</label>

									<div class="col-sm-3">
										<select class="form-control" id="order" name="order" data-placeholder="Choose a Sale Order...">

										</select>
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
	<script type="text/javascript">
		$("#formID").submit(function(e) {

			var form_date = $('.form_date').val();
			var to_date = $('.to_date').val();

			if (new Date(form_date) > new Date(to_date)) {

				$('.message_date').css('display', 'block');
				e.preventDefault();
			} else {
				$('.message_date').css('display', 'none');
			}

		});



		fetchData()

		function fetchData() {
			var sale_point_id = $('#salepoint').val();

			// Fetch vehicles
			$.ajax({
				url: '<?php echo SURL; ?>app/Order_log/orders_list',
				type: 'POST',
				data: {
					sale_point_id: sale_point_id
				},
				success: function(response) {
					$("#order").html(response);
					$("#order").attr("class", "chosen-select");
					jQuery(function($) {
						$('#order').trigger("chosen:updated");
						var $mySelect = $('#order');
						$mySelect.chosen(); 
					});
				},
				error: function(xhr, status, error) {
					console.error('AJAX Error while fetching vehicles:', status, error);
				}
			});
		}
	</script>

	<!-- inline scripts related to this page -->

	<?php $this->load->view('en/include/paymentreceipt_js.php'); ?>

</body>

</html>