<!DOCTYPE html>
<html lang="en">
<?php
$this->load->view('en/include/head');
$this->load->view('en/include/header');

 ?>

<body class="no-skin">

<div class="main-container ace-save-state" id="main-container">

<?php $this->load->view('app/include/sidebar');
 			?>

			<div class="main-content">
				<div class="main-content-inner">
					<div class="breadcrumbs ace-save-state" id="breadcrumbs">
						<ul class="breadcrumb">
							<li>
								<i class="ace-icon fa fa-home home-icon"></i>
								<a href="<?php echo SURL."admin"; ?>">Home</a>
							</li>

							<li>
									<a href="<?php echo SURL."user"; ?>">Map Area  </a>
							</li>
							<li class="active">Add Map Areas  </li>
						</ul><!-- /.breadcrumb -->

						<div class="nav-search" id="nav-search">
							<form class="form-search">
								<span class="input-icon">
									<input type="text" placeholder="Search ..." class="nav-search-input" id="nav-search-input" autocomplete="off" />
									<i class="ace-icon fa fa-search nav-search-icon"></i>
								</span>
							</form>
						</div><!-- /.nav-search -->
					</div>

					<div class="page-content">
					
						<div class="page-header">
							<h1>
								LPG 
								<small>
									<i class="ace-icon fa fa-angle-double-right"></i>
									Add Areas
								</small>
							</h1>
						</div><!-- /.page-header -->

						<div class="row">
							<div class="col-xs-12">
								<!-- PAGE CONTENT BEGINS -->

									<?php
									     if ($this->session->flashdata('err_message')) {
									     ?>
									   
									    <div class="alert alert-danger">
											<button type="button" class="close" data-dismiss="alert">
												<i class="ace-icon fa fa-times"></i>
											</button>

											<strong>
												<i class="ace-icon fa fa-times"></i>
												Oh snap!
											</strong>

											<?php echo $this->session->flashdata('err_message'); ?>
											<br>
										</div>

									    <?php
									     }   ?>
		
								<form id="formID" class="form-horizontal" role="form" method="post" action="<?php echo SURL."user"?>" enctype="multipart/form-data">
								<div class="row" style="background:#dadada;">

								<div class="col-sm-4">
									<div class="col-sm-8">
									<h5 style="margin-left: 8px;margin-top: -1px;color: #2679B5; ">Areas &nbsp;  </h5>
								</div>
								<div class="col-sm-2">
								
									<input onclick="select_all(this.id)" style="margin-left: -43px;margin-top: 2px;" type="checkbox" name="area_list" id="area_list">
								</div>
									
								</div>
							</div>


									<div class="form-group">
 
										<?php for ($i=0; $i<count($area_list); $i++) {  ?>
										
										<div class="col-sm-4">
									<input id="<?php echo $area_list[$i]['area_id']; ?>" onclick="update_map(<?php echo $area_list[$i]['area_id']; ?>)" <?php if(in_array($area_list[$i]['area_id'], $map_area))
		{ echo 'checked';  } ?> value="<?php echo $area_list[$i]['area_id'] ?>" type="checkbox" name="checkbox[]" placeholder="Page Name" class="col-xs-4 col-sm-2 page_checkbox area_list" title="Only Letters Allowed"/>
												<?php echo $area_list[$i]['aname'] ?>

										</div>

										<?php } ?>

									</div>

									<input type="hidden" name="uid" id="uid" value="<?php echo $userid; ?>" />
									<div class="row">
										<div class="form-actions center">
											<button class="btn btn-info">
												<i class="ace-icon fa fa-check bigger-110"></i>
												Submit
											</button>
										</div>
									</div>
								</form>

								<!-- PAGE CONTENT ENDS -->
							</div><!-- /.col -->
						</div><!-- /.row -->
					</div><!-- /.page-content -->
				</div>
			</div><!-- /.main-content -->

		</div><!-- /.main-container -->

<?php
	$this->load->view('en/include/footer');
?>
<?php
	$this->load->view('en/include/js');
?>
<script type="text/javascript">

	
	function select_all(area_id ) { 

		if ($('#'+area_id ).is(':checked')) {
			var status=1;
		}
		else
		{
			var status=0;
		}
		
		var uid =$('#uid').val();

		$('.'+area_id).each(function( index ) {


			var areaid = $(this).val();
 			
 			if(status==1)
 			{
				$(this).prop('checked', true);
 			}
 			else 
 			{	
				$(this).prop('checked', false);
 			}


	var request = $.ajax({
		  url: "<?php echo SURL."user/update_map/"?>"+areaid,
		  type: "POST",
		  data: {area_id:areaid,uid:uid,status:status},
		  dataType: "html"
		});
		request.done(function(msg) {
			//$('#tbody_id').html(msg);
		});
		request.fail(function(jqXHR, textStatus) {
		  alert( "Request failed: " + textStatus );
		});

		});

	}
	function update_map(area_id ) {

		if ($('#'+area_id ).is(':checked')) {

			var status=1;
		}
		else
		{
			var status=0;
		}		

		var uid =$('#uid').val();
	
	var request = $.ajax({
		  url: "<?php echo SURL."user/update_map/"?>"+area_id,
		  type: "POST",
		  data: {area_id:area_id,uid:uid,status:status},
		  dataType: "html"
		});
		request.done(function(msg) {
			//$('#tbody_id').html(msg);
		});
		request.fail(function(jqXHR, textStatus) {
		  alert( "Request failed: " + textStatus );
		});

	}	


</script>
	</body>
</html>
