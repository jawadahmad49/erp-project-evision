		<!-- inline scripts related to this page -->
		<script type="text/javascript">
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
			
			});
/* start editor*/
	$('#editor1').ace_wysiwyg({
		toolbar:
		[
			'font',
			null,
			'fontSize',
			null,
			{name:'bold', className:'btn-info'},
			{name:'italic', className:'btn-info'},
			{name:'strikethrough', className:'btn-info'},
			{name:'underline', className:'btn-info'},
			null,
			{name:'insertunorderedlist', className:'btn-success'},
			{name:'insertorderedlist', className:'btn-success'},
			{name:'outdent', className:'btn-purple'},
			{name:'indent', className:'btn-purple'},
			null,
			{name:'justifyleft', className:'btn-primary'},
			{name:'justifycenter', className:'btn-primary'},
			{name:'justifyright', className:'btn-primary'},
			{name:'justifyfull', className:'btn-inverse'},
			null,
			{name:'createLink', className:'btn-pink'},
			{name:'unlink', className:'btn-pink'},
			null,
			{name:'insertImage', className:'btn-success'},
			null,
			'foreColor',
			null,
			{name:'undo', className:'btn-grey'},
			{name:'redo', className:'btn-grey'}
		],
		'wysiwyg': {
			fileUploadError: showErrorAlert
		}
	}).prev().addClass('wysiwyg-style2');

		$('[data-toggle="buttons"] .btn').on('click', function(e){
		var target = $(this).find('input[type=radio]');
		var which = parseInt(target.val());
		var toolbar = $('#editor1').prev().get(0);
		if(which >= 1 && which <= 4) {
			toolbar.className = toolbar.className.replace(/wysiwyg\-style(1|2)/g , '');
			if(which == 1) $(toolbar).addClass('wysiwyg-style1');
			else if(which == 2) $(toolbar).addClass('wysiwyg-style2');
			if(which == 4) {
				$(toolbar).find('.btn-group > .btn').addClass('btn-white btn-round');
			} else $(toolbar).find('.btn-group > .btn-white').removeClass('btn-white btn-round');
		}
	});

		var enableImageResize = function() {
			$('.wysiwyg-editor')
			.on('mousedown', function(e) {
				var target = $(e.target);
				if( e.target instanceof HTMLImageElement ) {
					if( !target.data('resizable') ) {
						target.resizable({
							aspectRatio: e.target.width / e.target.height,
						});
						target.data('resizable', true);
						
						if( lastResizableImg != null ) {
							//disable previous resizable image
							lastResizableImg.resizable( "destroy" );
							lastResizableImg.removeData('resizable');
						}
						lastResizableImg = target;
					}
				}
			})
			.on('click', function(e) {
				if( lastResizableImg != null && !(e.target instanceof HTMLImageElement) ) {
					destroyResizable();
				}
			})
			.on('keydown', function() {
				destroyResizable();
			});
	    }

	function showErrorAlert (reason, detail) {
		var msg='';
		if (reason==='unsupported-file-type') { msg = "Unsupported format " +detail; }
		else {
			//console.log("error uploading file", reason, detail);
		}
		$('<div class="alert"> <button type="button" class="close" data-dismiss="alert">&times;</button>'+ 
		 '<strong>File upload error</strong> '+msg+' </div>').prependTo('#alerts');
	}
	/*End editor*/
		</script>
		<script src="<?php echo SURL; ?>assets/js/bootstrap-wysiwyg.min.js"></script>


		<!-- start add update delte rows using javascript -->
<script type="text/javascript">

$("#securityamt").blur(function(){
  
  var total_security= parseInt($('.security_sum_total_text').text());
  if(total_security==0)
  {
  	  	alert('Receive security is not allowed');
  		$(this).val(total_security);
  }
  var receive_sercurity=$(this).val();
  if(receive_sercurity>total_security)
  {
  	alert('Receive security must be smaller than total security');
  	$(this).val(total_security);
  }

});

$("#returngas").blur(function(){
  
  var amount_sum= parseInt($('#amount_sum_class_id').text());
  var returngas=parseInt($(this).val());
  var returnrate=parseInt($('#returnrate').val());

var total_return=returngas*returnrate;

 
	var gas_amount_sum_total_text=parseInt($('#gas_amount_sum_total_text').text());
	//alert(gas_amount_sum_total_text);




 //alert(total_return+'_________'+amount_sum);
  if(total_return>amount_sum)
  {
  	  	alert('Total return must be smalle than total amount');
  	  	$(this).val(0);
  	  	$('#gasamt').val(gas_amount_sum_total_text);
  	  	return false;
  }
  else
  {
	var gas_amount_sum_total_text=parseInt($('#gas_amount_sum_total_text').text());
	
	var returntotal=parseInt(document.getElementById('returntotal').value);
	
//	var totol_amount=
	document.getElementById('gasamt').value=gas_amount_sum_total_text-returntotal;

	var net_amount=gas_amount_sum_total_text-returntotal;
	  var securityamt=parseInt($('#securityamt').val());

	  $('#totalrecv').val(securityamt+net_amount);

}


});
$("#returnrate").blur(function(){
  
  var amount_sum= parseInt($('#amount_sum_class_id').text());
  var returngas=parseInt($(this).val());
  var returnrate=parseInt($('#returngas').val());

var total_return=returngas*returnrate;
 
  if(total_return>amount_sum)
  {
  	  	alert('Total return must be smalle than total amount');
  	  	$(this).val(0);
  	  	return false;
  }
  else
  {
  	var gas_amount_sum_total_text=parseInt($('#gas_amount_sum_total_text').text());
	
	var returntotal=parseInt(document.getElementById('returntotal').value);
	
	
//	var totol_amount=
	document.getElementById('gasamt').value=gas_amount_sum_total_text-returntotal;

	var net_amount=gas_amount_sum_total_text-returntotal;
	  var securityamt=parseInt($('#securityamt').val());

	  $('#totalrecv').val(securityamt+net_amount);

}

});

