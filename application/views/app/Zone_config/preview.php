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



						<li class="active" style="color: white;">Preview Zone Areas</li>



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



										<label class="col-sm-4 control-label no-padding-right" for="form-field-1">Zone Name</label>



										<div class="col-sm-3">



											<input type="text" class="form-control" disabled required id='zone_name' name="zone_name" value="<?php echo $record['zone_name'] ?>" maxlength="75" autofocus>



										</div>



									</div>







									<div class="form-group">



										<label class="col-sm-4 control-label no-padding-right" for="form-field-1">Select City</label>



										<div class="col-sm-3">



											<select class="form-control chosen-select" disabled name="city_id" id="city_id">



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



										<div id="map"></div>



										<input type="hidden" class="area" name="area" id="area" value="<?php echo $area ?>">



										<input id="pac-input" class="controls" type="text" placeholder="Search Box" style="height: 4rem; width: 40rem;"/>



									</div>










									<div class="form-group">



										<label class="col-sm-4 control-label no-padding-right" for="form-field-1">Area Name</label>



										<div class="col-sm-3">



											<input name='area_name' id="area_name" disabled value="<?php echo $area_name ?>">



											<div class="helper-message">Press Enter to add Area Name.</div>



										</div>



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
		// Initialize Tagify with the input field



		var input = document.querySelector('input[name=area_name]');



		var tagify = new Tagify(input, {



			// Additional Tagify settings if needed



		});







		// Function to convert Tagify tags to a format compatible with your table



		// function getTagifyValue() {



		// 	return tagify.value.map(tag => tag.value).join(',');



		// }







		let shopMarker;



		let map;



		let drawingManager;



		let selectedShape;



		let geocoder;



		let markers = [];



		let polygons = [];



		const pakistanBounds = new google.maps.LatLngBounds(



			new google.maps.LatLng(23.6345, 60.8718),



			new google.maps.LatLng(37.0841, 77.0861)



		);







		// Declare a global variable to store all area names



		let allAreaNames = [];







		function placeMarker(location) {



			if (shopMarker) {



				shopMarker.setPosition(location);



			} else {



				shopMarker = new google.maps.Marker({



					position: location,



					map: map,



					draggable: false




				});







				google.maps.event.addListener(shopMarker, 'dragend', function(event) {



					document.getElementById('shop-location').value = `${event.latLng.lat()},${event.latLng.lng()}`;



				});



			}



			document.getElementById('shop-location').value = `${location.lat()},${location.lng()}`;



		}







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







			geocoder = new google.maps.Geocoder();







			drawingManager = new google.maps.drawing.DrawingManager({



				drawingMode: google.maps.drawing.OverlayType.POLYGON,



				drawingControl: false,



				drawingControlOptions: {



					position: google.maps.ControlPosition.TOP_CENTER,



					drawingModes: ['polygon'] // Only allow polygon drawing



				},



				polygonOptions: {



					editable: false,



					draggable: false



				}



			});







			drawingManager.setMap(map);







			google.maps.event.addListener(drawingManager, 'polygoncomplete', (polygon) => {



				polygons.push(polygon);



				setSelection(polygon);



				// clearAreas();







				const area = getPolygonArea(polygon);



				const center = getPolygonCenter(polygon);



				reverseGeocode(center, area); // Get the area name



			});







			initAutocomplete();



			loadExistingAreas();



		}







		function getPolygonArea(polygon) {



			const paths = polygon.getPath();



			return google.maps.geometry.spherical.computeArea(paths);



		}







		function getPolygonCenter(polygon) {



			const paths = polygon.getPath();



			let latSum = 0,



				lngSum = 0;



			const len = paths.getLength();







			for (let i = 0; i < len; i++) {



				latSum += paths.getAt(i).lat();



				lngSum += paths.getAt(i).lng();



			}







			return {



				lat: latSum / len,



				lng: lngSum / len



			};



		}







		function reverseGeocode(location, area) {



			geocoder.geocode({



				location: location



			}, (results, status) => {



				if (status === 'OK' && results[0]) {



					const addressComponents = results[0].address_components;



					const streetComponents = addressComponents.filter(component =>



						component.types.includes('political') &&



						component.types.includes('sublocality') &&



						component.types.includes('sublocality_level_1')



					);



					const longNames = streetComponents.map(component => component.long_name);







					const concatenatedLongNames = longNames.join(', ');



					if (!allAreaNames.includes(concatenatedLongNames)) {



						allAreaNames.push(concatenatedLongNames);



					}

					//

					const longNames1 = streetComponents.map(component => component.long_name);

					const concatenatedLongNames1 = longNames1.join(', ');



					// Update the selected_area_name input field

					document.getElementById('drawed_area').value = concatenatedLongNames1;



					if (!allAreaNames.includes(concatenatedLongNames1)) {

						allAreaNames.push(concatenatedLongNames1);

					}





					// updateTagifyField(allAreaNames);



				}



			});



		}







		// function updateTagifyField(areaNames) {



		// 	tagify.removeAllTags();



		// 	areaNames.forEach(name => tagify.addTags([name]));



		// }







		function initAutocomplete() {



			const input = document.getElementById("pac-input");



			const searchBox = new google.maps.places.SearchBox(input);



			map.controls[google.maps.ControlPosition.TOP_LEFT].push(input);







			map.addListener("bounds_changed", () => {



				searchBox.setBounds(map.getBounds());



			});







			google.maps.event.addListenerOnce(map, 'idle', () => {



				searchBox.addListener("places_changed", () => {



					const places = searchBox.getPlaces();



					if (places.length === 0) return;







					markers.forEach(marker => marker.setMap(null));



					markers = [];







					const bounds = new google.maps.LatLngBounds();



					places.forEach((place) => {



						if (!place.geometry || !place.geometry.location) {



							return;



						}







						const marker = new google.maps.Marker({



							map,



							title: place.name,



							position: place.geometry.location,



						});



						markers.push(marker);







						if (place.geometry.viewport) {



							bounds.union(place.geometry.viewport);



						} else {



							bounds.extend(place.geometry.location);



						}



					});







					map.fitBounds(bounds);



					if (map.getZoom() > 15) {



						map.setZoom(15);



					}



				});



			});



		}







		function loadExistingAreas() {



			clearPolygons();



			// clearAreas();







			const areaData = document.getElementById('area').value;



			const areas = areaData.split('|');



			areas.forEach(areaString => {



				const coordinates = areaString.split(',').map(coord => parseFloat(coord));



				const latLngs = [];







				for (let i = 0; i < coordinates.length; i += 2) {



					latLngs.push({



						lat: coordinates[i],



						lng: coordinates[i + 1]



					});



				}







				const polygon = new google.maps.Polygon({



					paths: latLngs,



					strokeColor: '#FF0000',



					strokeOpacity: 0.8,



					strokeWeight: 2,



					fillColor: '#FF0000',



					fillOpacity: 0.35,



					editable: false,



					draggable: false



				});







				polygon.setMap(map);



				polygons.push(polygon);







				const area = getPolygonArea(polygon);



				const center = getPolygonCenter(polygon);



				reverseGeocode(center, area);



			});



		}







		function clearPolygons() {



			polygons.forEach(polygon => polygon.setMap(null));



			polygons = [];



		}







		function setSelection(shape) {



			clearSelection();



			selectedShape = shape;



			shape.setEditable(false);



			shape.setDraggable(false);



		}







		function clearSelection() {



			if (selectedShape) {



				selectedShape.setEditable(false);



				selectedShape = null;



			}



		}







		function saveAreas() {



			const areaInput = document.getElementById('area');



			areaInput.value = polygons.map(polygon => {



				return polygon.getPath().getArray().map(latLng => `${latLng.lat()},${latLng.lng()}`).join(',');



			}).join('|');



		}







		// function clearAreas() {



		// 	tagify.removeAllTags(); // Clear all tags in Tagify



		// }







		function deleteSelectedShape() {



			if (selectedShape) {



				selectedShape.setMap(null);



				polygons = polygons.filter(polygon => polygon !== selectedShape);



				selectedShape = null;



				// clearAreas();



			}



		}







		function makeAreaNameFieldReadonly() {



			document.getElementById('area_name').readOnly = true;



		}







		google.maps.event.addDomListener(window, 'load', () => {



			initMap();



			makeAreaNameFieldReadonly();



		});







		document.getElementById('save-button').addEventListener('click', saveAreas);



		document.getElementById('delete-button').addEventListener('click', deleteSelectedShape);
	</script>























</body>







</html>