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

							<li>
									<a href="<?php echo SURL."Menu_item"; ?>">Item List <?php if($arabic_check=='Yes'){?> (قائمة البند) <?php } ?></a>
							</li>
							<li class="active">Update Items <?php if($arabic_check=='Yes'){?>(تحديث العناصر)<?php } ?></li>
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
								POS 
								<small>
									<i class="ace-icon fa fa-angle-double-right"></i>
									Update Items <?php if($arabic_check=='Yes'){?>(تحديث العناصر)<?php } ?>
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
<style type="text/css">
    .bar_code_check{
        display: none;
        color: red;
    }
</style>
								<div class="row">
									<div class="col-sm-10">
<?php   $_SESSION["itemcode"]=$record['itemcode'];  ?>
								<form id="formID" class="form-horizontal" role="form" method="post" action="<?php echo SURL."Menu_item/update"?>" enctype="multipart/form-data" onsubmit="return validation();">
									<?php if($arabic_check=='Yes'){?> 
									<div class="form-group" id="language">
		<label class="col-sm-3 control-label no-padding-right" for="form-field-1"> Language </label>

		<div class="col-sm-3" style="margin-top: 8px;">
		<input type="radio" onclick="english_lang()" checked="checked" name="lang" id="english">
		English
	    <input style="margin-left: 2%;" type="radio" onclick="urdu_lang()" name="lang" id="urdu">Arabic
		</div>
		</div>
	<?php }?>

	                                <div class="form-group">
										
										<label class="col-sm-2 control-label no-padding-right" for="form-field-1">Bar Code <?php if($arabic_check=='Yes'){?> (الرمز الشريطي)<?php } ?></label>

										<div class="col-sm-3">
											<input maxlength="15" autofocus type="text" name="bar_code" id="bar_code" placeholder="Bar Code" class=" col-xs-12 col-sm-5"    style="width: 100%;" value="<?php echo $record['bar_code'] ?>" onchange="check_bar_code()"/>
										</div>
										<label class="col-sm-1 control-label no-padding-right" for="form-field-1">Short Code <?php if($arabic_check=='Yes'){?>(رمز قصير)<?php } ?></label>

										<div class="col-sm-3">
											<input maxlength="15"  type="text" name="short_code" placeholder="Short Code" class=" col-xs-12 col-sm-5" style="width: 100%;" value="<?php echo $record['short_code'] ?>" />
										</div>
									</div>
									<div class="form-group">
										
										<label class="col-sm-2 control-label no-padding-right " for="form-field-1">Class name <?php if($arabic_check=='Yes'){?> (اسم الفصل)<?php } ?></label>


										<div class="col-sm-3">
								
													<select class="chosen-select form-control" required  name="classname" id="classname" onchange="get_menu(this.value)">
												<option value="">Choose Class Name</option>
												<?php foreach($class_name as $key=>$value){?>
													<option <?php if($record['classcode'] == $value['classcode']){ echo "selected";}?> value="<?php echo $value['classcode']; ?>"><?php echo $value['classname']; ?></option>
												<?php } ?>	
											</select>
										
										</div>
									</div>
									<div class="form-group">
										
										<label class="col-sm-2 control-label no-padding-right" for="form-field-1">Category name <?php if($arabic_check=='Yes'){?> (القائمة الرئيسية)<?php } ?></label>

										<div class="col-sm-3">
											 <select class="form-control userid" name="itemcode" required id="itemcode" data-placeholder="Choose User..." autofocus >
														
											
													</select>
										
										</div>
											<div class="col-sm-5" style="margin-left: 50%; margin-top: -7%;">
								
																			<?php 
				if(!empty($record['image_path'])){
			?>		
<img style="width: 150px; height: 100px;"  id="image" src="<?php echo IMG .'menu/'.$record['image_path']; ?>">
												<?php } ?>

									</div>
									
							
							
									</div>
									<div class="form-group">
									<div class="col-sm-12" style="margin-left: 50%;">
										<input type="file" name="file" id="files" class="col-xs-12 col-sm-5"  accept="image/x-png,image/gif,image/jpeg"  />
											<input type="hidden" name="old_file" value="<?php echo $record['image_path']; ?>">
							</div>
						</div>
					
							
								
									<div class="form-group">
										<label class="col-sm-2 control-label no-padding-right" for="form-field-1">Bar Code Type <?php if($arabic_check=='Yes'){?> (نوع الرمز الشريطي)<?php } ?></label>

										<div class="col-sm-3">
											<select class="form-control " name="bar_code_type" id="bar_code_type" data-placeholder="Choose Item Type..." onchange="generate_bar_code()">
												<option <?php if($record['bar_code_type']=="Scan"){echo "selected";}?> value="Scan">Scan </option>
												<option <?php if($record['bar_code_type']=="Manual"){echo "selected";}?>  value="Manual">Manual</option>
												
												
												
												
											</select>
										</div>
										<label class="col-sm-1 control-label no-padding-right" for="form-field-1">Item <?php if($arabic_check=='Yes'){?> (بند)<?php } ?></label>

										<div class="col-sm-3">
											<input maxlength="50"  type="text" name="itemname" placeholder="Item Name" class="urdu_class col-xs-12 col-sm-5"   value="<?php echo $record['itemname']?>" required="required"   style="width: 100%;" />
										</div>
										
									</div>
									
										<div class="form-group" > 
										
										
										<label class="col-sm-2 control-label no-padding-right" for="form-field-1">Serial No <?php if($arabic_check=='Yes'){?> (رقم العنصر المسلسل)<?php } ?></label>

										<div class="col-sm-3">
											<input maxlength="15"  type="text" name="item_serial_no" id="item_serial_no" placeholder="Item Serial Number" class=" col-xs-12 col-sm-5"  pattern="^[0-9 ]*$" title="Only Numbers Allowed"  style="width: 100%;" value="<?php echo $record['item_serial_no'] ?>"/>
										</div>
										<label class="col-sm-1 control-label no-padding-right" for="form-field-1">Unit <?php if($arabic_check=='Yes'){?> (وحدة)<?php } ?></label>

										<div class="col-sm-3">
											<select class="form-control " name="unit" id="unit" data-placeholder="Choose Unit...">
												
												<option <?php if($record['unit']=="NOS"){echo "selected";}?> value="NOS">NOS</option>
												<option <?php if($record['unit']=="Grams"){echo "selected";}?> value="Grams">Grams</option>
												<option <?php if($record['unit']=="KG"){echo "selected";}?>  value="KG">KG</option>
												
											</select>
									</div>

										
									</div>
									<div class="form-group">
										<label class="col-sm-2 control-label no-padding-right" for="form-field-1">Retail Sale Rate <?php if($arabic_check=='Yes'){?>(معدل البيع بالتجزئة)<?php } ?></label>
										
										<div class="col-sm-3">
											<input maxlength="50"  type="text" name="saleprice" placeholder="Sale Rate" class=" col-xs-12 col-sm-5"  onkeypress='return /[0-9 .]/i.test(event.key)'  value="<?php echo $record['saleprice'] ?>" style="width: 100%;" />
										</div>

										<label class="col-sm-1 control-label no-padding-right" for="form-field-1">WholeSale Rate <?php if($arabic_check=='Yes'){?>(سعر البيع الكامل)<?php } ?></label>

										<div class="col-sm-3">
											<input maxlength="10"  type="text" name="whole_sale_rate" placeholder="Retail Sale Rate" class=" col-xs-12 col-sm-5"  onkeypress='return /[0-9 .]/i.test(event.key)'  style="width: 100%;" value="<?php echo $record['whole_sale_rate'] ?>"/>
										</div>
									</div>
								
										
									

								
									
									<div class="form-group">
										<label class="col-sm-2 control-label no-padding-right" for="form-field-1">Description <?php if($arabic_check=='Yes'){?> (وصف)<?php } ?></label>

										<div class="col-sm-3">
											<textarea type="text" id="description" name="description" maxlength="200" class="urdu_class validate[] text-input" style="width:100%;" spellcheck="false" ><?php echo $record['description']; ?></textarea>
										</div>
										<label class="col-sm-1 control-label no-padding-right" for="form-field-1">Select Status <?php if($arabic_check=='Yes'){?> (حدد الحالة
)<?php } ?></label>


										<div class="col-sm-3">
											<select class="  form-control" name="status" id="status" data-placeholder="Choose a Status...">
												
												<option <?php if($record['status']=="Active"){echo "selected";}?> value="Active">Active</option>
												<option <?php if($record['status']=="InActive"){echo "selected";}?>  value="InActive">InActive</option>
												
											</select>
										</div>
									</div>
									
								
									<div class="form-group" style="margin-left: 2%;">
										
										
										<div class="form-actions center">
											<button class="btn btn-info" style="margin-left: -20%;">
												<i class="ace-icon fa fa-check bigger-110"></i>
												Submit <?php if($arabic_check=='Yes'){?> (إرسال)<?php } ?>
											</button>
											
										</div>

									</div>
									</div>
								
								</div>
									<input type="hidden" id="edit" name="id" value="<?php echo $record['materialcode'] ?>" />	
										
								</form>

								<!-- PAGE CONTENT ENDS -->
							</div><!-- /.col -->
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

		<!-- inline scripts related to this page -->
