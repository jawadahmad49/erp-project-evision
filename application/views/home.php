<!DOCTYPE html>
<html lang="en">

<head>
	<meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1" />
	<meta charset="utf-8" />
	<title>OPI LPG LOGIN</title>

	<meta name="description" content="User login page" />
	<meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0" />

	<!-- bootstrap & fontawesome -->
	<link rel="stylesheet" href="assets/css/bootstrap.min.css" />
	<link rel="stylesheet" href="assets/font-awesome/4.5.0/css/font-awesome.min.css" />

	<!-- text fonts -->
	<link rel="stylesheet" href="assets/css/fonts.googleapis.com.css" />

	<!-- ace styles -->
	<link rel="stylesheet" href="assets/css/ace.min.css" />

	<!--[if lte IE 9]>
			<link rel="stylesheet" href="assets/css/ace-part2.min.css" />
		<![endif]-->
	<link rel="stylesheet" href="assets/css/ace-rtl.min.css" />

	<!--[if lte IE 9]>
		  <link rel="stylesheet" href="assets/css/ace-ie.min.css" />
		<![endif]-->

	<!-- HTML5shiv and Respond.js for IE8 to support HTML5 elements and media queries -->

	<!--[if lte IE 8]>
		<script src="assets/js/html5shiv.min.js"></script>
		<script src="assets/js/respond.min.js"></script>
		<![endif]-->
</head>

