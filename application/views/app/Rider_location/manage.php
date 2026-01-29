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

						<li class="active">Rider Location </li>
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
								Rider Location
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
							<?php } ?>
							<div class="col-md-12 form-group">
								<fieldset class="scheduler-border">
									<legend class="scheduler-border">Rider Detail</legend>
									<div class="form-group" style="padding-bottom: 5rem;">
										<label class="col-sm-2 control-label no-padding-right" for="form-field-1" align="right">Sale Point</label>
										<div class="col-sm-4">
											<select class="chosen-select form-control" name="salepoint" onchange="fetchData()" id="salepoint">
												<?php foreach ($salepoint as $key => $value) { ?>
													<option value="<?php echo $value['sale_point_id']; ?>" <?php if ($sale_point_id == $value['sale_point_id']) {
														   echo "selected";
													   } ?>><?php echo $value['sp_name']; ?></option>
												<?php } ?>
											</select>
										</div>
										<label class="col-sm-2 control-label no-padding-right" for="form-field-1" align="right">Rider </label>
										<div class="col-sm-4">
											<select required="required" class=" form-control" name="rider" id="rider" data-placeholder="Choose a rider..." autofocus>
											</select>
											<input type="hidden" value="<?php echo $rider_id ?>" id="rider_id">
										</div>
									</div>
									<br>
									<div class="form-group">
										<div class="col-sm-12">
											<div id="map" style="width:100%;height:400px;"></div>
										</div>
									</div>
								</fieldset>
							</div>
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

	<script type="module">
		import { initializeApp } from "https://www.gstatic.com/firebasejs/11.0.2/firebase-app.js";
		import { getAnalytics } from "https://www.gstatic.com/firebasejs/11.0.2/firebase-analytics.js";
		import { getFirestore, doc, getDoc } from "https://www.gstatic.com/firebasejs/11.0.2/firebase-firestore.js";

		const firebaseConfig = {
			apiKey: "AIzaSyDqszKYwG18rd4DEWnOrMofwI_x4-0-qAg",
			authDomain: "opi-gas-727d8.firebaseapp.com",
			projectId: "opi-gas-727d8",
			storageBucket: "opi-gas-727d8.appspot.com",
			messagingSenderId: "937802925107",
			appId: "1:937802925107:web:fd6f2f407e47f3e7c80f46",
			measurementId: "G-SEKV1K0KS4"
		};

		const app = initializeApp(firebaseConfig);
		const analytics = getAnalytics(app);
		const db = getFirestore(app);

		let intervalId = null; 

		function fetchRiderLocation() {
			const riderId = $("#rider").val();
			if (!riderId || riderId.trim() === "") {
				console.error("Invalid riderId: " + riderId);
				alert("Please select a valid rider.");
				return;
			}

			if (intervalId !== null) {
				clearInterval(intervalId);
				intervalId = null; 
			}

			const docRef = doc(db, 'dbtracking', riderId);

			getDoc(docRef).then((docSnap) => {
				if (docSnap.exists()) {
					intervalId = setInterval(() => {
						getDoc(docRef).then((docSnap) => {
							if (docSnap.exists()) {
								const data = docSnap.data();
								// console.log("Document data:", data);

								const currentLoc = data.currentLoc;
								const latitude = currentLoc.latitude;
								const longitude = currentLoc.longitude;

								initMap(latitude, longitude);
							} else {
								console.log("No such document!");
								alert("No such document!");
								clearInterval(intervalId);
								intervalId = null; // Reset the interval ID
							}
						}).catch((error) => {
							console.error("Error getting document:", error);
							alert("Error getting document: " + error.message);
							clearInterval(intervalId);
							intervalId = null; // Reset the interval ID
						});
					}, 3000); // 3000 ms = 3 seconds
				} else {
					console.log("No such document!");
					alert("No such document!");
				}
			}).catch((error) => {
				console.error("Error getting document:", error);
				alert("Error getting document: " + error.message);
			});
		}

		let map;
		function initMap(lat, lng) {
			const riderLatLng = { lat: lat, lng: lng };

			map = new google.maps.Map(document.getElementById('map'), {
				zoom: 16,
				center: riderLatLng
			});
			new google.maps.Marker({
				position: riderLatLng,
				map: map,
				title: 'Rider Location'
			});
		}

		$(document).on('change', '#rider', function () {
			console.log('Change event triggered (with delegation)');
			fetchRiderLocation();
		});

		fetchData();
		function fetchData() {
			const sale_point_id = $('#salepoint').val();
			const rider_id = $('#rider_id').val();

			$.ajax({
				url: '<?= SURL; ?>app/Rider_location/get_riders',
				type: 'POST',
				data: {
					sale_point_id: sale_point_id,
					rider_id: rider_id
				},
				success: function (response) {
					$("#rider").html(response);
					fetchRiderLocation();
				},
				error: function (xhr, status, error) {
					console.error('AJAX Error while fetching vehicles:', status, error);
				}
			});
		};
	</script>

	<script src="https://maps.googleapis.com/maps/api/js?key=AIzaSyCJPePs39ubzYGmfpcKbPV6k404GvXcL7s&libraries=places"></script>

	<script src="https://cdnjs.cloudflare.com/ajax/libs/chosen/1.8.7/chosen.jquery.min.js"></script>
	<script>
		jQuery(function ($) {
			$('#salepoint').trigger("chosen:updated");
			var $mySelect = $('#salepoint');
			$mySelect.chosen();
			$mySelect.trigger('chosen:activate');
		});
	</script>
</body>

</html>