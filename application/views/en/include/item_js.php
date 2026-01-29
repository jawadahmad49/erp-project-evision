
<script type="text/javascript">

$('#clas').on('change', function() {
	var class_id= $(this).val();
		if(class_id=='')
		{
			$("#category").html('<option>Choose class first</option>');
			return false;
		}
		var request = $.ajax({
		  url: "<?php echo SURL ?>/item/get_category",
		  type: "POST",
		  data: {class_id:class_id},
		  dataType: "html"
		});
		request.done(function(msg) {
		   $("#category").html(msg);

		});
		request.fail(function(jqXHR, textStatus) {
		  alert( "Request failed: " + textStatus );
		});
});


</script>