<script type="text/javascript">
  document.getElementById("classcode").focus();
</script>
<script>
	document.getElementById("img").onchange = function () {
    var reader = new FileReader();

    reader.onload = function (e) {
        // get loaded data and render thumbnail.
        document.getElementById("image").src = e.target.result;
    };

    // read the image file as a data URL.
    reader.readAsDataURL(this.files[0]);
};
</script>
<script>
	document.getElementById("files").onchange = function () {
    var reader = new FileReader();

    reader.onload = function (e) {
        // get loaded data and render thumbnail.
        document.getElementById("image").src = e.target.result;
    };

    // read the image file as a data URL.
    reader.readAsDataURL(this.files[0]);
};
</script>
<?php $this->load->view('en/include/paymentreceipt_js.php'); ?>
<script src="http://ajax.googleapis.com/ajax/libs/jquery/1.2.6/jquery.js" type="text/javascript"></script>
<script src="https://ajax.googleapis.com/ajax/libs/jqueryui/1.12.1/jquery-ui.min.js"></script>
<script src="<?php echo SURL ?>assets/js/jquery.UrduEditor.js" type="text/javascript"></script>  
<script type="text/javascript">

 var test_final = jQuery.noConflict($);

  $(document).ready(function($) {

  jQuery(".urdu_class").each(function( index ) {
  	
      test_final(this).UrduEditor();
      setEnglish($(this));
      jQuery(this).removeAttr('dir');

  });
}); 
  
  function english_lang() { 
    
    jQuery(".urdu_class").each(function( index ) {

       jQuery(this).removeAttr('dir');
      setEnglish(jQuery(this));

  });
}
  function urdu_lang() {
//alert('asd');
    jQuery(".urdu_class").each(function( index ) {

      jQuery(this).attr("dir", "rtl");

      setUrdu(jQuery(this));

  });
  
}