function edit_row(no)
{
    $("#edit_button"+no).hide();
    $("#save_button"+no).show();
    
    $("#item_"+no).attr("readonly",false);
    $("#type_change").prop("disabled",true);
    
    var qty=document.getElementById("qty_row"+no);
    var gst=document.getElementById("gst_row"+no);
    var price=document.getElementById("price_row"+no);
    var security=document.getElementById("security_row"+no);
    var returns=document.getElementById("returns_row"+no);
    var filledstock=document.getElementById("filledstock_row"+no);
    var gst_amounttotal=document.getElementById("gst_amounttotal_row"+no);
    var ex_amounttotal=document.getElementById("ex_amounttotal_row"+no);
    var amounttotal=document.getElementById("amounttotal_row"+no);

    var stotal_total=document.getElementById("stotal_row"+no);
  var gtotal_total=document.getElementById("gtotal_row"+no);
    //var amountreceived=document.getElementById("amountreceived_row"+no);

    //$("#item_"+no).prop("disabled", false);
    $("#item_"+no).attr("disabled",true);
	

    //var item_data=item.innerHTML;
    var qty_data=qty.innerHTML;
    var price_data=price.innerHTML;
    var security_data=security.innerHTML;
    var returns_data=returns.innerHTML;
    var filledstock_data=filledstock.innerHTML;
     var amounttotal_data=amounttotal.innerHTML;

    // var stotal_total_data=stotal_total.innerHTML;

    // var gtotal_total_data=gtotal_total.innerHTML;


    //var amountreceived_data=amountreceived.innerHTML;
//console.log();return false;
    //var item_val=document.getElementById("item_text"+no).value;
    var qty_val=document.getElementById("qty_text"+no).value;
    var gst_val=document.getElementById("gst_text"+no).value;
    var price_val=document.getElementById("price_text"+no).value;
    var security_val=document.getElementById("security_text"+no).value;
    var returns_val=document.getElementById("returns_text"+no).value;
    var filledstock_val=document.getElementById("filledstock_text"+no).value;

    var gst_amounttotal_val=document.getElementById("gst_amounttotal_text"+no).value;
    var ex_amounttotal_val=document.getElementById("ex_amounttotal_text"+no).value;
    var amounttotal_val=document.getElementById("amounttotal_text"+no).value;
    //var amountreceived_val=document.getElementById("amountreceived_text"+no).value;

    var stotal_text_val=document.getElementById("stotal_text"+no).value;

    var gtotal_text_val=document.getElementById("gtotal_text"+no).value;

    $('#row'+no+' input[type=text]').remove();

    var totalstock = parseInt(qty_val)+parseInt(filledstock_val);

	
	
		var pricing_centralized= document.getElementById("pricing_centralized").value;
	
	
    //item.innerHTML="<input type='text' id='item_text"+no+"' value='"+item_val+"' style='width:250px'>";
    qty.innerHTML="<input maxlength='6' type='text' id='qty_text"+no+"' value='"+qty_val+"'  onkeyup='CalAmounts("+no+")' pattern='^[0-9]+$' title='Only Numbers Allowed...' required>";
   
if(pricing_centralized=='Yes'){
   price.innerHTML="<input maxlength='5' type='text' readonly id='price_text"+no+"' value='"+price_val+"'  onkeyup='CalAmounts("+no+")' pattern='^[0-9]+$' readonly title='Only Numbers Allowed...' required>";
}else{
   price.innerHTML="<input maxlength='5' type='text'  id='price_text"+no+"' value='"+price_val+"'  onkeyup='CalAmounts("+no+")' pattern='^[0-9]+$' readonly title='Only Numbers Allowed...' required>";
	
}
    gst.innerHTML="<input maxlength='2' type='text' id='gst_text"+no+"' value='"+gst_val+"'  onkeyup='CalAmounts("+no+")' pattern='^[0-9]+$' title='Only Numbers Allowed...' required>";
    security.innerHTML="<input maxlength='5' type='text' id='security_text"+no+"' value='"+security_val+"'  onkeyup='CalAmounts("+no+")' pattern='^[0-9]+$' title='Only Numbers Allowed...' required>";
    returns.innerHTML="<input maxlength='5' type='text' id='returns_text"+no+"' value='"+returns_val+"'  onkeyup='Security_zero("+no+")' pattern='^[0-9]+$' title='Only Numbers Allowed...' required>";
    filledstock.innerHTML="<input type='text' id='filledstock_text"+no+"' value='"+totalstock+"'   disabled='disabled'>";

   	stotal_total.innerHTML="<input class='total_sum_input_security form-control' type='text' id='stotal_text"+no+"' value='"+stotal_text_val+"'   disabled='disabled'>";

    gtotal_total.innerHTML="<input class='total_sum_input_gas form-control' type='text' id='gtotal_text"+no+"' value='"+gtotal_text_val+"'   disabled='disabled'>";


    gst_amounttotal.innerHTML="<input type='text' id='gst_amounttotal_text"+no+"' value='"+gst_amounttotal_val+"'   disabled='disabled'>";
    ex_amounttotal.innerHTML="<input type='text' id='ex_amounttotal_text"+no+"' value='"+ex_amounttotal_val+"'   disabled='disabled'>";
    amounttotal.innerHTML="<input type='text' id='amounttotal_text"+no+"' value='"+amounttotal_val+"'  onkeyup='CalAmounts("+no+")' disabled='disabled'>";
    //amountreceived.innerHTML="<input type='text' id='amountreceived_text"+no+"' value='"+amountreceived_val+"'  onkeyup='CalAmounts("+no+")'>";

 $('.table>tbody>tr>td>input').addClass('form-control');
 $('.table>tbody>tr>td>select').addClass('form-control');
 $("#qty_text"+no).focus();

	$('.total_sum_input_gas').each(function(i, obj) {
		var input_value=parseInt($(this).val());
		total_sum_gas_amount=total_sum_gas_amount+input_value;
    
	});	

	//$('#gas_amount_sum_total_text').text(total_sum_gas_amount);

	$('.total_sum_input_security').each(function(i, obj) {
		var input_value=parseInt($(this).val());
		total_sum_security_amount=total_sum_security_amount+input_value;
    
	});	
	//$('#security_sum_total_text').text(total_sum_security_amount);

	if($('#row'+no+' td').attr('class')=='disables')
	{
		$("#security_text"+no).attr("readonly",false);
		$("#security_text"+no).attr("disabled",true);
		$("#returns_text"+no).attr("readonly",false);
		$("#returns_text"+no).attr("disabled",true);
		
	}
}
function save_row(no)
{
	 
    //var item_val=document.getElementById("item_text"+no).value;
    var item_val=document.getElementById("item_"+no).value;
    var qty_val=document.getElementById("qty_text"+no).value;
    var gst_val=document.getElementById("gst_text"+no).value;
    var price_val=document.getElementById("price_text"+no).value;
    var security_val=document.getElementById("security_text"+no).value;
    var returns_val=document.getElementById("returns_text"+no).value;
    var filledstock_val=document.getElementById("filledstock_text"+no).value;
    var ex_amounttotal_val=document.getElementById("ex_amounttotal_text"+no).value;
    var gst_amounttotal_val=document.getElementById("gst_amounttotal_text"+no).value;
    var amounttotal_val=document.getElementById("amounttotal_text"+no).value;
    //var amountreceived_val=document.getElementById("amountreceived_text"+no).value;

    //var filledstock = $("#filledstock").val();
	if(qty_val > Number(filledstock_val)){
		//$(".bootbox-confirm").show();
		//$(".modal-backdrop").show();
		document.getElementById("qty_text"+no).value="";
		document.getElementById("qty_text"+no).focus();
		alert("Quantity Should not greater than Filled Stock?");
		return false;
	}

	if(qty_val=='0' || price_val=='0' || qty_val=='0.00' || price_val=='0.00'){
		alert("Minimum 1 quantity and price allowed.");
		if(qty_val=='0' || qty_val=='0.00'){
		$("#qty_text"+no).focus();
		}else{$("#price_text"+no).focus();}
		return false;
	}

	var totalstock = parseInt(filledstock_val)-parseInt(qty_val);

	$('#data_table1 > #data_table2  > tr').each(function() {

	 	var id=$(this).closest("tr").find("td:eq(0)").attr('id');
	 	var itemids=id;
	 	if(id==item_val)
	 	{
	 	 	$(this).closest("tr").find("td:eq(0)").text(totalstock);
	 			 
	 	}
	 	 
	 });	

    //document.getElementById("item_row"+no).innerHTML="<input style='width: 250px' type='text' id='item_text"+no+"' name='item' value='"+item_val+"' disabled>";
    document.getElementById("qty_row"+no).innerHTML="<input  type='text' id='qty_text"+no+"' name='qty[]' value='"+qty_val+"' readonly>";
    document.getElementById("gst_row"+no).innerHTML="<input  type='text' id='gst_text"+no+"' name='gst[]' value='"+gst_val+"' readonly>";
    document.getElementById("price_row"+no).innerHTML="<input  type='text' id='price_text"+no+"' name='price[]' value='"+price_val+"' readonly>";
    document.getElementById("security_row"+no).innerHTML="<input class='security_sum_input' type='text' id='security_text"+no+"' name='security[]' value='"+security_val+"' readonly>";
    document.getElementById("returns_row"+no).innerHTML="<input  type='text' id='returns_text"+no+"' name='returns[]' value='"+returns_val+"' readonly>";
    document.getElementById("filledstock_row"+no).innerHTML="<input  type='text' id='filledstock_text"+no+"' name='filledstock[]' value='"+totalstock+"' readonly>";
    document.getElementById("gst_amounttotal_row"+no).innerHTML="<input class='total_sum_gst_amounttotal' type='text' id='gst_amounttotal_text"+no+"' name='gst_amounttotal[]' value='"+gst_amounttotal_val+"' readonly>";
    document.getElementById("ex_amounttotal_row"+no).innerHTML="<input class='total_sum_input_ex' type='text' id='ex_amounttotal_text"+no+"' name='ex_amounttotal[]' value='"+ex_amounttotal_val+"' readonly>";
    document.getElementById("amounttotal_row"+no).innerHTML="<input class='total_sum_input' type='text' id='amounttotal_text"+no+"' name='amounttotal[]' value='"+amounttotal_val+"' readonly>";
    //document.getElementById("amountreceived_row"+no).innerHTML="<input  type='text' id='amountreceived_text"+no+"' name='amountreceived[]' value='"+amountreceived_val+"' readonly>";

    //document.getElementById("edit_button"+no).style.display="block";
    //document.getElementById("save_button"+no).style.display="none";

    $("#edit_button"+no).show();
    $("#save_button"+no).hide();
 $('.table>tbody>tr>td>input').addClass('form-control');
 $('.table>tbody>tr>td>select').addClass('form-control');
 document.getElementById("item").value="";
 document.getElementById("filledstock").value="";
 $("#tempty").show();

}
function delete_row(no)
{

    document.getElementById("row"+no+"").outerHTML="";
    var table=document.getElementById("data_table2");
    var table_len=(table.rows.length);
    if(table_len==0){
    	$("#data_table1").hide();
    	$("#submithide").hide();
    	$("#id-date-picker-1").prop("disabled", false);

    	window.location.replace('<?php echo SURL."SaleLPG/" ?>');
    }
    
	
	
	totals_of_totals();
	
    // var tsamt=0;
	// var tgasamt=0;

    // $('#data_table1 > #data_table2  > tr').each(function() {

	 	// var samt=$(this).closest("tr").find("td:eq(1)").attr('id');
	 	// var gasamt=$(this).closest("tr").find("td:eq(1)").text();
		
		// alert(gasamt);
	 	// tsamt =parseInt(tsamt)+parseInt(samt);
	 	// tgasamt =parseInt(tgasamt)+parseInt(gasamt);
	 
	 // });
    // document.getElementById("securityamt").value=tsamt;
    // document.getElementById("gasamt").value=tgasamt;
    // document.getElementById("totalrecv").value=tsamt+tgasamt;
    
}


