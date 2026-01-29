<script type="text/javascript">

var count = 1;
function checking(){
	 var customer=document.getElementById('customer').value;
	 var nar = document.getElementById('nar').value;
	 var cdate = document.getElementById('cdate').value;
	 var cno = document.getElementById('cno').value;
	 var cr =  document.getElementById('cr').value;
	 var dr =document.getElementById('dr').value;
	 var name= $('#customer :selected').text();
	 var s_count = $('#sr_count').val();

	 if(s_count!='' || s_count!=0){
	 	count = parseInt(s_count)+1;
	 	$('#sr_count').val('');
	 }else{
	 	count++;
	 }

	 if(isNaN(dr) || isNaN(cr)){
	 	alert("Only letters are allowed. Please type again.");
	 	$('#dr').focus();
	 	return false;
	 }

	 if(dr==0 && cr==0){
	 	alert("Please enter one Debit or Credit. Please type again.");
	 	$('#dr').focus();
	 	return false;
	 }

	 if(customer=="" || dr=="" || cr==""){
	 	alert("Pleae fill the required fields");
	 }else{
	 	//count++;
	 	var tr = '<tr id="row_'+ (count) +'" class="i_row"><td><input style="width:50px;" type="text" id="srno_'+ (count) +'" readonly name="srno[]" value="'+count+'"></td><td style="width:350px;"><select class="form-control" name="customer[]" id="cus_'+ (count) +'" data-placeholder="Choose a Item..." readonly="readonly" required="required"><option value="'+customer+'">'+name+'</option></select></td><td><input style="width: 500px;" type="text" id="nar_'+ (count) +'" readonly value="'+nar+'" name="nar[]"></td><td> <input   id="cdate_'+ (count) +'"  name="cdate[]" value="'+cdate+'" type="hidden" />   <input type="hidden" id="cno_'+ (count) +'" readonly name="cno[]" value="'+cno+'"> <input style="width:80px;" type="text" id="dr_'+ (count) +'" readonly name="debit[]" class="debit" onchange="checkdrcr(\'dr\','+(count)+')" value="'+dr+'"></td><td><input style="width:80px;" type="text" class="credit" id="cr_'+ (count) +'" onchange="checkdrcr(\'cr\','+(count)+')" name="credit[]" readonly value="'+cr+'"></td><td><input type="button" id="edit_row_'+ (count) +'" value="Edit" class="btn btn-xs btn-success" onclick="edit_this_row(&#39;'+count+'&#39;)"><input type="button" id="save_button_'+ (count) +'" value="Save" class="btn btn-xs btn-warning" onclick="savechecking_row(&#39;'+count+'&#39;)" style="display:none"><input type="button" id="del_'+ (count) +'" value="Delete" onclick="delete_this_row(&#39;'+count+'&#39;)" class="btn btn-xs btn-danger"></td></tr>';

			 $('#inserted_data').show();
			 var ins = $(tr).insertBefore("#exist_rec_sb");

			 var db = $(".debit").val();

			 var calculated_total_sum = 0;
     
	       $("#inserted_data .debit").each(function () {
	           var get_textbox_value = $(this).val();
	           if ($.isNumeric(get_textbox_value)) {
	              calculated_total_sum += parseFloat(get_textbox_value);
	            }                  
            });
              //$("#total_sum_value").html(calculated_total_sum);

			 $('#show_debit_total').text(calculated_total_sum);
			 $('#d_total').val(calculated_total_sum);


			 var total_cr =0;
			 $("#inserted_data .credit").each(function () {
	           var get_total_cr = $(this).val();
	           if ($.isNumeric(get_total_cr)) {
	              total_cr += parseFloat(get_total_cr);
	            }                  
            });

			  $('#show_credit_total').text(total_cr);
			  $('#c_total').val(total_cr);

			  if(total_cr!="" || total_cr!=0 || total_cr>calculated_total_sum){
			  	var rem_cr = total_cr - calculated_total_sum;
			  	
			  	if(rem_cr<0){
			  		res = Math.abs(rem_cr);
			  		$('#cr').val(res);
			  		$('#dr').val('0');
			  	}else{
			  		$('#dr').val(rem_cr);
			  		$('#cr').val('0');
			  	}
			  	
			  }else if(calculated_total_sum!=0 || calculated_total_sum!="" || calculated_total_sum>total_cr){
			  	var rem_dr = calculated_total_sum - total_cr;
			  	
			  	if(rem_dr<0){
			  		res = Math.abs(rem_dr);
			  		$('#dr').val(res);
			  		$('#cr').val('0');
			  	}else{
			  		$('#cr').val(rem_dr);
			  		$('#dr').val('0');
			  	}
			  }

			 $('#nar').val('');
			 $('#cno').val('');
			 $('#dr').removeAttr('readonly');
			 $('#cr').removeAttr('readonly');
			 
		
	 }
	 
	 
 $(document).ready(function(){
    $("#customer").chosen();
    $('#customer a.chosen-single').focus();
});
}

