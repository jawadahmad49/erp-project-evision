<!DOCTYPE html>
<html lang="en">

<head>
	<meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1" />
	<meta charset="utf-8" />
	<title>LPG-LOGIN</title>

	<meta name="description" content="User login page" />
	<meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0" />

	<!-- bootstrap & fontawesome -->
	<link rel="stylesheet" href="<?php echo SURL; ?>assets/css/bootstrap.min.css" />
	<link rel="stylesheet" href="<?php echo SURL; ?>assets/font-awesome/4.5.0/css/font-awesome.min.css" />

	<!-- text fonts -->
	<link rel="stylesheet" href="<?php echo SURL; ?>assets/css/fonts.googleapis.com.css" />

	<!-- ace styles -->
	<link rel="stylesheet" href="<?php echo SURL; ?>assets/css/ace.min.css" />

	<!--[if lte IE 9]>
			<link rel="stylesheet" href="assets/css/ace-part2.min.css" />
		<![endif]-->
	<link rel="stylesheet" href="<?php echo SURL; ?>assets/css/ace-rtl.min.css" />

</head>
<style>
	.bg {
		background: url("<?php echo SURL; ?>background.png");
	}

	.bg::before {
		content: '';
		position: absolute;
		/* top: 0; */
		/* left: 0; */
		width: 100%;
		height: 100%;
		background-color: #2b3773;
		opacity: 0.5;
		/* Adjust opacity to control filter intensity */
		z-index: -1;
	}

	.login-layout {
		/* position: relative; */
		/* z-index: 1; Ensure content is above the overlay */
	}

	a {
		color: #91c7f6;
		text-decoration: none;
	}
</style>

