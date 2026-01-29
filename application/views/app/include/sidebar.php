<?php $CI = &get_instance();
$CI->load->model('mod_user', 'mod_common');
$result = $CI->mod_user->get_menu();


$user_id = $this->session->userdata('id');
$where_right = array('uid' => $user_id, 'pageid' => '10');
$data['bank_right'] = $this->db->query("SELECT * from tbl_user_rights where uid='$user_id' and pageid>='10'")->row_array();

// $data['bank_right'] = $this->mod_common->select_array_records('tbl_user_rights', "*", $where_right);

// 	        if(!empty($data['bank_right'])) {
// 	        	echo "string";
// 	        }
// 	        else{
// 	        	echo "aaaaaaaa";
// 	        }
// exit;
?>

<div id="sidebar" class="sidebar responsive ace-save-state">
	<script type="text/javascript">
		try {
			ace.settings.loadState('sidebar')
		} catch (e) {}
	</script>

	<div class="sidebar-shortcuts" id="sidebar-shortcuts">
		<div class="sidebar-shortcuts-large" id="sidebar-shortcuts-large">
			<button class="btn btn-success">
				<i class="ace-icon fa fa-signal"></i>
			</button>

			<button class="btn btn-info">
				<i class="ace-icon fa fa-pencil"></i>
			</button>

			<button class="btn btn-warning">
				<i class="ace-icon fa fa-users"></i>
			</button>

			<button class="btn btn-danger">
				<i class="ace-icon fa fa-cogs"></i>
			</button>
		</div>

		<div class="sidebar-shortcuts-mini" id="sidebar-shortcuts-mini">
			<span class="btn btn-success"></span>

			<span class="btn btn-info"></span>

			<span class="btn btn-warning"></span>

			<span class="btn btn-danger"></span>
		</div>
	</div><!-- /.sidebar-shortcuts -->

	<ul class="nav nav-list">
		<ul class="nav nav-list">


			<div class="sidebar-shortcuts" id="sidebar-shortcuts">
				<a href="<?php echo SURL . "" ?>">
					<button class="btn btn-danger">
						<i class="ace-icon fa fa-arrow-left icon-on-left"></i>
						BACK TO MAIN MENU
					</button>
				</a>
			</div>


			<li class="active">
				<a href="<?php echo SURL; ?>Module/app">
					<i class="menu-icon fa fa-tachometer"></i>
					<span class="menu-text"> Dashboard</span>
				</a>

				<b class="arrow"></b>
			</li>


			<?php $flag = 0;
			$new_jj = 0;
			for ($i = 0; $i < count($result); $i++) {

				if ($result[$i]['pageid'] > 1040 && $result[$i]['pageid'] <= 1070) {
					$flag = 1; ?>
					<?php if ($new_jj++ == 0) { ?>
						<li class="">

							<a href="#" class="dropdown-toggle">
								<i class="menu-icon fa fa-cogs"></i>
								<span class="menu-text">
									Configuration
								</span>

								<b class="arrow fa fa-angle-down"></b>
							</a>

							<b class="arrow"></b>

							<ul class="submenu">

							<?php } ?>

							<li class="">
								<a href="<?php echo SURL . "app/" . $result[$i]['linkname']; ?>">
									<i class="menu-icon fa fa-caret-right"></i>
									<?php echo $result[$i]['pagename']; ?>
								</a>

								<b class="arrow"></b>
							</li>
						<?php }
				}

				if ($flag == 1) {
					echo '	</ul>
							</li>';
				}

				$flag = 0;
				$new_jj = 0;
				// pm($result);
				for ($i = 0; $i < count($result); $i++) {

					if ($result[$i]['pageid'] > 1070 && $result[$i]['pageid'] <= 1085) {
						$flag = 1; ?>
							<?php if ($new_jj++ == 0) { ?>
								<li class="">

									<a href="#" class="dropdown-toggle">
										<i id="icons1" class="menu-icon fa fa-shopping-cart"></i>
										<span class="menu-text">
											Order Management
										</span>

										<b class="arrow fa fa-angle-down"></b>
									</a>

									<b class="arrow"></b>

									<ul class="submenu">
									<?php } ?>

									<li class="">
										<a for="<?= $result[$i]['pageid']; ?>" href="<?php echo SURL . "app/" . $result[$i]['linkname']; ?>">
											<i class="menu-icon fa fa-caret-right"></i>
											<?php echo $result[$i]['pagename']; ?>
										</a>

										<b class="arrow"></b>
									</li>
								<?php }
						}

						if ($flag == 1) {
							echo '	</ul>
										</li>';
						}



						$flag = 0;
						$new_jj = 0;
						for ($i = 0; $i < count($result); $i++) {

							if ($result[$i]['pageid'] > 1105 && $result[$i]['pageid'] < 1110) {
								$flag = 1; ?>
									<?php if ($new_jj++ == 0) { ?>
										<li class="">

											<a href="#" class="dropdown-toggle">
												<i id="icons1" class="menu-icon fa fa-light fa-comments-o"></i>
												<span class="menu-text">
													Feedback
												</span>

												<b class="arrow fa fa-angle-down"></b>
											</a>

											<b class="arrow"></b>

											<ul class="submenu">

											<?php } ?>

											<li class="">
												<a href="<?php echo SURL . "app/" . $result[$i]['linkname']; ?>">
													<i class="menu-icon fa fa-caret-right"></i>
													<?php echo $result[$i]['pagename']; ?>
												</a>

												<b class="arrow"></b>
											</li>
										<?php }
								}

								if ($flag == 1) {
									echo '	</ul></li>';
								}



								$flag = 0;
								$new_jj = 0;
								for ($i = 0; $i < count($result); $i++) {

									if ($result[$i]['pageid'] > 1095 && $result[$i]['pageid'] < 1105) {
										$flag = 1; ?>
											<?php if ($new_jj++ == 0) { ?>
												<li class="">

													<a href="#" class="dropdown-toggle">
														<i class="menu-icon fa fa-user"></i>

														<span class="menu-text">
															Customer
														</span>

														<b class="arrow fa fa-angle-down"></b>
													</a>

													<b class="arrow"></b>

													<ul class="submenu">

													<?php
												} ?>

													<li class="">
														<a href="<?php echo SURL . "app/" . $result[$i]['linkname']; ?>">
															<i class="menu-icon fa fa-caret-right"></i>
															<?php echo $result[$i]['pagename']; ?>
														</a>

														<b class="arrow"></b>
													</li>
													<?php }
											}
											if ($flag == 1) {
												echo '	</ul>
												</li>';
											}

											$flag = 0;
											$new_jj = 0;
											for ($i = 0; $i < count($result); $i++) {

												if ($result[$i]['pageid'] > 1160 && $result[$i]['pageid'] < 1179) {
													$flag = 1;
													if ($new_jj++ == 0) { ?>
														<li class="">
															<a href="#" class="dropdown-toggle">
																<i class="menu-icon fa fa-user"></i>
																<span class="menu-text">
																	Reports
																</span>
																<b class="arrow fa fa-angle-down"></b>
															</a>
															<b class="arrow"></b>
															<ul class="submenu">
															<?php } ?>
															<li class="">
																<a href="<?php echo SURL . "app/" . $result[$i]['linkname']; ?>">
																	<i class="menu-icon fa fa-caret-right"></i>
																	<?php echo $result[$i]['pagename']; ?>
																</a>

																<b class="arrow"></b>
															</li>
													<?php }
											}
											if ($flag == 1) {
												echo '	</ul></li>';
											}

											// Notification Section - Added after Reports
											$flag = 0;
											$new_jj = 0;
											for ($i = 0; $i < count($result); $i++) {

												if ($result[$i]['pageid'] >= 1200 && $result[$i]['pageid'] <= 1210) {
													$flag = 1; ?>

													<?php if ($new_jj++ == 0) { ?>

														<li class="">

															<a href="#" class="dropdown-toggle">
																<i id="icons1" class="menu-icon fa fa-bell"></i>
																<span class="menu-text">
																	Notification
																</span>
																<b class="arrow fa fa-angle-down"></b>
															</a>

															<b class="arrow"></b>

															<ul class="submenu">

															<?php } ?>

															<li class="">
																<a href="<?php echo SURL . "app/" . $result[$i]['linkname']; ?>">
																	<i class="menu-icon fa fa-caret-right"></i>
																	<?php echo $result[$i]['pagename']; ?>
																</a>

																<b class="arrow"></b>
															</li>
														<?php }
											}

											if ($flag == 1) {
												echo '</ul></li>';
											} ?>



													<li class="">
														<?php for ($i = 0; $i < count($result); $i++) {

															if ($result[$i]['pageid'] > 500 && $result[$i]['pageid'] < 601) { ?>

																<a href="<?php echo SURL . "app/" . $result[$i]['linkname']; ?>">
																	<i id="icons2" class="menu-icon fa fa-database"></i>
																	<?php echo $result[$i]['pagename']; ?>
																</a>

																<b class="arrow"></b>

															<?php } ?>
														<?php } ?>

													</li>


															</ul><!-- /.nav-list -->

															<div class="sidebar-toggle sidebar-collapse" id="sidebar-collapse">
																<i id="sidebar-toggle-icon" class="ace-icon fa fa-angle-double-left ace-save-state" data-icon1="ace-icon fa fa-angle-double-left" data-icon2="ace-icon fa fa-angle-double-right"></i>
															</div>
</div>