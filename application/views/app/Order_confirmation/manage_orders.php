<!DOCTYPE html>
<html lang="en">
<?php
$this->load->view('app/include/head');
$this->load->view('app/include/header'); ?>

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
							<a href="<?php echo SURL . "Module/app"; ?>">Home</a>
						</li>

						<li class="active">Order Confirmation </li>
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

						<!-- /.ace-settings-box -->
					</div><!-- /.ace-settings-container -->

					<div class="page-header">
						<h1>
							LPG
							<small>
								<i class="ace-icon fa fa-angle-double-right"></i>
								Order Confirmation
							</small>
						</h1>
					</div><!-- /.page-header -->
					<style>
						.scheduler-border {
							border: 1px solid #ccc;
							padding: 5px 10px;
							border-radius: 5px;
							background: #fff;
						}

						fieldset.scheduler-border {
							padding-bottom: 20px;
						}

						legend {
							width: 100%;
							margin-bottom: 20px;
							font-size: 21px;
							line-height: inherit;
							border-bottom: 1px solid #e5e5e5;
						}

						label {
							font-weight: bold;
						}
					</style>
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
							} ?>
							<form id="formID" class="form-horizontal" role="form" method="post" action="<?php echo SURL; ?>app/Order_confirmation/submit" enctype="multipart/form-data">

								<div class="col-md-12 form-group">
									<fieldset class="scheduler-border">
										<legend class="scheduler-border">Order Detail</legend>
										<div class="form-group">
											<label class="col-sm-2 control-label no-padding-right" for="form-field-1">Sale Point</label>
											<div class="col-sm-4">
												<select class="chosen-select form-control" name="salepoint" onchange="fetchData()" id="salepoint">
													<?php foreach ($salepoint as $key => $value) { ?>
														<option value="<?php echo $value['sale_point_id']; ?>" <?php if ($sale_point_id == $value['sale_point_id']) {
															   echo "selected";
														   } ?>><?php echo $value['sp_name']; ?></option>
													<?php } ?>
												</select>
											</div>
											<label class="col-sm-2 control-label no-padding-right" for="form-field-1">Pending Order </label>
											<div class="col-sm-4">
												<select required="required" class=" form-control" name="order" id="order" data-placeholder="Choose a order..." autofocus onchange="get_customer()">
												</select>
											</div>
											<input type="hidden" value="<?php echo $order_id ?>" id="order_id">
										</div>
									</fieldset>
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
													<textarea name="delivery_address" id="delivery_address" class="form-control" maxlength="250" style="resize: vertical;"></textarea>
													<textarea name="previous_delivery_address" id="previous_delivery_address" class="hidden" maxlength="250" style="resize: vertical;"></textarea>
												</div>
											</div>
											<div class="col-xs-12 form-group">
												<?php
												// Fetch zone details
												$zoneDetails = $this->db->query("SELECT area as area_id, area_name, zone_id FROM tbl_zone_detail")->result_array();
												$polygons = array();
												foreach ($zoneDetails as $values) {
													$areaStrings = explode('|', $values['area_id']);

													foreach ($areaStrings as $areaString) {
														$coordinates = explode(',', $areaString);
														$polygon = [];

														for ($i = 0; $i < count($coordinates); $i += 2) {
															$polygon[] = [
																'latitude' => floatval($coordinates[$i]),
																'longitude' => floatval($coordinates[$i + 1])
															];
														}
														$polygons[] = $polygon;
													}
												}
												// Convert $polygons array to JSON format
												$polygonsJson = json_encode($polygons);

												?>
												<label class="col-sm-4 control-label no-padding-right" for="form-field-1">Delivery Location</label>
												<div class="col-sm-6">
													<!-- Small map display -->
													<div id="smallMap" style="height: 200px; width: 100%;"></div>
													<input type="hidden" name="delivery_location" id="delivery_location">
													<input type="hidden" name="preivous_delivery_location" id="preivous_delivery_location">

													<!-- Button to open modal for full map -->
													<button type="button" id="modal_fullmap" class="btn btn-primary" data-toggle="modal" data-target="#mapModal">
														Edit Location
													</button>
												</div>
											</div>
											<style>
												.pac-container {
													background-color: #FFF;
													z-index: 20;
													position: fixed;
													display: inline-block;
													float: left;
												}

												.modal {
													z-index: 20;
												}

												.modal-backdrop {
													z-index: 10;
												}
											</style>
											<!-- Modal for full map view -->
											<div class="modal fade" id="mapModal" tabindex="-1" role="dialog" aria-labelledby="mapModalLabel" aria-hidden="true">
												<div class="modal-dialog modal-lg" role="document">
													<div class="modal-content">
														<div class="modal-header">
															<h5 class="modal-title" id="mapModalLabel">Update Delivery Location</h5>
															<button type="button" class="close" data-dismiss="modal" aria-label="Close">
																<span aria-hidden="true">&times;</span>
															</button>
														</div>
														<div class="modal-body">
															<div class="modal-body">
																<input id="pac-input" class="controls" type="text" placeholder="Search Box" />
																<div id="fullMap" style="width:100%; height:500px; border:11px ;"></div>
															</div>

														</div>
														<div class="modal-footer">
															<button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
															<button type="button" class="btn btn-primary" id="saveLocation" data-dismiss="modal">Save Location</button>
														</div>
													</div>
												</div>
											</div>
											<!-- Google Maps API -->
											<script async src="https://maps.googleapis.com/maps/api/js?key=AIzaSyCJPePs39ubzYGmfpcKbPV6k404GvXcL7s&libraries=places,geometry" defer></script>


											<script>
												var markers = [];
												var currentLocation = {
													lat: 33.57489122401441,
													lng: 73.15177079290152
												}; // Default location

												let smallMap, fullMap, marker, fullMarker;
												var areaCoordinates = <?php echo $polygonsJson; ?>;
												// console.log(areaCoordinates); // Check the structure

												// Track whether the selected location is valid
												var isLocationValid = false;

												// Initialize the small map
												function initSmallMap() {
													smallMap = new google.maps.Map(document.getElementById('smallMap'), {
														zoom: 15,
														center: currentLocation
													});

													marker = new google.maps.Marker({
														position: currentLocation,
														map: smallMap,
														title: "Delivery Location"
													});
												}

												// Initialize the full map
												function initFullMap() {
													fullMap = new google.maps.Map(document.getElementById('fullMap'), {
														zoom: 15,
														center: currentLocation
													});

													// Initialize the marker at the current location
													fullMarker = new google.maps.Marker({
														position: currentLocation,
														map: fullMap,
														draggable: true,
														title: "Update Location"
													});

													// Update the current location when the marker is dragged
													fullMarker.addListener('dragend', function (event) {
														validateAndSetLocation(event.latLng.lat(), event.latLng.lng());
													});

													// Setup the search box
													const input = document.getElementById('pac-input');
													const searchBox = new google.maps.places.SearchBox(input);
													fullMap.controls[google.maps.ControlPosition.TOP_LEFT].push(input);

													// Listen for search box places_changed event
													searchBox.addListener('places_changed', function () {
														var places = searchBox.getPlaces();
														if (places.length === 0) return;

														// Clear out the old markers
														markers.forEach(function (marker) {
															marker.setMap(null);
														});
														markers = [];

														var bounds = new google.maps.LatLngBounds();
														places.forEach(function (place) {
															if (!place.geometry) return;

															// Create a new marker for the searched place
															var newMarker = new google.maps.Marker({
																map: fullMap,
																icon: 'https://khaadim.com/img/newmarpmarker.png',
																title: place.name,
																position: place.geometry.location
															});
															markers.push(newMarker);

															// Validate the search result location
															validateAndSetLocation(place.geometry.location.lat(), place.geometry.location.lng());

															if (place.geometry.viewport) {
																bounds.union(place.geometry.viewport);
															} else {
																bounds.extend(place.geometry.location);
															}
														});

														fullMap.fitBounds(bounds);
													});
												}


												// Convert the coordinates to a format usable by Google Maps
												function parseCoordinates(areaCoordinates) {
													return areaCoordinates.map(area => area.map(point => ({
														lat: point.latitude,
														lng: point.longitude
													})));
												}

												// Initialize polygons for small map and full map
												function drawPolygonsOnMap(map, polygons) {
													var polygonShapes = [];
													polygons.forEach(function (coordinates) {
														const polygon = new google.maps.Polygon({
															paths: coordinates,
															strokeColor: "#000000",
															strokeOpacity: 0.8,
															strokeWeight: 2,
															fillOpacity: 0,
															map: map
														});

														polygonShapes.push(polygon); // Store polygons to check location inside
													});
													return polygonShapes;
												}

												function validateAndSetLocation(lat, lng) {
													var selectedLocation = new google.maps.LatLng(lat, lng);

													// Parse the coordinates for all polygons
													var parsedPolygons = parseCoordinates(areaCoordinates);
													isLocationValid = false; // Reset validity

													// Iterate over all polygons to check if the selected location is inside any of them
													parsedPolygons.forEach(function (coordinates) {
														var polygon = new google.maps.Polygon({
															paths: coordinates
														});

														if (google.maps.geometry.poly.containsLocation(selectedLocation, polygon)) {
															isLocationValid = true; // Location is inside a valid polygon
														}
													});

													if (isLocationValid) {
														// If the location is valid, save the location and update the marker
														updateMapLocation(lat, lng);
														var locationData = {
															latitude: lat,
															longitude: lng,
															latitudeDelta: 0.009,
															longitudeDelta: 0.009
														};
														document.getElementById('delivery_location').value = JSON.stringify(locationData);
														// alert("Location updated successfully.");
													} else {
														// If the location is outside the selected polygons, show an alert
														alert("Selected location is outside the allowed delivery areas. Please choose a location within the marked areas.");
													}
												}

												// Call this function after maps are initialized to draw polygons
												function markAreasOnMap() {
													var parsedPolygons = parseCoordinates(areaCoordinates);
													// Draw polygons on both maps
													smallMapPolygons = drawPolygonsOnMap(smallMap, parsedPolygons);
													fullMapPolygons = drawPolygonsOnMap(fullMap, parsedPolygons);
												}

												// Load small map on page load
												window.onload = function () {
													initFullMap();
													initSmallMap();
													markAreasOnMap();
												};

												// Load full map when modal is shown
												$('#mapModal').on('shown.bs.modal', function () {
													setTimeout(function () {
														initFullMap();
														google.maps.event.trigger(fullMap, 'resize');
														fullMap.setCenter(currentLocation); // Ensure correct centering
													}, 300);
												});

												// Save the updated location when "Save Location" is clicked
												document.getElementById('saveLocation').addEventListener('click', function () {
													if (isLocationValid) {
														var locationData = {
															latitude: currentLocation.lat,
															longitude: currentLocation.lng,
															latitudeDelta: 0.009,
															longitudeDelta: 0.009
														};
														document.getElementById('delivery_location').value = JSON.stringify(locationData);
													} else {
														alert("Please select a location within the valid delivery areas.");
													}
												});

												// Function to update the small map and marker with the new location
												function updateMapLocation(latitude, longitude) {
													currentLocation = {
														lat: latitude,
														lng: longitude
													};
													marker.setPosition(currentLocation);
													smallMap.setCenter(currentLocation);
													fullMarker.setPosition(currentLocation);
													fullMap.setCenter(currentLocation);
												}
											</script>


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
								<div class="row col-md-12">
									<div class="col-xs-12 col-sm-12 pricing-span-body" style="margin-left: 1%; display: flex;">
										<div class="pricing-span6">
											<div class="widget-box pricing-box-small widget-color-blue2">
												<div class="widget-header">
													<h6 class="widget-title smaller lighter">Item <?php if ($arabic_check == 'Yes') { ?> (بند) <?php } ?></h6>
												</div>

												<div class="widget-body">
													<select class="chosen-select form-control" id="materialcode" onchange="get_item_detail()" data-placeholder="Choose a Item...">
														<option value="">Select Item</option>
														<?php
														$item_list = $this->db->query("SELECT * FROM `tblmaterial_coding` where status='Active'")->result_array();
														foreach ($item_list as $key => $value) { ?>
															<option value="<?php echo $value['materialcode']; ?>"><?php echo ucwords($value['itemname']); ?> </option>
														<?php } ?>
													</select>
												</div>
											</div>
										</div>

										<div class="pricing-span5">
											<div class="widget-box pricing-box-small widget-color-blue2">
												<div class="widget-header">
													<h6 class="widget-title smaller lighter">Category</h6>
												</div>

												<div class="widget-body">
													<input type="text" class="form-control" id="category" tabindex="-1" readonly>
												</div>
											</div>
										</div>
										<div class="pricing-span5">
											<div class="widget-box pricing-box-small widget-color-blue2">
												<div class="widget-header">
													<h6 class="widget-title smaller lighter">Item Type</h6>
												</div>

												<div class="widget-body">
													<select class="form-control" id="item_type" onchange="get_item_detail()" data-placeholder="Choose a Item...">
													</select>
												</div>
											</div>
										</div>
										<div class="pricing-span4 brands" id="brandContainer" style="display: none;">
											<div class="widget-box pricing-box-small widget-color-blue2">
												<div class="widget-header">
													<h6 class="widget-title smaller lighter">Cylinder Brand</h6>
												</div>
												<div class="widget-body">
													<select class="form-control chosen-select" id="cylinder_brand" onchange="get_swap_charges()" data-placeholder="Choose a Brand...">
														<?php $brands = $this->db->query("SELECT * FROM `tbl_brand`")->result_array();
														foreach ($brands as $key => $value) { ?>
															<option value="<?php echo $value['brand_id'] ?>"><?php echo $value['brand_name'] ?></option>
														<?php } ?>
													</select>
												</div>
											</div>
										</div>

										<div class="pricing-span5" id="cylinderConditionContainer" style="display: none;">
											<div class="widget-box pricing-box-small widget-color-blue2">
												<div class="widget-header">
													<h6 class="widget-title smaller lighter">Cylinder Condition</h6>
												</div>
												<div class="widget-body">
													<select class="form-control chosen-select" id="cylinder_condition" onchange="get_item_detail()" data-placeholder="Choose Condition...">
														<option value="New/Good Condition">New/Good Condition</option>
														<option value="Average Condition">Average Condition</option>
													</select>
												</div>
											</div>
										</div>

										<div class="pricing-span5" id="swapChargesContainer" style="display: none;">
											<div class="widget-box pricing-box-small widget-color-blue2">
												<div class="widget-header">
													<h6 class="widget-title smaller lighter">Swap Credits</h6>
												</div>
												<div class="widget-body">
													<input type="text" class="form-control" onkeypress="return /[0-9-]/i.test(event.key)" onkeyup="CalAmount()" id="swap_charges">
												</div>
											</div>
										</div>
										<div class="pricing-span5" id="unitPriceContainer">
											<div class="widget-box pricing-box-small widget-color-blue2">
												<div class="widget-header">
													<h6 class="widget-title smaller lighter">Unit Price</h6>
												</div>
												<div class="widget-body">
													<input type="text" class="form-control" id="price" tabindex="-1" readonly>
												</div>
											</div>
										</div>

										<div class="pricing-span5" id="securityChargesContainer">
											<div class="widget-box pricing-box-small widget-color-blue2">
												<div class="widget-header">
													<h6 class="widget-title smaller lighter">Security Charges</h6>
												</div>
												<div class="widget-body">
													<input type="text" class="form-control" id="security_charges" tabindex="-1" readonly>
												</div>
											</div>
										</div>

										<div class="pricing-span3">
											<div class="widget-box pricing-box-small widget-color-blue2">
												<div class="widget-header">
													<b>
														<h6 class="widget-title smaller lighter" style="font-size: 10px;">Quantity <?php if ($arabic_check == 'Yes') { ?> (كمية)<?php } ?></h6>
													</b>
												</div>

												<div class="widget-body">
													<input class="form-control" type="text" id="qty" maxlength="6" onkeypress="return /[0-9 . ]/i.test(event.key)" onkeyup="CalAmount()" pattern="^[0-9]+$" title="Only Numbers Allowed...">

												</div>
											</div>
										</div>

										<div class="pricing-span3">
											<div class="widget-box pricing-box-small widget-color-grey">
												<div class="widget-header">
													<h6 class="widget-title smaller lighter">Amount <?php if ($arabic_check == 'Yes') { ?> (كمية)<?php } ?></h6>
												</div>

												<div class="widget-body">
													<input class="form-control" type="text" name="amount" id="amount" disabled="disabled" tabindex="-1">
												</div>
											</div>
										</div>
										<div class="pricing-span3">
											<div class="widget-box pricing-box-small widget-color-green">
												<div class="widget-header">
													<h6 class="widget-title smaller lighter" style="margin-left: 25%;"> Action <?php if ($arabic_check == 'Yes') { ?> (عمل) <?php } ?></h6>
												</div>
												<div class="widget-body" align="center">
													<input style=" height:34px;width: 40% !important;" id="addremove" class="btn btn-xs btn-info" type="button" onclick="temp_product();" value="Add">
												</div>
											</div>
										</div>
									</div>
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
													<th>Action</th>
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
												<tr>
													<td style="background:#848484; color:#fff">Order Status</td>
													<td>
														<select name="order_status" id="order_status" class="chosen-select form-control" onchange="toggleRiderField()">

														</select>
													</td>
												</tr>
												<!-- <tr id="rider_row" style="display:none;">
													<td style="background:#848484; color:#fff">Rider</td>
													<td>
														<select name="rider_id" id="rider_id" class="chosen-select form-control">
															<?php
															$rider_list = $this->db->query("SELECT * FROM `tbl_rider_coding`")->result_array();
															foreach ($rider_list as $key => $value) { ?>
																<option value="<?php echo $value['id']; ?>"><?php echo ucwords($value['rider_name']); ?> </option>
															<?php } ?>
														</select>
													</td>
												</tr> -->
												<tr id="reason_row" style="display:none;">
													<td style="background:#848484; color:#fff">Reject Reason</td>
													<td>
														<textarea style="width: 100%;" maxlength="250" name="reject_reason" id="reject_reason" cols="5" rows="5"></textarea>
													</td>
												</tr>
												<script>
													function toggleRiderField() {
														var orderStatus = document.getElementById('order_status').value;
														// var riderRow = document.getElementById('rider_row');
														var reasonRow = document.getElementById('reason_row');
														var rejectReason = document.getElementById('reject_reason');

														// if (orderStatus === 'Confirm') {
														// 	riderRow.style.display = 'table-row';
														// } else {
														// 	riderRow.style.display = 'none';
														// }
														if (orderStatus === 'Reject') {
															reasonRow.style.display = 'table-row';
															rejectReason.setAttribute('required', 'required'); // Make textarea required
														} else {
															reasonRow.style.display = 'none';
															rejectReason.removeAttribute('required'); // Remove required attribute
														}

														window.onload = toggleRiderField;
													}
												</script>

											</tbody>
										</table>
									</div>
								</div>
								<div class="form-action row center">
									<button type="submit" class="btn btn-sm btn-primary btnsubmit"><i class="fa fa-floppy-o" aria-hidden="true"></i> Submit</button>
								</div>
							</form>
						</div><!-- /.col -->
					</div><!-- /.row -->
				</div><!-- /.page-content -->
			</div>
		</div><!-- /.main-content -->

	</div><!-- /.main-container -->

	<?php
	$this->load->view('app/include/footer');
	$this->load->view('app/include/js');
	?>

	<script src="https://cdnjs.cloudflare.com/ajax/libs/chosen/1.8.7/chosen.jquery.min.js"></script>

	<script type="text/javascript">
		$(document).on('click', '.btnsubmit', function (e) {
			e.preventDefault();

			var grand_total = $("#grand_total").val();
			if (grand_total <= 0) {
				alert("Grand Total Can't be Less then or Equal to 0");
				return false;
			}
			var delivery_location = $("#delivery_location").val();
			var preivous_delivery_location = $("#preivous_delivery_location").val();

			var delivery_address = $("#delivery_address").val();
			var previous_delivery_address = $("#previous_delivery_address").val();
			if (delivery_location !== preivous_delivery_location && previous_delivery_address == delivery_address) {
				alert("Provide the new Selected delivery address!");
				$("#delivery_address").focus();
				return false;
			}
			$("#formID").submit();
		});
		jQuery(function ($) {
			$('#salepoint').trigger("chosen:updated");
			var $mySelect = $('#salepoint');
			$mySelect.chosen();
			$mySelect.trigger('chosen:activate');
		});

		fetchData()

		function fetchData() {
			var sale_point_id = $('#salepoint').val();
			var order_id = $('#order_id').val();

			// Fetch vehicles
			$.ajax({
				url: '<?php echo SURL; ?>app/Order_confirmation/get_orders',
				type: 'POST',
				data: {
					sale_point_id: sale_point_id,
					order_id: order_id
				},
				success: function (response) {
					$("#order").html(response);
					jQuery(function ($) {
						$('#order').trigger("chosen:updated");
						var $mySelect = $('#order');
						$mySelect.chosen();
						$mySelect.trigger('chosen:activate');
					});
					get_customer()
				},
				error: function (xhr, status, error) {
					console.error('AJAX Error while fetching vehicles:', status, error);
				}
			});
		}

		function get_customer() {
			var order = $('#order').val();
			$.ajax({
				url: '<?php echo SURL; ?>app/Order_confirmation/get_customer',
				type: 'POST',
				data: {
					order: order
				},
				success: function (response) {
					var res = response.split('|');

					// Update customer info fields
					$("#name").val(res[0]);
					$("#phone").val(res[1]);
					$("#city").val(res[2]);
					$("#area").val(res[3]);
					$("#delivery_type").val(res[6]);
					$("#delivery_charges").val(res[7]);
					$("#delivery_address").html(res[4]);
					$("#previous_delivery_address").html(res[4]);
					$("#target").attr('src', "<?php echo IMG . 'profile/' ?>" + res[5]);
					$("#order_status_actual").val(res[8]);

					// Update order status options based on actual order status
					var statusOptions = '';
					switch (res[8]) {
						case 'Booked':
							statusOptions = '<option value="Confirm">Confirm</option><option value="Reject">Reject</option>';
							break;
						case 'Confirm':
							statusOptions = '<option value="Dispatch">Dispatch</option><option value="Reject">Reject</option>';
							break;
						case 'Dispatch':
							statusOptions = '<option value="Delivered">Delivered</option><option value="Reject">Reject</option>';
							break;
						case 'Reject':
							statusOptions = '<option value="Reject">Reject</option><option value="Confirm">Confirm</option>';
							break;
						case 'Delivered':
							statusOptions = '<option value="Delivered">Delivered</option>';
							break;
						case 'Hold':
							statusOptions = '<option value="Confirm">Confirm</option><option value="Reject">Reject</option><option value="Hold">Hold</option>';
							break;
					}
					$("#order_status").html(statusOptions);
					if (statusOptions === 'Delivered') {
						$("#delivery_address").prop("readonly", true);
						$("#modal_fullmap").hide();
					} else {
						$("#delivery_address").prop("readonly", false); // Optional: reset to editable if status changes
						$("#modal_fullmap").show(); // Optional: show modal if status is not Dispatch/Delivered
					}
					toggleRiderField();

					// Disable "Add/Remove" button if the order is already dispatched or delivered
					$("#addremove").prop("disabled", res[8] === 'Delivered');
					if (res[8] === 'Delivered') {
						$("#modal_fullmap").hide();
					}

					// Handle delivery location map update
					if (res[9]) {
						try {
							var deliveryLocation = JSON.parse(res[9]);
							var latitude = deliveryLocation.latitude;
							var longitude = deliveryLocation.longitude;

							$("#delivery_location").val(res[9]);
							$("#preivous_delivery_location").val(res[9]);
							updateMapLocation(latitude, longitude);

						} catch (e) {
							console.error("Error parsing delivery location:", e);
						}
					}
					get_order_detail();
				},
				error: function (xhr, status, error) {
					console.error('AJAX Error while fetching customer details:', status, error);
				}
			});
		}

		function temp_product() {
			var materialcode = $('#materialcode').val();
			var qty = $('#qty').val();
			var item_type = $('#item_type').val();
			var order_id = $('#order').val();
			var swap_charges = $('#swap_charges').val();
			var cylinder_brand = $('#cylinder_brand').val();
			var cylinder_condition = $('#cylinder_condition').val();
			var sale_point_id = $('#salepoint').val();

			if (order_id == '' || order_id <= 0) {
				alert("Please Select Order !");
				return false;
			}
			if (materialcode == '' || materialcode <= 0) {
				alert("Please Select Item !");
				return false;
			}
			if (item_type == '' || item_type <= 0) {
				alert("Please Select Ttem Type !");
				return false;
			}
			if (qty == '' || qty <= 0) {
				alert("Please Enter Quantity !");
				return false;
			}
			if (item_type == 'Swap' && (swap_charges >= 0 || swap_charges == '-' || swap_charges > -1)) {
				alert("Please Enter Correct Swap Credits !");
				return false;
			}
			$.ajax({
				url: '<?php echo SURL; ?>app/Order_confirmation/temp_product',
				type: 'POST',
				data: {
					materialcode: materialcode,
					order: order_id,
					qty: qty,
					item_type: item_type,
					swap_charges: swap_charges,
					cylinder_condition: cylinder_condition,
					cylinder_brand: cylinder_brand,
					sale_point_id: sale_point_id
				},
				success: function (response) {
					if (response == 'success') {
						$('#materialcode').val('')
						$("#category").val('');
						$("#price").val('');
						$("#qty").val(0);
						$("#security_charges").val('');
						$('#item_type').html(''); // Assuming your select element ID
						get_order_detail()
					} else {
						alert(response);
					}
					$('#materialcode').focus()

				},
				error: function (xhr, status, error) {
					console.error('AJAX Error while fetching item details:', status, error);
				}
			});
		}

		function del_row(id) {
			$.ajax({
				url: '<?php echo SURL; ?>app/Order_confirmation/del_row',
				type: 'POST',
				data: {
					id: id,
				},
				success: function (response) {
					$('#row_' + id).remove();
					update_totals();

				},
				error: function (xhr, status, error) {
					console.error('AJAX Error while fetching item details:', status, error);
				}
			});
		}

		function get_item_detail() {
			var materialcode = $('#materialcode').val();
			var order = $('#order').val();
			var sale_point_id = $('#salepoint').val();
			var item_type = $('#item_type').val();
			var cylinder_condition = $('#cylinder_condition').val();

			if (order == '' || order <= 0) {
				alert("Please Select Order !");
				return false;
			}

			$.ajax({
				url: '<?php echo SURL; ?>app/Order_confirmation/get_item_detail',
				type: 'POST',
				data: {
					materialcode: materialcode,
					order: order,
					sale_point_id: sale_point_id,
					item_type: item_type,
					cylinder_condition: cylinder_condition
				},
				success: function (response) {
					var res = response.split('|');
					$("#category").val(res[0]);
					$("#price").val(res[1]);
					$("#security_charges").val(res[2]);
					$('#item_type').html(res[3]);

					// Show/Hide fields based on item type
					if (item_type === 'Swap') {
						$('#unitPriceContainer').hide();
						$('#securityChargesContainer').hide();
						$('#swapChargesContainer').show();
						$('#brandContainer').show();
						$('#cylinderConditionContainer').show();
					} else {
						$('#unitPriceContainer').show();
						$('#securityChargesContainer').show();
						$('#swapChargesContainer').hide();
						$('#brandContainer').hide();
						$('#cylinderConditionContainer').hide();
					}
					get_swap_charges()
				},
				error: function (xhr, status, error) {
					console.error('AJAX Error while fetching item details:', status, error);
				}
			});
		}


		function get_swap_charges() {
			var brand_id = $('#cylinder_brand').val();
			var materialcode = $('#materialcode').val();
			var order = $('#order').val();
			var sale_point_id = $('#salepoint').val();
			var cylinder_condition = $('#cylinder_condition').val();

			if (order == '' || order <= 0) {
				alert("Please Select Order !");
				return false;
			}

			$.ajax({
				url: '<?php echo SURL; ?>app/Order_confirmation/get_swap_charges',
				type: 'POST',
				data: {
					materialcode: materialcode,
					order: order,
					sale_point_id: sale_point_id,
					cylinder_condition: cylinder_condition,
					brand_id: brand_id
				},
				success: function (response) {
					$("#swap_charges").val(response); // Assuming this is part of your response for swap charges

					CalAmount();
				},
				error: function (xhr, status, error) {
					console.error('AJAX Error while fetching item details:', status, error);
				}
			});
		}

		function get_order_detail() {
			var order = $('#order').val();
			var sale_point_id = $('#salepoint').val();
			var delivery_charges = $('#delivery_charges').val();

			$.ajax({
				url: '<?php echo SURL; ?>app/Order_confirmation/get_order_detail',
				type: 'POST',
				data: {
					order: order,
					sale_point_id: sale_point_id,
					delivery_charges: delivery_charges
				},
				dataType: 'json',
				success: function (response) {
					$("#order_items").html(response.rows);

					// Update totals
					$('#total_qty').val(response.total_qty);
					$('#lpg_amount').val(response.lpg_amount);
					$('#ttl_gst').val(response.ttl_gst);
					$('#gst_perc').val(response.gst_perc);
					$('#ttl_accessories').val(response.ttl_accessories);
					$('#ttl_security_charges').val(response.ttl_security_charges);
					$('#ttl_swap_charges').val(response.ttl_swap_charges);
					$('#ttl_delivery_charge').val(response.ttl_delivery_charge);
					$('#grand_total').val(response.grand_total);
					$('#reject_reason').val(response.reject_reason);
					// Decrease quantity
					$('.spinbox-down').click(function (e) {
						e.preventDefault();
						var input = $(this).closest('.input-group').find('.quantity-input');
						var value = parseInt(input.val());
						var min = parseInt(input.attr('min')) || 1;

						if (value > min) {
							input.val(value - 1).change();
						}
					});

					// Increase quantity
					$('.spinbox-up').click(function (e) {
						e.preventDefault();
						var input = $(this).closest('.input-group').find('.quantity-input');
						var value = parseInt(input.val());
						var max = parseInt(input.attr('max')) || 100;

						if (value < max) {
							input.val(value + 1).change();
						}
					});

					$('.quantity-input').on('change', function () {
						var $input = $(this);
						var quantity = parseInt($input.val());
						var originalQuantity = parseInt($input.data('original'));
						var $row = $input.closest('tr');

						var gst = parseFloat($row.find('.gst').text().replace(/,/g, '')) || 0;
						var price = parseFloat($row.find('.saleprice').text().replace(/,/g, '')) || 0;
						var security_charges = parseFloat($row.find('.securitycharges').text().replace(/,/g, '')) || 0;
						var swap_charges = parseFloat($row.find('.swapcharges').text().replace(/,/g, '')) || 0;
						var type = $row.find('td').eq(2).text().trim();

						if (isNaN(quantity)) console.error("Missing quantity value.");
						if (isNaN(originalQuantity)) console.error("Missing original quantity value.");
						if (isNaN(gst)) console.error("Missing GST value.");
						if (isNaN(price)) console.error("Missing price value.");
						if (isNaN(security_charges)) console.error("Missing security charges value.");
						if (isNaN(swap_charges)) console.error("Missing swap charges value.");
						if (!type) console.error("Missing type value.");

						var totalAmount = 0;
						if (type === 'Swap') {
							totalAmount = swap_charges * quantity;
						} else {
							totalAmount = (price + gst) * quantity;
						}

						if (type === 'New') {
							totalAmount += security_charges * quantity;
						}

						$row.find('.amount').text(totalAmount.toLocaleString());

						update_totals();

						if (quantity !== originalQuantity) {
							$input.css('background-color', '#f0ad4e');
						} else {
							$input.css('background-color', '');
						}
					});

					// Reset quantity to original value
					$('.btn-reset').click(function (e) {
						e.preventDefault();
						var $input = $(this).closest('td').find('.quantity-input');
						var originalQuantity = $input.data('original');
						$input.val(originalQuantity).change(); // Reset the value and trigger change event
					});
				},
				error: function (xhr, status, error) {
					console.error('AJAX Error while fetching order details:', status, error);
				}
			});
		}

		function update_totals() {
			var total_qty = 0;
			var lpg_amount = 0;
			var ttl_gst = 0;
			var ttl_accessories = 0;
			var ttl_security_charges = 0;
			var ttl_swap_charges = 0;
			var ttl_delivery_charge = 0;

			$('#order_items tr').each(function () {
				var $row = $(this);
				var quantity = parseInt($row.find('.quantity-input').val());
				var saleprice = parseFloat($row.find('.saleprice').text().replace(/,/g, '')) || 0;
				// var gst = parseFloat($row.find('.gst').val());
				var security_charges = parseFloat($row.find('.securitycharges').text().replace(/,/g, '')) || 0;
				var swap_charges = parseFloat($row.find('.swapcharges').text().replace(/,/g, '')) || 0;
				var type = $row.find('td').eq(2).text().trim();

				var gst = parseFloat($row.find('.gst').text().replace(/,/g, '')) || 0;

				total_qty += quantity;
				if (type != 'Swap') {
					ttl_gst += gst * quantity;
				}
				if (type === 'Swap') {
					ttl_swap_charges += swap_charges * quantity;
				} else if (type === 'New') {
					lpg_amount += saleprice * quantity;
					ttl_security_charges += security_charges * quantity;
					ttl_delivery_charge += quantity * parseFloat($('#delivery_charges').val() || 0);
				} else if (type === 'Refill') {
					lpg_amount += saleprice * quantity;
					ttl_delivery_charge += quantity * parseFloat($('#delivery_charges').val() || 0);
				} else if (type === 'Accessories') {
					ttl_accessories += saleprice * quantity;
				}

			});
			$('#total_qty').val(total_qty);
			$('#lpg_amount').val(lpg_amount);
			$('#ttl_gst').val(ttl_gst.toFixed(0));
			$('#ttl_accessories').val(ttl_accessories);
			$('#ttl_security_charges').val(ttl_security_charges);
			$('#ttl_swap_charges').val(ttl_swap_charges);
			$('#ttl_delivery_charge').val(ttl_delivery_charge);
			$('#grand_total').val((lpg_amount + ttl_accessories + ttl_security_charges + ttl_delivery_charge + ttl_gst + ttl_swap_charges).toFixed(0));
		}



		function CalAmount() {
			var qty = $("#qty").val();
			var price = $("#price").val();
			var security_charges = $("#security_charges").val();
			var item_type = $("#item_type").val();
			if (security_charges > 0) {
				var amount = (qty * price) + (qty * security_charges);
			} else {
				var amount = (qty * price);
			}
			if (item_type == 'Swap') {
				var amount = (qty * $("#swap_charges").val());
			}
			$("#amount").val(amount);
		}
	</script>
</body>

</html>