<body class="login-layout light-login bg">
	<div class="main-container">
		<div class="main-content">
			<div class="row">
				<div class="col-sm-10 col-sm-offset-1">
					<div class="login-container">
						<div class="center">
							<!-- <img src="<?php echo SURL; ?>assets/lpglogo.png" style="width:300px" /> -->
							<img src="<?php echo SURL; ?>assets/images/logo-light.png" style="width:150px" />

						</div>

						<div class="space-6"></div>

						<div class="position-relative">
							<div id="login-box" class="login-box visible widget-box no-border">
								<div class="widget-body">
									<div class="widget-main">
										<h4 class="header blue lighter bigger">
											<i class="ace-icon glyphicon glyphicon-oil red"></i>
											Welcome&nbsp;to&nbsp;LPG&nbsp;Retail POS</b>
										</h4>
										<?php
										if ($this->session->flashdata('err_message')) {
											?>
											<div class="alert alert-danger"> <?php echo $this->session->flashdata('err_message'); ?> </div>
											<?php
										}
										?>

										<!--  <?php
										//if ($this->session->flashdata('logout')) {
										?>
												<div class="alert alert-danger"> <?php //echo $this->session->flashdata('logout'); 
												?> </div>
											<?php
											// }
											?>
											 <?php
											 // if ($this->session->flashdata('logout')) {
											 ?>
												<div class="row" style="margin-left:88px;" >
													 <div class="col-6">
														 <a href="<?php //echo SURL 
														 ?>login/logout">
													<button class="btn btn-xs btn-danger pull-left">
															<i class="ace-icon fa fa-times"></i>
															<span class="bigger-110">Logout</span>
														</button>
													</a>
												</div>
												</div>
											<?php
											// }
											?> -->

										<div class="space-6"></div>

										<form class="form-horizontal" role="form" id="login_id" action="<?php echo SURL . "login" ?>" method="post">

											<fieldset>
												<label class="block clearfix">Login Id
													<span class="block input-icon input-icon-right">
														<input type="text" name="email" class="form-control" placeholder="Enter login Id" />
														<i class="ace-icon fa fa-user"></i>
													</span>
												</label>

												<label class="block clearfix">Password
													<span class="block input-icon input-icon-right">
														<input type="password" name="password" class="form-control" placeholder="Enter Password" />
														<i class="ace-icon fa fa-lock"></i>
													</span>
												</label>
												<label class="block clearfix">Financial Year</label>

												<select class="chosen-select form-control" name="db" id="" data-placeholder="Choose a City...">
													< <!--option value="lpg_pos_erp">2021</option> -->
													<option value="gasablepk_live">gasablepk_live</option>
													<option value="Gasable_PK">2024</option>
														<!-- <option value="gm5625v4_gasablepk">2024</option> -->


												</select>




												<div class="space"></div>

												<div class="clearfix">
													<!-- <label class="inline">
															<input type="checkbox" class="ace" />
															<span class="lbl"> Remember Me</span>
														</label> -->
													<button type="submit" name="login_submit" value="login_submit" class="width-35 pull-right btn btn-sm btn-primary">
														<i class="ace-icon fa fa-key"></i>
														<span class="bigger-110">Login</span>
													</button>
												</div>

												<div class="space-4"></div>
											</fieldset>
										</form>


									</div><!-- /.widget-main -->

									<div class="toolbar clearfix">
										<!-- <div>
												<a href="#" data-target="#forgot-box" class="forgot-password-link">
													<i class="ace-icon fa fa-arrow-left"></i>
													I forgot my password
												</a>
											</div> -->


									</div>
								</div><!-- /.widget-body -->
							</div><!-- /.login-box -->

							<div id="forgot-box" class="forgot-box widget-box no-border">
								<div class="widget-body">
									<div class="widget-main">
										<h4 class="header red lighter bigger">
											<i class="ace-icon fa fa-key"></i>
											Retrieve Password
										</h4>

										<div class="space-6"></div>
										<p>
											Enter your email and to receive instructions
										</p>

										<form>
											<fieldset>
												<label class="block clearfix">
													<span class="block input-icon input-icon-right">
														<input type="email" class="form-control" placeholder="Email" />
														<i class="ace-icon fa fa-envelope"></i>
													</span>
												</label>

												<div class="clearfix">
													<button type="button" class="width-35 pull-right btn btn-sm btn-danger">
														<i class="ace-icon fa fa-lightbulb-o"></i>
														<span class="bigger-110">Send Me!</span>
													</button>
												</div>
											</fieldset>
										</form>
									</div><!-- /.widget-main -->

									<div class="toolbar center">
										<a href="#" data-target="#login-box" class="back-to-login-link">
											Back to login
											<i class="ace-icon fa fa-arrow-right"></i>
										</a>
									</div>
								</div><!-- /.widget-body -->
							</div><!-- /.forgot-box -->

							<div id="signup-box" class="signup-box widget-box no-border">
								<div class="widget-body">
									<div class="widget-main">
										<h4 class="header green lighter bigger">
											<i class="ace-icon fa fa-users blue"></i>
											New User Registration
										</h4>

										<div class="space-6"></div>
										<p> Enter your details to begin: </p>

										<form>
											<fieldset>
												<label class="block clearfix">
													<span class="block input-icon input-icon-right">
														<input type="email" class="form-control" placeholder="Email" />
														<i class="ace-icon fa fa-envelope"></i>
													</span>
												</label>

												<label class="block clearfix">
													<span class="block input-icon input-icon-right">
														<input type="text" class="form-control" placeholder="Username" />
														<i class="ace-icon fa fa-user"></i>
													</span>
												</label>

												<label class="block clearfix">
													<span class="block input-icon input-icon-right">
														<input type="password" class="form-control" placeholder="Password" />
														<i class="ace-icon fa fa-lock"></i>
													</span>
												</label>

												<label class="block clearfix">
													<span class="block input-icon input-icon-right">
														<input type="password" class="form-control" placeholder="Repeat password" />
														<i class="ace-icon fa fa-retweet"></i>
													</span>
												</label>

												<label class="block">
													<input type="checkbox" class="ace" />
													<span class="lbl">
														I accept the
														<a href="#">User Agreement</a>
													</span>
												</label>

												<div class="space-24"></div>

												<div class="clearfix">
													<button type="reset" class="width-30 pull-left btn btn-sm">
														<i class="ace-icon fa fa-refresh"></i>
														<span class="bigger-110">Reset</span>
													</button>

													<button type="button" class="width-65 pull-right btn btn-sm btn-success">
														<span class="bigger-110">Register</span>

														<i class="ace-icon fa fa-arrow-right icon-on-right"></i>
													</button>
												</div>
											</fieldset>
										</form>
									</div>

									<div class="toolbar center">
										<a href="#" data-target="#login-box" class="back-to-login-link">
											<i class="ace-icon fa fa-arrow-left"></i>
											Back to login
										</a>
									</div>
								</div><!-- /.widget-body -->
							</div><!-- /.signup-box -->
						</div><!-- /.position-relative -->

						<center>
							<h4 class="blue" id="id-company-text">&copy;<a href="http://evisionsystem.com">Evision System</a></h4>
						</center>
					</div>
				</div><!-- /.col -->
			</div><!-- /.row -->
		</div><!-- /.main-content -->
	</div><!-- /.main-container -->

	<!-- basic scripts -->

	<!--[if !IE]> -->

	<?php // if ($this->session->flashdata('logout')) { 
	?>
	<script type="text/javascript">
		// if(confirm('Your are already login, do you want to logout remote site !!'))
		//        {
		//          window.location='<?php echo SURL ?>login/se_session/1';
		//        }
	</script>

	<?php // } 
	?>
	<script src="<?php echo SURL; ?>assets/js/jquery-2.1.4.min.js"></script>

	<!-- <![endif]-->

	<!--[if IE]>