function checkdrcr(ty,id)

{
 
if(ty=='dr'){
document.getElementById('cr_'+id).value=0;
}else{
	
document.getElementById('dr_'+id).value=0;
}
 
}
function check_saving(){
	var d_total = $('#d_total').val();
	var c_total = $('#c_total').val();

	$('#dr').val('');
	$('#cr').val('');

	if(d_total!=c_total){
		alert("Debit Must be equal to credit.");
		return false;
	}else if(d_total==0 || d_total=="" && c_total==0 || c_total==""){
		alert("Please insert item to continue the process.");
	}else if(d_total==c_total && d_total!="" && c_total!=""){
		$('#cr').prop('required',false);
		$('#dr').prop('required',false);

		$('#button_submit').prop("type", "submit");

		
		// $("#form").submit();
		// $('#button_submit').attr("disabled",true);
	}

}

function edit_this_row(id){
	
	
	
	$('#nar_'+id).removeAttr('readonly');

	var dr = $('#dr_'+id).val();
	var cr = $('#cr_'+id).val();

	var dr_total = $('#d_total').val();
	var cr_total = $('#c_total').val();

	var total_cr_remaining="";
	var total_dr_remaining="";

	if(dr!="" && dr!=0){
		$('#dr_'+id).removeAttr('readonly');
		//$('#cr_'+id).prop('readonly',true);
		var total_dr_remaining = dr_total - dr;
		$('#d_total').val(total_dr_remaining);
	} 
	
	if(cr!="" && cr!=0){
		$('#cr_'+id).removeAttr('readonly');
		var total_cr_remaining = cr_total - cr;
		$('#c_total').val(total_cr_remaining);
	}

	$('#cdate_'+id).removeAttr('readonly');
	$('#cno_'+id).removeAttr('readonly');

	$('#edit_row_'+id).hide();
	$('#del_'+id).hide();
	$('#save_button_'+id).show();
	$('#nar_'+id).select();
	$('#nar_'+id).focus();

}

function savechecking_row(id){
	
	
	var c_val =  $('#cr_'+id).val();
	var d_val =  $('#dr_'+id).val();

	
	 
	 if(isNaN(c_val) || isNaN(d_val)){
	 	alert("Only letters are allowed. Please type again.");
	 	$('#dr'+id).focus();
	 	return false;
	 }

	 if(c_val==0 && d_val==0){
	 	alert("Please enter one Debit or Credit. Please type again.");
	 	$('#dr'+id).focus();
	 	return false;
	 }

	 if( c_val=="" || d_val==""){
	 	alert("Pleae fill the required fields");
	 return false;
	 }
	
	
	$('#nar_'+id).prop('readonly',true);
	$('#dr_'+id).prop('readonly',true);
	$('#cr_'+id).prop('readonly',true);
	$('#cdate_'+id).prop('readonly',true);
	$('#cno_'+id).prop('readonly',true);

	
	
	
	
	
	
	
	
	
	
	
	
	if(d_val==0 || d_val==""){
		var total_cr_remaining =  $('#c_total').val();
		total_cr_sum = parseInt(total_cr_remaining) + parseInt(c_val);
		$('#show_credit_total').text(total_cr_sum);
		$('#c_total').val(total_cr_sum);
	}else if(c_val==0 || c_val==""){
		var total_dr_remaining =  $('#d_total').val();	
		total_dr_sum = parseInt(total_dr_remaining) + parseInt(d_val);
		$('#show_debit_total').text(total_dr_sum);
		$('#d_total').val(total_dr_sum);
	}

	$('#edit_row_'+id).show();
	$('#del_'+id).show();
	$('#save_button_'+id).hide();	
}

