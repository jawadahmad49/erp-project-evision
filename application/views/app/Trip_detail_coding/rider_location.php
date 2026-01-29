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

						<!-- <li>
							<a href="<?php echo SURL . "app/Trip_detail_coding"; ?>">Trip Detail Coding List <?php if ($arabic_check == 'Yes') { ?>(قائمة العملاء)<?php } ?> </a>
						</li> -->
						<li class="active"><?php echo ucwords($filter); ?> Trip Detail Coding<?php if ($arabic_check == 'Yes') { ?>(أضف الزبون)<?php } ?> </li>
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
								<?php echo ucwords($filter); ?> Trip Detail Coding <?php if ($arabic_check == 'Yes') { ?>(أضف الزبون)<?php } ?>
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

					<form class="form-horizontal" role="form" id="formID" method="post" action="<?php echo SURL . "app/Trip_detail_coding/" . $filter ?>" enctype="multipart/form-data">


						<div class="col-xs-12 col-sm-12">
							<!-- PAGE CONTENT BEGINS-->

							<div class="row">
								<div class="col-xs-12 col-sm-12">
									<fieldset class="scheduler-border">
										<legend class="scheduler-border">Trip Detail Coding</legend>
										<div class="widget-main">
											<div class="form-group">
												<label class="col-sm-3 control-label no-padding-right" for="form-field-1"> Sale Point </label>
												<div class="col-sm-2">
													<select class="form-control" name="location" id="location" <?php if ($record && $record['status'] != 'Pending') { ?> disabled <?php } ?> required>
														<?php foreach ($salepoint as $key => $value) { ?>
															<option value="<?php echo $value['sale_point_id']; ?>" data-latlong="<?= $value['shop_location']; ?>" <?php if ($record['sale_point_id'] == $value['sale_point_id']) {
																	 echo 'selected';
																 } ?>><?php echo $value['sp_name']; ?></option>
														<?php } ?>
													</select>
												</div>
												<label class="col-sm-2 control-label no-padding-right" for="form-field-1"> Trip </label>
												<div class="col-sm-2">
													<select class="form-control" onchange="trip_detail()" name="trip" id="trip" <?php if ($record && $record['status'] != 'Pending') { ?> disabled <?php } ?> required>
													</select>
												</div>
											</div>
											<div class="col-md-12 form-group">
												<fieldset class="scheduler-border">
													<legend class="scheduler-border">Customer / Delivery Info</legend>
													<div class="form-group">
														<label class="col-sm-3 control-label no-padding-right" for="form-field-1">Vehicle</label>
														<div class="col-sm-3">
															<input type="text" class="form-control" tabindex="-1" readonly id="vehicle" value="">
															<input type="hidden" id="vehicle_id" name="vehicle_id">

														</div>
														<label class="col-sm-2 control-label no-padding-right" for="form-field-1">Rider</label>
														<div class="col-sm-3">
															<input type="text" class="form-control" tabindex="-1" readonly id="rider" value="">
															<input type="hidden" id="rider_id" name="rider_id">
														</div>
													</div>
													<div class="form-group">
														<label class="col-sm-3 control-label no-padding-right" for="form-field-1">Pickup Location</label>
														<div class="col-sm-3">
															<input type="text" class="form-control" tabindex="-1" readonly id="pickup_location" value="">
														</div>
														<label class="col-sm-2 control-label no-padding-right" for="form-field-1">Trip Status</label>
														<div class="col-sm-3">
															<input type="text" class="form-control" tabindex="-1" readonly id="trip_status" value="">
														</div>
													</div>
													<div class="form-group">
														<label class="col-sm-4 control-label no-padding-right" for="form-field-1">Orders</label>
														<div class="col-sm-5">
															<textarea class="form-control" readonly name="orders" id="orders"></textarea>
														</div>
													</div>
												</fieldset>
											</div>
											<div class="col-md-12 form-group">
												<div class="table-header">
													Order Detail
												</div>

												<div>
													<div>
														<table id="dynamic-table" class="table table-striped table-bordered table-hover">
															<thead>
																<tr>
																	<th>Order #</th>
																	<th>Customer Name</th>
																	<th>Profile Pic</th>
																	<th>Phone #</th>
																	<th>City</th>
																	<th>Area</th>
																	<th>Order Status</th>
																	<th>Delivery Type</th>
																	<th>Delivery Address</th>
																	<th>Action</th>
																</tr>
															</thead>
															<tbody id="order_items">
																<!-- Order #1 -->

															</tbody>
														</table>
													</div>
													<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
													<script>
														function initializeOrderAccordion() {
															$('.order-row').off('click').on('click', function () {
																var target = $($(this).data('target'));
																$('.collapse').not(target).slideUp();
																target.slideToggle();
															});
														}
														$(document).ready(function () {
															initializeOrderAccordion();
														});
													</script>
												</div>
											</div>

											<div class="form-group">
												<div class="col-sm-12">
													<div id="map" style="width:100%;height:400px;"></div>
												</div>
												<div class="col-sm-12">
													<a href="<?php echo $record['route_link'] ?>" id="route_link_display" class="btn btn-sm btn-danger" target="_blank">View Route On Google Map</a>
													<input type="hidden" name="route_link" id="route_link_input" value="<?php echo $record['route_link'] ?>">
												</div>
											</div>
											<div class="form-group">
												<table style="width:35%; float: right;" id="simple-table" class="table  table-bordered table-hover fc_currency">
													<tbody>
														<tr>
															<td style="background:#848484; color:#fff">Trip Status</td>
															<td>
																<style>
																	.chosen-container {
																		width: 100% !important;
																	}
																</style>
																<select name="trip_status" id="status" class="form-control" width="100%">
																</select>
															</td>
														</tr>
														<tr>
															<td style="background:#848484; color:#fff">Total Receivable</td>
															<td><input class="form-control" type="text" readonly tabindex="-1" id="total_receivable" name="total_receivable" value=""></td>
														</tr>
														<tr>
															<td style="background:#848484; color:#fff">Total Received</td>
															<td><input class="form-control" type="text" onkeypress="return /[0-9 . ]/i.test(event.key)" id="total_received" name="total_received" value=""></td>
														</tr>
													</tbody>
												</table>
											</div>
											<div class="row">
												<div class="center">
													<button type="submit" class="btn btn-info btnsubmit">
														<i class="ace-icon fa fa-check bigger-110"></i>
														Submit
													</button>
												</div>

												<input type="hidden" id='origin_lat' value="" />
												<input type="hidden" id='origin_lng' value="" />
												<input type="hidden" id='destination_latlng' value="" />

												<input type="hidden" id="order_sequence" name="order_sequence" placeholder="Order sequence will appear here" readonly>
												<input type="hidden" name="in_mile" value="" />
												<input type="hidden" name="in_kilo" value="" />
												<input type="hidden" name="duration_text" value="" />
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

		jQuery(function ($) {
			$('#location').trigger("chosen:updated");
			var $mySelect = $('#location');
			$mySelect.chosen();
			$mySelect.trigger('chosen:activate');
		});
		jQuery(function ($) {
			$('#trip').trigger("chosen:updated");
			var $mySelect = $('#trip');
			$mySelect.chosen();
			//	$mySelect.trigger('chosen:activate');
		});
		jQuery(function ($) {
			$('#status').trigger("chosen:updated");
			var $mySelect = $('#status');
			$mySelect.chosen();
			//	$mySelect.trigger('chosen:activate');
		});
	</script>
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
		$(document).ready(function () {
			// Call fetchData initially if needed
			fetchData();

			// Attach change event to the select element
			$('#location').on('change', function () {
				fetchData();
			});

			function fetchData() {
				var sale_point_id = $('#location').val();
				$.ajax({
					url: '<?php echo SURL; ?>app/Trip_detail_coding/get_trips',
					type: 'POST',
					data: {
						sale_point_id: sale_point_id,
					},
					success: function (response) {
						$("#trip").html(response);
						trip_detail(); // Call trip_detail after updating trips
						$("#trip").addClass("chosen-select").trigger("chosen:updated");

						// Initialize Chosen if it's not already initialized
						if (!$("#trip").data('chosen')) {
							$("#trip").chosen();
						}
					},
					error: function (xhr, status, error) {
						console.error('AJAX Error while fetching trips:', status, error);
					}
				});
			}
		});

		$(document).on('click', '.btnsubmit', function (e) {
			e.preventDefault();

			var trip = $("#trip").val();
			if (trip == "") {
				alert("Please Choose Trip");
				$('#trip').trigger("chosen:activate");
				return false;
			}

			var vehicle_id = $("#vehicle_id").val();
			if (vehicle_id == "") {
				alert("no vehicle id");
				return false;
			}

			var rider_id = $("#rider_id").val();
			if (rider_id == "") {
				alert("no rider id");
				return false;
			}

			var total_receivable = $('#total_receivable').val();
			if (total_receivable === "" || total_receivable === "0") {
				alert("Please insert total receivable");
				$('#total_receivable').focus();
				return false;
			}

			var status = $('#status').val();
			var total_received = $('#total_received').val();
			if (status !== "Started") {
				if (total_received === "" || total_received === "0") {
					alert("Please insert total received");
					$('#total_received').focus();
					return false;
				} else if (parseFloat(total_received) > parseFloat(total_receivable)) {
					alert("Total received cannot be greater than total receivable");
					$('#total_received').focus();
					return false;
				}
			}

			$(this).prop('disabled', true);
			$("#formID").submit();
		});


		var test = jQuery.noConflict();
		jQuery(function ($) {
			$('.date-picker').datepicker({
				autoclose: true,
				todayHighlight: true
			})
		});


		function calculateTotalReceivable() {
			let sum = 0;

			$('input[id="grand_total"][data-status="Delivered"]').each(function () {
				const value = parseFloat($(this).val().replace(/,/g, '')) || 0;
				sum += value;
			});

			$('#total_receivable').val(sum.toFixed(2));
		}

		function trip_detail() {
			var trip_id = $('#trip').val();

			if (trip_id) {
				$.ajax({
					url: '<?php echo SURL; ?>app/Trip_detail_coding/get_trip_detail',
					type: 'POST',
					data: {
						trip_id: trip_id
					},
					success: function (response) {
						$("#order_items").html(response.rows || '');
						$('#vehicle').val(response.vehicle_name || '');
						$('#rider').val(response.rider_name || '');
						$('#vehicle_id').val(response.vehicle_id || '');
						$('#rider_id').val(response.rider_id || '');
						$('#orders').val(response.order_names || '');
						$('#route_link_input').val(response.route_link || '');
						$('#route_link_display').attr('href', response.route_link || '#');
						$('#trip_status').val(response.status || '');
						$('#pickup_location').val(response.pickup_location || '');
						$('#total_received').val(response.total_received || '');
						$('#status').html(response.status_options || '');
						$('#status').chosen("destroy");
						$('#status').chosen();
						calculateTotalReceivable();
						initializeOrderAccordion();

						var destinations = [];
						setTimeout(function () {
							$('input[name="delivery_location"]').each(function () {
								var deliveryLocationData = $(this).data('delivery_location');
								if (deliveryLocationData) {
									destinations.push({
										lat: deliveryLocationData.latitude,
										lng: deliveryLocationData.longitude,
										order_number: $(this).data('order-id')
									});
								}
							});
							$('#destination_latlng').val(JSON.stringify(destinations));
							// console.log($('#destination_latlng').val());
						}, 100);
						setDestination();
						handleLocationChange();
					},
					error: function (xhr, status, error) {
						console.error('AJAX Error while fetching trip details:', status, error);
						resetTripFields();
					}
				});
			} else {
				alert("No Orders in this sale Point");
				resetTripFields();
			}
		}

		function resetTripFields() {
			$("#order_items").html('');
			$('#vehicle').val('');
			$('#rider').val('');
			$('#vehicle_id').val('');
			$('#rider_id').val('');
			$('#orders').val('');
			$('#route_link_input').val('');
			$('#route_link_display').attr('href', '#');
			$('#trip_status').val('');
			$('#pickup_location').val('');
			$('#status').val('');
			$('#total_received').val('');
		}
	</script>
	<script src="https://maps.googleapis.com/maps/api/js?key=AIzaSyCJPePs39ubzYGmfpcKbPV6k404GvXcL7s&libraries=places"></script>
	<script>
		var map;

		google.maps.event.addDomListener(window, 'load', function () {
			setDestination();
			initMap();
			var selectedOption = $('#location').find(':selected');
			var latLong = selectedOption.data('latlong');
			if (latLong) {
				var latLngArray = latLong.split(',');
				var from_lat = parseFloat(latLngArray[0]);
				var from_lng = parseFloat(latLngArray[1]);

				$('#origin_lat').val(from_lat);
				$('#origin_lng').val(from_lng);
			}
		});
		setDestination();
		function initMap() {
			var myLatLng = {
				lat: 30.3753,
				lng: 69.3451
			};
			map = new google.maps.Map(document.getElementById('map'), {
				zoom: 16,
				center: myLatLng,
			});

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
			var selectedOption = $('#location').find(':selected');
			var latLong = selectedOption.data('latlong');

			if (latLong) {
				var latLngArray = latLong.split(',');
				var from_lat = parseFloat(latLngArray[0]);
				var from_lng = parseFloat(latLngArray[1]);

				$('#origin_lat').val(from_lat);
				$('#origin_lng').val(from_lng);
				// console.log('Selected LatLong:', latLong);
				handleLocationChange();
			} else {
				console.error('No latlong data found for selected option.');
			}
			handleDestinationChange();
		}


		function handleDestinationChange() {
			var destinations = [];

			$('input[name="delivery_location"]').each(function () {
				var deliveryLocationData = $(this).data('delivery_location');
				var lat = deliveryLocationData.latitude;
				var lng = deliveryLocationData.longitude;
				var orderNumber = $(this).data('order-id');
				destinations.push({
					lat: lat,
					lng: lng,
					order_number: orderNumber
				});
			});
			$('#destination_latlng').val(JSON.stringify(destinations));
			handleLocationChange(); // Update the route display
		}

		// Function to display optimized route for all orders
		function displayRoute(travel_mode, origin, destinations, directionsService, directionsDisplay) {
			var waypoints = destinations.map(function (destination) {
				return {
					location: new google.maps.LatLng(destination.lat, destination.lng),
					stopover: true
				};
			});
			// console.log(origin)
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
			if (destinations.length > 0) {
				// Display the route for all orders
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