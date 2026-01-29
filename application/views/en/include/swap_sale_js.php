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
    $(".editbtn").prop("disabled",true);
	$(".deletebtn").prop("disabled",true);
    
    var qty=document.getElementById("qty_row"+no);
    var gst=document.getElementById("gst_row"+no);
    var price=document.getElementById("price_row"+no);  
    var filledstock=document.getElementById("filledstock_row"+no);
    var gst_amounttotal=document.getElementById("gst_amounttotal_row"+no);
    var ex_amounttotal=document.getElementById("ex_amounttotal_row"+no);
    var amounttotal=document.getElementById("amounttotal_row"+no);

  var gtotal_total=document.getElementById("gtotal_row"+no); 
    $("#item_"+no).attr("disabled",true);
	
    var qty_data=qty.innerHTML;
    var price_data=price.innerHTML;
  
    var filledstock_data=filledstock.innerHTML;
    var amounttotal_data=amounttotal.innerHTML;
    var qty_val=document.getElementById("qty_text"+no).value;
    var gst_val=document.getElementById("gst_text"+no).value;
    var price_val=document.getElementById("price_text"+no).value;
 
    var filledstock_val=document.getElementById("filledstock_text"+no).value;

    var gst_amounttotal_val=document.getElementById("gst_amounttotal_text"+no).value;
    var ex_amounttotal_val=document.getElementById("ex_amounttotal_text"+no).value;
    var amounttotal_val=document.getElementById("amounttotal_text"+no).value;
    var gtotal_text_val=document.getElementById("gtotal_text"+no).value; 

    $('#row'+no+' input[type=text]').remove();

    var totalstock = parseInt(qty_val)+parseInt(filledstock_val);

	var pricing_centralized= document.getElementById("pricing_centralized").value;
 
    qty.innerHTML="<input maxlength='6' type='text' id='qty_text"+no+"' value='"+qty_val+"'  onkeyup='CalAmounts("+no+")' pattern='^[0-9]+$' title='Only Numbers Allowed...' required>";
   
if(pricing_centralized=='Yes'){
   price.innerHTML="<input maxlength='5' type='text' readonly id='price_text"+no+"' value='"+price_val+"'  onkeyup='CalAmounts("+no+")' pattern='^[0-9]+$' title='Only Numbers Allowed...' required>";
}else{
   price.innerHTML="<input maxlength='5' type='text'  id='price_text"+no+"' value='"+price_val+"'  onkeyup='CalAmounts("+no+")' pattern='^[0-9]+$' title='Only Numbers Allowed...' required>";
	
}
    gst.innerHTML="<input maxlength='2' type='text' id='gst_text"+no+"' value='"+gst_val+"'  onkeyup='CalAmounts("+no+")' pattern='^[0-9]+$' title='Only Numbers Allowed...' required>";
    filledstock.innerHTML="<input type='text' id='filledstock_text"+no+"' value='"+totalstock+"'   disabled='disabled'>";
    
      gtotal_total.innerHTML="<input class='total_sum_input_gas form-control' type='text' id='gtotal_text"+no+"' value='"+gtotal_text_val+"'   disabled='disabled'>";


    gst_amounttotal.innerHTML="<input type='text' id='gst_amounttotal_text"+no+"' value='"+gst_amounttotal_val+"'   disabled='disabled'>";
    ex_amounttotal.innerHTML="<input type='text' id='ex_amounttotal_text"+no+"' value='"+ex_amounttotal_val+"'   disabled='disabled'>";
    amounttotal.innerHTML="<input type='text' id='amounttotal_text"+no+"' value='"+amounttotal_val+"'  onkeyup='CalAmounts("+no+")' disabled='disabled'>";

 $('.table>tbody>tr>td>input').addClass('form-control');
 $('.table>tbody>tr>td>select').addClass('form-control');
 $("#qty_text"+no).prop('readonly',false);
 $("#qty_text"+no).focus();

	$('.total_sum_input_gas').each(function(i, obj) {
		var input_value=parseInt($(this).val());
		total_sum_gas_amount=total_sum_gas_amount+input_value;
    
	});	

	if($('#row'+no+' td').attr('class')=='disables')
	{
	}
}
function save_row(no)
{
	 
    //var item_val=document.getElementById("item_text"+no).value;
    var item_val=document.getElementById("item_"+no).value;
    var qty_val=document.getElementById("qty_text"+no).value;
    var gst_val=document.getElementById("gst_text"+no).value;
    var price_val=document.getElementById("price_text"+no).value;
 
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
		alert("Quantity Should not greater than Empty Stock?");
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
    	window.location.replace('<?php echo SURL."Swap_cylinder_sale/" ?>');
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

			var empty_filled=msg.split('_');
			//alert(empty_filled);
	
			$("#filledstock_text"+row_number).val(empty_filled[0]);
			//$("#price_text"+row_number).val(empty_filled[2]);
			row_number=row_number+1;


		});	  
		request.fail(function(jqXHR, textStatus) {
		  alert( "Request failed: " + textStatus );
		});
});

