<!DOCTYPE html>
<html lang="en">
<?php
$this->load->view('app/include/head');
$this->load->view('app/include/header');

?>

<body class="no-skin">

	<div class="main-container ace-save-state" id="main-container">

		<?php $this->load->view('app/include/sidebar');?>



		<div class="main-content">
			<div class="main-content-inner">
				<div class="breadcrumbs ace-save-state" id="breadcrumbs">
					<ul class="breadcrumb">
						<li>
							<i class="ace-icon fa fa-home home-icon"></i>
							<a href="<?php echo SURL . "Module/app"; ?>">Home</a>
						</li>

						<li class="active"><?php echo ucwords($filter); ?>Accessories Price Configuration </li>
						<!-- <li>
							<a href="<?php //echo SURL . "Accessories_Price_config"; 
							?>">Price Configuration</a>
						</li> -->
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
									<label class="lbl" for="ace-settings-highlight"> Alt. Active Item</label>
								</div>
							</div><!-- /.pull-left -->
						</div><!-- /.ace-settings-box -->
					</div><!-- /.ace-settings-container -->

					<div class="page-header">
						<h1>
							Gasable PK
							<small>
								<i class="ace-icon fa fa-angle-double-right"></i>
								<?php echo ucwords($filter); ?>Accessories Price Configuration
							</small>
						</h1>
					</div><!-- /.page-header -->

					<div class="row">
						<span class="form-horizontal">

							<div class="col-xs-12 col-sm-12 ">
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

								<div class="form-group">
									<label class="col-sm-4 control-label no-padding-right" for="form-field-1"> Location </label>
									<div class="col-sm-3">
										<select class="form-control chosen-select" name="location" id="location" required onchange="cat_details();">
											<option value="">Select Location</option>
											<?php foreach ($name as $key => $value) { ?>
												<option value="<?php echo $value['sale_point_id']; ?>"><?php echo $value['sp_name']; ?></option>
											<?php } ?>
										</select>
									</div>
								</div>
								<div class="form-group">
									<label class="col-sm-4 control-label no-padding-right" for="form-field-1"> Category </label>

									<div class="col-sm-3 ">
										<select required="required" onchange="cat_details();show_price()" class="form-control chosen-select" name="category" id="category" data-placeholder="Choose a Category...">
											<option value=""> Select Category First... </option>
											<?php
											$catnames = $this->db->query("SELECT * FROM tblcategory WHERE catcode = 2")->result_array();
											foreach ($catnames as $key => $value) {
												?>
												<option value="<?php echo $value['catcode']; ?>"><?php echo $value['catname']; ?></option>

											<?php } ?>
										</select>
									</div>
								</div>

								<?php //$edit_detail = $this->db->query("SELECT edate from tbl_price_fluctuation where saleprice!=''||saleprice!=0 order by edate desc")->row_array();
								//echo ("SELECT edate from tbl_price_fluctuation where saleprice!=''||saleprice!=0 order by edate desc");exit;
								?>
								<div class="form-group">
									<label class="col-sm-4 control-label no-padding-right" for="form-field-1">Effective Date</label>

									<div class="col-sm-3">
										<div class="input-group">
											<input name="date" class="form-control date-picker" readonly data-date-start-date="1d" onchange="cat_details()" id="id-date-picker-1" type="text" data-date-format="yyyy-mm-dd" required value="<?php if (!empty($edit_detail['edate'])) {
												echo date("Y-m-d");

												// echo $edit_detail['edate'];
											} else {
												echo date("Y-m-d");
											} ?>">
											<span class="input-group-addon">
												<i class="fa fa-calendar bigger-110"></i>
											</span>
										</div>
									</div>
								</div>


								<div class="form-group" id="price_id" hidden>
									<label class="col-sm-3 control-label no-padding-right" for="form-field-1">Price of 11.8 kg</label>

									<div class="col-sm-3">
										<input name="price_11_8" class="form-control" id="price_11_8" type="text" onchange="cat_details()" onkeypress='return /[0-9 .]/i.test(event.key)'>
									</div>
								</div>
								<div class="fluc_tbl" id="fluc_tble"></div>

							</div><!-- /.col -->

							<tbody id="dynamic-table">

							</tbody>
						</span>

					</div><!-- /.row -->

				</div><!-- /.page-content -->
			</div>
		</div><!-- /.main-content -->

	</div><!-- /.main-container -->
	<script src="https://code.jquery.com/jquery-3.6.4.min.js"></script>

	<?php
	$this->load->view('app/include/footer');
	?>
	<?php $this->load->view('en/include/entertab_js.php'); ?>
	<?php
	$this->load->view('en/include/js');
	?>

	<script type="text/javascript">
		document.getElementById("eamount").focus();
	</script>



	<script>
		function add_details() {
			var date = $('#id-date-picker-1').val();
			var category = $('#category').val();
			var location = $('#location').val();
			var formData = [];
			var count = 0

			$('#tbody tr.master').each(function () {
				var row = $(this);
				// var saleprice = row.find('input[name="saleprice[]"]').val();
				var registered_saleprice = row.find('input[name="registered_saleprice[]"]').val();
				var un_registered_saleprice = row.find('input[name="un_registered_saleprice[]"]').val();
				var edit = row.find('input[name="edit[]"]').val();

				var Color = row.css('color');

				if (Color === 'rgb(255, 0, 0)' || Color.toLowerCase() === 'red') {
					count++;
				}


				if (registered_saleprice > 0 && un_registered_saleprice > 0) {
					var rowData = {
						item_id: row.find('input[name="materialcode[]"]').val(),
						// saleprice: saleprice,
						registered_saleprice: registered_saleprice,
						un_registered_saleprice: un_registered_saleprice,
						edit: edit,
					};
					formData.push(rowData);
				}
				console.log(formData)
			});
			
			var confirmation = confirm("Do you want to update?");

			if (confirmation) {
				var request = $.ajax({
					url: "<?php echo SURL . "app/Accessories_Price_config/add"; ?>",
					type: "POST",
					data: {
						category: category,
						location: location,
						date: date,
						formData: formData
					},
					dataType: "html",
				});
				request.done(function (html) {
					alert('Operation Completed Successfully');
					cat_details();
				});
				request.fail(function (jqXHR, textStatus) {
					alert("Request failed: " + textStatus);
				});
			} else {
				alert("Update canceled.");
			}
		}
	</script>

	<script type="text/javascript">
		function cat_details() {

			var category = $('#category').val();
			var location = $('#location').val();

			var date = $('#id-date-picker-1').val();
			var price_11_8 = $('#price_11_8').val();


			var request = $.ajax({
				url: "<?php echo SURL . "app/Accessories_Price_config/getDetails"; ?>",
				type: "POST",
				data: {
					category: category,
					location: location,
					date: date,
					price_11_8: price_11_8,
				},
				dataType: "html",
			});
			request.done(function (html) {
				$("#fluc_tble").html(html);
			});
			request.fail(function (jqXHR, textStatus) {
				alert("Request failed: " + textStatus);

			})


		}

		show_price()

		function show_price() {

			var category = $('#category').val();
			if (category == 1) {
				$('#price_id').show();

			} else {
				$('#price_id').hide();
			}
		}


		function add_data() {

			var date = $('#id-date-picker-1').val();

			var request = $.ajax({
				url: "<?php echo SURL . "app/Accessories_Price_config/add"; ?>",
				type: "POST",
				data: {

					date: date,
				},
				dataType: "html",
			});
			request.done(function (html) {
				//	 alert(html);
				$("#fluc_tble").html(html);

			});
			request.fail(function (jqXHR, textStatus) {
				alert("Request failed: " + textStatus);

			})
		}
	</script>


	<script type="text/javascript">
		$("#eamount").blur(function () {
			this.value = parseFloat(this.value).toFixed(2);
		});
		$("#nrate").blur(function () {
			this.value = parseFloat(this.value).toFixed(2);
		});
		document.getElementById("item").focus();
	</script>

	<script type="text/javascript">
		// function color_change(id) {
		// 	$('#' + id).css({
		// 		'color': 'red',
		// 	});
		// }
	</script>


	<!-- start editor  -->

	<!-- page specific plugin scripts -->

	<?php $this->load->view('en/include/bank_js.php'); ?>


	<!-- end editor -->
</body>

</html>