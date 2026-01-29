<!DOCTYPE html>
<html lang="en">
<?php
$this->load->view('en/include/head');
$this->load->view('en/include/header');
 ?>

<body class="no-skin">

<div class="main-container ace-save-state" id="main-container">

<?php $this->load->view('en/include/sidebar');
 			?>



			<div class="main-content">
				<div class="main-content-inner">
					<div class="breadcrumbs ace-save-state" id="breadcrumbs">
						<ul class="breadcrumb">
							<li>
								<i class="ace-icon fa fa-home home-icon"></i>
								<a href="<?php echo SURL."admin"; ?>">Home</a>
							</li>

						 
							<li class="active">Company</li>
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
									<?php echo ucwords($title); ?> 
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
												
											</strong>

											<?php echo $this->session->flashdata('err_message'); ?>
											<br>
										</div>

									    <?php
									     }
										if ($this->session->flashdata('ok_message')) {
									    ?>
									    
											<div class="alert alert-block alert-success">
												<button type="button" class="close" data-dismiss="alert">
												<i class="ace-icon fa fa-times"></i>
												</button>

												<p>
												<strong>
												<i class="ace-icon fa fa-check"></i>
												Well done!
												</strong>
												<?php echo $this->session->flashdata('ok_message'); ?>
												</p>
											</div>

									    <?php
									     }
									     ?>

								<form class="form-horizontal" role="form" method="post" action="<?php echo SURL."Company/".$filter ?>" enctype="multipart/form-data">

									<div class="form-group">
										<label class="col-sm-3 control-label no-padding-right" for="form-field-1"> Business Name </label>

										<div class="col-sm-9">
											<input maxlength="50" value="<?php echo ucwords($company['business_name']); ?>" type="text" id="bname" name="bname" placeholder="Business Name" class="col-xs-10 col-sm-5" pattern="^[a-zA-Z ]*$" required="required" title="Only Letters Allowed"/>
										</div>
									</div>

									<div class="form-group">
										<label class="col-sm-3 control-label no-padding-right" for="form-field-1">Company Logo </label>
										<div class="col-sm-9">
											<div id="targetLayer">
												<?php  if(isset($company['logo']) && $company['logo'] !='') {?>
												<img width="200" height="200" id="logo_id" src="<?php echo IMG .'company/'.$company['logo']; ?>">
												<?php } ?>
												
											</div>
											<input type="hidden" name="old_image" value="<?php echo $company['logo']; ?>">
											<label class="ace-file-input">
												<input type="file" accept="image/x-png,image/gif,image/jpeg" name="company_image" id="logo" class="col-xs-10 col-sm-5" onChange="showPreview(this);"><span class="ace-file-container col-xs-10 col-sm-5" data-title="Choose"><span class="ace-file-name" data-title="No File ..."><i class=" ace-icon fa fa-upload"></i></span></span><a class="remove" href="#"><i class=" ace-icon fa fa-times"></i></a></label>
										</div>
									</div>

									<div class="form-group">
										<label class="col-sm-3 control-label no-padding-right" for="form-field-1"> Owner Name </label>

										<div class="col-sm-9">
											<input maxlength="50" value="<?php echo ucwords($company['owner_name']); ?>" type="text" id="oname" name="oname" placeholder="Owner Name" class="col-xs-10 col-sm-5" pattern="^[a-zA-Z ]*$" required="required" title="Only Letters Allowed"/>
										</div>
									</div>

									
									<div class="form-group">
										<label class="col-sm-3 control-label no-padding-right" for="form-field-1"> Address </label>

									
											<div class="col-sm-6">
											<textarea name="address"  rows="5%" class="form-control" id="form-field-8" placeholder="Address"><?php echo $company['address']; ?></textarea>
											
										</div>
									</div>

									

									<div class="form-group">
										<label class="col-sm-3 control-label no-padding-right" for="form-field-1"> Phone No </label>

										<div class="col-sm-9">
											<input maxlength="14" minlength="11" value="<?php echo $company['phone']; ?>" type="text" id="phoneno" name="phoneno" placeholder="Phone No" class="col-xs-10 col-sm-5" pattern="^[0-9]+$" title="Only Numbers Allowed..." required=""/>
										</div>
									</div>

									<div class="form-group">
										<label class="col-sm-3 control-label no-padding-right" for="form-field-1"> Email </label>

										<div class="col-sm-9">
											<input  value="<?php echo $company['email']; ?>" type="email" id="email" name="email" placeholder="Email" class="col-xs-10 col-sm-5" required=""/>
										</div>
									</div>
									<div class="form-group">
										<label class="col-sm-3 control-label no-padding-right" for="form-field-1"> Ntn No </label>

										<div class="col-sm-9">
											<input  value="<?php echo $company['ntn']; ?>" type="text" id="ntn" name="ntn" placeholder="NTN No" class="col-xs-10 col-sm-5"  title="Only Numbers Allowed..." />
										</div>
									</div>
									<div class="form-group">
										<label class="col-sm-3 control-label no-padding-right" for="form-field-1"> Gst No </label>

										<div class="col-sm-9">
											<input  value="<?php echo $company['gst']; ?>" type="text" id="gst" name="gst" placeholder="GST No" class="col-xs-10 col-sm-5"  title="Only Numbers Allowed..." />
										</div>
									</div>

									
									<div class="form-group">
										<label class="col-sm-3 control-label no-padding-right" for="form-field-1"> Centralized Pricing  </label>

										<div class="col-sm-9" >
											  <select id="central_pricing" name="central_pricing" class="col-xs-10 col-sm-5">
												<option  value="Yes" <?php if($company['central_pricing']=='Yes'){ print 'selected'; } ?> >Yes</option>
												<option  value="No"  <?php if($company['central_pricing']=='No'){ print 'selected'; } ?>  >No</option>
											</select>
										</div>
									</div>


									<div class="form-group">
										<label class="col-sm-3 control-label no-padding-right" for="form-field-1"> Show Default Date  </label>

										<div class="col-sm-" >
											 <input type="checkbox" class="col-xs-10 col-sm-1" style="margin-top: 10px;" name="default_date" value="true" <?php if($company['show_default_date']=='true'){ print 'checked'; } ?> >
										</div>
										<label  class="col-sm-2 control-label no-padding-right" for="form-field-1"> Stock Available Check </label>

										<div class="col-sm-3" >
											 <input type="checkbox" class="col-xs-10 col-sm-1" style="margin-top: 10px;" name="stock_check" value="true" <?php if($company['stock_check']=='true'){ print 'checked'; } ?> >
										</div>
										
									</div>
										<div class="form-group">
											<label class="col-sm-3 control-label no-padding-right" for="form-field-1"> Redirect on Same Page</label>

										<div class="col-sm-" >
											 <input type="checkbox" class="col-xs-10 col-sm-1" style="margin-top: 10px;" name="same_page" value="true" <?php if($company['same_page']=='true'){ print 'checked'; } ?> >
										</div>
										<label class="col-sm-2 control-label no-padding-right" for="form-field-1"> Empty Return</label>

										<div class="col-sm-3" >
											 <input type="checkbox" class="col-xs-10 col-sm-1" style="margin-top: 10px;" name="empty_return" value="true" <?php if($company['empty_return']=='true'){ print 'checked'; } ?> >
										</div>
								
										
									</div>
								
										<div class="form-group" style="background: #B8B8B8;">
										<label class="col-sm-12" style="text-align: center;" for="form-field-1"><strong>Select Your Company Location </strong></label>
									<!-- <input id="search_box"class="form-control" style="margin-top: 9px !important; width: 49%; z-index: 0;position: absolute;left: 188px;top: 1px;height: 39px;border: 1px solid;background-color: white;font-weight: bold;" type="text" placeholder="Search Box"> -->
                                    <div id="mapp"  style="width:100%; height:500px; border:11px ;" ></div>
                                     <input type="hidden" name="lat" id="latitude" value="">
							<input type="hidden" name="long" id="longitude" value="">






									
									
									<!--google map ends here-->
								
									</div>
									
									 
									
									<div class="form-group" style="background: #B8B8B8;">
										<label class="col-sm-12" style="text-align: center;" for="form-field-1"><strong>Days</strong></label>
									</div>
									
									<div class="form-group">
										<label class="col-sm-3 control-label no-padding-right" for="form-field-1">Start Time</label>

										<div class="col-sm-4 bootstrap-timepicker timepicker" >
											 <input  value="<?php if($company['start_time']!=""){ echo $company['start_time']; }else{ echo date("H:i"); } ?>" type="time" id="start_time" name="start_time" placeholder="Start Time" class="col-xs-10 col-sm-5" required=""/>
										</div>
										
										<label class="col-sm-1 control-label no-padding-right" for="form-field-1">End Time</label>

										<div class="col-sm-4 bootstrap-timepicker timepicker" >
											  <input  value="<?php if($company['end_time']!=""){ echo $company['end_time']; }else{ echo date("H:i"); } ?>" type="time" id="end_time" name="end_time" placeholder="End Time" class="col-xs-10 col-sm-5" required=""/>
										</div>
									</div>
									
									
									
									<?php
									$f_check = "";
									$m_check = "";
									$tu_check = "";
									$w_check = "";
									$th_check = "";
									$sa_check = "";
									$su_check = "";
									$exp_days = explode(",",$company['opening_days']);
									foreach($exp_days as $v){
										if($v=="friday"){
											$f_check = "checked";
										}elseif($v=="monday"){
											$m_check = "checked";
										}elseif($v=="tuesday"){
											$tu_check = "checked";
										}elseif($v=="wednesday"){
											$w_check = "checked";
										}elseif($v=="thursday"){
											$th_check = "checked";
										}elseif($v=="saturday"){
											$sa_check = "checked";
										}elseif($v=="sunday"){
											$su_check = "checked";
										}
									}
									?>
									
									<div class="form-group">
										<label class="col-sm-1 control-label no-padding-right" for="form-field-1">Monday</label>
										<div class="col-sm-2 col-sm-offset-1" style="margin-top: 7px;">
											<input id="gritter-light" <?php echo $m_check; ?> value="monday" name="opening_days[]" type="checkbox" class="ace ace-switch ace-switch-5">
											<span class="lbl middle"></span>
										</div>
										
										<label class="col-sm-1 control-label no-padding-right" for="form-field-1">Tuesday</label>
										<div class="col-sm-2 col-sm-offset-1" style="margin-top: 7px;">
											<input id="gritter-light" <?php echo $tu_check; ?> value="tuesday" name="opening_days[]" type="checkbox" class="ace ace-switch ace-switch-5">
											
											
											<span class="lbl middle"></span>
										</div>
									
										<label class="col-sm-1 control-label no-padding-right" for="form-field-1">Wednesday</label>
										<div class="col-sm-2 col-sm-offset-1" style="margin-top: 7px;">
											<input id="gritter-light" <?php echo $w_check; ?> value="wednesday" name="opening_days[]" type="checkbox" class="ace ace-switch ace-switch-5">
											<span class="lbl middle"></span>
										</div>
									</div>
									
									<div class="form-group">
										<label class="col-sm-1 control-label no-padding-right" for="form-field-1">Thursday</label>
										<div class="col-sm-2 col-sm-offset-1" style="margin-top: 7px;">
											<input id="gritter-light" <?php echo $th_check; ?> value="thursday" name="opening_days[]" type="checkbox" class="ace ace-switch ace-switch-5">
											<span class="lbl middle"></span>
										</div>
									 
										<label class="col-sm-1 control-label no-padding-right" for="form-field-1">Friday</label>
										<div class="col-sm-2 col-sm-offset-1" style="margin-top: 7px;">
											<input id="gritter-light" <?php echo $f_check; ?> value="friday" name="opening_days[]" type="checkbox" class="ace ace-switch ace-switch-5">
											<span class="lbl middle"></span>
										</div> 
										
										<label class="col-sm-1 control-label no-padding-right" for="form-field-1">Saturday</label>
										<div class="col-sm-2 col-sm-offset-1" style="margin-top: 7px;">
											<input id="gritter-light" <?php echo $sa_check; ?> value="saturday" name="opening_days[]" type="checkbox" class="ace ace-switch ace-switch-5">
											<span class="lbl middle"></span>
										</div>
									</div>
									
									<div class="form-group">
										<label class="col-sm-1 control-label no-padding-right" for="form-field-1">Sunday</label>
										<div class="col-sm-2 col-sm-offset-1" style="margin-top: 7px;">
											<input id="gritter-light" <?php echo $su_check; ?> value="sunday" name="opening_days[]" type="checkbox" class="ace ace-switch ace-switch-5">
											<span class="lbl middle"></span>
										</div>
									</div>

									
								
									<div class="row">
										
										<hr/>
										
										<div class="form-actions center">
											<button class="btn btn-info">
												<i class="ace-icon fa fa-check bigger-110"></i>
												Submit
											</button>
											
										</div>

									</div>
										
										<input type="hidden" name="id" value="<?php echo $company['id']; ?>" />
								</form>

								<!-- PAGE CONTENT ENDS -->
							</div><!-- /.col -->
						</div><!-- /.row -->
					</div><!-- /.page-content -->
				</div>
			</div><!-- /.main-content -->

		</div><!-- /.main-container -->