$('#data_table1 > #data_table2  > tr').each(function() {

	var item_id=$(this).closest("tr").find("td:eq(0)").attr('id');
	var row_number_str=$(this).closest("tr").find("td:eq(4)").attr('id');
	var row_number= parseInt(row_number_str.slice(-1));
	var qty_new=parseInt($("#qty_text"+row_number).val());
	
	var date= $('#id-date-picker-1').val();
	//alert(date);return false;
		if(item_id=='' || date=='')
		{
			//$("#filledstock").value()="";
			document.getElementById("emptystock").value="";
			return false;
		}
		var request = $.ajax({
		  url: "<?php echo SURL ?>common/stock",
		  type: "POST",
		  data: {item_id:item_id,date:date},
		  dataType: "html"
		});
		request.done(function(msg) {
//alert(msg);
			var empty_filled=msg.split('_');
	
			$("#filledstock_text"+row_number).val(empty_filled[0]);
			if(empty_filled[2]>0){
			$("#price_text"+row_number).val(empty_filled[2]);
			}
			row_number=row_number+1;


		});	  
		request.fail(function(jqXHR, textStatus) {
		  alert( "Request failed: " + textStatus );
		});
});

function add_row()
{
	$("#id-date-picker-1").prop("disabled", true);
    $("#data_table1").show();
    $("#submithide").show();
	$("#item").prop('required',false); 
   	var item=document.getElementById("item").value;
    var qty=document.getElementById("qty1").value;
    var returns=document.getElementById("returns").value;
    var amounttotal=document.getElementById("amounttotal").value;
	var filledstock = parseInt(filledstock)-parseInt(qty);

    var table=document.getElementById("data_table2");
    var table_len=(table.rows.length);
    var row = table.insertRow(table_len).outerHTML="<tr id='row"+table_len+"'>"+
	"<td id='item_row"+table_len+"' style='width:15% !important;'><select class='form-control disable' name='item[]' id='item_"+table_len+"' data-placeholder='Choose a Item...' required='required' disabled onchange='itemchange(this.value,"+table_len+")'><?php 
					foreach ($item_list as $key => $data) {
						?><option value='<?php echo $data['materialcode']; ?>'><?php 
    							echo ucwords($data['itemname']); 
    					?></option><?php 
    				} 
    				?></select></td>"+
					"<td id='qty_row"+table_len+"'><input type='text' id='qty_text"+table_len+"' readonly value='"+qty+"' name='qty[]'></td>"+
					"<td id='price_row"+table_len+"'><input type='text' id='price_text"+table_len+"' readonly  readonly value='"+price+"' name='price[]'></td>"+
					"<td id='item_return_row"+table_len+"' style='width:15% !important;'><select class='form-control disable' name='item_return[]' id='item_return_"+table_len+"' disabled  data-placeholder='Choose Return...' required='required' ><?php 
					foreach ($item_list as $key => $data) {
						?><option value='<?php echo $data['materialcode']; ?>'><?php 
    							echo ucwords($data['itemname']); 
    					?></option><?php 
    				} 
    				?></select></td>"+
					"<td id='returns_row"+table_len+"'><input type='text' id='returns_text"+table_len+"' readonly  value='"+returns+"' name='returns[]'></td>"+
					"<td  class='hidden-480'  id='filledstock_row"+table_len+"'><input type='text' id='filledstock_text"+table_len+"' readonly  value='"+filledstock+"' name='filledstock[]'></td>"+
					"<td  class='hidden-480'  id='stotal_row"+table_len+"'><input type='text' id='stotal_text"+table_len+"' readonly  value='"+qty*security+"' class='total_sum_input_security' name='stotal[]'></td>"+
					"<td style='display:none;' class='hidden-480'  id='gtotal_row"+table_len+"'><input type='text' id='gtotal_text"+table_len+"' readonly  value='"+qty*price+"' class='total_sum_input_gas' name='gtotal[]'></td>"+
					"<td id='gst_amounttotal_row"+table_len+"'><input type='text' id='gst_amounttotal_text"+table_len+"' readonly  value='"+gst_amounttotal+"' class='total_sum_gst_amounttotal' name='gst_amounttotal[]'></td>"+
					"<td id='ex_amounttotal_row"+table_len+"'><input type='text' id='ex_amounttotal_text"+table_len+"' readonly  value='"+ex_amounttotal+"' class='total_sum_input_ex' name='ex_amounttotal[]'></td>"+
					"<td id='amounttotal_row"+table_len+"'><input type='text' id='amounttotal_text"+table_len+"' readonly  value='"+amounttotal+"' class='total_sum_input' name='amounttotal[]'></td>"+
					"<td style='display: inline-flex; border: 0px;'><input type='button' id='edit_button"+table_len+"' value='Edit' class='btn btn-xs btn-success' onclick='edit_row("+table_len+");'>"+
					"<input type='button' id='save_button"+table_len+"' value='Save' class='btn btn-xs btn-warning' onclick='savechecking("+table_len+"); culculate_security()' style='display:none'>"+
					"<input type='button' value='Delete' class='btn btn-xs btn-danger' onclick='delete_row("+table_len+");culculate_security()'></td></tr>";


			// if(table_len==0)
			// {
			// 	table.append("<tr><td id='total_security_sum_row'><input type='text' id='total_security_sum_text' readonly  value='"+filledstock+"' name='total_security_name[]'></td></tr>")
			// }

    var itemvalue = $("#item_"+table_len+"").val(item);
    var item_returnvalue = $("#item_return_"+table_len+"").val(item_return);
    document.getElementById("item").value="";
    document.getElementById("item_return").value="";
    document.getElementById("qty").value="0";
    document.getElementById("price").value="0";
    document.getElementById("security").value="0";
    document.getElementById("returns").value="0";
    document.getElementById("filledstock").value="";
    document.getElementById("amounttotal").value="0";
    document.getElementById("ex_amounttotal").value="0";
    document.getElementById("gst_amounttotal").value="0";
    document.getElementById("gst").value="0";
    //document.getElementById("amountreceived").value="";
 $('.table>tbody>tr>td>input').addClass('form-control');
 $('.table>tbody>tr>td>select').addClass('form-control');

	totals_of_totals();

}




