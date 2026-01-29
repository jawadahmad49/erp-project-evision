
<script type="text/javascript">

 $(document).ready(function(){
  $("#submit_id").click(function(event){


$.validator.addMethod("letters", function(value, element) {
  return this.optional(element) || value == value.match(/^[a-zA-Z\s]*$/);
});
$.validator.addMethod("alphanum", function(value, element) {
  return this.optional(element) || value == value.match(/^[A-Za-z\s][A-Za-z0-9\s]+$/);
});

$.validator.addMethod("letterspaces", function(value, element) {
  return this.optional(element) || value == value.match(/^[A-Za-z\s][A-Za-z\s]+$/);
});

$.validator.addMethod("mobile", function(value, element) {
  return this.optional(element) || value == value.match(/\(?([0-9]{3})\)?([ .-]?)([0-9]{3})\2([0-9]{4})/
);
});
$.validator.addMethod("specialch", function(value, element) {
  return this.optional(element) || value == value.match(/^[a-zA-Z0-9\[\]\.\-#!_%.|+$&:'"/@]*$/);
});
$.validator.addMethod("number", function(value, element) {
  return this.optional(element) || value == value.match(/^(?=.*\d)\d*(?:\.\d\d)?$/);
});



   var validator = $("#registration_form_id").validate({
  rules: {
    restaurant_name: {
      required: true,
      minlength: 3,
      maxlength:50,
      alphanum: true
    },
    owner_name: {
      required: true,
      minlength: 3,
      maxlength:50,
      letterspaces: true
    },
    contact_person: {
      required: true,
      minlength: 3,
      letterspaces: true
    },
    registration: {
      required: true,
      minlength: 3,
      specialch: true
    },
    vat: {
      required: true,
      minlength: 3,
      specialch: true
    },
    fax: {
      required: true,
      minlength: 3
    },
    
    address: {
      required: true,
      minlength: 3
    },
    area: {
      required: true,
      minlength: 1,
      alphanum: true
    },
    postal_code: {
      required: true,
      minlength: 3,
      alphanum: true
    },
    home_radius: {
      required: true,
      minlength: 1,
      number: true
    },
    mimimum_amount: {
      required: true,
      minlength: 1,
      number: true
    },
    website_name: {
      required: true,
      minlength: 3,
      letters: true
    },
    mobile_1: {
      required: true    
  	},

    phone_1: {
      required: true    
  	},

    email: {
      required: true,
      email: true
    }

    },
  messages: {
    restaurant_name: "Please enter valid restaurant name (Min length 3 charactor and Max length 50 , special characters are not allowed)",
    owner_name: "Please enter valid owner name (Number and special characters are not allowed)",
    contact_person: "Please enter valid contact person name (Number and special characters are not allowed)",
    registration: "Please enter valid registration number",
    vat: "Please enter valid vat",
    fax: "Please enter valid fax", 
    email: "Please enter valid email address",
    address: "Please enter valid address",
    area: "Please choose area", 
    mobile_1: "Please enter mobile number", 
    phone_1: "Please enter phone number", 
    postal_code: "Please enter valid postal code",
    mimimum_amount: "Please enter valid mimimum amount",
    website_name: "Please enter valid website name",
    home_radius: "Please enter valid home radius"
  },
   });


window.password_match(event);
window.confirm_password(event);
window.email_exist(event);
window.website_exist(event);

  });
 });

/*
var $form = $("#registration_form_new_id_idsss");
  $successMsg = $(".alert");

$.validator.addMethod("letters", function(value, element) {
  return this.optional(element) || value == value.match(/^[a-zA-Z\s]*$/);
});
$.validator.addMethod("alphanum", function(value, element) {
  return this.optional(element) || value == value.match(/^[A-Za-z\s][A-Za-z0-9\s]+$/);
});

$.validator.addMethod("letterspaces", function(value, element) {
  return this.optional(element) || value == value.match(/^[A-Za-z\s][A-Za-z\s]+$/);
});

$.validator.addMethod("mobile", function(value, element) {
  return this.optional(element) || value == value.match(/\(?([0-9]{3})\)?([ .-]?)([0-9]{3})\2([0-9]{4})/
);
});
$.validator.addMethod("specialch", function(value, element) {
  return this.optional(element) || value == value.match(/^[a-zA-Z0-9\[\]\.\-#!_%.|+$&:'"/@]*$/);
});
$.validator.addMethod("number", function(value, element) {
  return this.optional(element) || value == value.match(/^(?=.*\d)\d*(?:\.\d\d)?$/);
});
$form.validate({
  rules: {
    restaurant_name: {
      required: true,
      minlength: 3,
      maxlength:50,
      alphanum: true
    },
    owner_name: {
      required: true,
      minlength: 3,
      maxlength:50,
      letterspaces: true
    },
    contact_person: {
      required: true,
      minlength: 3,
      letterspaces: true
    },
    registration: {
      required: true,
      minlength: 3,
      specialch: true
    },
    vat: {
      required: true,
      minlength: 3,
      specialch: true
    },
    fax: {
      required: true,
      minlength: 3
    },
    
    address: {
      required: true,
      minlength: 3
    },
    area: {
      required: true,
      minlength: 1,
      alphanum: true
    },
    postal_code: {
      required: true,
      minlength: 3,
      alphanum: true
    },
    home_radius: {
      required: true,
      minlength: 1,
      number: true
    },
    mimimum_amount: {
      required: true,
      minlength: 1,
      number: true
    },
    website_name: {
      required: true,
      minlength: 3,
      letters: true
    },
    mobile_1: {
      required: true    
  	},

    phone_1: {
      required: true    
  	},

    email: {
      required: true,
      email: true
    }
    
      },
  messages: {
  	
    restaurant_name: "Please enter valid restaurant name (Min length 3 charactor and Max length 50 , special characters are not allowed)",
    owner_name: "Please enter valid owner name (Number and special characters are not allowed)",
    contact_person: "Please enter valid contact person name (Number and special characters are not allowed)",
    registration: "Please enter valid registration number",
    vat: "Please enter valid vat",
    fax: "Please enter valid fax", 
    email: "Please enter valid email address",
    address: "Please enter valid address",
    area: "Please choose area", 
    mobile_1: "Please enter mobile number", 
    phone_1: "Please enter phone number", 
    postal_code: "Please enter valid postal code",
    mimimum_amount: "Please enter valid mimimum amount",
    website_name: "Please enter valid website name",
    home_radius: "Please enter valid home radius"
    
  },
  submitHandler: function() {
    $successMsg.show();
  }
});
*/

function password_match(event)
{

	var password= $('#password').val();
	var comfirm_password= $('#conformpassword').val();
	if(password!=comfirm_password)
	{
		$(".confirm-message").css("display", "block");
		event.preventDefault();
		return false;	
	}
	else
	{
		$(".confirm-message").css("display", "none");
		$(".password-message").css("display", "none");

	}	
}

function confirm_password(event)
{
	var password= $('#password').val();
	var comfirm_password= $('#conformpassword').val();
	if(comfirm_password!=''){
		if(password!=comfirm_password)
		{
			$(".password-message").css("display", "block");
				event.preventDefault();
			return false;	
		}
		else
		{
			$(".confirm-message").css("display", "none");
			$(".password-message").css("display", "none");
		}
	}
}
function email_exist(event)
{
	alert('dddddddd');
	var SURL=<?php echo SURL; ?>;
	var email= $('#email').val();
		var request = $.ajax({
		  url: "SURL/restaurant/email_exist",
		  type: "POST",
		  data: {email:email},
		  dataType: "html"
		});
		request.done(function(msg) {

		if(msg==1)
		{
			$(".email-message").css("display", "block");
				event.preventDefault();

			return false;			
		}
		else if(msg==0)
		{

			$(".email-message").css("display", "none");

		}
	});
	request.fail(function(jqXHR, textStatus) {
	  alert( "Request failed: " + textStatus );
	});


}
function website_exist(event)
{
	var website_name= $('#website_name').val();
		var request = $.ajax({
		  url: "restaurant_ajax.php",
		  type: "POST",
		  data: {website_name:website_name},
		  dataType: "html"
		});

		request.done(function(msg) {

		if(msg==1)
		{
			$(".website_name-message").css("display", "block");
			event.preventDefault();

			return false;			
		}
		else if(msg==0)
		{

			$(".website_name-message").css("display", "none");

		}
	});
	request.fail(function(jqXHR, textStatus) {
	  alert( "Request failed: " + textStatus );
	});

}

$('#country').on('change', function() {

	alert('aaaaaaaaa');
	var country_id= $(this).val();
	if(country_id=='')
	{
		$("#city").html('<option>Select country</option>');
		return false;
	}

		var request = $.ajax({
		  url: "restaurant_ajax.php",
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



	$('#submit_id').on('click', function(e) {

		var country= $('#country').val();
		var city= $('#city').val();
		var logo= $('#logo').val();

		var logo_src= $('#logo_id').attr('src');
		var form_data='<?php echo $form_data; ?>';


		if(logo=='' && form_data =='')
		{
			$(".logo-message").css("display", "block");
			return false;			
		}
		else
		{
			$(".logo-message").css("display", "none");

		}
		if(country=='')
		{
			$(".country-message").css("display", "block");
			return false;			
		}
		else
		{

			$(".country-message").css("display", "none");

		}

		if(city=='')
		{
			$(".city-message").css("display", "block");
			return false;			
		}
		else
		{

			$(".city-message").css("display", "none");

		}

		//e.preventDefault();
	//	return false;

	});


 function checkbox_show(id)
	{
		if($('#'+id).is(':checked'))
		{
			$('.'+id).css("display", "block");
		}
		else
		{
			$('.'+id).css("display", "none");

		}
	}

</script>