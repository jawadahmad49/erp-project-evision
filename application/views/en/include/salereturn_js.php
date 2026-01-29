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
    
    $("#item_"+no).attr("readonly",false);

        
    

    
    var qty=document.getElementById("qty_row"+no);
    var gasamt=document.getElementById("gasamt_row"+no);
    var security=document.getElementById("security_row"+no);
   
    var amounttotal=document.getElementById("amounttotal_row"+no);
    var type=document.getElementById("type_"+no);

   


    $("#type_change").prop("disabled",true);
    $(".editbtn").prop("disabled",true);
	$(".deletebtn").prop("disabled",true);
   

    //$("#item_"+no).prop("disabled", false);
    $("#item_"+no).attr("disabled",true);
    $("#type_"+no).attr("disabled",true);


	

    //var item_data=item.innerHTML;
    var qty_data=qty.innerHTML;
    var gasamt_data=gasamt.innerHTML;
    var security_data=security.innerHTML;
    
    var amounttotal_data=amounttotal.innerHTML;
      var type_data=type.innerHTML;
    
//console.log();return false;
    //var item_val=document.getElementById("item_text"+no).value;
    var qty_val=document.getElementById("qty_text"+no).value;
    var gasamt_val=document.getElementById("gasamt_text"+no).value;
    var security_val=document.getElementById("security_text"+no).value;
   
    var amounttotal_val=document.getElementById("amounttotal_text"+no).value;
     var type_val=document.getElementById("type_"+no).value;
    
    $('#row'+no+' input[type=text]').remove();

    //var totalstock = parseInt(qty_val)+parseInt(filledstock_val);

    //item.innerHTML="<input type='text' id='item_text"+no+"' value='"+item_val+"' style='width:250px'>";
    qty.innerHTML="<input type='text' id='qty_text"+no+"' value='"+qty_val+"'   pattern='^[0-9]+$' title='Only Numbers Allowed...' required maxlength='6'>";
    gasamt.innerHTML="<input type='text' id='gasamt_text"+no+"' value='"+gasamt_val+"'  onkeyup='CalAmounts("+no+")' pattern='^[0-9]+$' title='Only Numbers Allowed...' required maxlength='5'>";
    security.innerHTML="<input type='text' id='security_text"+no+"' value='"+security_val+"'  onkeyup='CalAmounts("+no+")' pattern='^[0-9]+$' title='Only Numbers Allowed...' required maxlength='5'>";
   
    amounttotal.innerHTML="<input type='text' id='amounttotal_text"+no+"' value='"+amounttotal_val+"'  onkeyup='CalAmounts("+no+")' name='amounttotal[]' disabled='disabled'>";
    

 $('.table>tbody>tr>td>input').addClass('form-control');
 $('.table>tbody>tr>td>select').addClass('form-control');
 $("#qty_text"+no).focus();