function delete_this_row(id){
	var dr = $('#dr_'+id).val();
	var cr = $('#cr_'+id).val();

	var dr_total = $('#d_total').val();
	var cr_total = $('#c_total').val();

	var total_cr_remaining="";
	var total_dr_remaining="";

	if(dr!="" || dr!=0){
		var total_dr_remaining = dr_total - dr;
		$('#d_total').val(total_dr_remaining);
		$('#show_debit_total').text(total_dr_remaining);

		var d_r = cr_total - total_dr_remaining;
		if(d_r<0){
			//alert(d_r);
	  		res = Math.abs(d_r);
	  		$('#cr').val(res);
	  		$('#dr').val('0');
	  	}else if(d_r>0){
	  		$('#dr').val(d_r);
	  		$('#cr').val('0');
	  	}
	}

	if(cr!="" || cr!=0){
		var total_cr_remaining = cr_total - cr;
		$('#c_total').val(total_cr_remaining);
		$('#show_credit_total').text(total_cr_remaining);

		var c_r = dr_total - total_cr_remaining;
		//alert(c_r);
		if(c_r<0){
	  		res = Math.abs(c_r);
	  		$('#dr').val(res);
	  		$('#cr').val('0');
	  	}else if(c_r>0){
	  		$('#cr').val(c_r);
	  		$('#dr').val('0');
	  	}
	}

	$('#cus_'+id).remove();
	$('#nar_'+id).remove();
	$('#cno_'+id).remove();
	$('#dr_'+id).remove();
	$('#cr_'+id).remove();

	$('#row_'+id).remove();
	//$('#inserted_data').hide();
}