function totals_of_totals(){
	
	
	
	
var total_security=0;
var total_sum_amount=0;
var total_sum_gas_amount=0;
var total_sum_security_amount=0;
var total_sum_gst_amounttotal=0;
	
	$('.total_sum_input_ex').each(function(i, obj) {
		var input_value=parseInt($(this).val());
		total_sum_gas_amount=total_sum_gas_amount+input_value;
    
	});	

	$('#gas_amount_sum_total_text').text(total_sum_gas_amount);
	
	$('.total_sum_gst_amounttotal').each(function(i, obj) {
		var input_value=parseInt($(this).val());
		total_sum_gst_amounttotal=total_sum_gst_amounttotal+input_value;
    
	});	

	$('#total_sum_gst_amounttotal_text').text(total_sum_gst_amounttotal);

	$('.total_sum_input_security').each(function(i, obj) {
		var input_value=parseInt($(this).val());
		total_sum_security_amount=total_sum_security_amount+input_value;
    
	});	
	$('#security_sum_total_text').text(total_sum_security_amount);


	$('.total_sum_input').each(function(i, obj) {
		var input_value=parseInt($(this).val());
		total_sum_amount=total_sum_amount+input_value;
    
	});	

	$('#amount_sum_class_id').text(total_sum_amount)
	$('.security_sum_input').each(function(i, obj) {
		var input_value=parseInt($(this).val());
		total_security=total_security+input_value;

	});	
	$('#security_sum_text_id').val(total_security)
	
	
	
	
	 
	var tsamt=total_security;
	var tgasamt=total_sum_amount-total_security;

 
    // document.getElementById("securityamt").value=tsamt;
    // document.getElementById("gasamt").value=tgasamt;
    // document.getElementById("totalrecv").value=tsamt+tgasamt;
  document.getElementById("total_bill").value=tsamt+tgasamt;
  document.getElementById("after_discount_amt").value=tsamt+tgasamt-parseInt(document.getElementById("total_discount").value);
	$("#tempty").show();

	var fprice = $("#final_total_price").text();
	var fsecurity = $("#security_total_price").text();
	var ftotal = $("#total_total_price").text();

	$("#final_total_price").text(parseInt(fprice)+parseInt(price));
	$("#security_total_price").text(parseInt(fsecurity)+parseInt(security));
	$("#total_total_price").text(parseInt(ftotal)+parseInt(amounttotal));

	
}


function cal_net_bill(total_discount)
{
	  var total_bill= document.getElementById("total_bill").value;
	  
	  if(parseInt(total_discount)>parseInt(total_bill)){  document.getElementById("total_discount").value=0;    return false;}
  document.getElementById("after_discount_amt").value=total_bill-total_discount;
	
}
function culculate_security()
{
	var total_security=0;
	var total_sum_amount=0;
	var total_sum_amount_other=0;
	var total_security_final=0;

	$('.total_sum_input').each(function(i, obj) {
		var input_value=parseInt($(this).val());
		total_sum_amount=total_sum_amount+input_value;
    
	});	

	$('#amount_sum_class_id').val(total_sum_amount)


	$('.total_sum_input_security').each(function(i, obj) {
		var input_value=parseInt($(this).val());
		total_sum_amount_other=total_sum_amount_other+input_value;
    
	});	
	//alert(total_sum_amount);

	$('#security_sum_total_text').text(total_sum_amount_other)




 


	$('.total_sum_input_gas').each(function(i, obj) {
		var input_value=parseInt($(this).val());
		total_security_final=total_security_final+input_value;

	});	
	$('#gas_amount_sum_total_text').text(total_security_final)


	$('.security_sum_input').each(function(i, obj) {
		var input_value=parseInt($(this).val());
		total_security=total_security+input_value;

	});	
	$('#security_sum_text_id').val(total_security)
}

</script>
<script src="<?php echo SURL; ?>assets/js/bootbox.js"></script>

<!-- end add update delte rows using javascript -->

<script type="text/javascript">

function CalRAmount_general()
{


}

function CalRAmount()
{
	var returngas=document.getElementById('returngas').value;
	var returnrate=document.getElementById('returnrate').value;
	var returntotal=returngas*returnrate;
	document.getElementById("returntotal").value=returntotal;


}


function CalAmountRecv()
{
	var securityamt=document.getElementById('securityamt').value;
	var gasamt=document.getElementById('gasamt').value;
	var totalrecv=Number(securityamt)+Number(gasamt);

	document.getElementById("totalrecv").value=totalrecv;
  
}

function kg_price(){
	$("#item").val("");
	$("#qty1").val("");
	$("#price1").val("");
	$("#stock1").val("");
	$("#item2").val("");
	$("#qty2").val("");
	$("#price2").val("");
	$("#stock2").val("");
	$("#item3").val("");
	$("#qty3").val("");
	$("#price3").val("");
	$("#stock3").val("");
	$("#total_amount").val("");
}

function checkonlynumbers(val){
	if(isNaN(val) || val==" "){
		alert("Please enter only numbers");
		$("#totalrecv").val("");
		$("#totalrecv").focus();
		return false;
	}
}



function CalAmount()
	{
	var price1=parseInt(document.getElementById('price1').value); 
	var qty1=parseInt(document.getElementById('qty1').value);
	var stock1 = ($("#stock1").val());
	var item = $("#item").val();

	var price2=parseInt(document.getElementById('price2').value); 
	var qty2=parseInt(document.getElementById('qty2').value);
	var stock2 = parseInt($("#stock2").val());
	var item2 = $("#item2").val();

	var price3=parseInt(document.getElementById('price3').value); 
	var qty3=parseInt(document.getElementById('qty3').value);
	var stock3 = parseInt($("#stock3").val());
	var item3 = $("#item3").val();

	var total_amount = 0;
	var total_amount1 = 0;
	var total_amount2 = 0;

	//$("#total_amount").val("");
var stock_check = $("#stock_check").val();
	if(item!=""){
		if(qty1 > stock1 && stock_check=='true'){
			total_amount = 0;
			alert("Quantity must be less than current stock");
			$("#qty1").val("");
			$("#qty1").focus();
			return false;
		}else{
			if(qty1=="" || isNaN(qty1)){
				qty1 = 0;
			}

		 	total_amount = price1 * qty1;
		} 
	}

	 if(item2!=""){
		if(qty2 > stock2 && stock_check=='true'){
			total_amount1 = 0;
			alert("Quantity must be less than current stock");
			$("#qty2").val("");
			$("#qty2").focus();
			return false;
		}else{
			if(qty2=="" || isNaN(qty2)){
				qty2 = 0;
			}
			total_amount1 = price2 * qty2;
		} 
	}

	 if(item3!=""){
		if(qty3 > stock3 && stock_check=='true'){
			total_amount2 = 0;
			alert("Quantity must be less than current stock");
			$("#qty3").val("");
			$("#qty3").focus();
			return false;
		}else{
			if(qty3=="" || isNaN(qty3)){
				qty3 = 0;
			}
			total_amount2 = price3 * qty3;
		}
	} 


	$("#total_amount").val(total_amount + total_amount1 + total_amount2); 

	
}

 
function CalAmounts(no)
	{
	var qty=parseInt(document.getElementById('qty_text'+no).value);
	var gst=parseInt(document.getElementById('gst_text'+no).value);
	var price=parseInt(document.getElementById('price_text'+no).value);
	var security=parseInt(document.getElementById('security_text'+no).value);
	
	if(security!=0){
		document.getElementById('returns_text'+no).value="0";
	}

	var securityamt=qty*security;

	//document.getElementById("securityamt").value=securityamt;

	var gasamts=qty*price;
		document.getElementById('stotal_text'+no).value=securityamt;
		document.getElementById('gtotal_text'+no).value=gasamts;
	//document.getElementById("totalrecv").value=securityamt+gasamt;
	var tsamt=0;
	var tgasamt=0;

    $('#data_table1 > #data_table2  > tr').each(function() {

    	var clas=$(this).closest("tr").find("td:eq(1)").attr('class');
		if(clas==no){
	 		var samt=$(this).closest("tr").find("td:eq(1)").attr('id',securityamt);
	 		var gasamt=$(this).closest("tr").find("td:eq(1)").text(gasamts);
	 	}
	 	var samtss=$(this).closest("tr").find("td:eq(1)").attr('id');
	 	var gasamtss=$(this).closest("tr").find("td:eq(1)").text();
	 	tsamt =parseInt(tsamt)+parseInt(samtss);
	 	tgasamt =parseInt(tgasamt)+parseInt(gasamtss);
	 
	 });
    // document.getElementById("securityamt").value=tsamt;
    // document.getElementById("gasamt").value=tgasamt;
    document.getElementById("total_bill").value=tsamt+tgasamt;
    document.getElementById("after_discount_amt").value=tsamt+tgasamt-parseInt(document.getElementById("total_discount").value);
   

   	if(security!=0){
		document.getElementById("returns_text"+no).value="0";
	}

	var ucost_with_gst=0;
	var ucost_with_gst_only=0;
	if(gst!=0){
	  ucost_with_gst=price*gst/100;
	  ucost_with_gst_price=(price*gst/100)+price;
	  ucost_with_gst_only=ucost_with_gst;
	
	}else{
		  ucost_with_gst_price=price;
	}
	
	var amounttotal=parseInt(qty*ucost_with_gst_price)+parseInt(qty*security);
	var amounttotal_ex=qty*price+qty*security;
	  
	document.getElementById("amounttotal_text"+no).value=amounttotal;
	document.getElementById("gst_amounttotal_text"+no).value=ucost_with_gst_only*qty;
	document.getElementById("ex_amounttotal_text"+no).value=amounttotal_ex;

	
	}

 
