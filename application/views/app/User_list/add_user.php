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
							<a href="<?php echo SURL . "app/User_list"; ?>">User Coding List <?php if ($arabic_check == 'Yes') { ?>(قائمة العملاء)<?php } ?> </a>
						</li>
						<li class="active"><?php echo ucwords($filter); ?> User Coding<?php if ($arabic_check == 'Yes') { ?>(أضف الزبون)<?php } ?> </li>
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
								<?php echo ucwords($filter); ?> User Coding <?php if ($arabic_check == 'Yes') { ?>(أضف الزبون)<?php } ?>
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

					<form id="formID" class="form-horizontal" role="form" id="distance_form" method="post" action="<?php echo SURL . "app/User_list/" . $filter ?>" enctype="multipart/form-data">
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
												<label class="col-sm-4 control-label no-padding-right" for="form-field-1"> Name </label>
												<div class="col-sm-3">
													<input type="text" class="form-control" name="name" id="name" required onkeypress="return (event.charCode > 64 && event.charCode < 91) || (event.charCode > 96 && event.charCode < 123) || (event.charCode==32)" value="<?php echo $record['name'] ?>">
												</div>
											</div>
											<div class="form-group">
												<label class="col-sm-4 control-label no-padding-right" for="form-field-1"> Phone </label>
												<div class="col-sm-3">
													<input type="text" required id="phone" class="form-control" name="phone" value="<?php echo $record['phone'] ?>" maxlength="13" pattern="(\+923[0-9]{9}|03[0-9]{9})" title="Please enter a valid phone number in the format 03123456789 or +92312456789" onkeypress="return /[0-9+]/.test(event.key)" placeholder="03123456789 or +92312456789">
												</div>
											</div>
											<div class="form-group">
												<label class="col-sm-4 control-label no-padding-right" for="form-field-1"> Optional Phone </label>
												<div class="col-sm-3">
													<input type="texwt" id="optional_number" class="form-control" name="optional_number" value="<?php echo $record['optional_number'] ?>" maxlength="13" pattern="(\+923[0-9]{9}|03[0-9]{9})" title="Please enter a valid phone number in the format 03123456789 or +92312456789" onkeypress="return /[0-9+]/.test(event.key)" placeholder="03123456789 or +92312456789">
												</div>
											</div>
											<div class="form-group">
												<label class="col-sm-4 control-label no-padding-right" for="form-field-1"> Email </label>
												<div class="col-sm-3">
													<input type="email" id="email" class="form-control" name="email" value="<?php echo $record['email'] ?>" maxlength="50" placeholder="admin@gmail.com">
												</div>
											</div>

											<div class="form-group">
												<label class="col-sm-4 control-label no-padding-right" for="form-field-1"> Password </label>
												<div class="col-sm-3" style="display: flex;">
													<input maxlength="50" value="<?php echo base64_decode($record['admin_pwd']) ?>" type="password" id="admin_pwd" name="admin_pwd" placeholder="Password " style="width: 100%;" required="required" title="This field is required">
													<button type="button" id="togglePassword" style="cursor: pointer;">👁️</button>
												</div>
											</div>
											<div class="form-group">
												<label class="col-sm-4 control-label no-padding-right" for="form-field-1">City</label>
												<div class="col-sm-3">
													<select class="form-control chosen-select" name="city" id="city" required>
														<?php foreach ($city_list as $key => $data) { ?>
															<option value="<?php echo $data['city_id']; ?>" <?php if ($record['city'] == $data['city_id']) {
																   echo 'selected';
															   } ?>><?php echo $data['city_name']; ?></option>
														<?php } ?>
													</select>
												</div>
											</div>
											<div class="form-group">
												<label class="col-sm-4 control-label no-padding-right" for="form-field-1">Area</label>
												<div class="col-sm-3">
													<select class="form-control" name="area_id" id="area_id" required>
														<?php $_SESSION["area_id"] = $record['area_id']; ?>
													</select>
												</div>
											</div>
											<div class="form-group">
												<label class="col-sm-4 control-label no-padding-right" for="form-field-1"> Type </label>

												<div class="col-sm-3">
													<select class="form-control" name="tex_type" id="tex_type" required>
														<option value="filer" <?php if ($record['tex_type'] == 'filer') { ?>selected<?php } ?>>Filer</option>
														<option value="non_filer" <?php if ($record['tex_type'] == 'non_filer') { ?>selected<?php } ?>>Non Filer</option>
													</select>
												</div>
											</div>
											<div class="form-group ntn">
												<label class="col-sm-4 control-label no-padding-right" for="form-field-1"> NTN </label>

												<div class="col-sm-3">
													<input class="col-xs-12 col-sm-12" maxlength="50" value="<?php echo $record['ntn'] ?>" type="text" id="ntn" name="ntn" placeholder="NTN" onkeypress="return /[0-9 . ]/i.test(event.key)" title="Only Letters Allowed" />
												</div>
											</div>
											<div class="form-group ntn">
												<label class="col-sm-4 control-label no-padding-right" for="form-field-1"> NIC </label>

												<div class="col-sm-3">
													<input class="col-xs-12 col-sm-12" maxlength="50" value="<?php echo $record['nic'] ?>" type="text" id="nic" name="nic" placeholder="NIC" onkeypress="return /[0-9 . ]/i.test(event.key)" title="Only Letters Allowed" />
												</div>
											</div>
											<div class="form-group">
												<label class="col-sm-4 control-label no-padding-right" for="form-field-1">Registration Date</label>
												<div class="col-sm-3">
													<div class="input-group">
														<input name="joining_date" class="form-control date-picker" id="id-date-picker-1" type="text" data-date-format="yyyy-mm-dd" required value="<?php if (!empty($record['joining_date'])) {
															echo $record['joining_date'];
														} else {
															echo date("Y-m-d");
														} ?>">
														<span class="input-group-addon">
															<i class="fa fa-calendar bigger-110"></i>
														</span>
													</div>
												</div>
											</div>
											<div class="form-group">
												<label class="col-sm-4 control-label no-padding-right" for="form-field-1"> Address </label>
												<div class="col-sm-3">
													<textarea style="width: 100%;" name="address" id="address" maxlength="250"><?php echo $record['address']; ?></textarea>
												</div>
											</div>
											<div class="form-group">
												<label class="col-sm-4 control-label" for="form-field-1">Profile Pic</label>
												<div class="col-xs-3">
													<input type="hidden" name="old_image" id="old_image" value="<?php echo $record['dp']; ?>">
													<label class="ace-file-input">
														<input type="file" name="image" id="logo" accept="image/x-png,image/gif,image/jpeg" class="col-xs-12" onChange="showPreview(this);">
														<span class="ace-file-container col-xs-12" data-title="Choose">
															<span class="ace-file-name" data-title="No File ..."><i class="ace-icon fa fa-upload"></i></span>
														</span>
														<a class="remove" href="#"><i class=" ace-icon fa fa-times"></i></a>
													</label>
													<div id="targetLayer">
														<img width="268" height="210" id="target" src="<?php if (isset($record['dp'])) {
															echo IMG . 'user/' . $record['dp'];
														} else {
															echo IMG . 'user/default.JPG';
														} ?>" style="margin-left: 0%;">
													</div>
												</div>
											</div>
											<div class="form-group" style="background: #B8B8B8;">
												<label class="col-sm-12" style="text-align: center;" for="form-field-1"><strong>Select User Location</strong></label>
												<input id="search-box" type="text" placeholder="Search for a location" style="width: 100%; padding: 10px; margin-bottom: 10px;">
												<div id="map" style="width:100%; height:500px; border:1px solid #ccc;"></div>
												<input type="hidden" id="user-location" name="user_location" value='<?php echo $record['location']; ?>'>
											</div>

											<div class="form-group">
												<label class="col-sm-4 control-label no-padding-right" for="form-field-1">Status</label>
												<div class="col-sm-3">
													<select class="form-control chosen-select" name="status" id="status" required>
														<option value="Active" <?php if ($record['status'] == "Active") { ?>selected<?php } ?>>Active</option>
														<option value="InActive" <?php if ($record['status'] == "InActive") { ?>selected<?php } ?>>InActive</option>
													</select>
												</div>
											</div>
											<div class="row">
												<div class="center">
													<button class="btn btn-info btnsubmit">
														<i class="ace-icon fa fa-check bigger-110"></i>
														Submit <?php if ($arabic_check == 'Yes') { ?> (إرسال) <?php } ?>
													</button>
												</div>
												<input type="hidden" name="edit" id='edit' value="<?php echo $record['id']; ?>" />
											</div>
										</div>
									</div>
								</div>
							</div>
						</div>
					</form>
				</div>
			</div>
		</div>
	</div>
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
	<script>
		// Load polygons from PHP
		const serviceAreas = <?php echo $polygonsJson; ?>;

		let map;
		let marker;
		let polygons = []; // Declare polygons array in the global scope
		const defaultCenter = {
			lat: 30.3753,
			lng: 69.3451
		};
		let isLocationValid = false; // To track if the location is valid

		function initMap() {
			map = new google.maps.Map(document.getElementById('map'), {
				center: defaultCenter,
				zoom: 4,
				restriction: {
					latLngBounds: new google.maps.LatLngBounds(
						new google.maps.LatLng(23.6345, 60.8718),
						new google.maps.LatLng(37.0841, 77.0861)
					),
					strictBounds: true
				}
			});

			// Convert PHP polygons to Google Maps polygons and store them in the global `polygons` array
			polygons = serviceAreas.map(area => new google.maps.Polygon({
				paths: area.map(coord => ({
					lat: coord.latitude,
					lng: coord.longitude
				})),
				map: map,
				strokeColor: '#FF0000',
				strokeOpacity: 0.8,
				strokeWeight: 2,
				fillColor: '#000000',
				fillOpacity: 0
			}));

			const searchBox = new google.maps.places.SearchBox(document.getElementById('search-box'));
			map.controls[google.maps.ControlPosition.TOP_LEFT].push(document.getElementById('search-box'));

			map.addListener('bounds_changed', () => {
				searchBox.setBounds(map.getBounds());
			});

			searchBox.addListener('places_changed', () => {
				const places = searchBox.getPlaces();
				if (places.length === 0) return;

				const place = places[0];
				if (place.geometry && isLocationInsideServiceAreas(place.geometry.location)) {
					map.setCenter(place.geometry.location);
					placeMarker(place.geometry.location);
					map.setZoom(15);
				} else {
					alert("Selected location is outside the service areas.");
				}
			});

			map.addListener('click', (event) => {
				if (isLocationInsideServiceAreas(event.latLng)) {
					placeMarker(event.latLng);
				} else {
					alert("You can only select a location inside the service areas.");
				}
			});

			// Retrieve the stored location
			const shopLocation = document.getElementById('user-location').value;
			if (shopLocation) {
				try {
					const locationData = JSON.parse(shopLocation);
					const lat = parseFloat(locationData.latitude);
					const lng = parseFloat(locationData.longitude);
					if (!isNaN(lat) && !isNaN(lng)) {
						const position = {
							lat,
							lng
						};
						map.setCenter(position);
						placeMarker(position);
					}
				} catch (e) {
					console.error('Error parsing location data:', e);
				}
			}
		}

		function placeMarker(location) {
			if (marker) {
				marker.setPosition(location);
			} else {
				marker = new google.maps.Marker({
					position: location,
					map: map,
					draggable: true
				});

				// Prevent marker dragging outside the allowed areas
				marker.addListener('dragend', (event) => {
					if (isLocationInsideServiceAreas(event.latLng)) {
						updateLocation(event.latLng);
					} else {
						alert("You cannot drag the marker outside the service areas.");
						marker.setPosition(location); // Reset marker to last valid location
					}
				});
			}
			updateLocation(location);
		}

		function updateLocation(latLng) {
			const latitudeDelta = 0.009; // Adjust as needed
			const longitudeDelta = 0.009; // Adjust as needed

			const locationData = {
				latitude: latLng.lat(),
				longitude: latLng.lng(),
				latitudeDelta: latitudeDelta,
				longitudeDelta: longitudeDelta
			};
			document.getElementById('user-location').value = JSON.stringify(locationData);

			// Check if the location is valid inside service areas
			isLocationValid = isLocationInsideServiceAreas(latLng);
		}

		// Function to check if a location is inside any of the service areas
		function isLocationInsideServiceAreas(latLng) {
			for (let i = 0; i < polygons.length; i++) {
				if (google.maps.geometry.poly.containsLocation(latLng, polygons[i])) {
					return true; // Location is inside one of the polygons
				}
			}
			return false; // Location is outside all polygons
		}

		// On form submit, check if the location is valid
		function validateForm() {
			if (!isLocationValid) {
				alert("Selected location is outside the service areas. Please choose a valid location.");
				return false; // Prevent form submission
			}
			return true; // Allow form submission if the location is valid
		}

		window.onload = initMap;
	</script>



	<!-- Remember to include the required libraries -->


	<script src="https://maps.googleapis.com/maps/api/js?key=AIzaSyCJPePs39ubzYGmfpcKbPV6k404GvXcL7s&libraries=geometry,places" async defer></script>
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
	<script defer src="https://maps.googleapis.com/maps/api/js?libraries=places&language=<?= $_SESSION['lang'] ?>&key=AIzaSyCJPePs39ubzYGmfpcKbPV6k404GvXcL7s" type="text/javascript"></script>
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
			$('#city').trigger("chosen:updated");
			var $mySelect = $('#city');
			$mySelect.chosen();
			// $mySelect.trigger('chosen:activate');
		});
		jQuery(function ($) {
			$('#status').trigger("chosen:updated");
			var $mySelect = $('#status');
			$mySelect.chosen();
			// $mySelect.trigger('chosen:activate');
		});
		jQuery(function ($) {
			$('#tex_type').trigger("chosen:updated");
			var $mySelect = $('#tex_type');
			$mySelect.chosen();
			// $mySelect.trigger('chosen:activate');
		});
		$(document).ready(function () {
			function handleNtnVisibility() {
				var selectedValue = $('#tex_type').val();
				if (selectedValue === 'filer') {
					$('.ntn').show();
				} else {
					$('.ntn').hide();
				}
			}

			handleNtnVisibility();

			$('#tex_type').change(function () {
				handleNtnVisibility();
			});
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
	</script>
	<script type="text/javascript">
		function showPreview(objFileInput) {
			if (objFileInput.files[0]) {
				var fileReader = new FileReader();
				fileReader.onload = function (e) {
					$("#targetLayer").html('<img src="' + e.target.result + '" width="268" height="210px" class="upload-preview" style="margin-left: 0%;" />');
					//$("#targetLayer").css('opacity','0.7');
					$(".icon-choose-image").css('opacity', '0.5');
				}
				fileReader.readAsDataURL(objFileInput.files[0]);
			}
		}
		$('#name').focus();
		fetchdata();

		function fetchdata() {
			var city_id = $('#city').val();
			$.ajax({
				url: '<?php echo SURL; ?>app/User_list/get_areas',
				type: 'POST',
				data: {
					city_id: city_id
				},
				success: function (response) {
					$("#area_id").html(response);
					$("#area_id").addClass("chosen-select").trigger("chosen:updated");
					if (!$("#area_id").data('chosen')) {
						$("#area_id").chosen();
					}
				},
				error: function (xhr, status, error) {
					console.error('AJAX Error while fetching orders:', status, error);
				}
			});
		}
		$('#city').on('change', function () {
			fetchdata();
		});
		$(document).on('click', '.btnsubmit', function (e) {
			e.preventDefault();
			var tex_type = $("#tex_type").val();
			var nic = $("#nic").val();

			if (tex_type == 'filer' && nic == '') {
				alert("Please Enter NIC");
				$("#nic").focus();
				return false;
			}
			$("#formID").submit();
		});
	</script>
	<script>
		document.getElementById('togglePassword').addEventListener('click', function () {
			const passwordField = document.getElementById('admin_pwd');
			const type = passwordField.type === 'password' ? 'text' : 'password';
			passwordField.type = type;
		});
	</script>
</body>

</html>