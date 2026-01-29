			<?php $CI = &get_instance();
			$CI->load->model('mod_user', 'mod_common');

			$result = $CI->mod_user->get_menu();


			$user_id = $this->session->userdata('id');
			$where_right = array('uid' => $user_id, 'pageid' => '10');

			$data['bank_right'] = $this->mod_common->select_array_records('tbl_user_rights', "*", $where_right);

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
							<a href="<?php echo SURL ?>">
								<button class="btn btn-danger">
									<i class="ace-icon fa fa-arrow-left icon-on-left"></i>
									BACK TO MAIN MENU
								</button>
							</a>
						</div>


						<li class="active">
							<a href="<?php echo SURL; ?>admin">
								<i class="menu-icon fa fa-tachometer"></i>
								<span class="menu-text"> Dashboard</span>
							</a>

							<b class="arrow"></b>
						</li>

						<?php $flag = 0;
						for ($i = 0; $i < count($result); $i++) {

							if ($result[$i]['pageid'] < 50 || $result[$i]['pageid'] == 303) {
								$flag = 1;  ?>

								<?php if ($i == 0) { ?>
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



										<?php }

									//	if($result[$i]['pageid']==20)continue;
										?>

										<li class="">
											<a href="<?php echo SURL . $result[$i]['linkname']; ?>">
												<i class="menu-icon fa fa-caret-right"></i>
												<?php echo $result[$i]['pagename']; ?>
											</a>

											<b class="arrow"></b>
										</li>



									<?php  }
							}




							if ($flag == 1) { ?>
										</ul>
									</li>
								<?php  } ?>

								<?php $flag = 0;
								$new_jj = 0;
								for ($i = 0; $i < count($result); $i++) {

									if ($result[$i]['pageid'] > 1000 && $result[$i]['pageid'] < 1015) {
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

												<?php  } ?>

												<li class="">
													<a href="<?php echo SURL . $result[$i]['linkname']; ?>">
														<i class="menu-icon fa fa-caret-right"></i>
														<?php echo $result[$i]['pagename']; ?>
													</a>

													<b class="arrow"></b>
												</li>
											<?php  } ?>
										<?php  }

										?>
										<?php if ($flag == 1) { ?>

												</ul>
											</li>
										<?php } ?>
										<?php $flag = 0;
										$new_jj = 0;
										for ($i = 0; $i < count($result); $i++) {

											if ($result[$i]['pageid'] > 100 && $result[$i]['pageid'] < 201) {
												$flag = 1; ?>


												<?php if ($new_jj++ == 0) { ?>

													<li class="">

														<a href="#" class="dropdown-toggle">
															<i id="icons1" class="menu-icon fa fa-shopping-cart"></i>
															<span class="menu-text">
																Purchase
															</span>

															<b class="arrow fa fa-angle-down"></b>
														</a>

														<b class="arrow"></b>

														<ul class="submenu">

														<?php  } ?>

														<li class="">
															<a href="<?php echo SURL . $result[$i]['linkname']; ?>">
																<i class="menu-icon fa fa-caret-right"></i>
																<?php echo $result[$i]['pagename']; ?>
															</a>

															<b class="arrow"></b>
														</li>
													<?php  } ?>
												<?php  }

												?>
												<?php if ($flag == 1) { ?>

														</ul>
													</li>
												<?php } ?>
												<?php $flag = 0;
												$new_jj = 0;
												for ($i = 0; $i < count($result); $i++) {

													if ($result[$i]['pageid'] > 600 && $result[$i]['pageid'] < 701) {
														$flag = 1; ?>


														<?php if ($new_jj++ == 0) { ?>

															<li class="">

																<a href="#" class="dropdown-toggle">
																	<i id="icons1" class="menu-icon fa fa-shopping-cart"></i>
																	<span class="menu-text">
																		Sale
																	</span>

																	<b class="arrow fa fa-angle-down"></b>
																</a>

																<b class="arrow"></b>

																<ul class="submenu">

																<?php  } ?>

																<li class="">
																	<a href="<?php echo SURL . $result[$i]['linkname']; ?>">
																		<i class="menu-icon fa fa-caret-right"></i>
																		<?php echo $result[$i]['pagename']; ?>
																	</a>

																	<b class="arrow"></b>
																</li>
															<?php  } ?>
														<?php  }

														?>
														<?php if ($flag == 1) { ?>

																</ul>
															</li>
														<?php } ?>
														<?php $flag = 0;
														$new_jj = 0;
														for ($i = 0; $i < count($result); $i++) {

															if ($result[$i]['pageid'] > 700 && $result[$i]['pageid'] < 801) {
																$flag = 1; ?>


																<?php if ($new_jj++ == 0) { ?>

																	<li class="">

																		<a href="#" class="dropdown-toggle">
																			<i id="icons1" class="menu-icon fa fa-exchange"></i>
																			<span class="menu-text">
																				Swap Cylinder
																			</span>

																			<b class="arrow fa fa-angle-down"></b>
																		</a>

																		<b class="arrow"></b>

																		<ul class="submenu">

																		<?php  } ?>

																		<li class="">
																			<a href="<?php echo SURL . $result[$i]['linkname']; ?>">
																				<i class="menu-icon fa fa-caret-right"></i>
																				<?php echo $result[$i]['pagename']; ?>
																			</a>

																			<b class="arrow"></b>
																		</li>
																	<?php  } ?>
																<?php  }

																?>
																<?php if ($flag == 1) { ?>

																		</ul>
																	</li>
																<?php } ?>


																<?php $flag = 0;
																$new_jj = 0;
																for ($i = 0; $i < count($result); $i++) {

																	if ($result[$i]['pageid'] > 200 && $result[$i]['pageid'] < 301) {
																		$flag = 1; ?>

																		<?php if ($new_jj++ == 0) { ?>





																			<li class="">

																				<a href="#" class="dropdown-toggle">
																					<i id="icons4" class="menu-icon fa fa-undo"></i>
																					<span class="menu-text">
																						Return
																					</span>

																					<b class="arrow fa fa-angle-down"></b>
																				</a>

																				<b class="arrow"></b>

																				<ul class="submenu">
																				<?php } ?>
																				<li class="">
																					<a href="<?php echo SURL . $result[$i]['linkname']; ?>">
																						<i class="menu-icon fa fa-caret-right"></i>
																						<?php echo $result[$i]['pagename']; ?>
																					</a>

																					<b class="arrow"></b>
																				</li>
																			<?php  } ?>
																		<?php  } ?>


																		<?php if ($flag == 1) { ?>
																				</ul>
																			</li>
																		<?php  }  ?>


																		<?php $flag = 0;
																		$new_jj = 0;
																		for ($i = 0; $i < count($result); $i++) {

																			if ($result[$i]['pageid'] > 300 && $result[$i]['pageid'] < 401) {
																				$flag = 1; ?>

																				<?php if ($new_jj++ == 0) { ?>


																					<li class="">

																						<a href="#" class="dropdown-toggle">

																							<i id="icons2" class="menu-icon glyphicon glyphicon-euro"></i>

																							<span class="menu-text">
																								Accounts
																							</span>

																							<b class="arrow fa fa-angle-down"></b>
																						</a>

																						<b class="arrow"></b>

																						<ul class="submenu">

																						<?php }

																					if ($result[$i]['pageid'] == 303) continue;
																						?>

																						<li class="">
																							<a href="<?php echo SURL . $result[$i]['linkname']; ?>">
																								<i class="menu-icon fa fa-caret-right"></i>
																								<?php echo $result[$i]['pagename']; ?>
																							</a>

																							<b class="arrow"></b>
																						</li>
																					<?php  } ?>
																				<?php  } ?>

																				<?php if ($flag == 1) { ?>
																						</ul>
																					</li>
																				<?php  }  ?>

																				<?php $flag = 0;
																				$new_jj = 0;
																				for ($i = 0; $i < count($result); $i++) {

																					if ($result[$i]['pageid'] > 400 && $result[$i]['pageid'] < 501) {
																						$flag = 1; ?>

																						<?php if ($new_jj++ == 0) { ?>


																							<li class="">

																								<a href="#" class="dropdown-toggle">

																									<i id="icons2" class="menu-icon glyphicon glyphicon-euro"></i>

																									<span class="menu-text">
																										Reports
																									</span>

																							<b class="arrow fa fa-angle-down"></b>
																								</a>

																								<b class="arrow"></b>

																								<ul class="submenu">

																								<?php } ?>

																								<li class="">
																									<a href="<?php echo SURL . $result[$i]['linkname']; ?>">
																										<i class="menu-icon fa fa-caret-right"></i>

																										<?php
																										if ($result[$i]['pageid'] == 407) {

																											if (!empty($data['bank_right'])) {
																												echo $result[$i]['pagename'];
																											} else {
																												echo 'Vendor Ledger';
																											}
																										} else {
																											echo $result[$i]['pagename'];
																										} ?>

																									</a>

																									<b class="arrow"></b>
																								</li>


																							<?php  } ?>
																						<?php  } ?>

																						<?php if ($flag == 1) { ?>
																								</ul>
																							</li>
																						<?php  }  ?>


																						<li class="">
																							<?php for ($i = 0; $i < count($result); $i++) {

																								if ($result[$i]['pageid'] > 500 && $result[$i]['pageid'] < 601) { ?>

																									<a href="<?php echo SURL . $result[$i]['linkname']; ?>">
																										<i id="icons2" class="menu-icon fa fa-database"></i>
																										<?php echo $result[$i]['pagename']; ?>
																									</a>

																									<b class="arrow"></b>

																								<?php  } ?>
																							<?php  } ?>

																						</li>




					</ul><!-- /.nav-list -->

					<div class="sidebar-toggle sidebar-collapse" id="sidebar-collapse">
						<i id="sidebar-toggle-icon" class="ace-icon fa fa-angle-double-left ace-save-state" data-icon1="ace-icon fa fa-angle-double-left" data-icon2="ace-icon fa fa-angle-double-right"></i>
					</div>
			</div>