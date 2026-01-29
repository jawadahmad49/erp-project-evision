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
function edit_row(no)
{
    $("#edit_button"+no).hide();
    $("#save_button"+no).show();
    
    $("#item_"+no).attr("disabled",true);
    $("#refillnew_"+no).attr("disabled",true);
    
    var qty=document.getElementById("qty_row"+no);
    //var refillnew=document.getElementById("refillnew_row"+no);
    //var security=document.getElementById("security_row"+no);
    //var returns=document.getElementById("returns_row"+no);
    //var amounttotal=document.getElementById("amounttotal_row"+no);
    //var amountreceived=document.getElementById("amountreceived_row"+no);

    //$("#item_"+no).prop("disabled", false);

    //var item_data=item.innerHTML;
    var qty_data=qty.innerHTML;
    //var price_data=price.innerHTML;
    //var security_data=security.innerHTML;
    //var returns_data=returns.innerHTML;
    //var amounttotal_data=amounttotal.innerHTML;
    //var amountreceived_data=amountreceived.innerHTML;
//console.log();return false;
    //var item_val=document.getElementById("item_text"+no).value;
    var qty_val=document.getElementById("qty_text"+no).value;
    //var price_val=document.getElementById("price_text"+no).value;
    //var security_val=document.getElementById("security_text"+no).value;
    //var returns_val=document.getElementById("returns_text"+no).value;
    //var amounttotal_val=document.getElementById("amounttotal_text"+no).value;
    //var amountreceived_val=document.getElementById("amountreceived_text"+no).value;
    $('#row'+no+' input[type=text]').remove();

    //item.innerHTML="<input type='text' id='item_text"+no+"' value='"+item_val+"' style='width:250px'>";
    qty.innerHTML="<input type='text' id='qty_text"+no+"' value='"+qty_val+"' pattern='^[0-9]+$' title='Only Numbers Allowed...' required maxlength='5'>";
    //price.innerHTML="<input type='text' id='price_text"+no+"' value='"+price_val+"' style='width:60px' onkeyup='CalAmounts("+no+")'>";
    //security.innerHTML="<input type='text' id='security_text"+no+"' value='"+security_val+"' style='width:75px' onkeyup='CalAmounts("+no+")'>";
    //returns.innerHTML="<input type='text' id='returns_text"+no+"' value='"+returns_val+"' style='width:75px' onkeyup='CalAmounts("+no+")'>";
    //amounttotal.innerHTML="<input type='text' id='amounttotal_text"+no+"' value='"+amounttotal_val+"' style='width:75px' onkeyup='CalAmounts("+no+")'>";
    //amountreceived.innerHTML="<input type='text' id='amountreceived_text"+no+"' value='"+amountreceived_val+"' style='width:75px' onkeyup='CalAmounts("+no+")'>";
 $('.table>tbody>tr>td>input').addClass('form-control');
 $('.table>tbody>tr>td>select').addClass('form-control');
 $("#qty_text"+no).focus();
    
}
function save_row(no)
{
	$("#item_"+no).attr("readonly",true);
	$("#refillnew_"+no).attr("readonly",true);
    //var item_val=document.getElementById("item_text"+no).value;
    var qty_val=document.getElementById("qty_text"+no).value;
   // var price_val=document.getElementById("price_text"+no).value;
    //var security_val=document.getElementById("security_text"+no).value;
    //var returns_val=document.getElementById("returns_text"+no).value;
    //var amounttotal_val=document.getElementById("amounttotal_text"+no).value;
    //var amountreceived_val=document.getElementById("amountreceived_text"+no).value;

	$("#item_"+no).attr("disabled",false);
	$("#refillnew_"+no).attr("disabled",false);
	$("#item_"+no).attr("readonly",true);
	$("#refillnew_"+no).attr("readonly",true);

    //document.getElementById("item_row"+no).innerHTML="<input style='width: 250px' type='text' id='item_text"+no+"' name='item' value='"+item_val+"' disabled>";
    document.getElementById("qty_row"+no).innerHTML="<input  type='text' id='qty_text"+no+"' name='qty[]' value='"+qty_val+"' readonly>";
    //document.getElementById("price_row"+no).innerHTML="<input style='width: 60px' type='text' id='price_text"+no+"' name='price[]' value='"+price_val+"' readonly>";
    //document.getElementById("security_row"+no).innerHTML="<input style='width: 75px' type='text' id='security_text"+no+"' name='security[]' value='"+security_val+"' readonly>";
    //document.getElementById("returns_row"+no).innerHTML="<input style='width: 75px' type='text' id='returns_text"+no+"' name='returns[]' value='"+returns_val+"' readonly>";
    //document.getElementById("amounttotal_row"+no).innerHTML="<input style='width: 75px' type='text' id='amounttotal_text"+no+"' name='amounttotal[]' value='"+amounttotal_val+"' readonly>";
    //document.getElementById("amountreceived_row"+no).innerHTML="<input style='width: 75px' type='text' id='amountreceived_text"+no+"' name='amountreceived[]' value='"+amountreceived_val+"' readonly>";

    //document.getElementById("edit_button"+no).style.display="block";
    //document.getElementById("save_button"+no).style.display="none";

    $("#edit_button"+no).show();
    $("#save_button"+no).hide();
     $('.table>tbody>tr>td>input').addClass('form-control');
 $('.table>tbody>tr>td>select').addClass('form-control');

}
function delete_row(no)
{
    document.getElementById("row"+no+"").outerHTML="";
    var table=document.getElementById("data_table2");
    var table_len=(table.rows.length);
    if(table_len==0){
    	$("#data_table1").hide();
    	$("#submithide").hide();
    }
    
}
function add_row()
{
var refillnew=document.getElementById("refillnew").value;
var item=document.getElementById("item").value;

    $("#data_table1").show();
    $("#submithide").show();
		$("#item").prop('required',false);
		$("#qty").prop('required',false);
		//$("#price").prop('required',false);
		document.getElementById("addremove").removeAttribute("type");
		$("#addremove").prop('type','button');
   	var item=document.getElementById("item").value;
   	//return false;
    var qty=document.getElementById("qty").value;
   	var refillnew=document.getElementById("refillnew").value;
  
$('#data_table1 > #data_table2  > tr').each(function() {

	 	var ids=$(this).closest("tr").find("td:eq(0)").attr('id');
	 	var refillnews=$(this).closest("tr").find("td:eq(0)").attr('class');
	 	
	 	/*if(id==item)
	 	{
	 	 var value=$(this).closest("tr").find("td:eq(0)").text();
	 		
	 	 qtys11 =parseInt(qtys11)+ parseInt(value);
	 	 
	 	}*/
	 	if(ids==item && refillnew==refillnews){
	 		alert("Item has been already added.");
			//var itemvalue = $("#item_"+table_len+"").val(item);
			//var typevalue = $("#type_"+table_len+"").val(type);
			document.getElementById("item").value="";
			document.getElementById("qty").value="";
			document.getElementById("refillnew").value="refill";
			$('.table>tbody>tr>td>input').addClass('form-control');
			$('.table>tbody>tr>td>select').addClass('form-control');
	 		console.log(x);
	 		return false;
	 	}
	 	 
	 });

    
    // <input type='text' id='item_text"+table_len+"' style='width: 250px' disabled value='"+item+"'>
    var table=document.getElementById("data_table2");
    var table_len=(table.rows.length);
    var row = table.insertRow(table_len).outerHTML="<tr id='row"+table_len+"'><td style='display:none;' id='"+item+"' class='"+refillnew+"'></td><td id='item_row"+table_len+"' style='width:62%'><select class='form-control' name='item[]' id='item_"+table_len+"' data-placeholder='Choose a Item...' required='required' readonly><?php 
					foreach ($item_list as $key => $data) {
						?><option value='<?php echo $data['materialcode']; ?>'><?php 
    							echo ucwords($data['itemname']); 
    					?></option><?php 
    				} 
    				?></select>		</td>	<td style='width:10%' id='qty_row"+table_len+"'>	<input type='text' id='qty_text"+table_len+"' readonly  readonly value='"+qty+"' name='qty[]'>	</td>	<td style='width:12%'><select readonly='readonly' class='form-control' name='refillnew[]' id='refillnew_"+table_len+"'><option value='refill'>Refill</option><option value='new'>New</option></select></td><td style='display: inline-flex; border: 0px;width: 100%'><input type='button' id='edit_button"+table_len+"' value='Edit' class='btn btn-xs btn-success' onclick='edit_row("+table_len+")'> <input type='button' id='save_button"+table_len+"' value='Save' class='btn btn-xs btn-warning' onclick='savechecking("+table_len+")' style='display:none'> <input type='button' value='Delete' class='btn btn-xs btn-danger' onclick='delete_row("+table_len+")'></td></tr>";

    var itemvalue = $("#item_"+table_len+"").val(item);
    var refillnewvalue = $("#refillnew_"+table_len+"").val(refillnew);
    document.getElementById("item").value="";
    document.getElementById("qty").value="";
    document.getElementById("refillnew").value="refill";
     $('.table>tbody>tr>td>input').addClass('form-control');
 $('.table>tbody>tr>td>select').addClass('form-control');
}
</script>
<!-- end add update delte rows using javascript -->
<script type="text/javascript">
function CalAmount()
	{
	var price=document.getElementById('price').value;
	var security=document.getElementById('security').value;
	var qty=document.getElementById('qty').value;
	var amounttotal=qty*price+qty*security;
	document.getElementById("amounttotal").value=amounttotal;
}
function CalAmounts(no)
	{
	var price=document.getElementById('price_text'+no).value;
	var security=document.getElementById('security_text'+no).value;
	var qty=document.getElementById('qty_text'+no).value;
	var amounttotal=qty*price+qty*security;
	document.getElementById("amounttotal_text"+no).value=amounttotal;
}

