<?php $actual_url_final = 'http://' . $_SERVER["SERVER_NAME"] . ":" . $_SERVER["SERVER_PORT"] . $_SERVER["REQUEST_URI"];
$CI = &get_instance();
$CI->load->model('mod_user');
$result = $CI->mod_user->get_language();
$result['lang_opt'];
$companyname = $this->db->query("SELECT * from tbl_company")->result_array()[0]['business_name'];
?>
<div id="navbar" class="navbar navbar-default          ace-save-state">
	<div class="navbar-container ace-save-state" id="navbar-container">
		<button type="button" class="navbar-toggle menu-toggler pull-left" id="menu-toggler" data-target="#sidebar">
			<span class="sr-only">Toggle sidebar</span>
			<span class="icon-bar"></span>
			<span class="icon-bar"></span>
			<span class="icon-bar"></span>
		</button>
		<?php
		$login_user = $this->session->userdata('id');
		$sale_point_ids = $this->db->query("SELECT location FROM tbl_admin WHERE id = '$login_user'")->row_array()['location'];
		if ($sale_point_ids) {
			$sale_point_id_array = explode(',', $sale_point_ids);
			$sale_point_id_list = implode("','", $sale_point_id_array);
			$where_location = "and sale_point_id IN ('$sale_point_id_list')";
		} else {
			$where_location = "";
		}
		$booked_orders = $this->db->query("SELECT * FROM `tbl_place_order` where deliveryStatus='Booked' $where_location")->result_array();
		?>
		<div class="navbar-buttons navbar-header pull-right" role="navigation">
			<ul class="nav ace-nav">
				<li class="purple dropdown-modal">
					<a data-toggle="dropdown" class="dropdown-toggle" href="#">
						<i class="ace-icon fa fa-bell icon-animated-bell"></i>
						<span class="badge badge-important"><?php echo count($booked_orders) ?></span>
					</a>
					<ul class="dropdown-menu-right dropdown-navbar navbar-pink dropdown-menu dropdown-caret dropdown-close">
						<li class="dropdown-header">
							<i class="ace-icon fa fa-exclamation-triangle"></i>
							<?php echo count($booked_orders) ?> Pending Orders
						</li>
						<li class="dropdown-content">
							<ul class="dropdown-menu dropdown-navbar navbar-pink">
								<?php foreach ($booked_orders as $key => $value) {
									$order_id = $value['id'];
									$userid = $value['userid'];
									$user_detail = $this->db->query("SELECT * FROM `tbl_user` where id='$userid'")->row_array();
									if ($user_detail['dp']) {
										$dp = $user_detail['dp'];
									} else {
										$dp = "default.jpeg";
									}
								?>
									<li>
										<a href="<?php echo SURL . "app/Order_confirmation/index/$order_id" ?>" target="_blank" class="clearfix">
											<img src="<?php echo IMG . 'profile/' . $dp ?>" class="msg-photo" alt="Customer Img" />
											<span class="msg-body">
												<span class="msg-title">
													<span class="blue">Order # <?php echo $order_id . " - " . $value['deliveryType'] ?></span><br>
													<?php echo $user_detail['name'] ?>
												</span>
												<span class="msg-time">
													<i class="ace-icon fa fa-clock-o"></i>
													<span><?php echo $value['date'] ?></span> <span><?php echo date('h:i A', strtotime($value['time'])); ?></span>
												</span>
											</span>
										</a>
									</li>
								<?php } ?>
							</ul>
						</li>
						<li class="dropdown-footer">
							<a href="<?php echo SURL . "app/Admin/notification_list" ?>" target="blank">
								See all Orders
								<i class="ace-icon fa fa-arrow-right"></i>
							</a>
						</li>
					</ul>
				</li>
				<li class="light-blue dropdown-modal">
					<a data-toggle="dropdown" href="#" class="dropdown-toggle">
						<!-- <img class="nav-user-photo" src="<?php echo SURL ?>assets/images/avatars/user.jpg" alt="Jason's Photo" /> -->
						<span class="user-info">
							<small>Welcome,</small>
							<?php echo ucwords($_SESSION['admin_name']); ?>
						</span>
						<i class="ace-icon fa fa-caret-down"></i>
					</a>
					<ul class="user-menu dropdown-menu-right dropdown-menu dropdown-yellow dropdown-caret dropdown-close">
						<li>
							<a href="<?php echo SURL ?>login/change_password">
								<i class="ace-icon fa fa-cog"></i>
								Change Password
							</a>
						</li>
						<li class="divider"></li>
						<li>
							<a href="<?php echo SURL ?>login/logout">
								<i class="ace-icon fa fa-power-off"></i>
								Logout
							</a>
						</li>
					</ul>
				</li>
			</ul>
		</div>
		<div class="navbar-header pull-left">
			<a href="<?php echo SURL; ?>admin" class="navbar-brand">
				<small>
					<i class="glyphicon glyphicon-oil"></i>
					<?php $year = date('Y'); ?>
					<?php echo $companyname; ?> (Financial Year : <?php echo $year; ?>)
				</small>
			</a>
		</div>
	</div><!-- /.navbar-container -->
</div>
<?php
//////////////////check of posting last 3 days//////////////////////////////////////////////
$result = $CI->mod_user->get_last_posted();
$last_posted = $result[0]['post_date'];
$ci = &get_instance();
$current_controler = $ci->uri->segment(1);
//////////////////check for last backup //////////////////////////////////////////////
$result = $CI->mod_user->get_last_backupdate();
$last_backup_date = $result[0]['dt'];



if ($last_backup_date) {
	$today_is = date('Y-m-d');
	$max_three_days = date("Y-m-d", strtotime($today_is . "-3 days"));
	$dt1 =  strtotime($max_three_days);
	$dt2 =  strtotime($last_backup_date);
	if ($dt2 < $dt1) {
		$this->session->set_flashdata('err_message', 'Alert! Please take database backup First, backup is not taken for last three days, Maintain proper log of database backups in case of any lost!');
	}
} else {
	$this->session->set_flashdata('err_message', 'Database backup is not taken yet , please backup your data first !!');
}
////////////////////////////////////////////////////////////////////////////////////////////
?>