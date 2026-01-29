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
    
    $("#type_change").prop("disabled",true);
    $(".editbtn").prop("disabled",true);
	$(".deletebtn").prop("disabled",true);
    $("#item_"+no).attr("disabled",true);
	$("#category_id_"+no).attr("disabled",true);

    

    var qty=document.getElementById("qty_row"+no);
    var unitcost=document.getElementById("unitcost_row"+no);
    var gst=document.getElementById("gst_row"+no);
    //var ereturn=document.getElementById("ereturn_row"+no);
    var gstamount=document.getElementById("gstamount_row"+no);
    var amount=document.getElementById("amount_row"+no);
    var examount=document.getElementById("examount_row"+no);
    //var emptystock=document.getElementById("emptystock_row"+no);

    //$("#item_"+no).prop("disabled", false);

    //var item_data=item.innerHTML;
    var qty_data=qty.innerHTML;
    var unitcost_data=unitcost.innerHTML;
    var gst_data=gst.innerHTML;
    //var ereturn_data=ereturn.innerHTML;
    var gstamount_data=gstamount.innerHTML;
    var amount_data=amount.innerHTML;
    var examount_data=examount.innerHTML;
    //var emptystock_data=emptystock.innerHTML;
//console.log();return false;
    //var item_val=document.getElementById("item_text"+no).value;
    var qty_val=document.getElementById("qty_text"+no).value;
    var unitcost_val=document.getElementById("unitcost_text"+no).value;
    var gst_val=document.getElementById("gst_text"+no).value;
    //var ereturn_val=document.getElementById("ereturn_text"+no).value;
    var gstamount_val=document.getElementById("gstamount_text"+no).value;
    var amount_val=document.getElementById("amount_text"+no).value;
    var examount_val=document.getElementById("examount_text"+no).value;
    //var emptystock_val=document.getElementById("emptystock_text"+no).value;
	
