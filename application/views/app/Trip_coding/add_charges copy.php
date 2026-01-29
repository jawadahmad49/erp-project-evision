<!DOCTYPE html>
<html lang="en">
<?php
$this->load->view('app/include/head');
$this->load->view('app/include/header');
?>

<body class="no-skin">

	<div class="main-container ace-save-state" id="main-container">

		<?php $this->load->view('app/include/sidebar'); ?>


		<!-- <fieldset class="scheduler-border"> -->

		<div class="main-content">
			<div class="main-content-inner">
				<div class="breadcrumbs ace-save-state" id="breadcrumbs">
					<ul class="breadcrumb">
						<li>
							<i class="ace-icon fa fa-home home-icon"></i>
							<a href="<?php echo SURL . "admin"; ?>">Home</a>
						</li>

						<li>
							<a href="<?php echo SURL . "app/Trip_coding"; ?>">Trip Coding List <?php if ($arabic_check == 'Yes') { ?>(قائمة العملاء)<?php } ?> </a>
						</li>
						<li class="active"><?php echo ucwords($filter); ?> Trip Coding<?php if ($arabic_check == 'Yes') { ?>(أضف الزبون)<?php } ?> </li>
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
							LPG <?php if ($arabic_check == 'Yes') { ?>(نقاط البيع)<?php } ?>
							<small>
								<i class="ace-icon fa fa-angle-double-right"></i>
								<?php echo ucwords($filter); ?> Trip Coding <?php if ($arabic_check == 'Yes') { ?>(أضف الزبون)<?php } ?>
							</small>
						</h1>
					</div><!-- /.page-header -->
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
									Oh snap!
								</strong>

								<?php echo $this->session->flashdata('err_message'); ?>
								<br>
							</div>

							<?php
						} ?>


					</div>

					<form class="form-horizontal" role="form" id="c_form" method="post" action="<?php echo SURL . "app/Rider_coding/" . $filter ?>" enctype="multipart/form-data">
						<!-- <form class="form-horizontal" role="form" id="c_form" method="post" action="<?php echo SURL . "app/Rider_coding/getExtra_DevCharges" ?>" enctype="multipart/form-data"> -->
						<?php if ($arabic_check == 'Yes') { ?>
							<div class="form-group">
								<label class="col-sm-4 control-label no-padding-right" for="form-field-1"> Language </label>

								<div class="col-sm-4" style="margin-top: 8px;">
									<input type="radio" onclick="english_lang()" checked="checked" name="lang" id="english">
									English
									<input style="margin-left: 2%;" type="radio" onclick="urdu_lang()" name="lang" id="urdu">Arabic
								</div>
							</div>
						<?php } ?>


						<div class="col-xs-12 col-sm-12">
							<!-- PAGE CONTENT BEGINS-->

							<div class="row">
								<div class="col-xs-12 col-sm-12">
									<div class=" widget-body " style="display: block;">

										<div class="widget-main">
											<div class="form-group">
												<label class="col-sm-1 control-label no-padding-right" for="form-field-1"> Location </label>
												<div class="col-sm-2">
													<select class="form-control" name="location" id="location" required>
														<?php foreach ($salepoint as $key => $value) { ?>
															<option value="<?php echo $value['sale_point_id']; ?>" data-latlong="<?= $value['shop_location']; ?>" <?php if ($record['sale_point_id'] == $value['sale_point_id']) {
																	 echo 'selected';
																 } ?>><?php echo $value['sp_name']; ?></option>
														<?php } ?>
													</select>
												</div>
												<label class="col-sm-1 control-label no-padding-right" for="form-field-1"> Vehicle </label>
												<div class="col-sm-2">
													<?php $_SESSION["vehicle_id"] = $record['vehicle_id']; ?>
													<select class="form-control" name="vehicle_id" id="vehicle_id" required>

													</select>
												</div>
												<label class="col-sm-1 control-label no-padding-right" for="form-field-1"> Rider </label>
												<div class="col-sm-2">
													<?php $_SESSION["rider_id"] = $record['rider_id']; ?>
													<select class="form-control" name="rider_id" id="rider_id" required>

													</select>
												</div>
												<label class="col-sm-1 control-label no-padding-right" for="form-field-1"> Order </label>
												<div class="col-sm-2">
													<?php $_SESSION["order_id"] = $record['order_id']; ?>
													<select class="form-control" name="order_id[]" id="order_id" required multiple>

													</select>
												</div>
											</div>
											<div class="form-group" style="background: #B8B8B8;">
												<label class="col-sm-12" style="text-align: center;" for="form-field-1"><strong>Select Your Trip Location</strong></label>
												<div id="mapp" style="width:100%; height:500px; border:11px;"></div>
												<input type="text" id="shop-location" name="shop_location" value="<?php echo $record['shop_location'] ?>">
											</div>
											<div class="row">
												<div class="center">
													<button class="btn btn-info" onclick="reload_parent();">
														<i class="ace-icon fa fa-check bigger-110"></i>
														Submit <?php if ($arabic_check == 'Yes') { ?> (إرسال) <?php } ?>
													</button>
												</div>
												<input type="hidden" name="action" value="" />
												<input type="hidden" name="edit" id='edit' value="<?php echo $record['id']; ?>" />
											</div>
										</div>
									</div>
								</div>
							</div>
					</form>
				</div>
			</div>
		</div>
		<!-- </fieldset> -->
	</div><!-- /.main-container -->

	<?php
	$this->load->view('app/include/footer');
	$this->load->view('app/include/js');
	?>


	<!-- <?php $this->load->view('app/include/customer_js.php'); ?> -->


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
	</script>
	<!-- jQuery library -->
	<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>

	<!-- Bootstrap Datepicker -->
	<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.9.0/css/bootstrap-datepicker.min.css">
	<script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.9.0/js/bootstrap-datepicker.min.js"></script>
	<script src="https://cdnjs.cloudflare.com/ajax/libs/chosen/1.8.7/chosen.jquery.min.js"></script>
	<script defer src="https://maps.googleapis.com/maps/api/js?libraries=places&language=<?= $_SESSION['lang'] ?>&key=AIzaSyCJPePs39ubzYGmfpcKbPV6k404GvXcL7s" type="text/javascript"></script>
	<script>
		$("#id-date-picker-1").blur(function () {
			//alert('asdas');
			// date = $(this).val();
			// alert(date);
			// stock(date);
		});

		function english_lang() {

			jQuery(".urdu_class").each(function (index) {

				jQuery(this).removeAttr('dir');
				setEnglish(jQuery(this));

			});

		}


		function reload_parent() {
			var type = '<?php echo $_GET["type"] ?>';
			if (type == "sale") {
				//self.close();
				window.opener.document.location.reload(true);
			}
		}
		jQuery(function ($) {
			$('#location').trigger("chosen:updated");
			var $mySelect = $('#location');
			$mySelect.chosen();
			$mySelect.trigger('chosen:activate');
		});
	</script>


	<!-- end editor -->
	<style>
		.scheduler-border {
			border: 1px solid #ccc;
			/* Border style */
			padding: 5px 10px;
			/* Padding to give some space around the text */
			border-radius: 5px;
			/* Optional: Rounded corners */
		}
	</style>
	<script src="<?php echo SURL ?>assets/js/jquery-2.1.4.min.js"></script>
	<script src="<?php echo SURL ?>assets/js/bootstrap-datepicker.min.js"></script>
	<script src="<?php echo SURL ?>assets/js/moment.min.js"></script>
	<script type="text/javascript">
		var test = jQuery.noConflict();
		jQuery(function ($) {
			//datepicker plugin
			//link
			$('.date-picker').datepicker({
				autoclose: true,
				todayHighlight: true
			})
		});
		function fetchData() {
			var sale_point_id = $('#location').val();

			// Fetch vehicles
			$.ajax({
				url: '<?php echo SURL; ?>app/Trip_coding/get_vehicles',
				type: 'POST',
				data: { sale_point_id: sale_point_id },
				success: function (response) {
					$("#vehicle_id").html(response);
					$("#vehicle_id").addClass("chosen-select").trigger("chosen:updated");
					if (!$("#vehicle_id").data('chosen')) {
						$("#vehicle_id").chosen();
					}
					console.log('Vehicles loaded successfully:', response);
				},
				error: function (xhr, status, error) {
					console.error('AJAX Error while fetching vehicles:', status, error);
				}
			});

			// Fetch riders
			$.ajax({
				url: '<?php echo SURL; ?>app/Trip_coding/get_riders',
				type: 'POST',
				data: { sale_point_id: sale_point_id },
				success: function (response) {
					$("#rider_id").html(response);
					$("#rider_id").addClass("chosen-select").trigger("chosen:updated");
					if (!$("#rider_id").data('chosen')) {
						$("#rider_id").chosen();
					}
					console.log('Riders loaded successfully:', response);
				},
				error: function (xhr, status, error) {
					console.error('AJAX Error while fetching riders:', status, error);
				}
			});

			// Fetch orders
			$.ajax({
				url: '<?php echo SURL; ?>app/Trip_coding/get_orders',
				type: 'POST',
				data: { sale_point_id: sale_point_id },
				success: function (response) {
					$("#order_id").html(response);
					$("#order_id").addClass("chosen-select").trigger("chosen:updated");
					if (!$("#order_id").data('chosen')) {
						$("#order_id").chosen();
					}
					console.log('Orders loaded successfully:', response);
				},
				error: function (xhr, status, error) {
					console.error('AJAX Error while fetching orders:', status, error);
				}
			});
		}
		$(document).ready(function () {
			fetchData();
		});
		$('#location').on('change', function () {
			fetchData();
		});

	</script>
</body>

</html>