function checking(){
	 var customer=document.getElementById('customer').value;
	 var kg_11_price = $("#kg_11_price").val();
	 var pay_mode = $("#pay_mode").val();
	 var bank_code = $("#bank_code").val();
	 var cheque_no = $("#cheque_no").val();
	 var cheque_date = $("#cheque_date").val();

	 var item = $("#item").val();
	 var item2 = $("#item2").val();
	 var item3 = $("#item3").val();

	 var qty1 = $("#qty1").val();
	 var qty2 = $("#qty2").val();
	 var qty3 = $("#qty3").val();
	 
if(customer=='0' || customer=='' || customer=='0.00'){
	alert("Please select customer!");
	$("#customer").focus();
	return false;
}else if(kg_11_price==0 || kg_11_price=='' || kg_11_price=='0.00' || isNaN(kg_11_price)){
	alert("Please enter 11 kg price.");
	$("#kg_11_price").val("");
	$("#kg_11_price").focus();
	return false;
}else if((item==0 || item=='') && (item2=='0' || item2=="") && (item3=="" || item3==0)){
	alert("Please select item.");
	$("#item").focus();
	return false;
}else if((qty1==0 || qty1=='') && (qty2=='0' || qty2=="") && (qty3=="" || qty3==0)){
	alert("Please enter item quantity");
	$("#qty1").focus();
	return false;
}else if(pay_mode=="Bank"){
	if(bank_code==""){
		alert("Please select Bank.");
		$("#bank_code").focus();
		return false;
	}else if(cheque_no=="" || cheque_no==0){
		alert("Please enter cheque no.");
		$("#cheque_no").focus();
		return false;
	}else if(cheque_date==0 || cheque_date==""){
		alert("Please select cheque date.");
		$("#cheque_date").focus();
		return false;
	}else{
		return true;
	}
}
else {
		document.getElementById("addremove").removeAttribute("type");
		$("#addremove").prop('type','button');
		// add_row_new();		
	}
}





















	var x = ""; //Initial field counter is 1
    var maxField = 100; //Input fields increment limitation
    var addButton = $('.addremove'); //Add button selector
    var wrapper = $('.field_wrapper'); //Input field wrapper 


     //Once add button is clicked
   function add_more(){

   		var customer = $("#customer").val();
    	var kg_11_price = $("#kg_11_price").val();
    	var total_amount = $("#total_amount").val();
    	var total_discount = $("#total_discount").val();
    	var amount_payed = $("#amount_payed").val();
    	var totalrecv = $("#totalrecv").val();
    	var pay_mode = $("#pay_mode").val();
    	var pay_mode_name = $("#pay_mode option:selected").text();
    	var bank_code = $("#bank_code").val();
    	var bank_code_text = $("#bank_code option:selected").text();
    	var cheque_no = $("#cheque_no").val();
    	var cheque_date = $("#cheque_date").val();

    	var customer_name = $("#customer option:selected").text();

    	var price1=parseInt(document.getElementById('price1').value); 
		var qty1=parseInt(document.getElementById('qty1').value);
		var stock1 = ($("#stock1").val());
		var item = $("#item").val();
		var item_name = $("#item option:selected").text();
		var return_qty = $("#returns").val();

		var price2=parseInt(document.getElementById('price2').value); 
		var qty2=parseInt(document.getElementById('qty2').value);
		var stock2 = parseInt($("#stock2").val());
		var item2 = $("#item2").val();
		var item2_name = $("#item2 option:selected").text();
		var return_qty2 = $("#returns2").val();

		var price3=parseInt(document.getElementById('price3').value); 
		var qty3=parseInt(document.getElementById('qty3').value);
		var stock3 = parseInt($("#stock3").val());
		var item3 = $("#item3").val();
		var item3_name = $("#item3 option:selected").text();
		var return_qty3 = $("#returns3").val();
 
		if(item==""){
			qty1 = 0;
		}else if(item2==""){
			qty2 = 0;
		}

		if(item3=="" || item3==0 || isNaN(item3)){
			qty3 = 0;
		}

 
   	if(check_data()){
   		var response_id = 0;

   		insert_data_per_row(function(d){
   		response_id = d;

   		if(x < maxField){ 
            x++;
 
            $(wrapper).append('<div class="col-xs-12 col-sm-12 pricing-span3-body"><div class="pricing-span3" ><div class="widget-box pricing-box-small widget-color-blue2"> <div id="items" class="widget-body" style="height: 68px;" ><select class=" form-control" readonly style="height: 68px;" name="customer" id="customer'+x+'" data-placeholder="Choose a Vendor..."><option value="'+customer+'">'+customer_name+'</option> </select></div></div></div><div class="pricing-span1"><div class="widget-box pricing-box-small widget-color-blue2"> <div class="widget-body" style="height: 68px;"><input type="text" readonly style="height: 68px;" id="kg_11_price'+x+'" name="kg_11_price" pattern="^[0-9]+$" title="Only Numbers Allowed" maxlength="4" class="form-control" value="'+kg_11_price+'" placeholder="11.8 kg price"></div></div></div><div class="pricing-span3"><div class="widget-box pricing-box-small widget-color-blue2"> <div id="items" class="widget-body" style="height: 68px;"> <select readonly class="form-control" onchange="itemchange(this.value,1); CalAmount();" id="item'+x+'" data-placeholder="Choose a Item..." ><option value="'+item+'">'+item_name+'</option></select><input maxlength="6" class="form-group" type="text" id="qty1'+x+'" style="width:65%; margin-left: 0px;" pattern="^[0-9]+$" title="Only Numbers Allowed..." onkeyup="CalAmount()" value="'+qty1+'" readonly placeholder="Qty"><input pattern="^[0-9]+$" class="form-group" maxlength="5" style="width:55%;" value="'+return_qty+'" readonly type="text" name="return[]" id="returns'+x+'" placeholder="Return" title="Only Numbers Allowed..."><input type="hidden" value="'+stock1+'" name="" id="stock1'+x+'"><input type="hidden" name="" id="price1'+x+'" value="'+price1+'"></div></div></div><div class="pricing-span3"><div class="widget-box pricing-box-small widget-color-blue2"> <div id="items" class="widget-body" style="height: 68px;"> <select class="form-control" readonly onchange="itemchange(this.value,2); CalAmount();" name="item[]" id="item2'+x+'" data-placeholder="Choose a Item..." ><option value="'+item2+'">'+item2_name+'</option> </select><input maxlength="6" readonly class="form-group" type="text" name="qty[]" id="qty2'+x+'" style="width:65%; margin-left: 0px;" pattern="^[0-9]+$" title="Only Numbers Allowed..." onkeyup="CalAmount()" value="'+qty2+'" readonly placeholder="Qty"><input pattern="^[0-9]+$" readonly class="form-group" maxlength="5" style="width:55%;" value="'+return_qty2+'" type="text" name="return[]" id="returns2'+x+'" placeholder="Return" title="Only Numbers Allowed..."><input type="hidden" value="'+stock2+'" name="" id="stock2'+x+'"><input value="'+price2+'" type="hidden" name="" id="price2'+x+'"></div></div></div><div class="pricing-span3"><div class="widget-box pricing-box-small widget-color-blue2"> <div id="items" class="widget-body" style="height: 68px;"> <select readonly class="form-control" onchange="itemchange(this.value,3); CalAmount();" name="item[]" id="item3'+x+'" data-placeholder="Choose a Item..." ><option value="'+item3+'">'+item3_name+'</option> </select><input maxlength="6" readonly class="form-group" type="text" name="qty[]" id="qty3'+x+'" style="width:65%; margin-left: 0px;" pattern="^[0-9]+$" title="Only Numbers Allowed..." onkeyup="CalAmount()" value="'+qty3+'" placeholder="Qty"><input pattern="^[0-9]+$" value="'+return_qty3+'" readonly class="form-group" maxlength="5" style="width:55%;" type="text" name="return[]" id="returns3'+x+'" placeholder="Return" title="Only Numbers Allowed..."><input type="hidden" value="'+stock3+'" name="" id="stock3'+x+'"><input type="hidden" name="" value="'+price3+'" id="price3'+x+'"></div></div></div><div class="pricing-span1"><div class="widget-box pricing-box-small widget-color-blue2"><div class="widget-body" style="height: 68px;"><input maxlength="5" placeholder="Total" type="text" name="" style="width:100%; height: 68px;" id="total_amount'+x+'" pattern="^[0-9]+$" title="Only Numbers Allowed..." readonly onkeyup="CalAmount()" value="'+total_amount+'"></div></div></div><div class="pricing-span1"><div class="widget-box pricing-box-small widget-color-blue2"><div class="widget-body" style="height: 68px;"><input maxlength="5" placeholder="Discount" type="text" name="" style="width:100%; height: 68px;" id="total_discount'+x+'" pattern="^[0-9]+$" title="Only Numbers Allowed..." readonly onkeyup="discount()" value="'+total_discount+'"></div></div></div><div class="pricing-span1"><div class="widget-box pricing-box-small widget-color-blue2"><div class="widget-body" style="height: 68px;"><input maxlength="5" placeholder="Amount Payed" type="text" name="" style="width:100%; height: 68px;" id="amount_payed'+x+'" pattern="^[0-9]+$" title="Only Numbers Allowed..." readonly onkeyup="discount()" value="'+amount_payed+'"></div></div></div><div class="pricing-span1"><div class="widget-box pricing-box-small widget-color-blue2"><div class="widget-body" style="height: 68px;"> <select class="form-control" name="pay_mode" style="height: 68px;" readonly onchange="change_pay_mode(this.value);" id="pay_mode'+x+'" data-placeholder="Choose Mode..." ><option value="'+pay_mode+'">'+pay_mode_name+'</option></select></div></div></div><div class="pricing-span1"><div class="widget-box pricing-box-small widget-color-blue2"><div class="widget-body" style="height: 68px;"><input type="text" style="height: 68px;" readonly id="totalrecv'+x+'" pattern="^[0-9]+$" maxlength="6" value="'+totalrecv+'" placeholder="Amount Received" name="totalrecv" class="form-control"></div></div></div><div class="pricing-span1 bank_amount_row'+x+'"><div class="widget-box pricing-box-small widget-color-blue2"><div class="widget-body" style="height: 68px;"><select class="form-control" style="height: 68px;" readonly name="bank_code" id="bank_code'+x+'" required data-placeholder="Choose bank..." ><option value="'+bank_code+'">'+bank_code_text+'</option></select></div></div></div><div class="pricing-span1 bank_amount_row'+x+'"><div class="widget-box pricing-box-small widget-color-blue2"><div class="widget-body" style="height: 68px;"><input readonly type="text" style="height: 68px;" id="cheque_no'+x+'" maxlength="15" value="'+cheque_no+'" name="cheque_no" class="form-control"></div></div></div><div class="pricing-span1 bank_amount_row'+x+'"><div class="widget-box pricing-box-small widget-color-blue2"><div class="widget-body" style="height: 68px;"><input readonly name="cheque_date" style="height: 68px;" class="form-control date-picker" id="cheque_date'+x+'" type="text" data-date-format="yyyy-mm-dd" required value="'+cheque_date+'"></div></div></div><div class="pricing-span1"><div class="widget-box pricing-box-small widget-color-green"> <div class="widget-body" align="center" style="height:68px;"><input type="hidden" id="delete_id" value="'+response_id+'"> <input style=" height:68px; !important; width: 100%;" id="remove" class="btn btn-xs btn-danger remove_button" onclick="delete_data('+response_id+')" type="button" value="Remove"> </div></div></div>'); //Add field html

	            if(pay_mode=="Cash"){
					$(".bank_amount_row"+x).css("display","none");
				}

	            $("#customer").val("");
	            $("#kg_11_price").val("");
	            $("#item").val("");
	            $("#qty1").val("");
	            $("#returns").val("");
	            $("#item2").val("");
	            $("#qty2").val("");
	            $("#returns2").val("");
	            $("#item3").val("");
	            $("#qty3").val("");
	            $("#returns3").val("");
	            $("#total_amount").val("");
	            $("#total_discount").val("");
	            $("#amount_payed").val("");
	            $("#pay_mode").val("Cash");
	            $("#totalrecv").val("");
	            //$("#bank_code").val("");
	            $("#cheque_no").val("");


	            $("#stock_item_1").val("");
	            $("#stock_item_2").val("");
	            $("#stock_item_3").val("");


	            $("#item_h1").val("");
	            $("#item_h2").val("");
	            $("#item_h3").val("");


	            $("#existing_item1").val("");
	            $("#existing_item2").val("");
	            $("#existing_item3").val("");

	            $(".bank_amount_row").css("display","none");
				
				
				//today_amount_recv();
				
	        }
        });
	}else{
		return false;
	}
}