var str = $('#row'+no+'').find('td.Filled').text();
strs = str.replace(/\s/g, '');
	if(strs=='disables')
	{
		$("#security_text"+no).attr("readonly",false);
		$("#security_text"+no).attr("disabled",true);
		$('.gasamounts').hide();
	$('.prices').show();
	}else{
	$('.gasamounts').show();
	$('.prices').hide();
}
      if (type_val=='Empty') {
      	// alert(type_val);
    $("#gasamt_text"+no).attr("readonly",true);
}else{
	$("#gasamt_text"+no).attr("readonly",false);
}

}
function save_row(no)
{
	//$("#item_"+no).attr("disabled",false);
	//$("#type_"+no).attr("disabled",false);
	//$("#item_"+no).attr("readonly",true);
	//$("#type_"+no).attr("readonly",true);

    //var item_val=document.getElementById("item_text"+no).value;
    var type=document.getElementById("type_"+no).value;
    var item_val=document.getElementById("item_"+no).value;
    var qty_val=document.getElementById("qty_text"+no).value;
    var gasamt_val=document.getElementById("gasamt_text"+no).value;
    var security_val=document.getElementById("security_text"+no).value;
    
    var amounttotal_val=document.getElementById("amounttotal_text"+no).value;
   

	if(qty_val=='0' || qty_val=='0.00'){
		alert("Minimum 1 quantity allowed.");
		if(qty_val=='0' || qty_val=='0.00'){
		$("#qty_text"+no).focus();
		}else{//$("#price_text"+no).focus();
	}
		return false;
	}

	//var totalstock = parseInt(filledstock_val)-parseInt(qty_val);

	$('#data_table1 > #data_table2  > tr').each(function() {

	 	var id=$(this).closest("tr").find("td:eq(0)").attr('id');
	 	var itemids=id;
	 	if(id==item_val)
	 	{
	 	 	//$(this).closest("tr").find("td:eq(0)").text(totalstock);
	 			 
	 	}
	 	
	 	 
	 });	

    //document.getElementById("item_row"+no).innerHTML="<input style='width: 250px' type='text' id='item_text"+no+"' name='item' value='"+item_val+"' disabled>";
    document.getElementById("qty_row"+no).innerHTML="<input  type='text' id='qty_text"+no+"' name='qty[]' value='"+qty_val+"' readonly>";
    document.getElementById("gasamt_row"+no).innerHTML="<input  type='text' id='gasamt_text"+no+"' name='gasamt[]' value='"+gasamt_val+"' readonly>";
    document.getElementById("security_row"+no).innerHTML="<input  type='text' id='security_text"+no+"' name='security[]' value='"+security_val+"' readonly>";
   
    
    document.getElementById("amounttotal_row"+no).innerHTML="<input  type='text' id='amounttotal_text"+no+"' class='amounttotal' name='amounttotal[]' value='"+amounttotal_val+"' readonly>";
    

    //document.getElementById("edit_button"+no).style.display="block";
    //document.getElementById("save_button"+no).style.display="none";

    $("#edit_button"+no).show();
    $("#save_button"+no).hide();
 $('.table>tbody>tr>td>input').addClass('form-control');
 $('.table>tbody>tr>td>select').addClass('form-control');
 document.getElementById("item").value="";
 document.getElementById("type").value="Filled";
 

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

    	window.location.replace('<?php echo SURL."Salereturn/" ?>');
    }
    
}
function add_row()
{

if($("#security").prop('disabled')==true){
	$('.gasamounts').hide();
	$('.prices').show();
	var security_disable="disables";
}else{
	$('.gasamounts').show();
	$('.prices').hide();
}

$("#id-date-picker-1").prop("disabled", true);
    $("#data_table1").show();
    $("#submithide").show();
		$("#item").prop('required',false);
		$("#qty").prop('required',false);
		$("#gasamt").prop('required',false);
		$("#security").prop('required',false);
		document.getElementById("addremove").removeAttribute("type");
		$("#addremove").prop('type','button');
   	var item=document.getElementById("item").value;
   	var type=document.getElementById("type").value;
   	//return false;
    var qty=document.getElementById("qty").value;
    var gasamt=document.getElementById("gasamt").value;
    var security=document.getElementById("security").value;
    
    var amounttotal=document.getElementById("amounttotal").value;
    if (isNaN(gasamt)) {
    	alert("Please enter gas amount in numbers format.");
	 	$("#gasamt").val("");
	 	$("#gasamt").focus();
	 	return false;
    }
     if (isNaN(security)) {
    	alert("Please enter gas amount in numbers format.");
	 	$("#security").val("");
	 	$("#security").focus();
	 	return false;
    }

    $('#data_table1 > #data_table2  > tr').each(function() {

	 	var id=$(this).closest("tr").find("td:eq(0)").attr('id');
	 	var types=$(this).closest("tr").find("td:eq(0)").attr('class');
	 	var itemids=id;
	 	if(id==item)
	 	{
	 	 
	 	}
	 	if(item==itemids && type==types){
	 		alert("Item has been already added.");
			var itemvalue = $("#item_"+table_len+"").val(item);
			var typevalue = $("#type_"+table_len+"").val(type);
			//var typevalue = $("#type_"+table_len+"").val(type);
			document.getElementById("type").value="Filled";
			document.getElementById("item").value="";
			document.getElementById("qty").value="0";
			document.getElementById("gasamt").value="0";
			document.getElementById("security").value="0";
			
			document.getElementById("amounttotal").value="0";
			$('.table>tbody>tr>td>input').addClass('form-control');
			$('.table>tbody>tr>td>select').addClass('form-control');
			$("#item").focus();
	 		console.log(x);
	 		return false;
	 	}
	 	 
	 });
    
    // <input type='text' id='item_text"+table_len+"' style='width: 250px' disabled value='"+item+"'>
    var table=document.getElementById("data_table2");
    var table_len=(table.rows.length);
    var row = table.insertRow(table_len).outerHTML="<tr id='row"+table_len+"'><td style='display:none;' id='"+item+"' class='"+type+"'>"+security_disable+"</td><td id='item_row"+table_len+"' style='width:15% !important;'><select class='form-control disable' tabindex='-1' name='item[]' id='item_"+table_len+"' data-placeholder='Choose a Item...' required='required' disabled onchange='itemchange(this.value,"+table_len+")'><?php 
					foreach ($item_list as $key => $data) {
						?><option value='<?php echo $data['materialcode']; ?>'><?php 
    							echo ucwords($data['itemname']); 
    					?></option><?php 
    				} 
    				?></select></td><td class='hidden-480'  style='width:9% !important;'><select disabled='disabled' class='form-control disable' tabindex='-1' name='type[]' id='type_"+table_len+"'><option value='Filled'>Filled</option><option value='Empty'>Empty</option><option value='Other'>Other</option></select></td><td id='qty_row"+table_len+"'><input type='text' id='qty_text"+table_len+"' readonly tabindex='-1' value='"+qty+"' required name='qty[]'></td><td id='gasamt_row"+table_len+"'><input type='text' id='gasamt_text"+table_len+"' readonly tabindex='-1' required  readonly value='"+gasamt+"' name='gasamt[]'></td><td id='security_row"+table_len+"' style='display: none;'><input type='text' id='security_text"+table_len+"' readonly tabindex='-1' value='"+security+"' name='security[]'></td>	<td class='hidden-480'   id='amounttotal_row"+table_len+"'><input type='text' id='amounttotal_text"+table_len+"' class='amounttotal' readonly tabindex='-1' value='"+amounttotal+"' name='amounttotal[]'></td>	<td style='display: inline-flex; border: 0px;'><input type='button' id='edit_button"+table_len+"' value='Edit' class='btn btn-xs btn-success editbtn' onclick='edit_row("+table_len+")'> <input type='button' id='save_button"+table_len+"' value='Save' class='btn btn-xs btn-warning' onclick='savechecking("+table_len+")' style='display:none'> <input type='button' value='Delete' class='btn btn-xs btn-danger deletebtn' onclick='delete_row("+table_len+")'></td></tr>";

    var itemvalue = $("#item_"+table_len+"").val(item);
    var typevalue = $("#type_"+table_len+"").val(type);
    $("#item").focus();
    document.getElementById("item").value="";
    document.getElementById("type").value="Filled";
    document.getElementById("qty").value="0";
    document.getElementById("gasamt").value="0";
    document.getElementById("security").value="0";
    document.getElementById("amounttotal").value="0";
    
 $('.table>tbody>tr>td>input').addClass('form-control');
 $('.table>tbody>tr>td>select').addClass('form-control');

 totals_of_totals();

}
function totals_of_totals(){
	var total_sum_amount=0;
	
//var total_bill_amount=$(".amounttotal").val();
$('.amounttotal').each(function(i, obj) {

		var input_value=parseInt($(this).val());
		total_sum_amount=total_sum_amount+input_value;
		
    
	});	
	var tgasamt=total_sum_amount;
	
	 document.getElementById("total_bill").value=tgasamt;
	//$("#total_bill").val(tgasamt);

	$("#tempty").show();
	
}