<body>
	<style>
		* {
			font-family: sans-serif;
			margin: 0;
			padding: 0;
		}

		body {
			background: url(http://192.168.10.92:8012/Gasable_PK/assets/images/still-life-waving-fabric-wind.jpg) no-repeat center / cover;
			/* background: url(https://lpginsight.com/GasablePK/assets/images/still-life-waving-fabric-wind.webp) no-repeat center / cover; */
			/* background: url(http://192.168.10.92:8012/Gasable_PK/assets/images/file.jpg) no-repeat center / cover; */
			/* background: url(https://lpginsight.com/GasablePK/assets/images/file.jpg) no-repeat center / cover; */
		}

		.txth {
			color: #ffffff;
			font-weight: 700;
			letter-spacing: 2px;
			font-family: monospace;
		}

		.txth2 {
			font-weight: bold;
			color: #ffffff;
			font-family: fangsong;
			text-decoration: underline;
			letter-spacing: 4px;
		}

		.figure {
			box-shadow: 0px 1px 9px 5px rgb(0 0 0 / 10%);
			padding: inherit;
			height: fit-content;
			border-radius: 8px;
			background-color: rgb(235 235 235);
			padding-top: 4%;
			margin-top: 10%;
		}

		.txt {
			font-family: system-ui;
			font-size: initial;
			font-weight: 500;
			padding-bottom: 2%;
			cursor: pointer;
		}

		.txt:hover {
			text-decoration: underline;
		}

		.btn {
			border-radius: 10px;
			font-size: 14px;
			text-transform: uppercase;
			height: auto;
			margin-top: 6%;
		}

		@media only screen and (max-width: 768px) {
			.row {


				position: relative;
				left: 34%;


			}

			img,
			svg {
				vertical-align: middle;
				width: 50%;
				height: auto;
			}

			.txt {
				font-family: sans-serif;
				font-weight: 600;
				position: relative;
				/* left: -28px; */
				margin: 38px;
				font-size: 41px;
			}

			.txth {
				font-size: 57px;
				position: relative;
				/* left: 37%; */
			}

			.txth2 {
				position: relative;
				left: 36%;


				font-size: 40px;
			}


			.bg1 {
				display: none;
			}
		}

		@media only screen and (max-width: 1200px) {
			.txth {
				position: relative;
				left: 36%;
				bottom: 37%;
				bottom: 10%;

			}

			.txth2 {
				position: relative;
				left: 32%;
				bottom: 11%;
			}
		}
	</style>

	<div class="col-xs-12 form-group align-center">
		<h1 class="txth">Welcome To</h1>
		<h2 class="txth2">OPI LPG</h2>
		<div class="col-xs-2"></div>
		<div class="col-xs-8">
			<?php
			if ($this->session->flashdata('ok_message')) {
			?>
				<div class="alert alert-success"> <?php echo $this->session->flashdata('ok_message'); ?> </div>
			<?php
			}
			?>
		</div>
	</div>
	<?php
	$userid = $this->session->userdata('id');
	//$Admin = $this->db->query("SELECT count(*) as count from tbl_menu, tbl_user_rights where tbl_user_rights.uid = $userid and tbl_user_rights.pageid = '902'  ORDER BY `id` DESC")->row_array()['count'];
	//$DMS = $this->db->query("SELECT count(*) as count from tbl_menu, tbl_user_rights where tbl_user_rights.uid = $userid and tbl_user_rights.pageid = '903'  ORDER BY `id` DESC")->row_array()['count'];
	//$Society = $this->db->query("SELECT count(*) as count from tbl_menu, tbl_user_rights where tbl_user_rights.uid = $userid and tbl_user_rights.pageid = '905'  ORDER BY `id` DESC")->row_array()['count'];

	?>
	<input type="hidden" value="<?php echo $Society ?>" id="soceity">
	<input type="hidden" value="<?php echo $DMS ?>" id="dms">

	<div class="container form-group ">
		<div class="col-xs-12">
			<div class="col-xs-12 align-center">
				<div class="form-group align-center" style="margin-top: 3%;">
					<div class="col-xs-2"></div>
					<!-- <div class="col-xs-3 form-group">
						<div class="figure" style="width: 80%;">

							<a id="lpg" href="<?php echo SURL ?>admin">

								<figure>
									<img src="<?php echo IMG ?>noorlpg.jpg" class="img1" width="150px" height="120px" alt="">

									<figcaption class="txt center">LPG</figcaption>
								</figure>
							</a>
						</div>
					</div> -->
					<!-- <div class="col-xs-3 form-group">

						<div class="figure" style="width: 80%;">

							<a id="crm" href="<?php echo SURL ?>Module/crm">

								<figure>
									<img src="<?php echo IMG ?>crm.jpg" class="img1" width="150px" height="120px" alt="">
									<figcaption class="txt">CRM</figcaption>
								</figure>
							</a>
						</div>
					</div> -->
					<div class="col-xs-3 form-group"></div>
					<div class="col-xs-3 form-group">

						<div class="figure" style="width: 80%;">

							<a id="mob_app" href="<?php echo SURL ?>Module/app">

								<figure>
									<img src="<?php echo IMG ?>app.png" class="img1" width="150px" height="120px" alt="">
									<figcaption class="txt">Mobile App</figcaption>
								</figure>
							</a>
						</div>
					</div>

					<div class="form-group col-xs-12">
						<a href="<?php echo SURL . "login/logout"; ?>" class="btn btn-primary submit">
							<i class="ace-icon fa fa-sign-out bigger-130"></i>
							<span class="bigger-110">Logout</span>
						</a>
					</div>
				</div>
			</div>

		</div><!-- /.main-container -->
		<!--[if !IE]> -->
		<script src="assets/js/jquery-2.1.4.min.js"></script>

		<!-- <![endif]-->

		<!--[if IE]>
<script src="assets/js/jquery-1.11.3.min.js"></script>
<![endif]-->
		<script type="text/javascript">
			if ('ontouchstart' in document.documentElement) document.write("<script src='assets/js/jquery.mobile.custom.min.js'>" + "<" + "/script>");
		</script>

		<!-- inline scripts related to this page -->
		<script type="text/javascript">
			$(document).ready(function() {
				var a = $("#lpg").val();
				var b = $("#crm").val();


				if (a == '0') {
					document.getElementById('lpg').style.cursor = 'no-drop';
					document.getElementById('lpg').style.color = '#337AB7';
					document.getElementById('lpg').style.textDecoration = 'none';
					document.getElementById('lpg').removeAttribute('href');
				}
				if (b == '0') {
					document.getElementById('crm').removeAttribute('href');
					document.getElementById('crm').style.color = '#337AB7';
					document.getElementById('crm').style.textDecoration = 'none';
					document.getElementById('crm').style.cursor = 'no-drop';

				}

			})
			jQuery(function($) {
				$(document).on('click', '.toolbar a[data-target]', function(e) {
					e.preventDefault();
					var target = $(this).data('target');
					$('.widget-box.visible').removeClass('visible'); //hide others
					$(target).addClass('visible'); //show target
				});
			});


			//you don't need this, just used for changing background
			jQuery(function($) {
				$('#btn-login-dark').on('click', function(e) {
					$('body').attr('class', 'login-layout');
					$('#id-text2').attr('class', 'white');
					$('#id-company-text').attr('class', 'blue');

					e.preventDefault();
				});
				$('#btn-login-light').on('click', function(e) {
					$('body').attr('class', 'login-layout light-login');
					$('#id-text2').attr('class', 'grey');
					$('#id-company-text').attr('class', 'blue');

					e.preventDefault();
				});
				$('#btn-login-blur').on('click', function(e) {
					$('body').attr('class', 'login-layout blur-login');
					$('#id-text2').attr('class', 'white');
					$('#id-company-text').attr('class', 'light-blue');

					e.preventDefault();
				});

			});
		</script>
</body>


</html>