function delete_data(id){
	$.ajax({ 
       	data: {id:id},
       	type: "POST",
        url: "<?php echo SURL ?>SaleLPG/delete_row_ajax",
       	cache:false,
        dataType: "html",
       	success: function(response) {
       		$("#stock_item_1").val("");
            $("#stock_item_2").val("");
            $("#stock_item_3").val("");
            $("#item_h1").val("");
            $("#item_h2").val("");
            $("#item_h3").val("");

            $("#existing_item1").val("");
            $("#existing_item2").val("");
            $("#existing_item3").val("");
		//	today_amount_recv();
           }
      });
	  
	//  today_amount_recv();
}



function insert_data_per_row(callback){

    	var date= $('#id-date-picker-1').val();
        var customer = $("#customer").val();
    	var remarks = $("#remarks").val();
    	var kg_11_price = $("#kg_11_price").val();
    	var item = $("#item").val();
    	var qty1 = $("#qty1").val();
    	var return_qty1 = $("#returns").val();
    	var item2 = $("#item2").val();
    	var qty2 = $("#qty2").val();
    	var return_qty2 = $("#returns2").val();
    	var item3  = $("#item3").val();
    	var qty3 = $("#qty3").val();
    	var return_qty3 = $("#returns3").val();
    	var total_amount = $("#total_amount").val();
    	var total_discount = $("#total_discount").val();
    	var amount_payed = $("#amount_payed").val();
    	var totalrecv = $("#totalrecv").val();
    	var pay_mode = $("#pay_mode").val();
    	var bank_code = $("#bank_code").val();
    	var cheque_no = $("#cheque_no").val();
    	var cheque_date = $("#cheque_date").val();
    	var price1 = $("#price1").val();
    	var price2 = $("#price2").val();
    	var price3 = $("#price3").val();

    	var total_p1 =  parseInt(price1) * parseInt(qty1);
    	var total_p2 =  parseInt(price2) * parseInt(qty2);
    	var total_p3 =  parseInt(price3) * parseInt(qty3); 

    	$.ajax({ 
       	data: {date:date, customer:customer, remarks:remarks,kg_11_price:kg_11_price,item:item,qty1:qty1,return_qty1:return_qty1,item2:item2,qty2:qty2, return_qty2:return_qty2, item3:item3,qty3:qty3,return_qty3:return_qty3, amounttotal:total_amount,total_discount:total_discount,amount_payed:amount_payed,pay_mode:pay_mode,totalrecv:totalrecv,bank_code:bank_code,cheque_no:cheque_no,cheque_date:cheque_date,price1:price1,price2:price2,price3:price3,total_p1:total_p1,total_p2:total_p2,total_p3:total_p3},
       	type: "POST",
        url: "<?php echo SURL ?>SaleLPG/add_sale_new",
       	cache:false,
        dataType: "html",
       	success: function(response) {
       		callback(response);
           }
      });
    }

    function single_submit(){
    	if(x==""){
    		if(check_data()){
				insert_data_per_row();
				window.location.replace("<?php echo SURL."SaleLPG" ?>");
			}
   		}else{
   			window.location.replace("<?php echo SURL."SaleLPG" ?>");
   		}
    }


    function check_data(){

    	var customer = $("#customer").val();
    	var kg_11_price = $("#kg_11_price").val();
    	var total_amount = $("#total_amount").val();
    	var total_discount = $("#total_discount").val();
    	var amount_payed = $("#amount_payed").val();
    	var pay_mode = $("#pay_mode").val();
    	var bank_code = $("#bank_code").val();
    	var cheque_no = $("#cheque_no").val();
    	var cheque_date = $("#cheque_date").val();

    	var price1=parseInt(document.getElementById('price1').value); 
		var qty1=parseInt(document.getElementById('qty1').value);
		var stock1 = ($("#stock1").val());
		var item = $("#item").val();
		var returns_qty1 = $("#returns").val();

		var price2=parseInt(document.getElementById('price2').value); 
		var qty2=parseInt(document.getElementById('qty2').value);
		var stock2 = parseInt($("#stock2").val());
		var item2 = $("#item2").val();
		var returns_qty2 = $("#returns2").val();

		var price3=parseInt(document.getElementById('price3').value); 
		var qty3=parseInt(document.getElementById('qty3').value);
		var stock3 = parseInt($("#stock3").val());
		var item3 = $("#item3").val();
		var returns_qty3 = $("#returns3").val();
		//alert(returns_qty1);

		if(returns_qty1!="" && isNaN(returns_qty1)){

			alert("Please enter return quantity in number format.");
			$("#returns").val("");
			$("#returns").focus();
			return false;

		}

		if(returns_qty2!="" && isNaN(returns_qty2)){

			alert("Please enter return quantity in number format.");
			$("#returns2").val("");
			$("#returns2").focus();
			return false;

		}

		if(returns_qty3!="" && isNaN(returns_qty3)){

			alert("Please enter return quantity in number format.");
			$("#returns3").val("");
			$("#returns3").focus();
			return false;

		}

		if(item!=""){ 
			if(qty1=="" || qty1==0 || isNaN(qty1)){
				alert("Please enter quantity in number format.");
				$("#qty1").val("");
				$("#qty1").focus();
				return false;
			}
		}

		if(item2!=""){ 
			if(qty2=="" || qty2==0 || isNaN(qty2)){
				alert("Please enter quantity in number format.");
				$("#qty2").val("");
				$("#qty2").focus();
				return false;
			}
		}

		if(item3!=""){ 
			
			if(qty3=="" || qty3==0 || isNaN(qty3)){
				alert("Please enter quantity in number format.");
				$("#qty3").val("");
				$("#qty3").focus();
				return false;
			}
		}


		if(customer==""){
			alert("Please select customer");
			$("#customer").focus();
			return false; 
		}else if(kg_11_price=="" || kg_11_price==0 || isNaN(kg_11_price)){
			alert("Please enter amount in number format.");
			$("#kg_11_price").val("");
			$("#kg_11_price").focus();
			return false;
		}
		else if(item=="" && item2=="" && item3==""){
			alert("Please select atleast one item.");
			$("#item").focus();
			return false;
		}else if(totalrecv!="" && isNaN(totalrecv)){
			alert("Please enter receive amount in number format.");
			$("#totalrecv").val("");
			$("#totalrecv").focus();
			return false;
		}else{
			return true;
		}


    }



    //Once remove button is clicked
    $(wrapper).on('click', '.remove_button', function(e){
        e.preventDefault();
        $(this).parent('div').parent('div').parent('div').parent('div').remove(); //Remove field html
        x--; //Decrement field counter

        //$("#type_change").prop("disabled",false);
        // $("#add_more").prop("disabled",false);
    });















