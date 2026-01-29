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
							<a href="<?php echo SURL . "app/Sale_point_coding"; ?>" style="color: white;">Sale Point List <?php if ($arabic_check == 'Yes') { ?> (قائمة البند) <?php } ?></a>
						</li>
						<li class="active" style="color: white;">Add Sale Point <?php if ($arabic_check == 'Yes') { ?>(اضافة عنصر)<?php } ?></li>
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
									<label class="lbl" for="ace-settings-highlight"> Alt. Active Sale Point</label>
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
								Add Sale Point <?php if ($arabic_check == 'Yes') { ?>(اضافة عنصر)<?php } ?>
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

						<form id="formID" class="form-horizontal" role="form" method="post" action="<?php echo SURL . "app/Sale_point_coding/add" ?>" enctype="multipart/form-data">


							<div class="row">


								<div class="col-sm-12">
									<div class="form-group">
										<label class="col-sm-4 control-label no-padding-right" for="form-field-1">Full Name</label>
										<div class="col-sm-3">
											<input type="text" onkeypress="return (event.charCode > 64 && event.charCode < 91) || (event.charCode > 96 && event.charCode < 123) || (event.charCode==32)" class="form-control" required name="name" value="<?php echo $record['sp_name'] ?>" maxlength="50" autofocus>
										</div>
									</div>
									<div class="form-group">
										<label class="col-sm-4 control-label no-padding-right" for="form-field-1">Email</label>
										<div class="col-sm-3">
											<input type="email" required class="form-control" name="email" value="<?php echo $record['email_id'] ?>" maxlength="40">
										</div>
									</div>
									<div class="form-group">
										<label class="col-sm-4 control-label no-padding-right" for="form-field-1">Mobile Number</label>
										<div class="col-sm-3">
											<input type="text" required class="form-control" name="phone_no" value="<?php echo $record['phone_num'] ?>" maxlength="11" minlength="11" onkeypress="return /[0-9 . ]/i.test(event.key)" title="Only Numbers Allowed...">
										</div>
									</div>



									<div class="form-group">
										<label class="col-sm-4 control-label" for="form-field-1"><b>Shop Logo</b></label>

										<div class="col-xs-3">
											<input type="hidden" name="old_image" id="old_image" value="<?php echo $record['sp_logo']; ?>">
											<label class="ace-file-input"><input type="file" name="image" id="logo" accept="image/x-png,image/gif,image/jpeg" class="col-xs-12" onChange="showPreview(this);"><span class="ace-file-container col-xs-12" data-title="Choose"><span class="ace-file-name" data-title="No File ..."><i class=" ace-icon fa fa-upload"></i></span></span><a class="remove" href="#"><i class=" ace-icon fa fa-times"></i></a></label>

											<div id="targetLayer">
												<img width="268" height="210" id="target" src="<?php if (isset($record['sp_logo'])) {
													echo IMG . 'shop_logo/' . $record['sp_logo'];
												} else {
													echo IMG . 'shop_logo/default.png';
												} ?>" style="margin-left: 0%;">
											</div>

										</div>

									</div>


									<div class="form-group">
										<label class="col-sm-4 control-label no-padding-right" for="form-field-1">Select City</label>
										<div class="col-sm-3">
											<select class="form-control chosen-select" name="city_id" id="city_id">



												<?php foreach ($city_list as $key => $data) {
													$name = $data['name'];
													$latitude = $data['lat'];
													$longitude = $data['lng'];

													$selected_cities = explode('|', $city_config);

													if (!in_array($latitude . "," . $longitude, $selected_cities)) {
														continue;
													}

													?>

													<option value="<?php echo $name; ?>" <?php if ($record['city_id'] == $name) {
														   echo 'selected';
													   } ?>><?php echo $name; ?></option>

													<?php
												} ?>
											</select>
										</div>
									</div>

									<!-- <script src="https://maps.googleapis.com/maps/api/js?key=AIzaSyCcFOE6o37oTX5ptY5MupQMWhKtJ_jRlFw&libraries=drawing"></script> -->
									<script src="https://maps.googleapis.com/maps/api/js?key=AIzaSyCcFOE6o37oTX5ptY5MupQMWhKtJ_jRlFw&libraries=drawing" async defer></script>


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

										/* .tagify {
											display: contents !important;
										}

										.tagify__input {
											white-space: normal !important;
											border: 1px solid #d5d5d5;
										} */
									</style>

									<div class="form-group" style="background: #B8B8B8;">
										<label class="col-sm-12" style="text-align: center;" for="form-field-1"><strong>Select Your Shop Location</strong></label>
										<div id="mapp" style="width:100%; height:500px; border:11px;"></div>
										<input type="text" id="shop-location" name="shop_location" value="<?php echo $record['shop_location'] ?>">
									</div>

									<div class="form-group">
										<div id="map"></div>
										<input type="hidden" class="area" name="area" id="area" value="<?php echo $record['area_id'] ?>">
									</div>

									<div class="form-group center">
										<input type="button" id="delete-button" value="Delete Selected Area" class="btn btn-sm btn-danger">
									</div>

									<div class="form-group">
										<label class="col-sm-4 control-label no-padding-right" for="form-field-1">Area Name</label>
										<div class="col-sm-3">
											<input name='area_name' id="area_name" pattern='^[A-Za-z_✲ ]{1,15}$' required value="<?php echo $record['areaname'] ?>">
											<div class="helper-message">Press Enter to add Area Name.</div>
										</div>
									</div>

									<!-- <input id="search-box" type="text" placeholder="Search for a place"> -->
									<!-- <div class="controls">
										 <button id="save-button">Save Areas</button>
									 </div> -->
									<!-- <div class="form-group">
										 <label class="col-sm-4 control-label no-padding-right" for="form-field-1">Select Area</label>
										 <div class="col-sm-4">
											 <div id="area-container">
												 <?php
												 if (!empty($record)) {
													 $areaStrings = explode(' || ', $record['area_id']);
													 $areanameArray = explode('|', $record['areaname']);
													 $citynameArray = explode('|', $record['cityname']);

													 foreach ($areaStrings as $index => $areaString) {
														 $areaData = json_decode($areaString, true);
														 $latitude = $areaData['latitude'];
														 $longitude = $areaData['longitude'];
														 $areaname = isset($areanameArray[$index]) ? $areanameArray[$index] : '';
														 $cityname = isset($citynameArray[$index]) ? $citynameArray[$index] : '';
														 ?>
														 <div class="area-input-group">
															 <input type="text" class="area" name="areaname[]" value="<?php echo htmlspecialchars($areaname); ?>">
															 <input type="text" class="lat" name="lat[]" value="<?php echo htmlspecialchars($latitude); ?>" readonly tabindex="-1">
															 <input type="text" class="lng" name="lng[]" value="<?php echo htmlspecialchars($longitude); ?>" readonly tabindex="-1">
															 <input type="text" class="city" name="city[]" value="<?php echo htmlspecialchars($cityname); ?>" readonly tabindex="-1">
															 <button type="button" class="remove-area btn btn-sm btn-danger">Remove</button>
														 </div>
												 <?php
													 }
												 }
												 ?>
											 </div>
											 <button type="button" id="add-area" onclick="add_area()" class="btn btn-sm btn-primary">Add Another Area</button>
										 </div>
									 </div> -->


									<div class="form-group">
										<label class="col-sm-4 control-label no-padding-right" for="form-field-1">Login Id</label>
										<div class="col-sm-3">
											<input type="text" required class="form-control" name="loginid" value="<?php echo $record['loginid'] ?>" maxlength="25">
										</div>
									</div>
									<div class="form-group">
										<label class="col-sm-4 control-label no-padding-right" for="form-field-1">Password</label>
										<div class="col-sm-3">
											<input type="password" required class="form-control" name="password" value="<?php echo base64_decode($record['password']) ?>" maxlength="15">
										</div>
									</div>

									<input type="hidden" id="edit" name="id" value="<?php echo $record['sale_point_id'] ?>" />

									<div class="form-group" style="margin-left: 2%;">


										<div class="form-actions center">
											<button class="btn btn-info" style="margin-left: -20%;" id="save-button">
												<i class="ace-icon fa fa-check bigger-110"></i>
												Submit
											</button>

										</div>

									</div>
								</div>

							</div>


						</form>

						<!-- PAGE CONTENT ENDS -->
					</div>
					<!-- /.col -->
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
	<script>
		// Initialize Tagify on the input field
		var input = document.querySelector('input[name=area_name]');
		var tagify = new Tagify(input, {
			// maxTags: 2,
			pattern: /^[a-zA-Z\s]+$/,
		});
	</script>

	<script type="text/javascript">
		var test_final = jQuery.noConflict($);

		$(document).ready(function ($) {

			jQuery(".urdu_class").each(function (index) {

				test_final(this).UrduEditor();
				setEnglish($(this));
				jQuery(this).removeAttr('dir');

			});
		});

		function english_lang() {

			jQuery(".urdu_class").each(function (index) {

				jQuery(this).removeAttr('dir');
				setEnglish(jQuery(this));

			});
		}

		function urdu_lang() {
			//alert('asd');
			jQuery(".urdu_class").each(function (index) {

				jQuery(this).attr("dir", "rtl");

				setUrdu(jQuery(this));

			});

		}
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
	</script>
	<script>
		let shopMap;
		let shopMarker;

		function initShopMap() {
			shopMap = new google.maps.Map(document.getElementById('mapp'), {
				center: {
					lat: 30.3753,
					lng: 69.3451
				},
				zoom: 4,
				restriction: {
					latLngBounds: new google.maps.LatLngBounds(
						new google.maps.LatLng(23.6345, 60.8718),
						new google.maps.LatLng(37.0841, 77.0861)
					),
					strictBounds: true
				}
			});

			const shopLocation = document.getElementById('shop-location').value;
			if (shopLocation) {
				const [lat, lng] = shopLocation.split(',').map(Number);
				if (!isNaN(lat) && !isNaN(lng)) {
					placeMarker({
						lat,
						lng
					});
					shopMap.setCenter({
						lat,
						lng
					});
				} else {
					console.error('Invalid coordinates:', shopLocation);
				}
			}

			shopMap.addListener('click', function (event) {
				console.log('Map clicked at:', event.latLng.toJSON());
				placeMarker(event.latLng);
			});
		}

		function placeMarker(location) {
			console.log('Placing marker at:', location);
			if (shopMarker) {
				shopMarker.setPosition(location);
			} else {
				shopMarker = new google.maps.Marker({
					position: location,
					map: shopMap,
					draggable: true
				});

				google.maps.event.addListener(shopMarker, 'dragend', function (event) {
					console.log('Marker dragged to:', event.latLng.toJSON());
					document.getElementById('shop-location').value = `${event.latLng.lat()},${event.latLng.lng()}`;
				});
			}

			// Ensure the location is in the correct format
			if (location instanceof google.maps.LatLng) {
				document.getElementById('shop-location').value = `${location.lat()},${location.lng()}`;
			} else {
				console.error('Invalid location object:', location);
			}
		}



		let map;
		let drawingManager;
		let selectedShape;
		let polygons = [];

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
					strictBounds: true
				}
			});

			drawingManager = new google.maps.drawing.DrawingManager({
				drawingMode: google.maps.drawing.OverlayType.POLYGON,
				drawingControl: true,
				drawingControlOptions: {
					position: google.maps.ControlPosition.TOP_CENTER,
					drawingModes: ['polygon']
				},
				polygonOptions: {
					editable: true,
					draggable: true
				}
			});
			drawingManager.setMap(map);

			google.maps.event.addListener(drawingManager, 'overlaycomplete', function (event) {
				if (event.type === google.maps.drawing.OverlayType.POLYGON) {
					const polygon = event.overlay;
					polygon.addListener('click', () => setSelection(polygon));
					setSelection(polygon);
					polygons.push(polygon);
				}
			});

			function setSelection(shape) {
				if (selectedShape) {
					selectedShape.setEditable(false);
				}
				selectedShape = shape;
				shape.setEditable(true);
			}

			document.getElementById('save-button').addEventListener('click', saveAreas);
			document.getElementById('delete-button').addEventListener('click', deleteSelectedShape);

			loadExistingAreas();
		}

		function loadExistingAreas() {
			const areaData = document.getElementById('area').value;
			const areas = areaData.split('|');

			areas.forEach(areaString => {
				const coordinatesString = areaString;
				const latLngs = [];
				const coordinates = coordinatesString.split(',');

				for (let i = 0; i < coordinates.length; i += 2) {
					const lat = parseFloat(coordinates[i]);
					const lng = parseFloat(coordinates[i + 1]);
					latLngs.push({
						lat,
						lng
					});
				}

				const polygon = new google.maps.Polygon({
					paths: latLngs,
					strokeColor: '#FF0000',
					strokeOpacity: 0.8,
					strokeWeight: 2,
					fillColor: '#FF0000',
					fillOpacity: 0.35,
					editable: true,
					draggable: true
				});

				polygon.setMap(map);
				polygon.addListener('click', () => setSelection(polygon));
				polygons.push(polygon);
			});
		}

		function saveAreas() {
			const areaNames = tagify.value.map(tag => tag.value);

			const areaDataArray = polygons.map((polygon, index) => {
				const path = polygon.getPath().getArray();
				const coordinates = path.map(latLng => `${latLng.lat()},${latLng.lng()}`).join(',');
				return coordinates;
			});

			console.log('Raw coordinates:', areaDataArray);

			const areaData = areaDataArray.filter(Boolean).join('|'); // Filter out empty values

			document.getElementById("area").value = areaData;
			console.log('Areas to save:', areaData);
		}


		function deleteSelectedShape() {
			if (selectedShape) {
				selectedShape.setMap(null);
				polygons = polygons.filter(polygon => polygon !== selectedShape);
				selectedShape = null;
			}
		}

		window.onload = function () {
			initMap();
			initShopMap();
		};
	</script>

</body>

</html>