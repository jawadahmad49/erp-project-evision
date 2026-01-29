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

						<li>
							<a href="<?php echo SURL . "app/item"; ?>">Item List </a>
						</li>
						<li class="active">Add Item </li>
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
								Add Item
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

							<form id="formID" class="form-horizontal" role="form" method="post" action="<?php echo SURL . "app/item/add" ?>" enctype="multipart/form-data" onsubmit="submitform.disabled = true; return true;">

								<input type="hidden" name="clas" value="1">

								<div class="form-group brand">
									<label class="col-sm-4 control-label no-padding-right" for="form-field-1">Salepoint</label>

									<div class="col-sm-3">

										<select class="chosen-select form-control" name="salepoint" id="salepoint">
											<?php
											if ($salepoint) {
												foreach ($salepoint as $key => $value) { ?>

													<option value="<?php echo $value['sale_point_id']; ?>" <?php if ($sale_Point == $value['sale_point_id']) {
														   echo "selected";
													   } ?>><?php echo $value['sp_name']; ?></option>

												<?php }
											} else {
												echo ' <option value="All">All Locations</option>';
											} ?>

										</select>

										<input type="hidden" id="sale_Point" value="<?php echo $report['sale_Point'] ?>">
									</div>
								</div>


								<div class="form-group">
									<label class="col-sm-4 control-label no-padding-right" for="form-field-1"> Category </label>

									<div class="col-sm-3">
										<select required="required" class="form-control chosen-select" name="category" id="category" data-placeholder="Choose a City..." autofocus onchange="show_hide()">
											<option value=""> Select Class First... </option>
											<?php foreach ($category_list as $key => $value) {
												?>
												<option value="<?php echo $value['id']; ?>"><?php echo $value['catname']; ?></option>

											<?php } ?>
										</select>
									</div>
								</div>



								<div class="form-group brand" style="display: none;">
									<label class="col-sm-4 control-label no-padding-right" for="form-field-1"> Brand Name </label>

									<div class="col-sm-3">
										<select required="required" class="form-control" name="brandname" id="brandname" data-placeholder="Brand Name">
											<option value=""> Select Brand Name... </option>
											<?php foreach ($brand as $key => $value) {
												?>
												<option value="<?php echo $value['brand_id']; ?>"><?php echo $value['brand_name']; ?></option>

											<?php } ?>
										</select>

									</div>
								</div>


								<div class="form-group">
									<label class="col-sm-4 control-label no-padding-right" for="form-field-1"> Item Name </label>

									<div class="col-sm-3">
										<input maxlength="50" style="width: 100%;" value="" type="text" id="itemname" name="itemname" placeholder="Item Name" pattern="[-+]?[0-9]*\.?[0-9]*" class="col-xs-12  col-sm-5 urdu_class" required="required" />
									</div>
								</div>
								<div class="form-group itemnameint">
									<label class="col-sm-4 control-label no-padding-right" for="form-field-1"> Cylinder Weight </label>
									<div class="col-sm-3">
										<input maxlength="4" style="width: 100%;" value="<?php echo $item['itemnameint']; ?>" type="text" id="itemnameint" name="itemnameint" onkeypress="return /[0-9.]/i.test(event.key)" placeholder="Cylinder Weight" class="col-xs-12 col-sm-5 urdu_class" <?php if ($item['catcode'] == 1) { ?> pattern="[-+]?[0-9]*\.?[0-9]*" <?php } ?> required="required" />
									</div>
								</div>
								<!-- <div class="form-group security">
									<label class="col-sm-4 control-label no-padding-right" for="form-field-1"> Security </label>

									<div class="col-sm-3">
										<input maxlength="7" style="width: 100%;" value="0" type="text" id="sprice" name="sprice" placeholder="Security Price" pattern="[-+]?[0-9]*\.?[0-9]*" class="col-xs-12  col-sm-5 urdu_class" required="required" />
									</div>
								</div> -->
								<div class="form-group">
									<label class="col-sm-4 control-label no-padding-right" for="form-field-1">Select Status </label>

									<div class="col-sm-3">
										<select class="chosen-select form-control" name="status" id="status" data-placeholder="Choose a Status...">

											<option value="Active">Active</option>
											<option value="InActive">InActive</option>

										</select>
									</div>
								</div>

								<div class="form-group">
									<label class="col-sm-4 control-label no-padding-right" for="form-field-1">Cylinder Picture </label>
									<div class="col-sm-7">
										<div id="targetLayer">
											<?php if (isset($company['logo']) && $company['logo'] != '') { ?>
												<img width="200" height="200" id="logo_id" src="<?php echo IMG . 'company/' . $company['logo']; ?>">
											<?php } ?>
										</div>

										<label class="ace-file-input">
											<input type="file" accept="image/x-png,image/gif,image/jpeg" name="company_image" id="logo" onchange="validateImage(this); showPreview(this);" required>
											<span class="ace-file-container col-xs-10 col-sm-5" data-title="Choose">
												<span class="ace-file-name" data-title="No File ...">
													<i class=" ace-icon fa fa-upload"></i>
												</span>
											</span>
											<a class="remove" href="#"><i class=" ace-icon fa fa-times"></i></a></label>
									</div>
								</div>

								<div class="row">
									<input type="hidden" id="code_id" name="code_id" value="0" />
									<hr />

									<div class="form-actions center">
										<button class="btn btn-info" onclick="checkPic()">
											<i class="ace-icon fa fa-check bigger-110"></i>
											Submit
										</button>
									</div>

								</div>

								<input type="hidden" name="id" value="<?php echo $item['item_id'] ?>" />
							</form>

							<!-- PAGE CONTENT ENDS -->
						</div><!-- /.col -->
					</div><!-- /.row -->
				</div><!-- /.page-content -->
			</div>
		</div><!-- /.main-content -->

	</div><!-- /.main-container -->

	<?php
	$this->load->view('app/include/footer');
	?>

	<script type="text/javascript">
		function checkPic() {
			var pic = $('#logo').val();

			if (!pic) {
				alert("Cylinder picture is required");
				return;
			}

		}


		function english_lang() {

			$('#itemname').setUrduInput({
				value_in: 15
			});
			$('#itemname').removeAttr('dir');

		}

		function urdu_lang() {

			$('#itemname').attr("dir", "rtl");
			$('#itemname').setUrduInput({
				value_in: 17
			});


		}
	</script>
	<?php
	$this->load->view('app/include/js');
	?>

	<!-- inline scripts related to this page -->
	<!-- <script type="text/javascript">
		$("#itemname").removeAttr("pattern");

		$('#category').on('change', function() {
			var cat_id = $(this).val();

			if (cat_id == 1 || cat_id == 7) {
				$("#brandname").prop('required', true);
				$("#sprice").prop('required', true);
				$('.brand').css('display', 'block');
				$('.security').css('display', 'block');

				$('#itemname').attr('pattern', '[-+]?[0-9]*\.?[0-9]*');
			} else {
				$("#brandname").prop('required', false);
				$("#sprice").prop('required', false);
				$('.brand').css('display', 'none');
				$('.security').css('display', 'none');
				$("#itemname").removeAttr("pattern");
			}
		});
	</script> -->

	<script type="text/javascript">
		function show_hide() {
			var cat_id = $("#category").val();

			if (cat_id == 1) {
				$(".brand").show();
				$(".itemnameint").show();
				$("#brandname").attr('required', 'required');
				$("#itemnameint").attr('required', 'required');
				$('.security').show();
				$('#itemname').attr('pattern', '[-+]?[0-9]*\\.?[0-9]*');
			} else {
				$(".brand").hide();
				$(".itemnameint").hide();
				$('.security').hide();
				$("#brandname").removeAttr('required');
				$('#itemname').removeAttr('pattern');
				$('#itemnameint').removeAttr('required');
			}
		}
	</script>
	<?php $this->load->view('app/include/item_js.php'); ?>

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

		function english_lang() {

			jQuery(".urdu_class").each(function (index) {

				jQuery(this).removeAttr('dir');
				setEnglish(jQuery(this));

			});

		}

		function urdu_lang() {

			jQuery(".urdu_class").each(function (index) {

				jQuery(this).attr("dir", "rtl");

				setUrdu(jQuery(this));

			});

		}
	</script>
	<script type="text/javascript">
		function validateImage(input) {
			const file = input.files[0];
			const maxSize = 500 * 1024; // 500KB in bytes

			if (file && file.size > maxSize) {
				alert("The selected image exceeds the maximum size of 500KB. Please select a smaller image.");
				input.value = ''; // Clear the file input
			}
		}
		function showPreview(objFileInput) {
			if (objFileInput.files[0]) {
				var fileReader = new FileReader();
				fileReader.onload = function (e) {
					$("#targetLayer").html('<img src="' + e.target.result + '" width="200px" height="200px" class="upload-preview" />');
					$("#targetLayer").css('opacity', '0.7');
					$(".icon-choose-image").css('opacity', '0.5');
				}
				fileReader.readAsDataURL(objFileInput.files[0]);
			}
		}
	</script>

</body>

</html>