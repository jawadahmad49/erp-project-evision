<!DOCTYPE html>
<html lang="en">
<?php
$this->load->view('app/include/head');
$this->load->view('app/include/header');

?>
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
						<style>
							.chosen-container {
								width: 100% !important;
							}
						</style>
						<form id="formID" class="form-horizontal" role="form" method="post" action="<?php echo SURL . "app/Sale_point_coding/add" ?>" enctype="multipart/form-data">


							<div class="row">


								<div class="col-sm-12">
									<div class="form-group">
										<label class="col-sm-4 control-label no-padding-right" for="form-field-1">Full Name</label>
										<div class="col-sm-3">
											<input type="text" class="form-control" required id='name' name="name" value="<?php echo $record['sp_name'] ?>" maxlength="50" autofocus>
										</div>
									</div>
									<div class="form-group">
										<label class="col-sm-4 control-label no-padding-right" for="form-field-1">Email</label>
										<div class="col-sm-3">
											<input type="email" id="email" required class="form-control" name="email" value="<?php echo $record['email_id'] ?>" maxlength="40">
										</div>
									</div>
									<div class="form-group">
										<label class="col-sm-4 control-label no-padding-right" for="form-field-1">Mobile Number</label>
										<div class="col-sm-3">
											<input type="text" required id="phone_no" class="form-control" name="phone_no" value="<?php echo $record['phone_num'] ?>" maxlength="13" pattern="\+92[0-9]{10}|92[0-9]{10}" title="Please enter a valid phone number in the format +923123456789 or 92123456789 (excluding the leading 0)" onkeypress="return /[0-9+]/.test(event.key)" placeholder="+923123456789 or 92123456789">
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
											<select class="form-control chosen-select" name="city_id" id="city_id" onchange="get_zones()">
												<?php foreach ($city_list as $key => $data) { ?>
													<option value="<?php echo $data['city_id']; ?>" <?php if ($record['city_id'] == $data['city_id']) {
														   echo 'selected';
													   } ?>><?php echo $data['city_name']; ?></option>
												<?php } ?>
											</select>
										</div>
									</div>
									<div class="form-group">
										<label class="col-sm-4 control-label no-padding-right" for="form-field-1">Zone Name</label>
										<div class="col-sm-3">
											<?php $_SESSION["zone_id"] = $record['zone_id']; ?>
											<select class="form-control" name="zone_id[]" id="zone_id" multiple required>

											</select>
										</div>
									</div>
									<div class="form-group">
										<label class="col-sm-4 control-label no-padding-right" for="form-field-1">Address</label>
										<div class="col-sm-3">
											<textarea class="col-sm-12" name="address" id="address"><?php echo $record['address'] ?></textarea>
										</div>
									</div>
									<script src="https://maps.googleapis.com/maps/api/js?key=AIzaSyCJPePs39ubzYGmfpcKbPV6k404GvXcL7s&libraries=places" async defer></script>
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

									<div class="form-group" style="background: #B8B8B8;">
										<label class="col-sm-12" style="text-align: center;" for="form-field-1"><strong>Select Your Shop Location</strong></label>
										<input type="text" id="search-box" placeholder="Search for a place" style="height: 4rem; width: 40rem;">
										<div id="map"></div>
										<input type="hidden" id="shop-location" name="shop_location" value="<?php echo $record['shop_location'] ?>">
									</div>

									<div class="form-group hidden">
										<label class="col-sm-4 control-label no-padding-right" for="form-field-1">Login Id</label>
										<div class="col-sm-3">
											<input type="text" id='loginid' class="form-control" name="loginid" value="<?php echo $record['loginid'] ?>" maxlength="25">
										</div>
									</div>
									<div class="form-group hidden">
										<label class="col-sm-4 control-label no-padding-right" for="form-field-1">Password</label>
										<div class="col-sm-3">
											<input type="password" id='password' class="form-control" name="password" value="<?php echo base64_decode($record['password']) ?>" maxlength="15">
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
	<script src="https://cdn.jsdelivr.net/npm/@yaireo/tagify"></script>
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
		let map;
		let marker;
		let geocoder;
		const defaultCenter = {
			lat: 30.3753,
			lng: 69.3451
		};

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

			geocoder = new google.maps.Geocoder();

			const searchBox = new google.maps.places.SearchBox(document.getElementById('search-box'));
			map.controls[google.maps.ControlPosition.TOP_LEFT].push(document.getElementById('search-box'));

			map.addListener('bounds_changed', () => {
				searchBox.setBounds(map.getBounds());
			});

			searchBox.addListener('places_changed', () => {
				const places = searchBox.getPlaces();
				if (places.length == 0) return;

				const place = places[0];
				if (place.geometry) {
					map.setCenter(place.geometry.location);
					placeMarker(place.geometry.location);
					map.setZoom(15);
				}
			});

			map.addListener('click', (event) => {
				placeMarker(event.latLng);
			});

			// Retrieve the stored location
			const shopLocation = document.getElementById('shop-location').value;
			if (shopLocation) {
				const [lat, lng] = shopLocation.split(',').map(Number);
				if (!isNaN(lat) && !isNaN(lng)) {
					const position = {
						lat,
						lng
					};
					map.setCenter(position);
					placeMarker(position);
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

				marker.addListener('dragend', (event) => {
					document.getElementById('shop-location').value = `${event.latLng.lat()},${event.latLng.lng()}`;
				});
			}
			document.getElementById('shop-location').value = `${location.lat()},${location.lng()}`;
		}

		window.onload = initMap;
	</script>
	<!-- Chosen CSS -->
	<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/chosen/1.8.7/chosen.min.css">

	<!-- jQuery (required by Chosen) -->
	<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

	<!-- Chosen JavaScript -->
	<script src="https://cdnjs.cloudflare.com/ajax/libs/chosen/1.8.7/chosen.jquery.min.js"></script>

	<script>
		get_zones()

		function get_zones() {
			var city_id = $('#city_id').val();

			$.ajax({
				url: '<?php echo SURL; ?>app/Sale_point_coding/get_zones',
				type: 'POST',
				data: {
					city_id: city_id,
				},
				success: function (response) {
					$('#zone_id').html(response);
					$("#zone_id").attr("class", "chosen-select");
					jQuery(function ($) {
						$('#zone_id').trigger("chosen:updated");
						var $mySelect = $('#zone_id');
						$mySelect.chosen();
					});
				},
				error: function (xhr, status, error) {
					console.error('AJAX Error while fetching item details:', status, error);
				}
			});
		}
	</script>
</body>

</html>