<script src="assets/js/jquery-1.11.3.min.js"></script>
<![endif]-->
	<script type="text/javascript">
		if ('ontouchstart' in document.documentElement) document.write("<script src='<?php echo SURL; ?>assets/js/jquery.mobile.custom.min.js'>" + "<" + "/script>");
	</script>
	<script src="<?php echo SURL; ?>assets/js/bootstrap.min.js"></script>

	<!-- page specific plugin scripts -->
	<!-- 		<script src="<?php echo SURL; ?>assets/js/jquery.dataTables.min.js"></script>
		<script src="<?php echo SURL; ?>assets/js/jquery.dataTables.bootstrap.min.js"></script>
		<script src="<?php echo SURL; ?>assets/js/dataTables.buttons.min.js"></script>
		<script src="<?php echo SURL; ?>assets/js/buttons.flash.min.js"></script>
		<script src="<?php echo SURL; ?>assets/js/buttons.html5.min.js"></script>
		<script src="<?php echo SURL; ?>assets/js/buttons.print.min.js"></script>
		<script src="<?php echo SURL; ?>assets/js/buttons.colVis.min.js"></script>
		<script src="<?php echo SURL; ?>assets/js/dataTables.select.min.js"></script>
 -->
	<!-- ace scripts -->
	<!-- 		<script src="<?php echo SURL; ?>assets/js/ace-elements.min.js"></script>
		<script src="<?php echo SURL; ?>assets/js/ace.min.js"></script> -->

	<script src="<?php echo SURL; ?>assets/js/bootbox.js"></script>
	<script type="text/javascript">
		<?php if ($this->session->flashdata('logout')) { ?>
			bootbox.confirm("Your are already login, do you want to logout remote site !!", function (result) {
				if (result) {
					window.location = '<?php echo SURL ?>login/se_session/1';
				}
			});

		<?php } ?>
	</script>

	<!-- <![endif]-->

	<!--[if IE]>
<script src="assets/js/jquery-1.11.3.min.js"></script>
<![endif]-->
	<script type="text/javascript">
		if ('ontouchstart' in document.documentElement) document.write("<script src='<?php echo SURL; ?>assets/js/jquery.mobile.custom.min.js'>" + "<" + "/script>");
	</script>

	<!-- inline scripts related to this page -->
	<script type="text/javascript">
		jQuery(function ($) {
			$(document).on('click', '.toolbar a[data-target]', function (e) {
				e.preventDefault();
				var target = $(this).data('target');
				$('.widget-box.visible').removeClass('visible'); //hide others
				$(target).addClass('visible'); //show target
			});
		});



		//you don't need this, just used for changing background
		jQuery(function ($) {
			$('#btn-login-dark').on('click', function (e) {
				$('body').attr('class', 'login-layout');
				$('#id-text2').attr('class', 'white');
				$('#id-company-text').attr('class', 'blue');

				e.preventDefault();
			});
			$('#btn-login-light').on('click', function (e) {
				$('body').attr('class', 'login-layout light-login');
				$('#id-text2').attr('class', 'grey');
				$('#id-company-text').attr('class', 'blue');

				e.preventDefault();
			});
			$('#btn-login-blur').on('click', function (e) {
				$('body').attr('class', 'login-layout blur-login');
				$('#id-text2').attr('class', 'white');
				$('#id-company-text').attr('class', 'light-blue');

				e.preventDefault();
			});

		});
	</script>
</body>

</html>