<?php 
if(empty($company['lat']) && empty($company['longitude'])){
	$latitude = "33.7204997";
		$longitude = "73.04052769999998";
}else{
	$latitude = $company['lat'];
		$longitude = $company['longitude'];
}

?>

		<script>
	var markers = [];
      function initAutocomplete() {
      	<?php
      	$latitude; 
      	$longitude; 
        ?>
        var map = new google.maps.Map(document.getElementById('mapp'), {
          zoom: 12,
	  center: {lat: <?php echo $latitude; ?>, lng: <?php echo $longitude; ?>}
        });
        
        var myLatlng = new google.maps.LatLng(<?php echo $latitude; ?>,<?php echo $longitude; ?>);
	addMarker(myLatlng,map);
	
        // Create the search box and link it to the UI element.
        var input = document.getElementById('search_box');
        var searchBox = new google.maps.places.SearchBox(input);
        map.controls[google.maps.ControlPosition.TOP_LEFT].push(input);

        // Bias the SearchBox results towards current map's viewport.
        map.addListener('bounds_changed', function() {
          searchBox.setBounds(map.getBounds());
        });

        map.addListener('click', function(event) {
        	addMarker(event.latLng,map);
        });
        // Adds a marker to the map and push to the array.
	
        
        // Listen for the event fired when the user selects a prediction and retrieve
        // more details for that place.
        searchBox.addListener('places_changed', function() {
          var places = searchBox.getPlaces();

          if (places.length == 0) {
            return;
          }

          // Clear out the old markers.
          markers.forEach(function(marker) {
            marker.setMap(null);
          });
          markers = [];

          // For each place, get the icon, name and location.
          var bounds = new google.maps.LatLngBounds();
          places.forEach(function(place) {
            if (!place.geometry) {
              console.log("Returned place contains no geometry");
              return;
            }
            
            
            // Create a marker for each place.
            markers.push(new google.maps.Marker({
              map: map,
              icon: 'https://khaadim.com/img/newmarpmarker.png',
              title: place.name,

              position: place.geometry.location
            }));
            
            update_latlng(place.geometry.location);
            
            if (place.geometry.viewport) {
              // Only geocodes have viewport.
              bounds.union(place.geometry.viewport);
            } else {
              bounds.extend(place.geometry.location);
            }
          });
          map.fitBounds(bounds);
        });
      }
      
      function addMarker(location,map) {
		for (var i = 0; i < markers.length; i++) {
			markers[0].setMap(null);
		}
		markers = [];
		var marker = new google.maps.Marker({
			position: location,
			icon: '',
			map: map
		});
		markers.push(marker);
		
		update_latlng(location);
	}
	
	function update_latlng(location) { 
		document.getElementById('latitude').value=location.lat();
		document.getElementById('longitude').value=location.lng();
	}

    </script>
    
    

<script src="https://maps.googleapis.com/maps/api/js?key=AIzaSyB9EPIEhD6cT-JutxmsvFkYcmd-__UJxj4&libraries=places&callback=initAutocomplete"
         async defer>
         
</script> 

<?php
	$this->load->view('en/include/footer');
?>

<?php
	$this->load->view('en/include/js');
?>

		<!-- inline scripts related to this page -->




<script type="text/javascript">

  document.getElementById("bname").focus();

  function showPreview(objFileInput) {
    if (objFileInput.files[0]) {
        var fileReader = new FileReader();
        fileReader.onload = function (e) {
            $("#targetLayer").html('<img src="'+e.target.result+'" width="200px" height="200px" class="upload-preview" />');
			$("#targetLayer").css('opacity','0.7');
			$(".icon-choose-image").css('opacity','0.5');
        }
		fileReader.readAsDataURL(objFileInput.files[0]);
    }
}

 

</script>



	
<!-- start editor  -->

		<!-- page specific plugin scripts -->
		
		
		<!-- end editor -->
	</body>
</html>
