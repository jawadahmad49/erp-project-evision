<head>
	<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" />


	<script src="https://code.jquery.com/jquery-3.7.1.js" integrity="sha256-eKhayi8LEQwp4NKxN+CfCh+3qOVUtJn3QNZ0TciWLP4=" crossorigin="anonymous"></script>


	<meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1" />
	<meta charset="utf-8" />
	<title><?php echo $title; ?></title>
	<meta name="description" content="Common form elements and layouts" />
	<meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0" />
	<!-- bootstrap & fontawesome -->
	<link rel="stylesheet" href="<?php echo SURL ?>assets/css/bootstrap.min.css" />
	<link rel="stylesheet" href="<?php echo SURL ?>assets/font-awesome/4.5.0/css/font-awesome.min.css" />

	<!-- page specific plugin styles -->
	<link rel="stylesheet" href="<?php echo SURL ?>assets/css/jquery-ui.custom.min.css" />
	<link rel="stylesheet" href="<?php echo SURL ?>assets/css/chosen.min.css" />
	<link rel="stylesheet" href="<?php echo SURL ?>assets/css/bootstrap-datepicker3.min.css" />
	<link rel="stylesheet" href="<?php echo SURL ?>assets/css/bootstrap-timepicker.min.css" />
	<link rel="stylesheet" href="<?php echo SURL ?>assets/css/daterangepicker.min.css" />
	<link rel="stylesheet" href="<?php echo SURL ?>assets/css/bootstrap-datetimepicker.min.css" />
	<link rel="stylesheet" href="<?php echo SURL ?>assets/css/bootstrap-colorpicker.min.css" />

	<!-- text fonts -->
	<link rel="stylesheet" href="<?php echo SURL ?>assets/css/fonts.googleapis.com.css" />

	<!-- ace styles -->
	<link rel="stylesheet" href="<?php echo SURL ?>assets/css/ace.min.css" class="ace-main-stylesheet" id="main-ace-style" />

	<!--[if lte IE 9]>
			<link rel="stylesheet" href="assets/css/ace-part2.min.css" class="ace-main-stylesheet" />
		<![endif]-->
	<link rel="stylesheet" href="<?php echo SURL ?>assets/css/ace-skins.min.css" />
	<link rel="stylesheet" href="<?php echo SURL ?>assets/css/ace-rtl.min.css" />

	<!--[if lte IE 9]>
		  <link rel="stylesheet" href="../assets/css/ace-ie.min.css" />
		<![endif]-->

	<!-- inline styles related to this page -->

	<!-- ace settings handler -->
	<script src="<?php echo SURL ?>assets/js/ace-extra.min.js"></script>

	<!-- HTML5shiv and Respond.js for IE8 to support HTML5 elements and media queries -->

	<!--[if lte IE 8]>
		<script src="../assets/js/html5shiv.min.js"></script>
		<script src="../assets/js/respond.min.js"></script>
		<![endif]-->


	<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
	<link rel="stylesheet" type="text/css" href="https://npmcdn.com/flatpickr/dist/themes/material_blue.css">

</head>
<style>
	.select2-container {
		width: 100% !important;
	}
</style>
<?php

$table = 'tbl_admin';
$ci = &get_instance();

// $session_array = array(
// 	'email' => $ci->session->userdata('email'),
// 	'admin_pwd' => $ci->session->userdata('admin_pwd'),
// );
// $login_success =  $ci->mod_common->select_single_records($table, $session_array);
$loginid = $ci->session->userdata('loginid');
$admin_pwd = $ci->session->userdata('admin_pwd');
$login_success =  $this->db->query("SELECT * from tbl_admin where loginid='$loginid' and admin_pwd='$admin_pwd'")->row_array();

if ($ci->session->userdata('logincode') != $login_success['logincode']) {
	//echo 'sssssssssss'; exit;
	redirect(SURL . 'login/ses_session');
}
if ($this->session->userdata('loginid') == '') {
	redirect(SURL . 'login');
}

?>