
<script type="text/javascript">

$('#country').on('change', function() {
	var country_id= $(this).val();
		if(country_id=='')
		{
			$("#city").html('<option> Select Country First... </option>');
			return false;
		}
		var request = $.ajax({
		  url: "<?php echo SURL ?>/area/get_city",
		  type: "POST",
		  data: {country_id:country_id},
		  dataType: "html"
		});
		request.done(function(msg) {
		   $("#city").html(msg);

		});
		request.fail(function(jqXHR, textStatus) {
		  alert( "Request failed: " + textStatus );
		});
});

$('#city').on('change', function() {
	var city_id= $(this).val();
		if(city_id=='')
		{
			$("#area").html('<option> Select City First... </option>');
			return false;
		}
		var request = $.ajax({
		  url: "<?php echo SURL ?>/customer/get_area",
		  type: "POST",
		  data: {city_id:city_id},
		  dataType: "html"
		});
		request.done(function(msg) {
		   $("#area").html(msg);

		});
		request.fail(function(jqXHR, textStatus) {
		  alert( "Request failed: " + textStatus );
		});
});

</script>