function add_row()
{
	if($("#security").prop('disabled')==true){
		var security_disable="disables";
	}


	$("#id-date-picker-1").prop("disabled", true);
	//document.getElementById("filledstock").value="";
    $("#data_table1").show();
    $("#submithide").show();
		$("#item").prop('required',false);
		$("#qty").prop('required',false);
		$("#price").prop('required',false);
		document.getElementById("addremove").removeAttribute("type");
		$("#addremove").prop('type','button');
   	var item=document.getElementById("item").value;
   	//return false;
    var qty=document.getElementById("qty").value;
    var gst=document.getElementById("gst").value;
    var price=document.getElementById("price").value;
  
    var filledstock=document.getElementById("filledstock").value;
    var amounttotal=document.getElementById("amounttotal").value;
    var ex_amounttotal=document.getElementById("ex_amounttotal").value;
    var gst_amounttotal=document.getElementById("gst_amounttotal").value;
    //var amountreceived=document.getElementById("amountreceived").value;

	var filledstock = parseInt(filledstock)-parseInt(qty);

 
	var gasamt=qty*price;

	//document.getElementById("securityamt").value=securityamt;
    //document.getElementById("gasamt").value=gasamt;
    //document.getElementById("totalrecv").value=securityamt+gasamt;
	
	
    //alert(tsamt);
    


    $('#data_table1 > #data_table2  > tr').each(function() {

	 	var id=$(this).closest("tr").find("td:eq(0)").attr('id');
	 	var itemids=id;
	 	if(id==item)
	 	{
	 	 //var value=$(this).closest("tr").find("td:eq(0)").text();
	 		
	 	 //qtys11 =parseInt(qtys11)+ parseInt(value);
	 	 
	 	}
	 	if(item==itemids){
	 		alert("Item has been already added.");
			var itemvalue = $("#item_"+table_len+"").val(item);
			//var typevalue = $("#type_"+table_len+"").val(type);
			document.getElementById("item").value="";
			document.getElementById("qty").value="0";
			document.getElementById("price").value="0";
 
		 
			//document.getElementById("amountreceived").value="";
			document.getElementById("filledstock").value="";
			document.getElementById("amounttotal").value="0";
			$('.table>tbody>tr>td>input').addClass('form-control');
			$('.table>tbody>tr>td>select').addClass('form-control');
	 		console.log(x);
	 		return false;
	 	}
	 	 
	 });
    
	var securityamt = "";
		var pricing_centralized= document.getElementById("pricing_centralized").value;
	
    // <input type='text' id='item_text"+table_len+"' style='width: 250px' disabled value='"+item+"'>
    var table=document.getElementById("data_table2");
    var table_len=(table.rows.length);
    var row = table.insertRow(table_len).outerHTML="<tr id='row"+table_len+"'>"+
	"<td style='display:none;' id='"+item+"' class='"+security_disable+"'>"+filledstock+"</td>"+
	"<td style='display:none;' class='"+table_len+"' id='"+securityamt+"' >"+gasamt+"</td>"+
	"<td id='item_row"+table_len+"' style='width:15% !important;'><select class='form-control disable' name='item[]' id='item_"+table_len+"' data-placeholder='Choose a Item...' required='required' disabled onchange='itemchange(this.value,"+table_len+")'><?php 
					foreach ($item_list as $key => $data) {
						?><option value='<?php echo $data['materialcode']; ?>'><?php 
    							echo ucwords($data['itemname']); 
    					?></option><?php 
    				} 
    				?></select></td>"+
					"<td id='qty_row"+table_len+"'><input type='text' id='qty_text"+table_len+"' readonly tabindex='-1' value='"+qty+"' name='qty[]'></td>"+
					"<td id='price_row"+table_len+"'><input type='text' id='price_text"+table_len+"' readonly  tabindex='-1' value='"+price+"' name='price[]'></td>"+
					"<td hidden class='hidden-480'  id='gst_row"+table_len+"'><input type='hidden' id='gst_text"+table_len+"' readonly  value='"+gst+"' name='gst[]'></td>"+
					  
					"<td  class='hidden-480'  id='filledstock_row"+table_len+"'><input type='text' id='filledstock_text"+table_len+"' readonly tabindex='-1' value='"+filledstock+"' name='filledstock[]'></td>"+
 
					"<td style='display:none;' class='hidden-480'  id='gtotal_row"+table_len+"'><input type='text' id='gtotal_text"+table_len+"' readonly tabindex='-1' value='"+qty*price+"' class='total_sum_input_gas' name='gtotal[]'></td>"+
					"<td hidden id='gst_amounttotal_row"+table_len+"'><input type='hidden' id='gst_amounttotal_text"+table_len+"' readonly  value='"+gst_amounttotal+"' class='total_sum_gst_amounttotal' name='gst_amounttotal[]'></td>"+
					"<td hidden id='ex_amounttotal_row"+table_len+"'><input type='hidden' id='ex_amounttotal_text"+table_len+"' readonly  value='"+ex_amounttotal+"' class='total_sum_input_ex' name='ex_amounttotal[]'></td>"+
					"<td id='amounttotal_row"+table_len+"'><input type='text' id='amounttotal_text"+table_len+"' readonly tabindex='-1' value='"+amounttotal+"' class='total_sum_input' name='amounttotal[]'></td>"+
					"<td style='display: inline-flex; border: 0px;'><input type='button' id='edit_button"+table_len+"' value='Edit' class='btn btn-xs btn-success editbtn' onclick='edit_row("+table_len+");'>"+
					"<input type='button' id='save_button"+table_len+"' value='Save' class='btn btn-xs btn-warning' onclick='savechecking("+table_len+"); culculate_security()' style='display:none'>"+
					"<input type='button' value='Delete' class='btn btn-xs btn-danger deletebtn' onclick='delete_row("+table_len+");culculate_security()'></td></tr>";


			// if(table_len==0)
			// {
			// 	table.append("<tr><td id='total_security_sum_row'><input type='text' id='total_security_sum_text' readonly  value='"+filledstock+"' name='total_security_name[]'></td></tr>")
			// }

    var itemvalue = $("#item_"+table_len+"").val(item);
     document.getElementById("item").value=""; 
    document.getElementById("qty").value="0";
    document.getElementById("price").value="0";

    document.getElementById("filledstock").value="";
    document.getElementById("amounttotal").value="0";
    document.getElementById("ex_amounttotal").value="0";
    document.getElementById("gst_amounttotal").value="0";
    document.getElementById("gst").value="0";
    //document.getElementById("amountreceived").value="";
    $("#item").focus();
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
		//alert(total_sum_amount);
    
	});	

 
	
	 
	var tgasamt=total_sum_amount;

 
    // document.getElementById("securityamt").value=tsamt;
    // document.getElementById("gasamt").value=tgasamt;
    // document.getElementById("totalrecv").value=tsamt+tgasamt;

  document.getElementById("total_bill").value=tgasamt;
	$("#tempty").show();

	var fprice = $("#final_total_price").text();
	var fsecurity = $("#security_total_price").text();
	var ftotal = $("#total_total_price").text();

	$("#final_total_price").text(parseInt(fprice)+parseInt(price));
	$("#security_total_price").text(parseInt(fsecurity)+parseInt(security));
	$("#total_total_price").text(parseInt(ftotal)+parseInt(amounttotal));

	 var gstp= parseFloat($('#gstp').val());
    var total_bill= parseFloat($('#total_bill').val());
	var vat_amount=total_bill*gstp/100;
	$('#vat_amount').val(vat_amount.toFixed(2));
	var inc_vat_amount=parseFloat(total_bill)+parseFloat(vat_amount);
	$('#inc_vat_amount').val(inc_vat_amount.toFixed(2));

	
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
function culculate_gst(gstp) {

    var total_bill= parseFloat($('#total_bill').val());
	var vat_amount=total_bill*gstp/100;
	$('#vat_amount').val(vat_amount.toFixed(2));
	var inc_vat_amount=parseFloat(total_bill)+parseFloat(vat_amount);
	$('#inc_vat_amount').val(inc_vat_amount.toFixed(2));

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

//alert(totalrecv);

	//var net_amount=gas_amount_sum_total_text-returntotal;
	//var securityamt=parseInt($('#securityamt').val());

	 // $('#totalrecv').val(securityamt+net_amount);




}


