	<!-- basic scripts -->

		<!--[if !IE]> -->
		<script src="<?php echo SURL ?>assets/js/jquery-2.1.4.min.js"></script>

		<!-- <![endif]-->

		<!--[if IE]>
<script src="assets/js/jquery-1.11.3.min.js"></script>
<![endif]-->
		<script type="text/javascript">
			if('ontouchstart' in document.documentElement) document.write("<script src='<?php echo SURL ?>assets/js/jquery.mobile.custom.min.js'>"+"<"+"/script>");
		</script>
		<script src="<?php echo SURL ?>assets/js/bootstrap.min.js"></script>

		<!-- page specific plugin scripts -->

		<!--[if lte IE 8]>
		  <script src="assets/js/excanvas.min.js"></script>
		<![endif]-->
		<script src="<?php echo SURL ?>assets/js/jquery-ui.custom.min.js"></script>
		<script src="<?php echo SURL ?>assets/js/jquery.ui.touch-punch.min.js"></script>
		<script src="<?php echo SURL ?>assets/js/chosen.jquery.min.js"></script>
		<script src="<?php echo SURL ?>assets/js/spinbox.min.js"></script>
		<script src="<?php echo SURL ?>assets/js/bootstrap-datepicker.min.js"></script>
		<script src="<?php echo SURL ?>assets/js/bootstrap-timepicker.min.js"></script>
		<script src="<?php echo SURL ?>assets/js/moment.min.js"></script>
		<script src="<?php echo SURL ?>assets/js/daterangepicker.min.js"></script>
		<script src="<?php echo SURL ?>assets/js/bootstrap-datetimepicker.min.js"></script>
		<script src="<?php echo SURL ?>assets/js/bootstrap-colorpicker.min.js"></script>
		<script src="<?php echo SURL ?>assets/js/jquery.knob.min.js"></script>
		<script src="<?php echo SURL ?>assets/js/autosize.min.js"></script>
		<script src="<?php echo SURL ?>assets/js/jquery.inputlimiter.min.js"></script>
		<script src="<?php echo SURL ?>assets/js/jquery.maskedinput.min.js"></script>
		<script src="<?php echo SURL ?>assets/js/bootstrap-tag.min.js"></script>

		<!-- ace scripts -->
		<script src="<?php echo SURL ?>assets/js/ace-elements.min.js"></script>
		<script src="<?php echo SURL ?>assets/js/ace.min.js"></script>

	<script src="https://cdn.jsdelivr.net/npm/jquery-validation@1.17.0/dist/jquery.validate.js"></script>
	<script src="https://cdn.jsdelivr.net/npm/jquery-validation@1.17.0/dist/jquery.validate.min.js"></script>
	<script src="https://cdn.jsdelivr.net/npm/jquery-validation@1.17.0/dist/additional-methods.js"></script>
	<script src="https://cdn.jsdelivr.net/npm/jquery-validation@1.17.0/dist/additional-methods.min.js"></script>

 <script type="text/javascript">
 $("#ace-settings-container").hide();
 //$("#breadcrumbs ul li:nth-child(0)").text('<i class="ace-icon fa fa-home home-icon"></i><a href="<?php echo SURL."admin"; ?>">Home</a>');
 $('#nav-search').hide();
</script>
<script type="text/javascript">
	
	$('#icons1').addClass('fa fa-shopping-cart');
	$("#icons2").addClass("glyphicon glyphicon-euro");
	$("#icons3").addClass("fa fa-pencil-square-o");

	 var test = jQuery.noConflict();

	test(function() {
    test('input[name="daterange"]').daterangepicker({
        locale: {
            //format: 'YYYY-MM-DD',
            format: 'YYYY-MM-DD',"separator": " / "
        }
    });

  //    $('input[name="daterange"]').on('apply.daterangepicker', function(ev, picker) {
  //     $(this).val(picker.startDate.format('YYYY-MM-DD') + ' / ' + picker.endDate.format('YYYY-MM-DD'));
  // });
});



</script>