</script>
<script type="text/javascript">
	get_menu();
		function get_menu() {
	    //alert('sasda');
		var classname= $('#classname').val();
		//alert(itemcode);
	     $.ajax({
		  url: "<?php echo SURL."Menu_item/get_detail";?>",
		  cache: false,
		  type: "POST",
		  data: {classname : classname},
		  success: function(html){
		  	//alert(html);
		   $("#itemcode").html(html);
		   
		  }
		});
	 }
	 	function get_menuu(classname) {
	    //alert('sasda');
		//var classname= $('#classname').val();
		//alert(itemcode);
	     $.ajax({
		  url: "<?php echo SURL."Menu_item/get_detail";?>",
		  cache: false,
		  type: "POST",
		  data: {classname : classname},
		  success: function(html){
		  	//alert(html);
		   $("#itemcode").html(html);
		   
		  }
		});
	 }
	  function generate_bar_code() {
	    
		var bar_code_type= $('#bar_code_type').val();
		var classname= $('#classname').val();
		var itemcode= $('#itemcode').val();
		var edit= $('#edit').val();
		if (bar_code_type=='Manual') {
	     $.ajax({
		  url: "<?php echo SURL."Menu_item/generate_bar_code";?>",
		  cache: false,
		  type: "POST",
		  data: {classname : classname,itemcode : itemcode},
		  success: function(html){
		  	//alert(html);
		  	var bar_code= $('#bar_code').val();
        if (edit=='') {
		   $("#bar_code").val(html);
		}
		   
		  }
		
		});
	 }

	 }
</script>
<script type="text/javascript">

		function check_bar_code() {
	    //alert('sasda');
		var bar_code= $('#bar_code').val();
		//alert(bar_code);
	     $.ajax({
		  url: "<?php echo SURL."Menu_item/check_bar_code";?>",
		  cache: false,
		  type: "POST",
		  data: {bar_code : bar_code},
		  success: function(html){
		  	//alert(html);
		   if(html!='') {
           var obj = jQuery.parseJSON(html);
            $("#itemname").val(obj.itemname);
		  	$("#classname").val(obj.classcode);
		  	get_menuu(obj.classcode);
			$("#itemcode").val(obj.catcode);
			$("#bar_code_type").val(obj.bar_code_type);
			$("#short_code").val(obj.short_code);
			$("#item_serial_no").val(obj.item_serial_no);
			$("#unit").val(obj.unit);
			$("#saleprice").val(obj.saleprice);
			$("#whole_sale_rate").val(obj.whole_sale_rate);
			$("#description").val(obj.description);
			$("#status").val(obj.status);

            }
           
		   
		  }
		});
	 }
</script>
<script type="text/javascript">

		function check_bar_code() {
	    //alert('sasda');
		var bar_code= $('#bar_code').val();
		//alert(bar_code);
	     $.ajax({
		  url: "<?php echo SURL."Menu_item/check_bar_code";?>",
		  cache: false,
		  type: "POST",
		  data: {bar_code : bar_code},
		  success: function(html){
		   if(html==1) {

                $('.bar_code_check').css('display','block');
                $('#bar_code').focus();
                e.preventDefault();

            }
            else
            {
                $('.bar_code_check').css('display','none');
            }
		   
		  }
		});
	 }
</script>
<script type="text/javascript">
  
   function validation() {
   	//alert('asd');
var retail_sale_rate = $("#saleprice").val();

var whole_sale_rate = $("#whole_sale_rate").val();

if (whole_sale_rate=='' && retail_sale_rate=='' ) {
	alert("Please Enter Rate");
	$("#saleprice").focus();
	return false;

}
   }
</script>

	</body>
</html>