// function submitForm(){
  
//   var total_amount = document.getElementById('total_amount').value;
//   if(total_amount>0 && total_amount!=='0'){
//     return true;
//   //document.forms[0].submit();
//   }else{
//     alert('Please enter item details');
//   }
// }
function checking(){
	 var vendor=document.getElementById('customer').value;
	 var item=document.getElementById('item').value;
	 var qty=document.getElementById('qty').value;
	
	 
if (item=='' || isNaN(qty)) {
		$("#item").prop('required',true);
		$("#qty").prop('required',true);
		
		$("#addremove").prop('type','submit');

} else if (item=='' || qty=='') {
		$("#item").prop('required',true);
		$("#qty").prop('required',true);
		
		$("#addremove").prop('type','submit');
	
} else {
		document.getElementById("addremove").removeAttribute("type");
		$("#addremove").prop('type','button');
		add_row();
				
	}
}
function savechecking(no){
	 //var vendor=document.getElementById('customer').value;
	 //var item=document.getElementById('item').value;
	 var qty=document.getElementById("qty_text"+no).value;
	
	 
if (isNaN(qty)) {
		
		$("#save_button"+no).prop('type','submit');

} else if (qty=='') {
		
		$("#save_button"+no).prop('type','submit');
	
} else {
		//document.getElementById("addremove").removeAttribute("type");
		$("#save_button"+no).prop('type','button');
		save_row(no);
				
	}
}
function setting_submit(){
	var customer=document.getElementById('customer').value;
	if(customer==''){
		var emailerror=document.getElementById('email-error');
		emailerror.innerHTML="Please provide a customer.";
		return false;
	}
	$("#item").prop('required',false);
	$("#qty").prop('required',false);
	//$("#price").prop('required',false);
	$("#type_change").prop('type','submit');
}
function delete_record(deleteid,idd)
{
	var request = $.ajax({
	  url: "<?php echo SURL ?>/bookorder/record_delete",
	  type: "POST",
	  data: {deleteid:deleteid},
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
</script>