function savechecking(no){
	 var qty=document.getElementById("qty_text"+no).value;
	 var price=document.getElementById("price_text"+no).value;
	 var security_text=document.getElementById('security_text'+no).value;
	 var returns_text=document.getElementById('returns_text'+no).value;
	 var gst = $("#gst_text"+no).val();
	 var security_text = $("#security_text"+no).val(); 
	 
 if (qty=='' || qty==0 || isNaN(qty)) {

	alert("Please enter sale quantity.");
	$("#qty_text"+no).focus();
	return false;

}else if (isNaN(gst)) {

	alert("Please enter only number");
	$("#gst_text"+no).focus();
	return false;

}else if (isNaN(security_text)) {

	alert("Please enter only number");
	$("#security_text"+no).focus();
	return false;

} else if (isNaN(returns_text)) {

	alert("Please enter only number");
	$("#returns_text"+no).focus();
	return false;

} else { 

		$("#type_change").prop("disabled",false);
		$("#save_button"+no).prop('type','button');
		save_row(no);
				
	}
	
	
		totals_of_totals();

}
function setting_submit(){
	
	$("#id-date-picker-1").prop("disabled", false);
	var customer=document.getElementById('customer').value;
    var emailerror=document.getElementById('email-error');
	emailerror.innerHTML="";
	if(customer==''){
		var emailerror=document.getElementById('email-error');
		emailerror.innerHTML="Please provide a customer.";
		return false;
	}
	$('.disable').attr("disabled",false);
	$('.disable').attr("readonly",true);
	$("#item").prop('required',false);
	$("#qty").prop('required',false);
	$("#price").prop('required',false);
	$("#security").prop('required',false);
	$("#returns").prop('required',false);
	 
	$("#type_change").prop('type','submit');
 

}
function delete_record(deleteid,parentid,idd)
{
	var request = $.ajax({
	  url: "<?php echo SURL ?>/SaleLPG/record_delete",
	  type: "POST",
	  data: {deleteid:deleteid,parentid:parentid},
	  dataType: "html"
	});
	request.done(function(msg) {
		if(msg==1)
		{
			delete_row(idd);
			event.preventDefault();
			return false;			
		}
		else if(msg==0)
		{
			//$(".email-message").css("display", "none");
		}
	});
	request.fail(function(jqXHR, textStatus) {
	alert( "Request failed: " + textStatus );
	});
}

function today_amount_recv()
{
	var request = $.ajax({
	  url: "<?php echo SURL ?>/SaleLPG/today_amount_recv",
	  type: "POST",
	  data: {},
	  dataType: "html"
	});
	request.done(function(msg) {
		 
				$("#amt_recv").val(msg);
			 	
		 
	});
	request.fail(function(jqXHR, textStatus) {
	alert( "Request failed: " + textStatus );
	});
}

// $('#item').on('change', function() {
// 	var item_id= $(this).val();
// 	var date= $('#id-date-picker-1').val();

// 	var qtys11=0;
	
// 	$('#data_table1 > #data_table2  > tr').each(function() {

// 		var id=$(this).closest("tr").find("td:eq(0)").attr('id');
// 		if(id==item_id)
// 		{

// 		 var value=$(this).closest("tr").find("td:eq(0)").text();
			
// 		 qtys11 =parseInt(value);

// 		}

// 	});
	
	

	 
// 	//alert(date);return false;
// 		if(item_id=='' || date=='')
// 		{
// 			//$("#filledstock").value()="";
// 			document.getElementById("filledstock").value="";
// 			return false;
// 		}
// 		var request = $.ajax({
// 		  url: "<?php echo SURL ?>Common/stock",
// 		  type: "POST",
// 		  data: {item_id:item_id,date:date},
// 		  dataType: "html"
// 		});
// 		request.done(function(msg) {
 

// 			var empty_filled=msg.split('_');
// 			$("#filledstock").val(empty_filled[0]);
			
// 				var catcode = empty_filled[4];
			
// 				/////////////////////////////// here is price logic
				
// if(catcode==1){
// 	var actual_price_11=  $('#kg_11_price').val()  ;
// 	var kg_11_price=  (($('#kg_11_price').val()/11.8)).toFixed(2) ;
	 