function add_row_without()
{
$("#id-date-picker-1").prop("disabled", true);
    $("#data_table1").show();
    $("#submithide").show();
		$("#item").prop('required',false);
		$("#qty").prop('required',false);
		$("#gasamt").prop('required',false);
		$("#security").prop('required',false);
		document.getElementById("addremove").removeAttribute("type");
		$("#addremove").prop('type','button');
   	var item=document.getElementById("item").value;
   	var type=document.getElementById("type").value;
   	//return false;
    var qty=document.getElementById("qty").value;
    var gasamt=document.getElementById("gasamt").value;
    var security=document.getElementById("security").value;
    
    var amounttotal=document.getElementById("amounttotal").value;
    

//var filledstock = parseInt(filledstock)-parseInt(qty);

    $('#data_table1 > #data_table2  > tr').each(function() {

	 	var id=$(this).closest("tr").find("td:eq(0)").attr('id');
	 	var types=$(this).closest("tr").find("td:eq(0)").attr('class');
	 	var itemids=id;
	 	if(id==item)
	 	{
	 	 //var value=$(this).closest("tr").find("td:eq(0)").text();
	 		
	 	 //qtys11 =parseInt(qtys11)+ parseInt(value);
	 	 
	 	}
	 	if(item==itemids && type==types){
	 		alert("Item has been already added.");
			var itemvalue = $("#item_"+table_len+"").val(item);
			var typevalue = $("#type_"+table_len+"").val(type);
			//var typevalue = $("#type_"+table_len+"").val(type);
			document.getElementById("type").value="Filled";
			document.getElementById("item").value="";
			document.getElementById("qty").value="0";
			document.getElementById("gasamt").value="0";
			document.getElementById("security").value="0";
			
			document.getElementById("amounttotal").value="0";
			$('.table>tbody>tr>td>input').addClass('form-control');
			$('.table>tbody>tr>td>select').addClass('form-control');
			$("#item").focus();
	 		console.log(x);
	 		return false;
	 	}
	 	 
	 });
    
    // <input type='text' id='item_text"+table_len+"' style='width: 250px' disabled value='"+item+"'>
    var table=document.getElementById("data_table2");
    var table_len=(table.rows.length);
    var row = table.insertRow(table_len).outerHTML="<tr id='row"+table_len+"'><td style='display:none;' id='"+item+"' class='"+type+"'></td><td id='item_row"+table_len+"' style='width:15% !important;'><select class='form-control disable' name='item[]' id='item_"+table_len+"' data-placeholder='Choose a Item...' required='required' disabled onchange='itemchange(this.value,"+table_len+")'><?php 
					foreach ($item_list as $key => $data) {
						?><option value='<?php echo $data['materialcode']; ?>'><?php 
    							echo ucwords($data['itemname']); 
    					?></option><?php 
    				} 
    				?></select></td>	<td id='qty_row"+table_len+"'><input type='text' id='qty_text"+table_len+"' readonly value='"+qty+"' name='qty[]'></td><td id='gasamt_row"+table_len+"'><input type='text' id='gasamt_text"+table_len+"' readonly  readonly value='"+gasamt+"' name='gasamt[]'></td>					<td id='amounttotal_row"+table_len+"'><input type='text' id='amounttotal_text"+table_len+"' class='amounttotal' readonly  value='"+amounttotal+"'  name='amounttotal[]'></td>	<td style='display: inline-flex; border: 0px;'><input type='button' id='edit_button"+table_len+"' value='Edit' class='btn btn-xs btn-success' onclick='edit_row_without("+table_len+")'> <input type='button' id='save_button"+table_len+"' value='Save' class='btn btn-xs btn-warning' onclick='savechecking_without("+table_len+")' style='display:none'> <input type='button' value='Delete' class='btn btn-xs btn-danger' onclick='delete_row("+table_len+")'></td></tr>";

    var itemvalue = $("#item_"+table_len+"").val(item);
    var typevalue = $("#type_"+table_len+"").val(type);
    document.getElementById("item").value="";
    document.getElementById("type").value="Filled";
    document.getElementById("qty").value="0";
    document.getElementById("gasamt").value="0";
    document.getElementById("security").value="0";
    
    document.getElementById("amounttotal").value="0";
    
 $('.table>tbody>tr>td>input').addClass('form-control');
 $('.table>tbody>tr>td>select').addClass('form-control');
 
}
</script>
<script src="<?php echo SURL; ?>assets/js/bootbox.js"></script>

