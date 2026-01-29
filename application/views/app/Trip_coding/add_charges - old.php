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

					<form class="form-horizontal" role="form" id="distance_form" method="post" action="<?php echo SURL . "app/Trip_coding/" . $filter ?>" enctype="multipart/form-data">


						<div class="col-xs-12 col-sm-12">
							<!-- PAGE CONTENT BEGINS-->

							<div class="row">
								<div class="col-xs-12 col-sm-12">
									<fieldset class="scheduler-border">
										<legend class="scheduler-border">Trip Coding</legend>
										<div class="widget-main">
											<div class="form-group">
												<label class="col-sm-1 control-label no-padding-right" for="form-field-1"> Sale Point </label>
												<div class="col-sm-2">
													<select class="form-control" name="location" id="location" <?php if ($record && $record['status'] != 'Pending') { ?> disabled <?php } ?> required>
														<!-- <option>Select an option</option> -->
														<?php foreach ($salepoint as $key => $value) { ?>
															<option value="<?php echo $value['sale_point_id']; ?>" data-latlong="<?= $value['shop_location']; ?>" <?php if ($record['sale_point_id'] == $value['sale_point_id']) {
																	 echo 'selected';
																 } ?>><?php echo $value['sp_name']; ?></option>
														<?php } ?>
													</select>
												</div>
												<?php $check = $this->db->query("Select delivery_by from tbl_company where id = '1'")->row_array()['delivery_by'];
												if ($check == 'delivery_by_rider') { ?>
													<label class="col-sm-1 control-label no-padding-right" for="form-field-1"> Vehicle </label>
													<div class="col-sm-2">
														<?php $_SESSION["vehicle_id"] = $record['vehicle_id']; ?>
														<select class="form-control" <?php if ($record && $record['status'] != 'Pending') { ?> disabled <?php } ?> name="vehicle_id" id="vehicle_id" required>

														</select>
													</div>
													<label class="col-sm-1 control-label no-padding-right" for="form-field-1"> Rider </label>
													<div class="col-sm-2">
														<?php $_SESSION["rider_id"] = $record['rider_id']; ?>
														<select class="form-control" <?php if ($record && $record['status'] != 'Pending') { ?> disabled <?php } ?> name="rider_id" id="rider_id" required>

														</select>
													</div>
												<?php } ?>
												<label class="col-sm-1 control-label no-padding-right" for="form-field-1"> Order </label>
												<div class="col-sm-2">
													<?php $_SESSION["order_id"] = $record['order_id']; ?>
													<select class="form-control" <?php if ($record && $record['status'] != 'Pending') { ?> disabled <?php } ?> name="order_id[]" id="order_id" onchange="get_customer()" required multiple>

													</select>
												</div>
											</div>
											<div class="col-md-12 form-group">
												<fieldset class="scheduler-border">
													<legend class="scheduler-border">Customer / Delivery Info</legend>
													<div class="col-xs-8">
														<div class="col-xs-12 form-group">
															<label class="col-sm-4 control-label no-padding-right" for="form-field-1">Customer Name</label>
															<div class="col-sm-6">
																<input type="text" class="form-control" tabindex="-1" readonly id="name">
															</div>
														</div>
														<div class="col-xs-12 form-group">
															<label class="col-sm-4 control-label no-padding-right" for="form-field-1">Phone #</label>
															<div class="col-sm-6">
																<input type="text" class="form-control" tabindex="-1" readonly id="phone">
															</div>
														</div>
														<div class="col-xs-12 form-group">
															<label class="col-sm-4 control-label no-padding-right" for="form-field-1">City</label>
															<div class="col-sm-6">
																<input type="text" class="form-control" tabindex="-1" readonly id="city">
															</div>
														</div>
														<div class="col-xs-12 form-group">
															<label class="col-sm-4 control-label no-padding-right" for="form-field-1">Area</label>
															<div class="col-sm-6">
																<input type="text" class="form-control" tabindex="-1" readonly id="area">
															</div>
														</div>
														<div class="col-xs-12 form-group">
															<label class="col-sm-4 control-label no-padding-right" for="form-field-1">Order Status</label>
															<div class="col-sm-6">
																<input type="text" class="form-control" tabindex="-1" readonly id="order_status_actual">
															</div>
														</div>
														<div class="col-xs-12 form-group">
															<label class="col-sm-4 control-label no-padding-right" for="form-field-1">Delivery Type</label>
															<div class="col-sm-6">
																<input type="text" class="form-control" tabindex="-1" readonly id="delivery_type">
															</div>
														</div>
														<div class="col-xs-12 form-group">
															<label class="col-sm-4 control-label no-padding-right" for="form-field-1">Delivery Charges</label>
															<div class="col-sm-6">
																<input type="text" class="form-control" tabindex="-1" readonly id="delivery_charges">
															</div>
														</div>
														<div class="col-xs-12 form-group">
															<label class="col-sm-4 control-label no-padding-right" for="form-field-1">Delivery Address</label>
															<div class="col-sm-6">
																<textarea name="delivery_address" id="delivery_address" class="form-control" readonly maxlength="250" style="resize: vertical;"></textarea>
															</div>
														</div>

													</div>
													<div class="col-xs-4">
														<img width="268" height="210" id="target" src="<?php if (isset($record['dp'])) {
															echo IMG . 'user/' . $record['dp'];
														} else {
															echo IMG . 'user/default.JPG';
														} ?>" style="margin-left: 0%;">
													</div>
												</fieldset>
											</div>
											<div class="col-md-12">
												<div class="table-header">
													Order Detail
												</div>

												<div>
													<table id="dynamic-table" class="table table-striped table-bordered table-hover">
														<thead>
															<tr>
																<th>Sr No</th>
																<th>Item</th>
																<th>Type</th>
																<th>Unit Price</th>
																<th>Quantity</th>
																<th>Amount</th>
															</tr>
														</thead>
														<tbody id="order_items">
															<!-- Order items will be populated here -->
														</tbody>
													</table>

													<table style="width:35%; float: right;" id="simple-table" class="table  table-bordered table-hover fc_currency">
														<thead>
															<tr>
																<th colspan="2">Bill Details <span class="currency"></span> </th>
															</tr>
														</thead>
														<tbody>
															<tr>
																<td style="background:#848484; color:#fff">Total Quantity</td>
																<td><input class="form-control" type="text" tabindex="-1" readonly="" id="total_qty" name="total_qty" value=""></td>
															</tr>
															<tr>
																<td style="background:#848484; color:#fff">LPG Amount</td>
																<td><input class="form-control" type="text" tabindex="-1" readonly="" id="lpg_amount" name="lpg_amount" value=""></td>
															</tr>
															<tr>
																<td style="background:#848484; color:#fff">GST Percentage</td>
																<td><input class="form-control" type="text" tabindex="-1" readonly="" id="gst_perc" name="gst_perc" value=""></td>
															</tr>
															<tr>
																<td style="background:#848484; color:#fff">GST Amount</td>
																<td><input class="form-control" type="text" tabindex="-1" readonly="" id="ttl_gst" name="ttl_gst" value=""></td>
															</tr>
															<tr>
																<td style="background:#848484; color:#fff">Total Security Charges</td>
																<td><input class="form-control" type="text" tabindex="-1" readonly="" id="ttl_security_charges" name="ttl_security_charges" value=""></td>
															</tr>
															<tr>
																<td style="background:#848484; color:#fff">Accessories Amount</td>
																<td><input class="form-control" type="text" tabindex="-1" readonly="" id="ttl_accessories" name="ttl_accessories" value=""></td>
															</tr>
															<tr>
																<td style="background:#848484; color:#fff">Total Delivery Charges</td>
																<td><input class="form-control" type="text" tabindex="-1" readonly="" id="ttl_delivery_charge" name="ttl_delivery_charge" value=""></td>
															</tr>

															<tr>
																<td style="background:#848484; color:#fff">Total Swap Credits</td>
																<td><input class="form-control" type="text" tabindex="-1" readonly="" id="ttl_swap_charges" name="ttl_swap_charges" value=""></td>
															</tr>
															<tr>
																<td style="background:#848484; color:#fff">Grand Total</td>
																<td><input class="form-control" type="text" tabindex="-1" readonly="" id="grand_total" name="grand_total" value=""></td>
															</tr>

															<tr id="rider_row" style="display:none;">
																<td style="background:#848484; color:#fff">Rider</td>
																<td>
																</td>
															</tr>
															<tr id="reason_row" style="display:none;">
																<td style="background:#848484; color:#fff">Reject Reason</td>
																<td>
																	<textarea style="width: 100%;" maxlength="250" name="reject_reason" id="reject_reason" cols="5" rows="5"></textarea>
																</td>
															</tr>
														</tbody>
													</table>
												</div>
											</div>
											<div class="form-group">
												<div class="col-sm-12">
													<div id="map" style="width:100%;height:400px;"></div>
												</div>
												<!-- <div id="directions-panel"></div> -->

												<div class="col-sm-12">
													<a href="<?php echo $record['route_link'] ?>" id="route_link_display" class="btn btn-sm btn-danger" target="_blank">View Route On Google Map</a>
													<input type="hidden" name="route_link" id="route_link_input" value="<?php echo $record['route_link'] ?>">
												</div>
											</div>
											<div class="row">
												<div class="center">
													<?php if (empty($record) || $record['status'] == 'Pending') { ?>
														<button type="submit" class="btn btn-info" onclick="reload_parent();">
															<i class="ace-icon fa fa-check bigger-110"></i>
															Submit
														</button>
													<?php } ?>
												</div>
												<input type="hidden" name="action" value="" />
												<input type="hidden" name="edit" id='edit' value="<?php echo $record['id']; ?>" />

												<input type="hidden" id='origin_lat' value="<?php echo $origin_lat ?>" />
												<input type="hidden" id='origin_lng' value="<?php echo $origin_lng ?>" />
												<input type="hidden" id='destination_latlng' value="" />

												<input type="hidden" id="order_sequence" name="order_sequence" placeholder="Order sequence will appear here" readonly>
												<input type="hidden" name="in_mile" value="<?php echo $record['distance_in_mile']; ?>" />
												<input type="hidden" name="in_kilo" value="<?php echo $record['distance_in_kilo']; ?>" />
												<input type="hidden" name="duration_text" value="<?php echo $record['duration_text']; ?>" />
											</div>
										</div>
									</fieldset>
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

	<script src="https://cdnjs.cloudflare.com/ajax/libs/chosen/1.8.7/chosen.jquery.min.js"></script>
	<script>
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
			$('.date-picker').datepicker({
				autoclose: true,
				todayHighlight: true
			})
		});

		function fetchData() {
			var sale_point_id = $('#location').val();
			var trip_id = $("#edit").val();

			// Fetch vehicles
			$.ajax({
				url: '<?php echo SURL; ?>app/Trip_coding/get_vehicles',
				type: 'POST',
				data: {
					sale_point_id: sale_point_id,
					trip_id: trip_id
				},
				success: function (response) {
					$("#vehicle_id").html(response);
					$("#vehicle_id").addClass("chosen-select").trigger("chosen:updated");
					if (!$("#vehicle_id").data('chosen')) {
						$("#vehicle_id").chosen();
					}
				},
				error: function (xhr, status, error) {
					console.error('AJAX Error while fetching vehicles:', status, error);
				}
			});

			// Fetch riders
			$.ajax({
				url: '<?php echo SURL; ?>app/Trip_coding/get_riders',
				type: 'POST',
				data: {
					sale_point_id: sale_point_id,
					trip_id: trip_id
				},
				success: function (response) {
					$("#rider_id").html(response);
					$("#rider_id").addClass("chosen-select").trigger("chosen:updated");
					if (!$("#rider_id").data('chosen')) {
						$("#rider_id").chosen();
					}
				},
				error: function (xhr, status, error) {
					console.error('AJAX Error while fetching riders:', status, error);
				}
			});
			// Fetch orders
			$.ajax({
				url: '<?php echo SURL; ?>app/Trip_coding/get_orders',
				type: 'POST',
				data: {
					sale_point_id: sale_point_id,
					trip_id: trip_id,
				},
				success: function (response) {
					$("#order_id").html(response);
					$("#order_id").addClass("chosen-select").trigger("chosen:updated");
					if (!$("#order_id").data('chosen')) {
						$("#order_id").chosen();
					}
					var destinations = [];
					// Capture all selected delivery locations
					$('#order_id').find('option:selected').each(function () {
						var delivery_location = $(this).data('delivery_location');
						var lat = delivery_location.latitude; // Assuming latitude is available
						var lng = delivery_location.longitude; // Assuming longitude is available
						var order_number = $(this).val(); // Get order number from option value

						// Save the destination with associated order number
						destinations.push({
							lat: lat,
							lng: lng,
							order_number: order_number // Associate order number with the location
						});
					});

					// Save all selected destinations
					$('#destination_latlng').val(JSON.stringify(destinations));
					handleLocationChange()
				},
				error: function (xhr, status, error) {
					console.error('AJAX Error while fetching orders:', status, error);
				}
			});
		}
		var selectedOrders = [];

		// Listen for changes in the select element
		$('#order_id').on('change', function () {
			var selected = $(this).val(); // Get all selected values
			selectedOrders = selectedOrders.filter(value => selected.includes(value)); // Keep only the previously selected values that are still selected

			// Add new selections
			selected.forEach(function (value) {
				if (!selectedOrders.includes(value)) {
					selectedOrders.push(value); // Add new selections in the order they are clicked
				}
			});

			// alert(selectedOrders); // Display the selected values in the order of clicks
		});

		function get_customer() {
			var order = selectedOrders[selectedOrders.length - 1]; // Get the last selected value based on click order


			$.ajax({
				url: '<?php echo SURL; ?>app/Order_confirmation/get_customer',
				type: 'POST',
				data: {
					order: order
				},
				success: function (response) {
					var res = response.split('|');
					$("#name").val(res[0]);
					$("#phone").val(res[1]);
					$("#city").val(res[2]);
					$("#area").val(res[3]);
					$("#delivery_type").val(res[6]);
					$("#delivery_charges").val(res[7]);
					$("#delivery_address").html(res[4]);
					$("#target").attr('src', "<?php echo IMG . "profile/" ?>" + res[5]);
					$("#order_status_actual").val(res[8]);


					get_order_detail();
				},
				error: function (xhr, status, error) {
					console.error('AJAX Error while fetching vehicles:', status, error);
				}
			});
		}

		function get_order_detail() {
			var order = selectedOrders[selectedOrders.length - 1];

			var sale_point_id = $('#location').val();
			var delivery_charges = $('#delivery_charges').val();

			$.ajax({
				url: '<?php echo SURL; ?>app/Trip_coding/get_order_detail',
				type: 'POST',
				data: {
					order: order,
					sale_point_id: sale_point_id,
					delivery_charges: delivery_charges
				},
				dataType: 'json',
				success: function (response) {
					console.log(response)
					$("#order_items").html(response.rows);

					// Update totals
					$('#total_qty').val(response.total_qty);
					$('#lpg_amount').val(response.lpg_amount);
					$('#ttl_gst').val(response.ttl_gst);
					$('#gst_perc').val(response.gst_perc);
					$('#ttl_accessories').val(response.ttl_accessories);
					$('#ttl_security_charges').val(response.ttl_security_charges);
					$('#ttl_delivery_charge').val(response.ttl_delivery_charge);
					$('#ttl_swap_charges').val(response.ttl_swap_charges);
					$('#grand_total').val(response.grand_total);
					$('#reject_reason').val(response.reject_reason);

				},
				error: function (xhr, status, error) {
					console.error('AJAX Error while fetching order details:', status, error);
				}
			});
		}

		$(document).ready(function () {
			fetchData();
		});
		fetchData();

		$('#location').on('change', function () {
			fetchData();
		});
	</script>
	<script src="https://maps.googleapis.com/maps/api/js?key=AIzaSyCJPePs39ubzYGmfpcKbPV6k404GvXcL7s&libraries=places"></script>
	<!-- <script>
		$(function() {
			var map;

			// Initialize the map
			google.maps.event.addDomListener(window, 'load', function(listener) {
				setDestination();
				initMap();
			});

			function initMap() {
				var myLatLng = {
					lat: 30.3753, // Set default location (Pakistan in this case)
					lng: 69.3451
				};
				map = new google.maps.Map(document.getElementById('map'), {
					zoom: 16,
					center: myLatLng,
				});
			}

			function setDestination() {
				// On selecting shop location
				$('#location').change(function() {
					var selectedOption = $(this).find(':selected');
					var latLong = selectedOption.data('latlong');
					var latLngArray = latLong.split(',');
					var from_lat = parseFloat(latLngArray[0]);
					var from_lng = parseFloat(latLngArray[1]);

					// Save shop location (origin)
					$('#origin_lat').val(from_lat);
					$('#origin_lng').val(from_lng);

					// Update the map after selecting new location
					handleLocationChange();
				});

				// On selecting multiple orders (delivery locations)
				$('#order_id').change(function() {
					var destinations = [];
					$(this).find('option:selected').each(function() {
						var delivery_location = $(this).data('delivery_location');
						var lat = delivery_location.latitude;
						var lng = delivery_location.longitude;
						var order_number = $(this).val(); // Get order number from option value

						// Save the destination with associated order number
						destinations.push({
							lat: lat,
							lng: lng,
							order_number: order_number // Associate order number with the location
						});
					});

					// Save all selected destinations
					$('#destination_latlng').val(JSON.stringify(destinations));
					handleLocationChange();
				});
			}

			// Function to display optimized route for all orders
			function displayRoute(travel_mode, origin, destinations, directionsService, directionsDisplay) {
				var waypoints = destinations.map(function(destination) {
					return {
						location: new google.maps.LatLng(destination.lat, destination.lng),
						stopover: true
					};
				});

				// Use the optimizeWaypoints flag to calculate the shortest route for multiple destinations
				directionsService.route({
					origin: new google.maps.LatLng(origin.lat, origin.lng),
					destination: waypoints[waypoints.length - 1].location, // Last destination
					waypoints: waypoints.slice(0, -1), // All other points as waypoints
					optimizeWaypoints: true, // Find the optimal order of delivery locations
					travelMode: travel_mode,
					avoidTolls: true
				}, function(response, status) {
					if (status === 'OK') {
						directionsDisplay.setMap(map); // Display the route on the map
						directionsDisplay.setDirections(response); // Show the directions

						// Suppress default A, B, C markers
						directionsDisplay.setOptions({
							suppressMarkers: true
						});
						// Create a custom marker for the shop (origin)
						var origin = response.request.origin; // Extract the origin (shop location)
						var shopMarker = new google.maps.Marker({
							position: origin.location || origin, // Use origin's location (it may be LatLng or string)
							label: "S", // Set the label as "Shop"
							map: map
						});
						// Get the optimized order of waypoints from Google Maps
						var route = response.routes[0];
						// var summaryPanel = document.getElementById('directions-panel');
						// summaryPanel.innerHTML = '';
						var orderNumbers = []; // Array to hold order numbers

						// Display the ordered list of destinations and distances
						for (var i = 0; i < route.legs.length; i++) {
							var routeSegment = i + 1;

							// Find the order number associated with this destination
							var destination = destinations[i];
							var orderNumber = destination.order_number.toString();

							// Add marker with correct order number
							var marker = new google.maps.Marker({
								position: route.legs[i].end_location,
								label: orderNumber, // Use order number as marker label
								map: map
							});

							// Display route details
							// summaryPanel.innerHTML += '<b>Route Segment: ' + routeSegment +
							// 	'</b><br>';
							// summaryPanel.innerHTML += route.legs[i].start_address + ' to ';
							// summaryPanel.innerHTML += route.legs[i].end_address + '<br>';
							// summaryPanel.innerHTML += route.legs[i].distance.text + '<br><br>';

							// Store the order number in the array
							orderNumbers.push(orderNumber);
						}
						document.getElementById('order_sequence').value = orderNumbers.join(',');

					} else {
						directionsDisplay.setMap(null);
						directionsDisplay.setDirections(null);
						alert('Could not display directions due to: ' + status);
					}
				});
			}

			// Handle location changes (both shop and delivery locations)
			function handleLocationChange() {
				var from_lat = $('#origin_lat').val(); // Shop's latitude
				var from_lng = $('#origin_lng').val(); // Shop's longitude
				var destinationLatLngStr = $('#destination_latlng').val(); // Array of delivery locations

				var destinations = [];
				try {
					destinations = JSON.parse(destinationLatLngStr); // Parse the delivery locations
				} catch (e) {
					console.error('Error parsing destination lat/lng:', e);
					$('#result').html('Error processing destination data.');
					return;
				}

				var travel_mode = "DRIVING"; // Default travel mode
				var directionsDisplay = new google.maps.DirectionsRenderer({
					'draggable': false
				});
				var directionsService = new google.maps.DirectionsService();
				var origin = {
					lat: parseFloat(from_lat),
					lng: parseFloat(from_lng)
				};

				// Display the route for all orders
				displayRoute(travel_mode, origin, destinations, directionsService, directionsDisplay);
				generateRouteLink(origin, destinations)
			}
		});

		function generateRouteLink(origin, destinations) {
			// Prepare the origin
			var originStr = origin.lat + "," + origin.lng;

			let baseUrl = 'https://www.google.com/maps/dir/?api=1';
			let routeLink = `${baseUrl}&origin=${encodeURIComponent(originStr)}`;

			if (destinations.length > 0) {
				// Set the final destination
				let finalDestination = `${destinations[destinations.length - 1].lat},${destinations[destinations.length - 1].lng}`;
				routeLink += `&destination=${encodeURIComponent(finalDestination)}`;

				if (destinations.length > 1) {
					// Generate waypoints from all destinations except the origin and final destination
					let waypoints = destinations.slice(0, -1).map(destination => `${destination.lat},${destination.lng}`).join('|');
					routeLink += `&waypoints=${encodeURIComponent(waypoints)}`;
				}
			} else {
				// Handle case with no destinations
				console.warn('No destinations selected. The route link may not be complete.');
			}

			// Set the generated link to the elements
			$('#route_link_display').attr('href', routeLink);
			$('#route_link_input').val(routeLink);
		}
	</script> -->

	<script>
		var map;

		// Initialize the map
		google.maps.event.addDomListener(window, 'load', function () {
			setDestination();
			initMap();
			var selectedOption = $('#location').find(':selected');
			var latLong = selectedOption.data('latlong');
			if (latLong) {
				var latLngArray = latLong.split(',');
				var from_lat = parseFloat(latLngArray[0]);
				var from_lng = parseFloat(latLngArray[1]);

				// Save shop location (origin)
				$('#origin_lat').val(from_lat);
				$('#origin_lng').val(from_lng);
			}
		});

		function initMap() {
			var myLatLng = {
				lat: 30.3753, // Default location (Pakistan)
				lng: 69.3451
			};
			map = new google.maps.Map(document.getElementById('map'), {
				zoom: 16,
				center: myLatLng,
			});

			// If in edit mode, display trip markings
			if ($("#edit").val() > 0) {
				initializeEditMode();
			}
		}

		function initializeEditMode() {
			// Populate existing shop location and delivery locations
			var existingShopLat = $('#origin_lat').val();
			var existingShopLng = $('#origin_lng').val();



			// Populate selected delivery locations

			handleLocationChange();
		}


		// Check if in edit mode (define your own logic here)
		function isEditMode() {
			return $('#edit_mode_flag').val() === 'true'; // Assuming you have a hidden input to indicate edit mode
		}

		function setDestination() {
			// On selecting shop location
			$('#location').change(function () {
				var selectedOption = $(this).find(':selected');
				var latLong = selectedOption.data('latlong');
				var latLngArray = latLong.split(',');
				var from_lat = parseFloat(latLngArray[0]);
				var from_lng = parseFloat(latLngArray[1]);

				// Save shop location (origin)
				$('#origin_lat').val(from_lat);
				$('#origin_lng').val(from_lng);

				// Update the map after selecting new location
				handleLocationChange();
			});

			// On selecting multiple orders (delivery locations)
			$('#order_id').change(function () {
				handleDestinationChange(); // Call the function to handle destination change
			});
		}

		function handleDestinationChange() {
			var destinations = [];

			$('#order_id').find('option:selected').each(function () {
				var delivery_location = $(this).data('delivery_location');

				if (delivery_location && delivery_location.latitude && delivery_location.longitude) {
					var lat = delivery_location.latitude;
					var lng = delivery_location.longitude;
					var order_number = $(this).val();

					destinations.push({
						lat: lat,
						lng: lng,
						order_number: order_number 
					});
				} else {
					console.warn("Delivery location data is missing latitude or longitude.");
				}
			});

			$('#destination_latlng').val(JSON.stringify(destinations));

			if (destinations.length > 0) {
				handleLocationChange();
			} else {
				console.warn("No valid destinations selected.");
			}
		}
		function displayRoute(travel_mode, origin, destinations, directionsService, directionsDisplay) {
			var waypoints = destinations.map(function (destination) {
				return {
					location: new google.maps.LatLng(destination.lat, destination.lng),
					stopover: true
				};
			});
			// Use the optimizeWaypoints flag to calculate the shortest route for multiple destinations
			directionsService.route({
				origin: new google.maps.LatLng(origin.lat, origin.lng),
				destination: waypoints[waypoints.length - 1].location, // Last destination
				waypoints: waypoints.slice(0, -1), // All other points as waypoints
				optimizeWaypoints: true, // Find the optimal order of delivery locations
				travelMode: travel_mode,
				avoidTolls: true
			}, function (response, status) {
				if (status === 'OK') {
					directionsDisplay.setMap(map); // Display the route on the map
					directionsDisplay.setDirections(response); // Show the directions

					// Suppress default A, B, C markers
					directionsDisplay.setOptions({
						suppressMarkers: true
					});

					// Create a custom marker for the shop (origin)
					var origin = response.request.origin; // Extract the origin (shop location)
					var shopMarker = new google.maps.Marker({
						position: origin.location || origin, // Use origin's location (it may be LatLng or string)
						label: "S", // Set the label as "Shop"
						map: map
					});

					// Get the optimized order of waypoints from Google Maps
					var route = response.routes[0];
					var orderNumbers = []; // Array to hold order numbers

					// Display the ordered list of destinations and distances
					for (var i = 0; i < route.legs.length; i++) {
						var routeSegment = i + 1;

						// Find the order number associated with this destination
						var destination = destinations[i];
						var orderNumber = destination.order_number.toString();

						// Add marker with correct order number
						var marker = new google.maps.Marker({
							position: route.legs[i].end_location,
							label: orderNumber, // Use order number as marker label
							map: map
						});

						// Store the order number in the array
						orderNumbers.push(orderNumber);
					}
					document.getElementById('order_sequence').value = orderNumbers.join(',');

				} else {
					directionsDisplay.setMap(null);
					directionsDisplay.setDirections(null);
					alert('Could not display directions due to: ' + status);
				}
			});
		}

		// Handle location changes (both shop and delivery locations)
		function handleLocationChange() {
			var from_lat = $('#origin_lat').val(); // Shop's latitude
			var from_lng = $('#origin_lng').val(); // Shop's longitude
			var destinationLatLngStr = $('#destination_latlng').val(); // Array of delivery locations
			var destinations = [];
			try {
				destinations = JSON.parse(destinationLatLngStr); // Parse the delivery locations
			} catch (e) {
				console.error('Error parsing destination lat/lng:', e);
				$('#result').html('Error processing destination data.');
				return;
			}

			var travel_mode = "DRIVING"; // Default travel mode
			var directionsDisplay = new google.maps.DirectionsRenderer({
				'draggable': false
			});
			var directionsService = new google.maps.DirectionsService();
			var origin = {
				lat: parseFloat(from_lat),
				lng: parseFloat(from_lng)
			};
			// Display the route for all orders
			if (destinations.length > 0) {
				displayRoute(travel_mode, origin, destinations, directionsService, directionsDisplay);
				generateRouteLink(origin, destinations);
			}
		}

		function generateRouteLink(origin, destinations) {
			// Prepare the origin
			var originStr = origin.lat + "," + origin.lng;

			let baseUrl = 'https://www.google.com/maps/dir/?api=1';
			let routeLink = `${baseUrl}&origin=${encodeURIComponent(originStr)}`;

			if (destinations.length > 0) {
				// Set the final destination
				let finalDestination = `${destinations[destinations.length - 1].lat},${destinations[destinations.length - 1].lng}`;
				routeLink += `&destination=${encodeURIComponent(finalDestination)}`;

				// Add waypoints
				if (destinations.length > 1) {
					let waypoints = destinations.slice(0, -1).map(d => `${d.lat},${d.lng}`).join('|');
					routeLink += `&waypoints=${encodeURIComponent(waypoints)}`;
				}
				$('#route_link_display').attr('href', routeLink);
				$('#route_link_input').val(routeLink);

				// $('#route_link').attr('href', routeLink);
				// $('#route_link').text('Get Directions'); // Update link text
			}
		}
	</script>



</body>

</html>