function CalAmount()
	{
	var price=parseInt(document.getElementById('price').value);
	var security=parseInt(document.getElementById('security').value);
	var gst=parseInt(document.getElementById('gst').value);
	var qty=parseInt(document.getElementById('qty').value);
	
	if(security!=0){
		document.getElementById("returns").value="0";
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
	
	var amounttotal=(parseInt(qty*ucost_with_gst_price)+parseInt(qty*security)).toFixed(0);
	var amounttotal_ex=qty*price+qty*security;
	document.getElementById("amounttotal").value=amounttotal;

 
	document.getElementById("gst_amounttotal").value=ucost_with_gst_only*qty;
	document.getElementById("ex_amounttotal").value=amounttotal_ex;
	
	
	
	
}

 
function CalAmounts(no)
	{
	var qty=parseInt(document.getElementById('qty_text'+no).value);
	var gst=parseInt(document.getElementById('gst_text'+no).value);
	var price=parseInt(document.getElementById('price_text'+no).value);
 
	
	// if(security!=0){
		// document.getElementById('returns_text'+no).value="0";
	// }

 

	//document.getElementById("securityamt").value=securityamt;

	var gasamts=qty*price;
	 
		document.getElementById('gtotal_text'+no).value=gasamts;
	//document.getElementById("totalrecv").value=securityamt+gasamt;
	var tsamt=0;
	var tgasamt=0;

    $('#data_table1 > #data_table2  > tr').each(function() {

    	var clas=$(this).closest("tr").find("td:eq(1)").attr('class');
		if(clas==no){
 
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
   

   	// if(security!=0){
		// document.getElementById("returns_text"+no).value="0";
	// }

	var ucost_with_gst=0;
	var ucost_with_gst_only=0;
	if(gst!=0){
	  ucost_with_gst=price*gst/100;
	  ucost_with_gst_price=(price*gst/100)+price;
	  ucost_with_gst_only=ucost_with_gst;
	
	}else{
		  ucost_with_gst_price=price;
	}
	
	var amounttotal=parseInt(qty*ucost_with_gst_price);
	var amounttotal_ex=qty*price;
	  
	document.getElementById("amounttotal_text"+no).value=amounttotal;
	document.getElementById("gst_amounttotal_text"+no).value=ucost_with_gst_only*qty;
	document.getElementById("ex_amounttotal_text"+no).value=amounttotal_ex;

	
	}

 
function checking(){
	 var vendor=document.getElementById('customer').value;
	 var item=document.getElementById('item').value;
	 var qty=document.getElementById('qty').value;
	 var price=document.getElementById('price').value;	 
if(item==""){
	alert("Please select item.");
	$("#item").focus();
	return false;
}else if(qty=='0' || qty=='0.00' || isNaN(qty)){
	alert("Minimum 1 quantity allowed.");
	$("#qty").focus();
	return false;
}else if(price=='0' || price=='0.00' || isNaN(price)){
	alert("Minimum 1 quantity and price allowed.");
	$("#price").focus();
	return false;
}else if (isNaN(qty) && isNaN(price)  ) {
		$("#item").prop('required',true);
		$("#qty").prop('required',true);
		$("#price").prop('required',true);

		alert("Please enter numbers in quantity and price.");
		return false;

}  else {
		document.getElementById("addremove").removeAttribute("type");
		$("#addremove").prop('type','button');
		add_row();
				
	}
}
function savechecking(no){
	 var qty=document.getElementById("qty_text"+no).value;
	 var price=document.getElementById("price_text"+no).value;
	 var gst = $("#gst_text"+no).val();
 
	 
if (isNaN(qty) || qty=="" || qty==0 ) {
	alert("Please enter quantity in numbers format.");
	$("#qty_text"+no).focus();
	return false;

} else if (price=='' || price==0 || isNaN(price) ) {
	alert("Please enter price in numbers format.");
	$("#price_text"+no).focus();
	return false;
} else if (isNaN(gst) ) {
	alert("Please enter gst in numbers format.");
	$("#gst_text"+no).focus();
	return false;
} else { 
		$("#save_button"+no).prop('type','button');
		$("#type_change").prop("disabled",false);
		$(".editbtn").prop("disabled",false);
	    $(".deletebtn").prop("disabled",false);
		save_row(no);
				
	}
		totals_of_totals();
}
function setting_submit(){
	
	$("#id-date-picker-1").prop("disabled", false);
	var customer=document.getElementById('customer').value;

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
	  url: "<?php echo SURL ?>/Swap_cylinder_sale/record_delete",
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

$('#item').on('change', function() {

	var item_id= $(this).val();
	var date= $('#id-date-picker-1').val();

	var qtys11=0;
	
	$('#data_table1 > #data_table2  > tr').each(function() {

		var id=$(this).closest("tr").find("td:eq(0)").attr('id');
		if(id==item_id)
		{

		 var value=$(this).closest("tr").find("td:eq(0)").text();
			
		 qtys11 =parseInt(value);

		}

	});
	//alert(date);return false;
		if(item_id=='' || date=='')
		{
			 
			document.getElementById("filledstock").value="";
			return false;
		}
		var request = $.ajax({
		  url: "<?php echo SURL ?>Common/stock",
		  type: "POST",
		  data: {item_id:item_id,date:date},
		  dataType: "html"
		});
		request.done(function(msg) {
 // alert(msg);

			var empty_filled=msg.split('_');
			$("#filledstock").val(empty_filled[0]);
			 

		var catcode = myObj.catcode;
		
		if(catcode!=1){
			//$('.types').hide();
			//$('.gasamount').hide();
			//$('#type').attr('disabled',true);
			$('#security').attr('disabled',true);
			$('#returns').attr('disabled',true);
			 
		}else{
		 
			$('#security').attr('disabled',false);
			$('#returns').attr('disabled',false);
		 

		}


		});
	 
		request.fail(function(jqXHR, textStatus) {
		  alert( "Request failed: " + textStatus );
		});
});

$('#id-date-picker-1').on('change', function() {
	//alert("juan");
	//var itemid= $('#item_0').val();
	//console.log(itemid);return false;
	var date= $(this).val();
	var item_id= $('#item').val();
	//alert(item_id);return false;
		if(item_id=='' || date=='')
		{
			//$("#filledstock").value()="";
			document.getElementById("filledstock").value="";
			//alert("junaid");
			//$("#city").html('<option> Select Country First... </option>');
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
	var stock_check = $("#stock_check").val();
	var filledstock = $("#filledstock").val();
	if(Number(qty) > Number(filledstock) && stock_check=='true'){
		//$(".bootbox-confirm").show();
		//$(".modal-backdrop").show();
		document.getElementById("qty").value="";
		document.getElementById("qty").focus();
		alert("Quantity Should not greater than Empty Stock?");
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
	//document.getElementById("filledstock").value
	var item_id= itemid;
	var date= $('#id-date-picker-1').val();
	//alert(date);return false;
		if(item_id=='' || date=='')
		{
			//$("#filledstock").value()="";
			document.getElementById("filledstock_text"+no).value="";
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

		   $("#filledstock_text"+no).val(empty_filled[0]);
		   $("#price_text"+no).val(empty_filled[2]);

		});
		request.fail(function(jqXHR, textStatus) {
		  alert( "Request failed: " + textStatus );
		});
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