<!-- end add update delte rows using javascript -->
<script type="text/javascript">

function CalRAmount()
{
	var returngas=document.getElementById('returngas').value;
	var returnrate=document.getElementById('returnrate').value;
	var returntotal=returngas*returnrate;
	document.getElementById("returntotal").value=returntotal;
}
function CalAmount()
	{
	var gasamt=document.getElementById('gasamt').value;
	var security=document.getElementById('security').value;
	
	var amounttotal=parseInt(gasamt)+parseInt(security);
	document.getElementById("amounttotal").value=amounttotal;
}
function CalAmounts(no)
	{

	var gasamt=document.getElementById('gasamt_text'+no).value;
	var security=document.getElementById('security_text'+no).value;
	
	var amounttotal=parseInt(gasamt)+parseInt(security);
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
	 var qty=parseInt(document.getElementById('qty').value);
	 var gasamt=document.getElementById('gasamt').value;
	 var security=document.getElementById('security').value;
	 var new_stock=parseInt(document.getElementById('new_stock').value);
	 var type=document.getElementById('type').value;
													
	 var catcode_new=parseInt(document.getElementById('catcode_new').value);

	 if(vendor=="" || vendor==0){
	 	alert("Please select customer first.");
	 	$("#customer").focus();
	 	return false;
	 }else if(item=="" || item==0){
	 	alert("Please select item.");
	 	$("#item").focus();
	 	return false;
	 }else if(qty=="" || qty==0 || isNaN(qty)){
	 	alert("Please enter quantity in number format.");
	 	$("#qty").focus();
	 	return false;
	 }else if((type=="Filled") && (gasamt=="" || gasamt==0 || isNaN(gasamt))){
	 	if(confirm("Cylinder type is  Filled and Gas Amount is not entered, are you sure !!")){
			document.getElementById("addremove").removeAttribute("type");
			$("#addremove").prop('type','button');
			add_row();
		}else{
			
	 	$("#qty").focus();
		}
			
	 	//return false;
	 }else if(isNaN(gasamt)){
	 	alert("Please enter gas amount in numbers format.");
	 	$("#gasamt").val("");
	 	$("#gasamt").focus();
	 	return false;
	 }else if(isNaN(security)){
	 	alert("Please enter security amount in numbers format.");
	 	$("#security").val("");
	 	$("#security").focus();
	 	return false;
	 }
	 else {
		document.getElementById("addremove").removeAttribute("type");
		$("#addremove").prop('type','button');
		add_row();
				
	}
	
	
	// else if(qty > new_stock){
	 	// alert("Return quantity must be less than Stock!");
	 	// $("#qty").focus();
	 	// return false;
	 // }
}