// 	if(typess=='Filled'){
// 	//var totalstock = parseInt(ereturn_val)+parseInt(emptystock_val);
// }else if(typess=='Empty'){
// 	//var totalstock = parseInt(ereturn_val)+parseInt(emptystock_val);
// 	//var totalstock = parseInt(emptystock_val)-parseInt(qty_val)+parseInt(ereturn_val);
// 	var totalstock = parseInt(qty_val);
// }
	//$("#emptystock_text"+no).hide();

    $('#row'+no+' input[type=text]').remove();

    //item.innerHTML="<input type='text' id='item_text"+no+"' value='"+item_val+"' style='width:250px'>";
    qty.innerHTML="<input name='qty[]' type='text' id='qty_text"+no+"' value='"+qty_val+"'  onkeyup='CalAmounts("+no+")' pattern='^[0-9]+$' title='Only Numbers Allowed...' required maxlength='6'>";
    unitcost.innerHTML="<input name='unitcost[]' type='text' id='unitcost_text"+no+"' value='"+unitcost_val+"'  onkeyup='CalAmounts("+no+")' pattern='^[0-9]+$' title='Only Numbers Allowed...' required maxlength='5'>";
    gst.innerHTML="<input name='gst[]' type='text' id='gst_text"+no+"' value='"+gst_val+"'  onkeyup='CalAmounts("+no+")' pattern='^[0-9]+$' title='Only Numbers Allowed...' required maxlength='2'>";

   // ereturn.innerHTML="<input name='ereturn[]' type='text' id='ereturn_text"+no+"' value='"+ereturn_val+"'  onkeyup='' pattern='^[0-9]+$' title='Only Numbers Allowed...' required>";

    //emptystock.innerHTML="<input type='text' id='emptystock_text"+no+"' value='"+totalstock+"'   disabled='disabled'>";

    gstamount.innerHTML="<input name='gst_amount[]' type='text' id='gstamount_text"+no+"' value='"+gstamount_val+"'  onkeyup='CalAmounts("+no+")' disabled='disabled'>";
    amount.innerHTML="<input class='amount_class' name='amount[]' type='text' id='amount_text"+no+"' value='"+amount_val+"'  onkeyup='CalAmounts("+no+")' disabled='disabled'>";
    examount.innerHTML="<input name='examount[]' type='text' id='examount_text"+no+"' value='"+examount_val+"'  onkeyup='CalAmounts("+no+")' disabled='disabled'>";

  $('.table>tbody>tr>td>input').addClass('form-control');
  $('.table>tbody>tr>td>select').addClass('form-control');
  $("#qty_text"+no).focus();
}
function save_row(no)
{
	
	//$("#item_"+no).prop("disabled", true);
    // var type=document.getElementById("type_"+no).value;
    var category_id=document.getElementById("category_id_"+no).value;
    var item_val=document.getElementById("item_"+no).value;
    var qty_val=document.getElementById("qty_text"+no).value;
    var unitcost__val=document.getElementById("unitcost_text"+no).value;
    var gst_val=document.getElementById("gst_text"+no).value;
    //var ereturn_val=document.getElementById("ereturn_text"+no).value;
    var gstamount_val=document.getElementById("gstamount_text"+no).value;
    var amount_val=document.getElementById("amount_text"+no).value;
    var examount_val=document.getElementById("examount_text"+no).value;
	//var emptystock_val=document.getElementById("emptystock_text"+no).value;
	if (isNaN(gst_val)) {
		alert("Please Enter Only Number!");
		$("#gst_text"+no).focus();
		return false;

	}

	//$("#item_"+no).attr("disabled",false);
	$("#category_id_"+no).attr("disabled",false);
	//$("#item_"+no).attr("readonly",true);
	$("#category_id_"+no).attr("readonly",true);


$('#data_table1 > #data_table2  > tr').each(function() {

	 	var id=$(this).closest("tr").find("td:eq(0)").attr('id');
	 	var category_ids=$(this).closest("tr").find("td:eq(0)").attr('class');
	 	var itemids=id;
	 	if(id==item_val && category_id==category_ids)
	 	{
	 	 
	 	}
	 	 
	 });

	

    //document.getElementById("item_row"+no).innerHTML="<input style='width: 250px' type='text' id='item_text"+no+"' name='item' value='"+item_val+"' disabled>";
    document.getElementById("qty_row"+no).innerHTML="<input  type='text' id='qty_text"+no+"' name='qty[]' value='"+qty_val+"' readonly='readonly'>";
    document.getElementById("unitcost_row"+no).innerHTML="<input  type='text' id='unitcost_text"+no+"' name='unitcost[]' value='"+unitcost__val+"' readonly='readonly'>";
    document.getElementById("gst_row"+no).innerHTML="<input  type='text' id='gst_text"+no+"' name='gst[]' value='"+gst_val+"' readonly='readonly'>";


    document.getElementById("gstamount_row"+no).innerHTML="<input  type='text' id='gstamount_text"+no+"' name='gst_amount[]' value='"+gstamount_val+"' readonly='readonly'>";
    document.getElementById("amount_row"+no).innerHTML="<input  type='text' id='amount_text"+no+"' class='amount_class' name='amount[]' value='"+amount_val+"' readonly='readonly'>";
    document.getElementById("examount_row"+no).innerHTML="<input  type='text' id='examount_text"+no+"' name='examount[]' value='"+examount_val+"' readonly='readonly'>";


    $("#edit_button"+no).show();
    $("#save_button"+no).hide();

  $('.table>tbody>tr>td>input').addClass('form-control');
  $('.table>tbody>tr>td>select').addClass('form-control');
  document.getElementById("item").value="";
	var total_amount=0;
	var total_ex_amount=0;
	var total_in_amount=0;

	$(".amount_class").each(function( index ) {
	 		total_amount=total_amount+parseFloat($(this).val());
	});


	$('#total_bill').val(total_amount.toFixed(2));
	var discount =$('#discount').val();
	var net_pay=total_amount-discount;
	$('#net_payable').val(net_pay.toFixed(2));
	var gstp= parseFloat($('#gstp').val());
	var vat_amount=net_payable*gstp/100;
	$('#vat_amount').val(vat_amount.toFixed(2));
	var inc_vat_amount=parseFloat(net_payable)+parseFloat(vat_amount);
	$('#inc_vat_amount').val(inc_vat_amount.toFixed(2));

	$('#total_in_amount_id').text(total_amount);

	$(".total_ex_amount_class").each(function( index ) {
	 		total_ex_amount=total_ex_amount+parseFloat($(this).val());
	});
	$('#total_ex_amount_id').text(total_ex_amount.toFixed(2));

	$(".total_amount_class").each(function( index ) {
	 		total_in_amount=total_in_amount+parseFloat($(this).val());
	});
	$('#total_amount_id').text(total_in_amount.toFixed(2));
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

    	window.location.replace('<?php echo SURL."Purchaseother/" ?>');
 
    	var deleteid = $('#edit_id').val();
    	var parentid =  document.getElementById("id").value;

    	var request = $.ajax({
			url: "<?php echo SURL ?>/Purchaseother/trans_delete",
			type: "POST",
			data: {deleteid:deleteid,parentid:parentid}, 
			dataType: "html"
			});
			request.done(function(msg) {
				if(msg==1)
				{
					// delete_row(idd);
					// event.preventDefault();
					// return false;			
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

    var total_amount=0;
	var total_ex_amount=0;
	var total_in_amount=0;

	$(".amount_class").each(function( index ) {
	 		total_amount=total_amount+parseFloat($(this).val());
	});
	

	$('#total_bill').val(total_amount);

	var discount =$('#discount').val();
	var net_pay=total_amount-discount;
	$('#net_payable').val(net_pay.toFixed(2));
	var gstp= parseFloat($('#gstp').val());
	var net_payable= parseFloat($('#net_payable').val());
	var vat_amount=net_payable*gstp/100;
	$('#vat_amount').val(vat_amount.toFixed(2));
	var inc_vat_amount=parseFloat(net_payable)+parseFloat(vat_amount);
	$('#inc_vat_amount').val(inc_vat_amount.toFixed(2));

   	$('#total_in_amount_id').text(total_amount.toFixed(2));

	$(".total_ex_amount_class").each(function( index ) {
	 		total_ex_amount=total_ex_amount+parseFloat($(this).val());
	});
	$('#total_ex_amount_id').text(total_ex_amount.toFixed(2));

	$(".total_amount_class").each(function( index ) {
	 		total_in_amount=total_in_amount+parseFloat($(this).val());
	});
	$('#total_amount_id').text(total_in_amount); 
    
}
var qtys=0;
function add_row()
{
	$("#id-date-picker-1").prop("disabled", true);
	var category_id=document.getElementById("category_id").value;
	

		$("#item").prop('required',false);
		$("#qty").prop('required',false);
		$("#unitcost").prop('required',false);
		$("#gst").prop('required',false);
		//$("#ereturn").prop('required',false);
		document.getElementById("addremove").removeAttribute("type");
		$("#addremove").prop('type','button');
    //var item=document.getElementById("item").children(':selected');
    //var item = $("#item option:selected").text();
   	var item=document.getElementById("item").value;
    var qty=document.getElementById("qty").value;
//alert(qty);
//var qtys=0;
    //var qtys+=var qty;
 qtys =parseInt(qtys)+ parseInt(qty);
//alert(qtys);
    //var qtys+=qty;



    var unitcost=document.getElementById("unitcost").value;
    var gst=document.getElementById("gst").value;
    //var ereturn=document.getElementById("ereturn").value;
    var gst_amount=document.getElementById("gst_amount").value;
    var amount=document.getElementById("amount").value;
    var examount=document.getElementById("examount").value;
    var category_id=document.getElementById("category_id").value;
    //var emptystock=document.getElementById("emptystock").value;

// if(typess=='Filled'){
// }else{
// 	var emptystock = parseInt(qty);
// }
	var qtys11=0;
	//var checkings=1;

$('#data_table1 > #data_table2  > tr').each(function() {

	 	var id=$(this).closest("tr").find("td:eq(0)").attr('id');
	 	var category_ids=$(this).closest("tr").find("td:eq(0)").attr('class');
	 	var itemids=id;
	 	if(id==item)
	 	{
	 	 var value=$(this).closest("tr").find("td:eq(0)").text();
	 		
	 	 qtys11 =parseInt(qtys11)+ parseInt(value);
	 	 
	 	}
	 	if(item==itemids && category_id==category_ids){
	 		alert("Item has been already added.");
			var itemvalue = $("#item_"+table_len+"").val(item);
			var typevalue = $("#category_id_"+table_len+"").val(category_id);
			document.getElementById("item").value="";
			document.getElementById("qty").value="0";
			document.getElementById("unitcost").value="0";
			document.getElementById("gst").value="0";
			//document.getElementById("ereturn").value="";
			//document.getElementById("emptystock").value="";
			document.getElementById("gst_amount").value="0";
			document.getElementById("amount").value="0";
			document.getElementById("examount").value="0";
			// document.getElementById("type").value="Empty";
			$('.table>tbody>tr>td>input').addClass('form-control');
			$('.table>tbody>tr>td>select').addClass('form-control');
	 		console.log(x);
	 		return false;
	 	}
	 });
	//emptystock=parseInt(emptystock)-parseInt(ereturn);
//if(checkings!=2){

    $("#data_table1").show();
    $("#submithide").show();
    // <input type='text' id='item_text"+table_len+"' style='width: 250px' disabled value='"+item+"'>
    var table=document.getElementById("data_table2");
    var table_len=(table.rows.length);
    var row = table.insertRow(table_len).outerHTML="<tr id='row"+table_len+"'><td style='display:none;' id='"+item+"' class='"+category_id+"'></td><td class='hidden-480'  id='category_id_row"+table_len+"' style='width:15% !important;'><select class='form-control disable' tabindex='-1' name='category_id[]' id='category_id_"+table_len+"' data-placeholder='Choose a Item...' required='required' disabled='disabled' onchange='category_idchange(this.value,"+table_len+")'><?php 
					foreach ($category_list as $key => $data) {
						?><option value='<?php echo $data['id']; ?>'><?php 
    							echo ucwords($data['catname']); 
    					?></option><?php 
    				} 
    				?></select></td><td id='item_row"+table_len+"' style='width:15% !important;'><select class='form-control disable' name='item[]' id='item_"+table_len+"' data-placeholder='Choose a Item...' required='required' disabled='disabled' tabindex='-1' onchange='itemchange(this.value,"+table_len+")'><?php 
					foreach ($item_list as $key => $data) {
						?><option value='<?php echo $data['materialcode']; ?>'><?php 
    							echo ucwords($data['itemname']); 
    					?></option><?php 
    				}
    				?></select></td><td id='qty_row"+table_len+"'><input type='text' id='qty_text"+table_len+"'  readonly='readonly' tabindex='-1' value='"+qty+"' name='qty[]'></td><td id='unitcost_row"+table_len+"'><input name='unitcost[]' type='text' id='unitcost_text"+table_len+"'  readonly='readonly' tabindex='-1' value='"+unitcost+"'></td><td hidden class='hidden-480'  id='gst_row"+table_len+"'><input name='gst[]' type='hidden' id='gst_text"+table_len+"' readonly='readonly' tabindex='-1' value='"+gst+"' ></td><td hidden class='hidden-480'  id='gstamount_row"+table_len+"'><input name='gst_amount[]' type='hidden' id='gstamount_text"+table_len+"' readonly='readonly' tabindex='-1' value='"+gst_amount+"' ></td><td hidden class='hidden-480'  id='amount_row"+table_len+"'><input name='amount[]'  class='amount_class'  type='hidden' id='amount_text"+table_len+"' readonly='readonly' tabindex='-1' value='"+amount+"' ></td><td id='examount_row"+table_len+"'><input name='examount[]' type='text' id='examount_text"+table_len+"' readonly='readonly' tabindex='-1' value='"+examount+"' ></td><td style='display: inline-flex; border: 0px;'><input type='button' id='edit_button"+table_len+"' value='Edit' class='btn btn-xs btn-success editbtn' onclick='edit_row("+table_len+")'><input type='button' id='save_button"+table_len+"' value='Save' class='btn btn-xs btn-warning' onclick='savechecking("+table_len+")' style='display:none'> <input type='button' value='Delete' class='btn btn-xs btn-danger deletebtn' onclick='delete_row("+table_len+")'></td></tr>";

    var itemvalue = $("#item_"+table_len+"").val(item);
    var typevalue = $("#category_id_"+table_len+"").val(category_id);
    
    jQuery(function($) {
				$('#category_id').trigger("chosen:activate");
	     
	             });
    document.getElementById("item").value="";
    document.getElementById("qty").value="0";
    document.getElementById("unitcost").value="0";
    document.getElementById("gst").value="0";
    //document.getElementById("ereturn").value="";
    //document.getElementById("emptystock").value="";
    document.getElementById("gst_amount").value="0";
    document.getElementById("amount").value="0";
    document.getElementById("examount").value="0";
    // document.getElementById("type").value="Empty";
  $('.table>tbody>tr>td>input').addClass('form-control');
  $('.table>tbody>tr>td>select').addClass('form-control');
//}



	var total_amount=0;
  	var total_ex_amount=0;
  	var total_in_amount=0;

	$(".amount_class").each(function( index ) {
	 		total_amount=total_amount+parseFloat($(this).val());
	});
	$('#total_bill').val(total_amount.toFixed(2));

		var discount =$('#discount').val();
		var net_pay=total_amount-discount;
		$('#net_payable').val(net_pay.toFixed(2));
		var gstp= parseFloat($('#gstp').val());
		var net_payable= parseFloat($('#net_payable').val());
	    var vat_amount=net_payable*gstp/100;
	    $('#vat_amount').val(vat_amount.toFixed(2));
 	    var inc_vat_amount=parseFloat(net_payable)+parseFloat(vat_amount);
	    $('#inc_vat_amount').val(inc_vat_amount.toFixed(2));

//}

	$('#total_in_amount_id').text(total_amount.toFixed(2));


	$(".total_ex_amount_class").each(function( index ) {
	 		total_ex_amount=total_ex_amount+parseFloat($(this).val());
	});
	$('#total_ex_amount_id').text(total_ex_amount.toFixed(2));

	$(".total_amount_class").each(function( index ) {
	 		total_in_amount=total_in_amount+parseFloat($(this).val());
	});
	$('#total_amount_id').text(total_in_amount.toFixed(2));


}
</script>
<!-- end add update delte rows using javascript -->
<script type="text/javascript">
function CalAmount()
	{
	var ucost=document.getElementById('unitcost').value;

	var gst=document.getElementById('gst').value;
	var qty=document.getElementById('qty').value;
	//var ed=document.getElementById('ed').value;
	var gstp=ucost*qty*gst/100;
	document.getElementById("gst_amount").value=gstp;
	//var ed_amount=ucost*qty*ed/100;
	//document.getElementById("ed_amount").value=ed_amount;
	document.getElementById('amount').value=ucost*qty+gstp;
	document.getElementById('examount').value=ucost*qty;
	document.getElementById('total_amount').value=ucost*qty;
}
function CalAmounts(no)
	{
	var ucost=document.getElementById('unitcost_text'+no).value;
	var gst=document.getElementById('gst_text'+no).value;
	var qty=document.getElementById('qty_text'+no).value;
	//var ed=document.getElementById('ed').value;
	var gstp=ucost*qty*gst/100;
	document.getElementById("gstamount_text"+no).value=gstp;
	//var ed_amount=ucost*qty*ed/100;
	//document.getElementById("ed_amount").value=ed_amount;
	document.getElementById('amount_text'+no).value=ucost*qty+gstp;
	document.getElementById('examount_text'+no).value=ucost*qty
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
	 var ucost=document.getElementById('unitcost').value;
	 var gst=document.getElementById('gst').value;
	 //var ereturn=document.getElementById('ereturn').value;
	 var qty=parseInt(document.getElementById('qty').value);
	 var item=document.getElementById('item').value;
	 var vendor = $("#vendor").val();
	 var category_id = $("#category_id").val();


	 if(vendor=='' || vendor==0)
	 { 
			 
	document.getElementById('err_message2').innerHTML='<div class="alert alert-danger"><button type="button" class="close" data-dismiss="alert"><i class="ace-icon fa fa-times"></i></button><strong><i class="ace-icon fa fa-times"></i>Oh snap!</strong>- Please select vendor!</div>';
		$("#vendor").prop('required',true);
		$("#vendor").focus();
		return false;
	
	 }else if(category_id=='' || category_id==0)
	 { 
	 	document.getElementById('err_message2').innerHTML='<div class="alert alert-danger"><button type="button" class="close" data-dismiss="alert"><i class="ace-icon fa fa-times"></i></button><strong><i class="ace-icon fa fa-times"></i>Oh snap!</strong>- Please select Category!</div>';
			$("#category_id").prop('required',true);
			$("#category_id").focus();
			return false;
	
	 }else if(item=='' || item==0)
	 { 
	 	document.getElementById('err_message2').innerHTML='<div class="alert alert-danger"><button type="button" class="close" data-dismiss="alert"><i class="ace-icon fa fa-times"></i></button><strong><i class="ace-icon fa fa-times"></i>Oh snap!</strong>- Please select item!</div>';
			$("#item").prop('required',true);
			$("#item").focus();
			return false;
	
	 }else if(qty=='' || qty==0) { 
	 	document.getElementById('err_message2').innerHTML='<div class="alert alert-danger"><button type="button" class="close" data-dismiss="alert"><i class="ace-icon fa fa-times"></i></button><strong><i class="ace-icon fa fa-times"></i>Oh snap!</strong>- Please enter quantity!</div>';
			$("#qty").prop('required',true);
			$("#qty").focus();
			return false;
	 }else if(ucost=='' || ucost==0) { 
	 	document.getElementById('err_message2').innerHTML='<div class="alert alert-danger"><button type="button" class="close" data-dismiss="alert"><i class="ace-icon fa fa-times"></i></button><strong><i class="ace-icon fa fa-times"></i>Oh snap!</strong>- Please enter unitcost!</div>';
			$("#unitcost").prop('required',true);
			$("#unitcost").focus();
			return false;
	 }


	 var vendor=document.getElementById('vendor').value;
if (isNaN(qty)) {
	alert("Please enter only numbers");
		
		$("#qty").prop('required',true);
		$("#qty").focus();
		return false;

} else if (item=='' || qty=='' || ucost=='') {
	alert("Fields must not be empty.")
		$("#item").prop('required',true);
		$("#qty").prop('required',true);
		$("#unitcost").prop('required',true);
		$("#gst").prop('required',true);
		return false;
	
}
if (isNaN(ucost)) {
	alert("Please enter only numbers");
	$("#unitcost").focus();
	$("#unitcost").prop('required',true);
	return false;
}
if (isNaN(gst)) {
	alert("Please enter only numbers");
	$("#gst").focus();
	$("#gst").prop('required',true);
	return false;
}
if (isNaN(item)) {
	alert("Please enter only numbers");
	$("#item").focus();
	$("#item").prop('required',true);
	return false;
}

 
if(qty=='0' || qty=='0.00' || qty==''){
document.getElementById("qty").value="0";
document.getElementById('err_message2').innerHTML='<div class="alert alert-danger"><button type="button" class="close" data-dismiss="alert"><i class="ace-icon fa fa-times"></i></button><strong><i class="ace-icon fa fa-times"></i>Oh snap!</strong>- Please enter quantity !</div>';
$("#qty").prop('min',1);
$("#qty").focus();
return false;
}
else if(ucost=='0' || ucost=='0.00' || ucost==''){
document.getElementById("unitcost").value="0";
document.getElementById('err_message2').innerHTML='<div class="alert alert-danger"><button type="button" class="close" data-dismiss="alert"><i class="ace-icon fa fa-times"></i></button><strong><i class="ace-icon fa fa-times"></i>Oh snap!</strong>- Please enter Unit Price !</div>';
$("#unitcost").prop('min',1);
$("#unitcost").focus();
return false;
}
else {
		document.getElementById("addremove").removeAttribute("type");
		$("#addremove").prop('type','button');
		add_row();
				
	}
}

function savechecking(no){
	var qty=document.getElementById("qty_text"+no).value;
    var ucost=document.getElementById("unitcost_text"+no).value;
    var gst=document.getElementById("gst_text"+no).value;
    //var ereturn=document.getElementById("ereturn_text"+no).value;


 if (qty==0 || qty=="" || isNaN(qty)) {
	
	alert('Please enter Quantity');
	$("#qty_text"+no).focus();
	return false;
}else if (ucost==0 || ucost=="" || isNaN(ucost)) {
	
	alert('Please enter unitcost');
	$("#unitcost_text"+no).focus();
	return false;
} else {
		$("#save_button"+no).prop('type','button');
		$("#type_change").prop("disabled",false);
		$(".editbtn").prop("disabled",false);
	    $(".deletebtn").prop("disabled",false);
		save_row(no);
				
	}
}



function setting_submit(){

	$("#id-date-picker-1").prop("disabled", false);
	var vendor=document.getElementById('vendor').value;
	if(vendor==''){
		var emailerror=document.getElementById('email-error');
		emailerror.innerHTML="Please provide a vendor.";
		return false;
	}
	var check_no= $('#cheque_no').val();

	var pay_mode=$('#pay_mode').val();
	

	if(pay_mode=='cash')
	{
		$('#cheque_no').attr("required",false);
		$('#bank_name').attr("required",false);
		$('#enter_amount_cash').attr("required",false);
		var amount=$('#enter_amount_cash').val();
	}
	else 
	{
		$('#enter_amount_bank').attr("required",false);
		var amount=$('#enter_amount_bank').val();
		
	}

	$('.disable').attr("disabled",false);
	$('.disable').attr("readonly",true);
	$("#item").prop('required',false);
	$("#qty").prop('required',false);
	//$("#ereturn").prop('required',false);
	$("#unitcost").prop('required',false);
	$("#gst").prop('required',false);

	if(pay_mode=='cash')
	{
		$("#type_change").prop('type','submit');
	}
	else 
	{

		if(check_no<=0 || check_no=='') {
			alert('Enter check no');
			$('#cheque_no').focus();
			$("#type_change").prop('type','button');
		}
		else
		{
			$("#type_change").prop('type','submit');
		}
	}
}



function delete_record(deleteid,parentid,idd)
{
	var request = $.ajax({
	  url: "<?php echo SURL ?>/Purchaseempty/record_delete",
	  type: "POST",
	  data: {deleteid:deleteid,parentid:parentid},//,parentid:parentid
	  dataType: "html"
	});
	request.done(function(msg) {
		if(msg==1)
		{
			delete_row(idd);
			net_pay();
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

function net_pay(){
	var total_amount=0;

	$(".amount_class").each(function( index ) {
	 	total_amount=total_amount+parseFloat($(this).val());
	});

	$('#total_bill').val(total_amount.toFixed(2));
	$('#discount').val(0);
	$('#net_payable').val(total_amount.toFixed(2));
	var gstp= parseFloat($('#gstp').val());
	var vat_amount=net_payable*gstp/100;
	$('#vat_amount').val(vat_amount.toFixed(2));
	var inc_vat_amount=parseFloat(net_payable)+parseFloat(vat_amount);
	$('#inc_vat_amount').val(inc_vat_amount.toFixed(2));
}

$('#item').on('change', function() {
	var item_id= $(this).val();
	//var type= $('#type').val();
	var qtys11=0;
	//var value=0;

	$('#data_table1 > #data_table2  > tr').each(function() {

		var id=$(this).closest("tr").find("td:eq(0)").attr('id');
		if(id==item_id)
		{

		 var value=$(this).closest("tr").find("td:eq(0)").text();
			
		 qtys11 =parseInt(value);

		}

		 

	});
//alert(qtys11);

	var date= $('#id-date-picker-1').val();
	//alert(date);return false;
		if(item_id=='' || date=='')
		{
			//$("#filledstock").value()="";
			document.getElementById("emptystock").value="";
			return false;
		}
		var request = $.ajax({
		  url: "<?php echo SURL ?>/SaleLPG/get_filledstock",
		  type: "POST",
		  data: {item_id:item_id,date:date},
		  dataType: "html"
		});
		request.done(function(msg) {
			var myObj = JSON.parse(msg);

			var emptystocked = myObj.empty;

			if(qtys11==0){
				$("#emptystock").val(myObj.empty);	
			}else{
				$("#emptystock").val(qtys11);
			}
				

			  


		});
		request.fail(function(jqXHR, textStatus) {
		  alert( "Request failed: " + textStatus );
		});
});
 



function itemchange(itemid,no){
	//document.getElementById("filledstock").value
	var item_id= itemid;
	var date= $('#id-date-picker-1').val();
	//alert(date);return false;
		if(item_id=='' || date=='')
		{
			//$("#filledstock").value()="";
			document.getElementById("emptystock_text"+no).value="";
			return false;
		}
		var request = $.ajax({
		  url: "<?php echo SURL ?>/SaleLPG/get_filledstock",
		  type: "POST",
		  data: {item_id:item_id,date:date},
		  dataType: "html"
		});
		request.done(function(msg) {
		   var objJSON = JSON.parse(msg);
		   $("#emptystock_text"+no).val(objJSON['empty']);

		});
		request.fail(function(jqXHR, textStatus) {
		  alert( "Request failed: " + textStatus );
		});
}

function culculat_disc(disc_value) {

	var total_bil= parseFloat($('#total_bill').val());

	if(total_bil<disc_value)
	{
		alert('Discount should be not greater than total Bill');
		$('#discount').val(0);
		return false;
	}

	var net_pay=total_bil-disc_value;
	$('#net_payable').val(net_pay.toFixed(2));	
	var gstp= parseFloat($('#gstp').val());

	var vat_amount=net_payable*gstp/100;
	$('#vat_amount').val(vat_amount.toFixed(2));
	var inc_vat_amount=parseFloat(net_payable)+parseFloat(vat_amount);
	$('#inc_vat_amount').val(inc_vat_amount.toFixed(2));	
}
function culculat_gst(gstp) {

    var net_payable= parseFloat($('#net_payable').val());
	var vat_amount=net_payable*gstp/100;
	$('#vat_amount').val(vat_amount.toFixed(2));
	var inc_vat_amount=parseFloat(net_payable)+parseFloat(vat_amount);
	$('#inc_vat_amount').val(inc_vat_amount.toFixed(2));	

}

function culculat_enter_amount(amount_value) {

	var pay_mode= $('#pay_mode').val();
	
	if(pay_mode=='cash'){
	
	var net_payable= parseFloat($('#cash_balance').val());
			if(amount_value>net_payable)
	{
		alert('Amount should be not greater than balance of Cash In Hand');
		$('.enter_amount_class').val(0);
		return false;
	}
		
	}else if(pay_mode=='bank'){
		
	var net_payable= parseFloat($('#bank_balance').val());
	
		if(amount_value>net_payable)
	{
		alert('Amount should be not greater than balance of bank');
		$('.enter_amount_class').val(0);
		return false;
	}
	}


}





function change_pay_mode(pay_value) {
		
	if(pay_value=='cash')
	{

		$(".bank_amount_row").hide();
		$(".pay_amount_row").show();
		$('.enter_amount_class').val(0);

 
			
			 var t_id=0;
		var request = $.ajax({
		  url: "<?php echo SURL ?>Purchaseother/get_cash_in_hand_bal/"+t_id,
		  type: "POST",
		   data: {t_id:t_id},
		  dataType: "html"
		});
		request.done(function(msg) {
			
			 
			$('#cash_balance').val(msg) ;
		 
		});
		request.fail(function(jqXHR, textStatus) {
		  alert( "Request failed: " + textStatus );
		});
	}
	 
	else
	{
		$(".bank_amount_row").show();
    	$(".pay_amount_row").hide();	
		$('.enter_amount_class').val(0);
			
			
			
			
			var t_id=0;
			var bankcode=$('#bank_name').val();
			var dt=$('#id-date-picker-1').val();
				var ids=$('#id').val();
			var request = $.ajax({
				url: "<?php echo SURL ?>Purchaseother/get_bank_bal/"+bankcode + '/'+dt+'/'+ ids,
			type: "POST",
			data: {bankcode:bankcode,dt:dt,ids:ids},
			dataType: "html"
			});
			request.done(function(msg) {


			$('#bank_balance').val(msg) ;

			});
			request.fail(function(jqXHR, textStatus) {
			alert( "Request failed: " + textStatus );
			});
	}

}


function bank_name_change() {
	 
			var edit_amount=$('.enter_amount_class').val(0);
			$('.enter_amount_class').val(0);
			var t_id=0;
			var bankcode=$('#bank_name').val();
			var dt=$('#id-date-picker-1').val();
			var ids=$('#id').val();
			
			
			var request = $.ajax({
			url: "<?php echo SURL ?>Purchaseother/get_bank_bal/"+bankcode + '/'+dt+'/'+ ids,
			type: "POST",
			 data: {bankcode:bankcode,dt:dt,ids:ids},
			    
			dataType: "html"
			});
			
			 
			request.done(function(msg) {
 
			$('#bank_balance').val(msg) ;

			});
			request.fail(function(jqXHR, textStatus) {
			alert( "Request failed: " + textStatus );
			});
}


</script>