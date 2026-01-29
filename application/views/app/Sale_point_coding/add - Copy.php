 <!DOCTYPE html>
 <html lang="en">
 <?php
	$this->load->view('en/include/head');
	$this->load->view('en/include/header');

	?>
 <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.4.1/jquery.min.js"></script>

 <body class="no-skin">

 	<div class="main-container ace-save-state" id="main-container">

 		<?php $this->load->view('en/include/sidebar'); ?>

 		<div class="main-content">
 			<div class="main-content-inner">
 				<div class="breadcrumbs ace-save-state" id="breadcrumbs" style="background-color: #5baa4f; color: white; font-weight: bold;">
 					<ul class="breadcrumb">
 						<li>
 							<i class="ace-icon fa fa-home home-icon"></i>
 							<a href="<?php echo SURL . "admin"; ?>" style="color: white;">Home</a>
 						</li>

 						<li>
 							<a href="<?php echo SURL . "Sale_point_coding"; ?>" style="color: white;">Sale Point List <?php if ($arabic_check == 'Yes') { ?> (قائمة البند) <?php } ?></a>
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
 							POS <?php if ($arabic_check == 'Yes') { ?>(نقاط البيع
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
								}   ?>

 						</div>

 						<form id="formID" class="form-horizontal" role="form" method="post" action="<?php echo SURL . "Sale_point_coding/add" ?>" enctype="multipart/form-data" onsubmit="return check_data();">


 							<div class="row">


 								<div class="col-sm-12">
 									<div class="form-group">
 										<label class="col-sm-4 control-label no-padding-right" for="form-field-1">Full Name</label>
 										<div class="col-sm-3">
 											<input type="text" class="form-control" required name="name" value="<?php echo $record['sp_name'] ?>" maxlength="50" autofocus>
 										</div>
 									</div>
 									<div class="form-group">
 										<label class="col-sm-4 control-label no-padding-right" for="form-field-1">Email</label>
 										<div class="col-sm-3">
 											<input type="text" required class="form-control" name="email" value="<?php echo $record['email_id'] ?>" maxlength="40">
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

 									<!-- 
 									<div class="form-group">
 										<label class="col-sm-4 control-label no-padding-right" for="form-field-1">Select Area</label>
 										<div class="col-sm-4">
 											<select class="form-control" name="area_id[]" id="area_id" multiple>

 											</select>
 											<input type="hidden" id="edit_area" value="<?php echo $record['area_id'] ?>">
 										</div>
 									</div> -->
 									<div class="form-group">
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
 									</div>









 									<!--google map ends here-->

 								</div>

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
 										<button class="btn btn-info" style="margin-left: -20%;" onclick="check_data();">
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
		$this->load->view('en/include/footer');
		?>

 	<?php
		$this->load->view('en/include/js');
		?>
 	<?php $this->load->view('en/include/paymentreceipt_js.php'); ?>
 	<script src="http://ajax.googleapis.com/ajax/libs/jquery/1.2.6/jquery.js" type="text/javascript"></script>
 	<script src="https://ajax.googleapis.com/ajax/libs/jqueryui/1.12.1/jquery-ui.min.js"></script>
 	<script src="<?php echo SURL ?>assets/js/jquery.UrduEditor.js" type="text/javascript"></script>

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
 	<script type="text/javascript">
 		// get_area()

 		// function get_area() {

 		// 	var city_id = $('#city_id').val();
 		// 	var edit_area = $("#edit_area").val();

 		// 	$.ajax({
 		// 		url: "<?php echo SURL . "Sale_point_coding/get_area"; ?>",
 		// 		cache: false,
 		// 		type: "POST",
 		// 		data: {
 		// 			city_id: city_id,
 		// 			edit_area: edit_area,
 		// 		},
 		// 		success: function(html) {

 		// 			$("#area_id").html(html);
 		// 			$("#area_id").attr("class", "chosen-select");
 		// 			jQuery(function($) {
 		// 				$('#area_id').trigger("chosen:updated");
 		// 				var $mySelect = $('#area_id');
 		// 				$mySelect.chosen();
 		// 			});
 		// 		}
 		// 	});
 		// }

 		function showPreview(objFileInput) {
 			if (objFileInput.files[0]) {
 				var fileReader = new FileReader();
 				fileReader.onload = function(e) {
 					$("#targetLayer").html('<img src="' + e.target.result + '" width="268" height="210px" class="upload-preview" style="margin-left: 0%;" />');
 					//$("#targetLayer").css('opacity','0.7');
 					$(".icon-choose-image").css('opacity', '0.5');
 				}
 				fileReader.readAsDataURL(objFileInput.files[0]);
 			}
 		}
 	</script>
 	<script>
 		// Function to set the width of select area equal to the width of select city
 		function setAreaWidth() {
 			var citySelectWidth = $('#city_id').outerWidth(); // Get the width of select city
 			$('#area_id').css('width', citySelectWidth + 'px'); // Set the width of select area equal to the width of select city
 		}

 		// Call the function when the document is ready and when the window is resized
 		$(document).ready(function() {
 			setAreaWidth(); // Set the initial width
 			$(window).resize(function() {
 				setAreaWidth(); // Set the width when window is resized
 			});
 		});
 	</script>

 	<script src="https://maps.googleapis.com/maps/api/js?key=AIzaSyCcFOE6o37oTX5ptY5MupQMWhKtJ_jRlFw&libraries=places&callback=initAutocomplete" async defer></script>
 	<script>
 		var autocomplete = [];

 		function initAutocomplete() {
 			if (!google || !google.maps || !google.maps.places) {
 				console.error("Google Maps API failed to load.");
 				return;
 			}
 			document.querySelectorAll('.area').forEach(function(input, index) {
 				if (!autocomplete[index]) {
 					autocomplete[index] = new google.maps.places.Autocomplete(input, {
 						types: ['geocode'],
 						componentRestrictions: {
 							country: 'PK'
 						}
 					});
 					autocomplete[index].addListener('place_changed', function() {
 						onPlaceChanged(index);
 					});
 				}
 			});
 		}

 		function onPlaceChanged(index) {
 			var place = autocomplete[index].getPlace();
 			if (!place.geometry) {
 				console.log("Place details not found");
 				return;
 			}
 			var address = place.formatted_address;
 			geocodeAddress(address, index);
 		}

 		function geocodeAddress(address, index) {
 			var geocoder = new google.maps.Geocoder();
 			geocoder.geocode({
 				'address': address
 			}, function(results, status) {
 				if (status === 'OK') {
 					var lat = results[0].geometry.location.lat().toFixed(8); // Use fixed precision
 					var lng = results[0].geometry.location.lng().toFixed(8); // Use fixed precision
 					var city = getCityName(results[0]);

 					console.log('Geocoded latitude: ' + lat + ', longitude: ' + lng); // Debugging

 					// Update hidden inputs
 					document.querySelectorAll('.lat')[index].value = lat;
 					document.querySelectorAll('.lng')[index].value = lng;
 					document.querySelectorAll('.city')[index].value = city;
 				} else {
 					console.error("Geocode was not successful for the following reason: " + status);
 				}
 			});
 		}

 		function getCityName(result) {
 			var addressComponents = result.address_components;
 			for (var i = 0; i < addressComponents.length; i++) {
 				var component = addressComponents[i];
 				if (component.types.includes('locality')) {
 					return component.long_name;
 				}
 			}
 			return null;
 		}

 		function add_area() {
 			var newAreaInputGroup = document.createElement('div');
 			newAreaInputGroup.className = 'area-input-group';
 			newAreaInputGroup.innerHTML = `
                <input type="text" class="area" name="areaname[]">
                <input type="text" class="lat" name="lat[]" readonly tabindex="-1">
                <input type="text" class="lng" name="lng[]" readonly tabindex="-1">
                <input type="text" class="city" name="city[]" readonly tabindex="-1">
                <button type="button" class="remove-area">Remove</button>
            `;
 			document.getElementById('area-container').appendChild(newAreaInputGroup);
 			// Initialize autocomplete on the newly added input
 			initAutocomplete();
 			addRemoveListeners(); // Ensure remove button works for newly added areas
 		};

 		addRemoveListeners(); // Ensure remove button works for initially loaded areas


 		function addRemoveListeners() {
 			document.querySelectorAll('.remove-area').forEach(function(button) {
 				button.addEventListener('click', function() {
 					this.parentElement.remove();
 				});
 			});
 		}
 	</script>

 	<script>
 		function check_data() {
 			var city_id = $('#city_id').val();
 			var cityNames = document.querySelectorAll('.city');
 			for (var i = 0; i < cityNames.length; i++) {
 				if (cityNames[i].value != city_id && (cityNames[i].value != '')) {
 					alert("Wrong Cities Selected. All must be the same.");
 					return false;
 				}
 			}
 			return true;
 		}
 	</script>
 </body>

 </html>