function checking_without(){
	 var vendor=document.getElementById('customer').value;
	 var item=document.getElementById('item').value;
	 var qty=document.getElementById('qty').value;
	 var gasamt=document.getElementById('gasamt').value;
	 var security=document.getElementById('security').value;
	 
if (item=='' || isNaN(qty) || isNaN(gasamt) || isNaN(security)) {
		$("#item").prop('required',true);
		$("#qty").prop('required',true);
		$("#gasamt").prop('required',true);
		$("#security").prop('required',true);
		//$("#addremove").prop('onclick',null);
		$("#addremove").prop('type','submit');

} else if (item=='' || qty=='' || gasamt=='' || security=='') {
		$("#item").prop('required',true);
		$("#qty").prop('required',true);
		$("#gasamt").prop('required',true);
		$("#security").prop('required',true);
		//$("#addremove").prop('onclick',null);
		$("#addremove").prop('type','submit');
	
}else if(qty=='0' || qty=='0.00'){
alert("Minimum 1 quantity allowed.");
if(qty=='0' || qty=='0.00'){
$("#qty").focus();
}else{ //$("#price").focus();
}
return false;
//document.getElementById("addremove").removeAttribute("type");
//		$("#addremove").prop('type','button');
} else {
		document.getElementById("addremove").removeAttribute("type");
		$("#addremove").prop('type','button');
		add_row_without();
				
	}
}

