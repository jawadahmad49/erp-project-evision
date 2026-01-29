<!DOCTYPE html>

<html lang="en">

<?php

$this->load->view('app/include/head');

$this->load->view('app/include/header');



?>

<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.4.1/jquery.min.js"></script>

<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@yaireo/tagify/dist/tagify.css">



<body class="no-skin">



	<div class="main-container ace-save-state" id="main-container">



		<?php $this->load->view('app/include/sidebar'); ?>



		<div class="main-content">

			<div class="main-content-inner">

				<div class="breadcrumbs ace-save-state" id="breadcrumbs" style="background-color: #5baa4f; color: white; font-weight: bold;">

					<ul class="breadcrumb">

						<li>

							<i class="ace-icon fa fa-home home-icon"></i>

							<a href="<?php echo SURL . "admin"; ?>" style="color: white;">Home</a>

						</li>



						<li>

							<a href="<?php echo SURL . "app/Zone_config"; ?>" style="color: white;">Zone List <?php if ($arabic_check == 'Yes') { ?> (قائمة البند) <?php } ?></a>

						</li>

						<li class="active" style="color: white;">Add Zone <?php if ($arabic_check == 'Yes') { ?>(اضافة عنصر)<?php } ?></li>

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



						<div class="ace-settings-box clearfix" id="ace-settings-box">

							<div class="pull-left width-50">

								<div class="ace-settings-item">

									<div class="pull-left">

										<select id="skin-colorpicker" class="hide">

											<option data-skin="no-skin" value="#438EB9">#438EB9</option>

											<option data-skin="skin-1" value="#222A2D">#222A2D</option>

											<option data-skin="skin-2" value="#C6487E">#C6487E</option>

											<option data-skin="skin-3" value="#D0D0D0">#D0D0D0</option>

										</select>

									</div>

									<span>&nbsp; Choose Skin</span>

								</div>



								<div class="ace-settings-item">

									<input type="checkbox" class="ace ace-checkbox-2 ace-save-state" id="ace-settings-navbar" autocomplete="off" />

									<label class="lbl" for="ace-settings-navbar"> Fixed Navbar</label>

								</div>



								<div class="ace-settings-item">

									<input type="checkbox" class="ace ace-checkbox-2 ace-save-state" id="ace-settings-sidebar" autocomplete="off" />

									<label class="lbl" for="ace-settings-sidebar"> Fixed Sidebar</label>

								</div>



								<div class="ace-settings-item">

									<input type="checkbox" class="ace ace-checkbox-2 ace-save-state" id="ace-settings-breadcrumbs" autocomplete="off" />

									<label class="lbl" for="ace-settings-breadcrumbs"> Fixed Breadcrumbs</label>

								</div>



								<div class="ace-settings-item">

									<input type="checkbox" class="ace ace-checkbox-2" id="ace-settings-rtl" autocomplete="off" />

									<label class="lbl" for="ace-settings-rtl"> Right To Left (rtl)</label>

								</div>



								<div class="ace-settings-item">

									<input type="checkbox" class="ace ace-checkbox-2 ace-save-state" id="ace-settings-add-container" autocomplete="off" />

									<label class="lbl" for="ace-settings-add-container">

										Inside

										<b>.container</b>

									</label>

								</div>

							</div><!-- /.pull-left -->



							<div class="pull-left width-50">

								<div class="ace-settings-item">

									<input type="checkbox" class="ace ace-checkbox-2" id="ace-settings-hover" autocomplete="off" />

									<label class="lbl" for="ace-settings-hover"> Submenu on Hover</label>

								</div>



								<div class="ace-settings-item">

									<input type="checkbox" class="ace ace-checkbox-2" id="ace-settings-compact" autocomplete="off" />

									<label class="lbl" for="ace-settings-compact"> Compact Sidebar</label>

								</div>



								<div class="ace-settings-item">

									<input type="checkbox" class="ace ace-checkbox-2" id="ace-settings-highlight" autocomplete="off" />

									<label class="lbl" for="ace-settings-highlight"> Alt. Active Zone</label>

								</div>

							</div><!-- /.pull-left -->

						</div><!-- /.ace-settings-box -->

					</div><!-- /.ace-settings-container -->



					<div class="page-header">

						<h1>

							LPG <?php if ($arabic_check == 'Yes') { ?>(نقاط البيع

							)<?php } ?>

							<small>

								<i class="ace-icon fa fa-angle-double-right"></i>

								Add Zone <?php if ($arabic_check == 'Yes') { ?>(اضافة عنصر)<?php } ?>

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

										Oh snap!

									</strong>



									<?php echo $this->session->flashdata('err_message'); ?>

									<br>

								</div>



							<?php

							} ?>



						</div>



						<form id="formID" class="form-horizontal" role="form" method="post" action="<?php echo SURL . "app/Zone_config/add" ?>" enctype="multipart/form-data">





							<div class="row">





								<div class="col-sm-12">

									<div class="form-group">

										<label class="col-sm-5 control-label no-padding-right" for="form-field-1">Zone Name</label>

										<div class="col-sm-3">

											<input type="text" class="form-control" required id='zone_name' name="zone_name" value="<?php echo $record['zone_name'] ?>" maxlength="75" autofocus>

										</div>

									</div>



									<div class="form-group">

										<label class="col-sm-5 control-label no-padding-right" for="form-field-1">Select City</label>

										<div class="col-sm-3">

											<select class="form-control chosen-select" name="city_id" id="city_id">

												<?php foreach ($city_list as $key => $data) { ?>

													<option value="<?php echo $data['city_id']; ?>" <?php if ($record['city_id'] == $data['city_id']) {

																										 echo 'selected';
																									} ?>><?php echo $data['city_name']; ?></option>

												<?php } ?>

											</select>

										</div>

									</div>

									<script src="https://maps.googleapis.com/maps/api/js?key=AIzaSyCJPePs39ubzYGmfpcKbPV6k404GvXcL7s&libraries=drawing,geometry,places"></script>



									<style>
										#map {

											height: 500px;

											width: 100%;
											

										}



										.controls {

											margin-top: 10px;

										}



										#search-box {

											margin: 10px;

											width: 300px;

										}



										.tagify__input {

											white-space: normal !important;

										}



										.helper-message {

											font-size: 0.9em;

											color: gray;

											margin-top: 5px;

										}
									</style>



									<div class="form-group">
										<input id="pac-input" class="controls" type="text" placeholder="Search Box" style="height: 4rem; width: 40rem;" />
										<div id="map"></div>
									</div>

									<div class="form-group">
										<label class="col-sm-5 control-label no-padding-right" for="form-field-1">Area Name</label>
										<div class="col-sm-3">
											<input name='area_name' id="area_name" value="" class="form-control">
											<input type="hidden" id="area_coordinates" name="area_coordinates">
										</div>
									</div>
									<div class="form-group center">
										<button type="button" id="add-button" class="btn btn-primary">Add Area</button>
									</div>

									<div class="form-group col-md-12">
										<div style="text-align: -webkit-center;">
											<div>
												<table id="dynamic-table" class="table table-striped table-bordered table-hover" style="width: 50%;">
													<thead>
														<tr>
															<th>Sr No</th>
															<th>Area Name</th>
															<th>Action</th>
														</tr>
													</thead>
													<tbody>
														<?php $count = 0;
														foreach ($detail as $key => $value) {
															$area_id = $value['id'];
															$check = $this->db->query("SELECT * from tbl_user where area_id='$area_id'")->row_array();
															$count++; ?>
															<tr>
																<td><?php echo $count; ?></td>
																<td><?php echo htmlspecialchars($value['area_name']); ?></td>
																<td>
																	<input type="hidden" name="detail_id[]" value="<?php echo $value['id']; ?>">
																	<input type="hidden" name="area_name[]" class="area_name" value="<?php echo htmlspecialchars($value['area_name']); ?>">
																	<input type="hidden" name="area[]" class="area-coordinates" value="<?php echo htmlspecialchars($value['area']); ?>">
																	<input type="button" class="btn btn-sm btn-info" value="Edit" onclick="editArea(<?php echo $count - 1; ?>)">
																	<?php if (empty($check)) { ?>
																		<input type="button" class="btn btn-sm btn-danger" value="Delete" onclick="deleteArea(<?php echo $count - 1; ?>)">
																	<?php } ?>
																</td>
															</tr>
														<?php } ?>
													</tbody>
												</table>
											</div>
										</div>
										<div class="form-group">

											<label class="col-sm-5 control-label no-padding-right" for="form-field-1">Select Status</label>

											<div class="col-sm-3">

												<select class="form-control chosen-select" name="status" id="status">

													<option value="Active" <?php if ($record['status'] == 'Active') {

																				echo 'selected';
																			} ?>>Active</option>

													<option value="InActive" <?php if ($record['status'] == 'InActive') {

																					echo 'selected';
																				} ?>>InActive</option>

												</select>

											</div>

										</div>
										<input type="hidden" id="edit" name="id" value="<?php echo $record['id'] ?>" />
										<div class="form-group" style="margin-left: 2%;">

											<div class="form-group form-actions center">
												<button class="btn btn-info" style="margin-left: -20%;" id="save-button">

													<i class="ace-icon fa fa-check bigger-110"></i>

													Submit

												</button>

											</div>

										</div>

									</div>

								</div>

						</form>

					</div>

				</div><!-- /.row -->

			</div><!-- /.page-content -->

		</div>

	</div><!-- /.main-content -->

	</div><!-- /.main-container -->



	<?php

	$this->load->view('app/include/footer');

	?>



	<?php

	$this->load->view('app/include/js');

	?>

	<?php $this->load->view('app/include/paymentreceipt_js.php'); ?>

	<script src="http://ajax.googleapis.com/ajax/libs/jquery/1.2.6/jquery.js" type="text/javascript"></script>

	<script src="https://ajax.googleapis.com/ajax/libs/jqueryui/1.12.1/jquery-ui.min.js"></script>

	<script src="<?php echo SURL ?>assets/js/jquery.UrduEditor.js" type="text/javascript"></script>



	<script src="https://cdn.jsdelivr.net/npm/@yaireo/tagify"></script>



	<script type="text/javascript">
		var test_final = jQuery.noConflict($);



		$(document).ready(function($) {



			jQuery(".urdu_class").each(function(index) {



				test_final(this).UrduEditor();

				setEnglish($(this));

				jQuery(this).removeAttr('dir');



			});

		});



		function english_lang() {



			jQuery(".urdu_class").each(function(index) {



				jQuery(this).removeAttr('dir');

				setEnglish(jQuery(this));



			});

		}



		function urdu_lang() {

			//alert('asd');

			jQuery(".urdu_class").each(function(index) {



				jQuery(this).attr("dir", "rtl");



				setUrdu(jQuery(this));



			});



		}
	</script>

	<script>
		let map;
		let drawingManager;
		let selectedShape;
		let geocoder;
		let polygons = [];
		let editedRowId = null; // To track the row being edited

		const pakistanBounds = new google.maps.LatLngBounds(
			new google.maps.LatLng(23.6345, 60.8718),
			new google.maps.LatLng(37.0841, 77.0861)
		);

		function initMap() {
			map = new google.maps.Map(document.getElementById('map'), {
				center: {
					lat: 30.3753,
					lng: 69.3451
				},
				zoom: 4,
				restriction: {
					latLngBounds: pakistanBounds,
					strictBounds: true,
				},
			});

			geocoder = new google.maps.Geocoder();

			drawingManager = new google.maps.drawing.DrawingManager({
				drawingMode: google.maps.drawing.OverlayType.POLYGON,
				drawingControl: true,
				drawingControlOptions: {
					position: google.maps.ControlPosition.TOP_CENTER,
					drawingModes: ['polygon'], // Only allow polygon drawing
				},
				polygonOptions: {
					editable: true,
					draggable: true,
				},
			});

			drawingManager.setMap(map);

			// Listen for the completion of a polygon drawing
			google.maps.event.addListener(drawingManager, 'polygoncomplete', (polygon) => {
				clearPolygons(); // Clear existing polygons
				polygons.push(polygon);
				setSelection(polygon);
				const coordinates = getPolygonCoordinates(polygon);
				document.getElementById('area_coordinates').value = coordinates; // Set the coordinates directly
			});

			// When clicking on the map, clear selection
			google.maps.event.addListener(map, 'click', () => {
				clearSelection();
			});

			document.getElementById('add-button').addEventListener('click', addAreaToTable);

			// Add search box functionality
			const input = document.getElementById('pac-input');
			const searchBox = new google.maps.places.SearchBox(input);
			map.controls[google.maps.ControlPosition.TOP_LEFT].push(input);

			// Listen for search box places_changed event
			searchBox.addListener('places_changed', () => {
				const places = searchBox.getPlaces();
				if (places.length === 0) return;

				const place = places[0];
				if (!place.geometry || !place.geometry.location) return;

				map.setCenter(place.geometry.location);
				map.setZoom(15);
			});
		}

		function setSelection(shape) {
			clearSelection();
			selectedShape = shape;
			shape.setEditable(true);
			shape.setDraggable(true);
		}

		function clearSelection() {
			if (selectedShape) {
				selectedShape.setEditable(false);
				selectedShape = null;
			}
		}

		function getPolygonCoordinates(polygon) {
			const paths = polygon.getPath();
			let coordinates = [];
			for (let i = 0; i < paths.getLength(); i++) {
				const latLng = paths.getAt(i);
				coordinates.push(latLng.lat() + ',' + latLng.lng());
			}
			return coordinates.join(','); // Change from ';' to ',' for the desired format
		}

		function addAreaToTable() {
			const areaName = document.getElementById('area_name').value.trim();
			let coordinates = document.getElementById('area_coordinates').value.trim();

			if (editedRowId !== null && selectedShape) {
				coordinates = getPolygonCoordinates(selectedShape); // Get updated coordinates if editing
			}

			// Check for duplicate area name
			const tableBody = document.querySelector('#dynamic-table tbody');
			const existingAreaNames = Array.from(tableBody.rows).map(row => row.cells[1].innerText.trim());

			// Allow duplicate if updating the same area
			if (existingAreaNames.includes(areaName) && editedRowId === null) {
				alert('Area name already exists. Please enter a unique area name.');
				return; // Exit the function if a duplicate is found
			}

			if (areaName && coordinates) {
				if (editedRowId !== null) {
					const existingRow = tableBody.rows[editedRowId];
					const currentAreaName = existingRow.cells[1].innerText.trim();

					// Allow the same name if it's the same area being edited
					if (areaName !== currentAreaName && existingAreaNames.includes(areaName)) {
						alert('Area name already exists. Please enter a unique area name.');
						return; // Exit the function if a duplicate is found
					}

					existingRow.cells[1].innerText = areaName; // Update area name
					existingRow.querySelector('.area-coordinates').value = coordinates; // Update coordinates
					existingRow.querySelector('.area_name').value = areaName; // Update coordinates

					// drawPolygon(coordinates); // Redraw the updated polygon
					editedRowId = null; // Reset editedRowId after updating
				} else {
							const rowHTML = `
					    <tr>
					        <td>${tableBody.rows.length + 1}</td>
					        <td>${areaName}</td>
					        <td>
					            <button type="button" class="btn btn-sm btn-info" onclick="editArea(${tableBody.rows.length})">Edit</button>
					            <button type="button" class="btn btn-sm btn-danger" onclick="deleteArea(${tableBody.rows.length})">Delete</button>
					            <input type="text" class="area-coordinates" value="${coordinates}" name="area[]" style="display:none;">
					            <input type="text" class="area_name" value="${areaName}" name="area_name[]" style="display:none;">
					            <input type="text" name="detail_id[]" value="0" style="display:none;">   
					        </td>
					    </tr>
					`;
			// 		const rowHTML = `
            //     <tr>
            //         <td>${tableBody.rows.length + 1}</td>
            //         <td>${areaName}</td>
            //         <td>
            //             <button type="button" class="btn btn-sm btn-info" onclick="editArea(${tableBody.rows.length})">Edit</button>
            //             <input type="text" class="area-coordinates" value="${coordinates}" name="area[]" style="display:none;">
            //             <input type="text" class="area_name" value="${areaName}" name="area_name[]" style="display:none;">
            //             <input type="text" name="detail_id[]" value="0" style="display:none;">   
            //         </td>
            //     </tr>
            // `;'
					tableBody.insertAdjacentHTML('beforeend', rowHTML);
					// drawPolygon(coordinates); // Draw the polygon for the new area
				}
				clearPolygons(); // Clear existing polygons on the map
				clearFormFields(); // Clear input fields after adding/updating
				updateSerialNumbers(); // Call to update serial numbers after adding a new row
			}
			document.getElementById('add-button').innerText = 'Add Area'; // Reset button text to Add Area
		}


		function updateSerialNumbers() {
			const tableBody = document.querySelector('#dynamic-table tbody');
			for (let i = 0; i < tableBody.rows.length; i++) {
				tableBody.rows[i].cells[0].innerText = i + 1; // Update SR# based on current index
			}
		}

		function clearFormFields() {
			document.getElementById('area_name').value = '';
			document.getElementById('area_coordinates').value = '';
			clearSelection(); // Clear any selected shapes on the map
		}

		function editArea(rowId) {
			const row = document.querySelector(`#dynamic-table tbody`).rows[rowId];
			const areaName = row.cells[1].innerText;
			const coordinates = row.querySelector('.area-coordinates').value; // Get coordinates from the row

			document.getElementById('area_name').value = areaName;
			document.getElementById('area_coordinates').value = coordinates; // Keep the original value

			clearPolygons(); // Clear existing polygons on the map
			drawPolygon(coordinates); // Draw the polygon for the selected area

			editedRowId = rowId; // Set the edited row ID to update later
			document.getElementById('add-button').innerText = 'Update Area'; // Change button text to Update Area
		}

		function deleteArea(rowId) {
			const tableBody = document.querySelector('#dynamic-table tbody');
			tableBody.deleteRow(rowId);
			updateSerialNumbers(); // Update SR# after deleting a row
			clearPolygons(); // Clear any polygons drawn on the map
		}

		function drawPolygon(latLngPairs) {
			// Clear existing polygons before drawing new one
			clearPolygons();

			// Convert the coordinates to LatLng objects
			const latLngs = coordinatesToLatLngs(latLngPairs);
			const polygon = new google.maps.Polygon({
				paths: latLngs,
				map: map,
				editable: true,
				draggable: true,
				fillColor: '#FF0000',
				fillOpacity: 0.35,
				strokeWeight: 2,
				strokeColor: '#FF0000',
			});

			polygons.push(polygon);
			setSelection(polygon);
		}

		function coordinatesToLatLngs(coordinates) {
			// Split the string into an array of coordinate pairs
			const coordinatesArray = coordinates.split(',');
			const latLngs = [];

			for (let i = 0; i < coordinatesArray.length; i += 2) {
				const lat = parseFloat(coordinatesArray[i]);
				const lng = parseFloat(coordinatesArray[i + 1]);
				if (!isNaN(lat) && !isNaN(lng)) {
					latLngs.push(new google.maps.LatLng(lat, lng));
				}
			}
			return latLngs;
		}


		function clearPolygons() {
			polygons.forEach(polygon => {
				polygon.setMap(null);
			});
			polygons = []; // Reset polygons array
		}

		google.maps.event.addDomListener(window, 'load', initMap);
	</script>






</body>



</html>