// 	if(kg_11_price!='' || kg_11_price!='0'){
// 		var new_price=0;
// 		if(empty_filled[3]=='11.8'){
// 		 new_price =actual_price_11;
// 		}else{
// 		 new_price=(kg_11_price*empty_filled[3]).toFixed(0) ;  
// 		}
		
// 	$("#price").val(new_price);
// 	}else{
			
// 			$("#price").val(empty_filled[2]);

// 				} 
// 				}else{
					
// 			$("#price").val(empty_filled[2]);
					
// 				}
		
// 		if(catcode!=1){
// 			//$('.types').hide();
// 			//$('.gasamount').hide();
// 			//$('#type').attr('disabled',true);
// 			$('#security').attr('disabled',true);
// 			$('#returns').attr('disabled',true);
// 			//$('.price').show();
// 			//document.getElementById('qty').value=0;
// 			//document.getElementById('gasamt').value=0;
// 			//document.getElementById('amounttotal').value=0;
// 			//document.getElementById('gasamt').disabled=false;
// 			//document.getElementById("type").value="Filled";
// 			//$("#addremove").attr("onclick","checking_without()");
// 		}else{
// 			//$('.types').show();
// 			//$('.gasamount').show();
// 			//$('.price').hide();
// 			//$('#type').attr('disabled',false);
// 			$('#security').attr('disabled',false);
// 			$('#returns').attr('disabled',false);
// 			//document.getElementById('qty').value=0;
// 			//document.getElementById('gasamt').value=0;
// 			//document.getElementById('amounttotal').value=0;
// 			//$("#addremove").attr("onclick","checking()");

// 		}


// 		});
// 		var request = $.ajax({
// 		  url: "<?php echo SURL ?>Common/similaritem",
// 		  type: "POST",
// 		  data: {item_id:item_id,date:date},
// 		  dataType: "html"
// 		});
// 		request.done(function(msg) { 

// 			var empty_filled=msg.split('_');
// 			$("#item_return").html(empty_filled[1]);

			 

// 		var catcode = empty_filled[0];
		
// 		if(catcode!=1){
// 			 // no return
			 
// 		}else{
// 			 /// return items

// 		}


// 		});
// 		request.fail(function(jqXHR, textStatus) {
// 		  alert( "Request failed: " + textStatus );
// 		});
// });

$('#id-date-picker-1').on('change', function() {
 
	var date= $(this).val();
	var item_id= $('#item').val();
 		if(item_id=='' || date=='')
		{
 			document.getElementById("filledstock").value=""; 
			return false;
		}
		var request = $.ajax({
		  url: "<?php echo SURL ?>common/stock",
		  type: "POST",
		  data: {item_id:item_id,date:date},
		  dataType: "html"
		});
		request.done(function(msg) {

			var empty_filled=msg.split('_');
			$("#filledstock").val(empty_filled[0]);
			$("#price").val(empty_filled[2]);

		});
		request.fail(function(jqXHR, textStatus) {
		  alert( "Request failed: " + textStatus );
		});
});

$('#qty').blur(function() {
	var qty = $("#qty").val();
	var filledstock = $("#filledstock").val();
	if(Number(qty) > Number(filledstock)){ 
		document.getElementById("qty").value="";
		document.getElementById("qty").focus();
		alert("Quantity Should not greater than Filled Stock?");
		return false;
	}	
});

$('#returns').blur(function() {
	var returns = $("#returns").val();
	if(returns!=0){
		document.getElementById("security").value="0";
	}
		
});

function Security_zero(no)
{
	var returns=document.getElementById('returns_text'+no).value;
	if(returns!=0){
		document.getElementById("security_text"+no).value="0";
	}
}

function itemchange(itemid,no){
	var kg_11_price = $("#kg_11_price").val();
	if(kg_11_price=="" || kg_11_price==0 || isNaN(kg_11_price)){
		alert("Please enter 11.8 kg price first.");
		if(no==1){
			no= "";
		}
		$("#item"+no).val("");

		$("#kg_11_price").val("");
		$("#kg_11_price").focus();
		return false;
	}

	var item_id= itemid;
	var date= $('#id-date-picker-1').val();
	var actual_price_11=  $('#kg_11_price').val();

	if(no=="1"){
		i = "";
	}else{
		i = no;
	}

	var item_text = $("#item"+i+" option:selected").text();
 

		if(item_id=='' || date=='')
		{
			document.getElementById("stock"+no).value="";
			return false;
		}
		var request = $.ajax({
		  url: "<?php echo SURL ?>common/stock",
		  type: "POST",
		  data: {item_id:item_id,date:date},
		  dataType: "html"
		});
		request.done(function(msg) {

			var empty_filled=msg.split('_');
 
		   $("#stock"+no).val(empty_filled[0]);
		   $("#stock_item_"+no).val(empty_filled[0]);

		   $("#item_h"+no).text(item_text);

		   checkExistingItem(itemid,no);

 			var kg_11_price=  (($('#kg_11_price').val()/11.8)).toFixed(2);

 			if(kg_11_price!='' || kg_11_price!='0'){
				var new_price=0;
				if(empty_filled[3]=='11.8'){
		 			new_price =actual_price_11;
				}else{
				 	new_price=(kg_11_price*empty_filled[3]).toFixed(0) ;  
				}

				$("#price"+no).val(new_price);
				

				CalAmount();
			}

		});
		request.fail(function(jqXHR, textStatus) {
		  alert( "Request failed: " + textStatus );
		});
}

function checkExistingItem(item,no){

	var item1 = $("#existing_item1").val();
	var item2 = $("#existing_item2").val();
	var item3 = $("#existing_item3").val();

	if(no=="1"){
		if(item==item2){
			alert("Item is already added.");
			$("#item").val("");
			$("#item").focus();
			$("#stock_item_1").val("");
			$("#item_h1").text("");
			return false;
		}else if(item==item3){
			alert("Item is already added.");
			$("#item").val("");
			$("#item").focus();
			$("#stock_item_1").val("");
			$("#item_h1").text("");
			return false;
		}else{
			$("#existing_item1").val(item);
		}
	}


	if(no=="2"){
		if(item==item1){
			alert("Item is already added.");
			$("#item2").val("");
			$("#item2").focus();
			$("#stock_item_2").val("");
			$("#item_h2").text("");
			return false;
		}else if(item==item3){
			alert("Item is already added.");
			$("#item2").val("");
			$("#item2").focus();
			$("#stock_item_2").val("");
			$("#item_h2").text("");
			return false;
		}else{
			$("#existing_item2").val(item);
		}
	}

	if(no=="3"){
		if(item==item1){
			alert("Item is already added.");
			$("#item3").val("");
			$("#item3").focus();
			$("#stock_item_3").val("");
			$("#item_h3").text("");

			return false;
		}else if(item==item2){
			alert("Item is already added.");
			$("#item3").val("");
			$("#item3").focus();
			$("#stock_item_3").val("");
			$("#item_h3").text("");
			return false;
		}else{
			$("#existing_item3").val(item);
		}
	}
}

/*$('#confirmboxcross').click(function(){
	alert("df");
	$(".bootbox-confirm").hide();
	$(".modal-backdrop").hide();
});
$('bootbox-close-button').on('click', function(e) {		
	alert("df");
	$(".bootbox-confirm").hide();
	$(".modal-backdrop").hide();
});*/
		var total_sum_gas_amount=0;
var total_sum_security_amount=0;
	
	$('.total_sum_input_gas').each(function(i, obj) {
		var input_value=parseInt($(this).val());
		total_sum_gas_amount=total_sum_gas_amount+input_value;
    
	});	

	$('#gas_amount_sum_total_text').text(total_sum_gas_amount);

	$('.total_sum_input_security').each(function(i, obj) {
		var input_value=parseInt($(this).val());
		total_sum_security_amount=total_sum_security_amount+input_value;
    
	});	
	$('#security_sum_total_text').text(total_sum_security_amount);
	
	
	
	
	
 
	
	
	
	
function change_pay_mode(pay_value) {
		
	if(pay_value=='Cash')
	{

		$(".bank_amount_row").hide();
	 
	 	$('.cheque_no').val('');
	}
	 
	else
	{
		$(".bank_amount_row").show();
    	 
		$('.cheque_date').val(0);
			
			
			
		 
	}

}

</script>