function savechecking(no){

	 var qty=document.getElementById("qty_text"+no).value;
	 var gasamt=document.getElementById("gasamt_text"+no).value;
	 var security_text=document.getElementById("security_text"+no).value;
	 
if (isNaN(qty) || qty=="" || qty==0) {
	alert("Please enter quantity in numbers format.");
	$("#qty_text"+no).val("");
	$("#qty_text"+no).focus();
	return false;

} else if (isNaN(gasamt)) {
	alert("Please enter gas amount in numbers format.");
	$("#gasamt_text"+no).val("");
	$("#gasamt_text"+no).focus();
	return false;

} else if (isNaN(security_text)) {
	alert("Please enter security amount in numbers format.");
	$("#security_text"+no).val("");
	$("#security_text"+no).focus();
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
	var pay_mode=$('#pay_mode').val();
	//alert(pay_mode);
	if(customer==''){
		var emailerror=document.getElementById('email-error');
		emailerror.innerHTML="Please provide a customer.";
		return false;
	}
		if(pay_mode=='Cash')
	{
		$('#cheque_no').attr("required",false);
		$('#bank_code').attr("required",false);
	}
	
	$('.disable').attr("disabled",false);
	$('.disable').attr("readonly",true);
	$("#item").prop('required',false);
	$("#qty").prop('required',false);
	$("#gasamt").prop('required',false);
	$("#security").prop('required',false);
	$("#type_change").prop('type','submit');
}
function delete_record(deleteid,parentid,idd)
{
	var request = $.ajax({
	  url: "<?php echo SURL ?>/Salereturn/record_delete",
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
	});
	request.fail(function(jqXHR, textStatus) {
	alert( "Request failed: " + textStatus );
	});
}


function return_gas_amount(return_rate) {
	var return_gas= parseFloat($('#return_gas').val());
	var return_amount=return_rate*return_gas;
	$('#return_amount').val(return_amount.toFixed(2));

	var amount= parseFloat($('#net_receivable_gas').val());
	var net_receivable_gas=parseFloat(amount)-parseFloat(return_amount);
	$('#after_discount_amt').val(net_receivable_gas.toFixed(2));

    var after_discount_amt= parseFloat($('#after_discount_amt').val());
    var security_amt= parseFloat($('#security_amt').val());
    var sale_security_amt=$('#sale_security_amt').val();
    //var total_bill=parseFloat(security_amt)+parseFloat(after_discount_amt)+parseFloat(sale_security_amt);
    var total_bill=parseFloat(after_discount_amt)+parseFloat(security_amt)+parseFloat(sale_security_amt);
    $('#total_bill').val(total_bill.toFixed(2));


}
function return_gas_kg(return_gas) {
	var return_rate= parseFloat($('#return_rate').val());
	var return_amount=return_gas*return_rate;
	$('#return_amount').val(return_amount.toFixed(2));

	var amount= parseFloat($('#net_receivable_gas').val());
	var net_receivable_gas=parseFloat(amount)-parseFloat(return_amount);
	$('#after_discount_amt').val(net_receivable_gas.toFixed(2));

    var after_discount_amt= parseFloat($('#after_discount_amt').val());
    var security_amt= parseFloat($('#security_amt').val());
     var sale_security_amt=$('#sale_security_amt').val();
    var total_bill=parseFloat(after_discount_amt)+parseFloat(security_amt)+parseFloat(sale_security_amt);
    $('#total_bill').val(total_bill.toFixed(2));


}



 $('#item').on('change', function() {


	var item_id= $(this).val();
	var customer= $('#customer').val();
	var scode= $('#scode').val();
	if(item_id=='' || customer =='')
	{
		alert('Choose Customer');
		return false;
	}
	var request = $.ajax({
	  url: "<?php echo SURL ?>ShopOpeningBalance/enable_disable_type_customer",
	  type: "POST",
	  data: {item_id:item_id,customer:customer,scode:scode},
	  dataType: "html"
	});
	request.done(function(msg) {
		//alert(msg);
		var array=msg.split('|');
		
		var myObj = JSON.parse(array[0]);
		var catcode = myObj[0].catcode;
		

		if(catcode!=1){
			//$('.types').hide();
			$('.gasamount').hide();
			$('#type').attr('disabled',true);
			$('#security').attr('disabled',true);
			$('.price').show();
			document.getElementById('qty').value=0;
			document.getElementById('gasamt').value=0;
			document.getElementById('amounttotal').value=0;
			document.getElementById('new_stock').value=0;

			document.getElementById('gasamt').disabled=false;
			document.getElementById("type").value="Other";
			document.getElementById('catcode_new').value=0;

			//$("#addremove").attr("onclick","checking_without()");
		}else{
			//$('.types').show();
			$('.gasamount').show();
			$('.price').hide();
			$('#type').attr('disabled',false);
			$('#security').attr('disabled',false);
			document.getElementById('new_stock').value=array[1];
			document.getElementById('qty').value=0;
			document.getElementById('gasamt').value=0;
			document.getElementById('catcode_new').value=1;
			document.getElementById('amounttotal').value=0;
			document.getElementById("type").value="Filled";
			//$("#addremove").attr("onclick","checking()");

		}

	});
	request.fail(function(jqXHR, textStatus) {
	 // alert( "Request failed: " + textStatus );
	});
});

		
</script>
<script type="text/javascript">
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