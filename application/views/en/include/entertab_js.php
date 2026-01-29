<script type="text/javascript">
$('body').on('keydown', 'input, select, textarea', function(e) {
  var self = $(this)
  , form = self.parents('form:eq(0)')
  , focusable
  , next
  ;
  if (e.keyCode == 13) {
    var inputs = $(this).parents("body").eq(0).find(":input:visible:not(:disabled):not([readonly])");
    var idx = inputs.index(this);
	//alert(idx);
	if(idx =='7'){
		 $("#additemsseelct").click();
		  inputs[4].focus();
		  $("#classcodes").val("none");
		   return false;
	}if(idx =='4'){
		
		  if($("#classcodes").val() =="none"){
			 
		$("#discount").focus();
		 return false;
		}else{
			 inputs[idx + 1].focus();
		}
		  
	}
	
	if(idx =='10'){
		 $("#save").click();
	return false;
	}
    if (idx == inputs.length - 1) {
      idx = -1;
    } else {
                    inputs[idx + 1].focus(); // handles submit buttons
                  }
                  try {
                    inputs[idx + 1].select();
                  }
                  catch (err) {

                  }
                  return false;
                }
              });
			  </script>
		