function setting_submit(){
	$("#id-date-picker-1").prop("disabled", false);
	var location=document.getElementById('location-to').value;
	if(location==''){
		var emailerror=document.getElementById('email-error');
		emailerror.innerHTML="Please provide a customer.";
		return false;
	}
	$('.disable').attr("disabled",false);
	$('.disable').attr("readonly",true);
	$("#itn-item").prop('required',false);
	$("#qty").prop('required',false);
	$("#type_change").prop('type','submit');
}






























	jQuery(function($) {
				$('#id-disable-check').on('click', function() {
					var inp = $('#form-input-readonly').get(0);
					if(inp.hasAttribute('disabled')) {
						inp.setAttribute('readonly' , 'true');
						inp.removeAttribute('disabled');
						inp.value="This text field is readonly!";
					}
					else {
						inp.setAttribute('disabled' , 'disabled');
						inp.removeAttribute('readonly');
						inp.value="This text field is disabled!";
					}
				});
			
			
				if(!ace.vars['touch']) {
					$('.chosen-select').chosen({allow_single_deselect:true}); 
					//resize the chosen on window resize
			
					$(window)
					.off('resize.chosen')
					.on('resize.chosen', function() {
						$('.chosen-select').each(function() {
							 var $this = $(this);
							 $this.next().css({'width': $this.parent().width()});
						})
					}).trigger('resize.chosen');
					//resize chosen on sidebar collapse/expand
					$(document).on('settings.ace.chosen', function(e, event_name, event_val) {
						if(event_name != 'sidebar_collapsed') return;
						$('.chosen-select').each(function() {
							 var $this = $(this);
							 $this.next().css({'width': $this.parent().width()});
						})
					});
			
			
					$('#chosen-multiple-style .btn').on('click', function(e){
						var target = $(this).find('input[type=radio]');
						var which = parseInt(target.val());
						if(which == 2) $('#form-field-select-4').addClass('tag-input-style');
						 else $('#form-field-select-4').removeClass('tag-input-style');
					});
				}
			
			
				$('[data-rel=tooltip]').tooltip({container:'body'});
				$('[data-rel=popover]').popover({container:'body'});
			
				autosize($('textarea[class*=autosize]'));
				
				$('textarea.limited').inputlimiter({
					remText: '%n character%s remaining...',
					limitText: 'max allowed : %n.'
				});
			
				$.mask.definitions['~']='[+-]';
				$('.input-mask-date').mask('99/99/9999');
				$('.input-mask-phone').mask('(999) 999-9999');
				$('.input-mask-eyescript').mask('~9.99 ~9.99 999');
				$(".input-mask-product").mask("a*-999-a999",{placeholder:" ",completed:function(){alert("You typed the following: "+this.val());}});
			
			
			
				$( "#input-size-slider" ).css('width','200px').slider({
					value:1,
					range: "min",
					min: 1,
					max: 8,
					step: 1,
					slide: function( event, ui ) {
						var sizing = ['', 'input-sm', 'input-lg', 'input-mini', 'input-small', 'input-medium', 'input-large', 'input-xlarge', 'input-xxlarge'];
						var val = parseInt(ui.value);
						$('#form-field-4').attr('class', sizing[val]).attr('placeholder', '.'+sizing[val]);
					}
				});
			
				$( "#input-span-slider" ).slider({
					value:1,
					range: "min",
					min: 1,
					max: 12,
					step: 1,
					slide: function( event, ui ) {
						var val = parseInt(ui.value);
						$('#form-field-5').attr('class', 'col-xs-'+val).val('.col-xs-'+val);
					}
				});
			
			
				
				//"jQuery UI Slider"
				//range slider tooltip example
				$( "#slider-range" ).css('height','200px').slider({
					orientation: "vertical",
					range: true,
					min: 0,
					max: 100,
					values: [ 17, 67 ],
					slide: function( event, ui ) {
						var val = ui.values[$(ui.handle).index()-1] + "";
			
						if( !ui.handle.firstChild ) {
							$("<div class='tooltip right in' style='display:none;left:16px;top:-6px;'><div class='tooltip-arrow'></div><div class='tooltip-inner'></div></div>")
							.prependTo(ui.handle);
						}
						$(ui.handle.firstChild).show().children().eq(1).text(val);
					}
				}).find('span.ui-slider-handle').on('blur', function(){
					$(this.firstChild).hide();
				});
				
				
				$( "#slider-range-max" ).slider({
					range: "max",
					min: 1,
					max: 10,
					value: 2
				});
				
				$( "#slider-eq > span" ).css({width:'90%', 'float':'left', margin:'15px'}).each(function() {
					// read initial values from markup and remove that
					var value = parseInt( $( this ).text(), 10 );
					$( this ).empty().slider({
						value: value,
						range: "min",
						animate: true
						
					});
				});
				
				$("#slider-eq > span.ui-slider-purple").slider('disable');//disable third item
			
				
				$('#id-input-file-1 , #id-input-file-2').ace_file_input({
					no_file:'No File ...',
					btn_choose:'Choose',
					btn_change:'Change',
					droppable:false,
					onchange:null,
					thumbnail:false //| true | large
					//whitelist:'gif|png|jpg|jpeg'
					//blacklist:'exe|php'
					//onchange:''
					//
				});
				//pre-show a file name, for example a previously selected file
				//$('#id-input-file-1').ace_file_input('show_file_list', ['myfile.txt'])
			
			
				$('#id-input-file-3').ace_file_input({
					style: 'well',
					btn_choose: 'Drop files here or click to choose',
					btn_change: null,
					no_icon: 'ace-icon fa fa-cloud-upload',
					droppable: true,
					thumbnail: 'small'//large | fit
					//,icon_remove:null//set null, to hide remove/reset button
					/**,before_change:function(files, dropped) {
						//Check an example below
						//or examples/file-upload.html
						return true;
					}*/
					/**,before_remove : function() {
						return true;
					}*/
					,
					preview_error : function(filename, error_code) {
						//name of the file that failed
						//error_code values
						//1 = 'FILE_LOAD_FAILED',
						//2 = 'IMAGE_LOAD_FAILED',
						//3 = 'THUMBNAIL_FAILED'
						//alert(error_code);
					}
			
				}).on('change', function(){
					//console.log($(this).data('ace_input_files'));
					//console.log($(this).data('ace_input_method'));
				});
				
				
				//$('#id-input-file-3')
				//.ace_file_input('show_file_list', [
					//{type: 'image', name: 'name of image', path: 'http://path/to/image/for/preview'},
					//{type: 'file', name: 'hello.txt'}
				//]);
			
				
				
			
				//dynamically change allowed formats by changing allowExt && allowMime function
				$('#id-file-format').removeAttr('checked').on('change', function() {
					var whitelist_ext, whitelist_mime;
					var btn_choose
					var no_icon
					if(this.checked) {
						btn_choose = "Drop images here or click to choose";
						no_icon = "ace-icon fa fa-picture-o";
			
						whitelist_ext = ["jpeg", "jpg", "png", "gif" , "bmp"];
						whitelist_mime = ["image/jpg", "image/jpeg", "image/png", "image/gif", "image/bmp"];
					}
					else {
						btn_choose = "Drop files here or click to choose";
						no_icon = "ace-icon fa fa-cloud-upload";
						
						whitelist_ext = null;//all extensions are acceptable
						whitelist_mime = null;//all mimes are acceptable
					}
					var file_input = $('#id-input-file-3');
					file_input
					.ace_file_input('update_settings',
					{
						'btn_choose': btn_choose,
						'no_icon': no_icon,
						'allowExt': whitelist_ext,
						'allowMime': whitelist_mime
					})
					file_input.ace_file_input('reset_input');
					
					file_input
					.off('file.error.ace')
					.on('file.error.ace', function(e, info) {
						//console.log(info.file_count);//number of selected files
						//console.log(info.invalid_count);//number of invalid files
						//console.log(info.error_list);//a list of errors in the following format
						
						//info.error_count['ext']
						//info.error_count['mime']
						//info.error_count['size']
						
						//info.error_list['ext']  = [list of file names with invalid extension]
						//info.error_list['mime'] = [list of file names with invalid mimetype]
						//info.error_list['size'] = [list of file names with invalid size]
						
						
						/**
						if( !info.dropped ) {
							//perhapse reset file field if files have been selected, and there are invalid files among them
							//when files are dropped, only valid files will be added to our file array
							e.preventDefault();//it will rest input
						}
						*/
						
						
						//if files have been selected (not dropped), you can choose to reset input
						//because browser keeps all selected files anyway and this cannot be changed
						//we can only reset file field to become empty again
						//on any case you still should check files with your server side script
						//because any arbitrary file can be uploaded by user and it's not safe to rely on browser-side measures
					});
					
					
					/**
					file_input
					.off('file.preview.ace')
					.on('file.preview.ace', function(e, info) {
						console.log(info.file.width);
						console.log(info.file.height);
						e.preventDefault();//to prevent preview
					});
					*/
				
				});
			
				$('#spinner1').ace_spinner({value:0,min:0,max:200,step:10, btn_up_class:'btn-info' , btn_down_class:'btn-info'})
				.closest('.ace-spinner')
				.on('changed.fu.spinbox', function(){
					//console.log($('#spinner1').val())
				}); 
				$('#spinner2').ace_spinner({value:0,min:0,max:10000,step:100, touch_spinner: true, icon_up:'ace-icon fa fa-caret-up bigger-110', icon_down:'ace-icon fa fa-caret-down bigger-110'});
				$('#spinner3').ace_spinner({value:0,min:-100,max:100,step:10, on_sides: true, icon_up:'ace-icon fa fa-plus bigger-110', icon_down:'ace-icon fa fa-minus bigger-110', btn_up_class:'btn-success' , btn_down_class:'btn-danger'});
				$('#spinner4').ace_spinner({value:0,min:-100,max:100,step:10, on_sides: true, icon_up:'ace-icon fa fa-plus', icon_down:'ace-icon fa fa-minus', btn_up_class:'btn-purple' , btn_down_class:'btn-purple'});
			
				//$('#spinner1').ace_spinner('disable').ace_spinner('value', 11);
				//or
				//$('#spinner1').closest('.ace-spinner').spinner('disable').spinner('enable').spinner('value', 11);//disable, enable or change value
				//$('#spinner1').closest('.ace-spinner').spinner('value', 0);//reset to 0
			
			
				//datepicker plugin
				//link
				$('.date-picker').datepicker({
					autoclose: true,
					todayHighlight: true
				})
				//show datepicker when clicking on the icon
				.next().on(ace.click_event, function(){
					$(this).prev().focus();
				});
			
				//or change it into a date range picker
				$('.input-daterange').datepicker({autoclose:true});
			
			
				//to translate the daterange picker, please copy the "examples/daterange-fr.js" contents here before initialization
				$('input[name=date-range-picker]').daterangepicker({
					'applyClass' : 'btn-sm btn-success',
					'cancelClass' : 'btn-sm btn-default',
					locale: {
						applyLabel: 'Apply',
						cancelLabel: 'Cancel',
					}
				})
				.prev().on(ace.click_event, function(){
					$(this).next().focus();
				});
			
			
				$('#timepicker1').timepicker({
					minuteStep: 1,
					showSeconds: true,
					showMeridian: false,
					disableFocus: true,
					icons: {
						up: 'fa fa-chevron-up',
						down: 'fa fa-chevron-down'
					}
				}).on('focus', function() {
					$('#timepicker1').timepicker('showWidget');
				}).next().on(ace.click_event, function(){
					$(this).prev().focus();
				});
				
				
			
				
				if(!ace.vars['old_ie']) $('#date-timepicker1').datetimepicker({
				 //format: 'MM/DD/YYYY h:mm:ss A',//use this option to display seconds
				 icons: {
					time: 'fa fa-clock-o',
					date: 'fa fa-calendar',
					up: 'fa fa-chevron-up',
					down: 'fa fa-chevron-down',
					previous: 'fa fa-chevron-left',
					next: 'fa fa-chevron-right',
					today: 'fa fa-arrows ',
					clear: 'fa fa-trash',
					close: 'fa fa-times'
				 }
				}).next().on(ace.click_event, function(){
					$(this).prev().focus();
				});
				
			
				$('#colorpicker1').colorpicker();
				//$('.colorpicker').last().css('z-index', 2000);//if colorpicker is inside a modal, its z-index should be higher than modal'safe
			
				$('#simple-colorpicker-1').ace_colorpicker();
				//$('#simple-colorpicker-1').ace_colorpicker('pick', 2);//select 2nd color
				//$('#simple-colorpicker-1').ace_colorpicker('pick', '#fbe983');//select #fbe983 color
				//var picker = $('#simple-colorpicker-1').data('ace_colorpicker')
				//picker.pick('red', true);//insert the color if it doesn't exist
			
			
				$(".knob").knob();
				
				
				var tag_input = $('#form-field-tags');
				try{
					tag_input.tag(
					  {
						placeholder:tag_input.attr('placeholder'),
						//enable typeahead by specifying the source array
						source: ace.vars['US_STATES'],//defined in ace.js >> ace.enable_search_ahead
						/**
						//or fetch data from database, fetch those that match "query"
						source: function(query, process) {
						  $.ajax({url: 'remote_source.php?q='+encodeURIComponent(query)})
						  .done(function(result_items){
							process(result_items);
						  });
						}
						*/
					  }
					)
			
					//programmatically add/remove a tag
					var $tag_obj = $('#form-field-tags').data('tag');
					$tag_obj.add('Programmatically Added');
					
					var index = $tag_obj.inValues('some tag');
					$tag_obj.remove(index);
				}
				catch(e) {
					//display a textarea for old IE, because it doesn't support this plugin or another one I tried!
					tag_input.after('<textarea id="'+tag_input.attr('id')+'" name="'+tag_input.attr('name')+'" rows="3">'+tag_input.val()+'</textarea>').remove();
					//autosize($('#form-field-tags'));
				}
				
				
				/////////
				$('#modal-form input[type=file]').ace_file_input({
					style:'well',
					btn_choose:'Drop files here or click to choose',
					btn_change:null,
					no_icon:'ace-icon fa fa-cloud-upload',
					droppable:true,
					thumbnail:'large'
				})
				
				//chosen plugin inside a modal will have a zero width because the select element is originally hidden
				//and its width cannot be determined.
				//so we set the width after modal is show
				$('#modal-form').on('shown.bs.modal', function () {
					if(!ace.vars['touch']) {
						$(this).find('.chosen-container').each(function(){
							$(this).find('a:first-child').css('width' , '210px');
							$(this).find('.chosen-drop').css('width' , '210px');
							$(this).find('.chosen-search input').css('width' , '200px');
						});
					}
				})
				/**
				//or you can activate the chosen plugin after modal is shown
				//this way select element becomes visible with dimensions and chosen works as expected
				$('#modal-form').on('shown', function () {
					$(this).find('.modal-chosen').chosen();
				})
				*/
			
				
				
				$(document).one('ajaxloadstart.page', function(e) {
					autosize.destroy('textarea[class*=autosize]')
					
					$('.limiterBox,.autosizejs').remove();
					$('.daterangepicker.dropdown-menu,.colorpicker.dropdown-menu,.bootstrap-datetimepicker-widget.dropdown-menu').remove();
				});
				
				
				
					$('#customer').trigger("chosen:updated");
	var $mySelect= $('#customer');
	$mySelect.chosen();
	$mySelect.trigger('chosen:activate');
			
			});
</script>