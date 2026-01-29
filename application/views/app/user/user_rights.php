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
							<a href="<?php echo SURL . "admin"; ?>">Home</a>
						</li>

						<li>
							<a href="<?php echo SURL . "app/user"; ?>">User Access </a>
						</li>
						<li class="active">Add Access Right </li>
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
								Add Access
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
							} ?>

							<form id="formID" class="form-horizontal" role="form" method="post" action="<?php echo SURL . "app/user" ?>" enctype="multipart/form-data">
								<style>
									.modules {
										background-color: #3498db;
										color: #fff;
										font-weight: bold;
										border-radius: 10px;
										box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
									}

									.modules:hover {
										background-color: #2980b9;
									}
								</style>
								<br>






								<div class="row">
									<div class="col-sm-12 modules" align="center">
										<h3>App &nbsp; </h3>
									</div>
								</div><br>
								<div class="row" class="row" style="background:#dadada;">

									<div class="col-sm-4">
										<div class="col-sm-8">
											<h5 style="margin-left: 8px;margin-top: -1px;color: #2679B5;">Configuration</h5>
										</div>
										<div class="col-sm-2">

											<input onclick="select_all(this.id)" style="margin-left: -43px;margin-top: 2px;" type="checkbox" name="app_config" id="app_config">
										</div>

									</div>
								</div>

								<div class="form-group">

									<?php for ($i = 0; $i < count($app_config); $i++) { ?>
										<?php $addid = 0;
										$editid = 0;
										$deleteid = 0;
										$printid = 0;
										$viewid = 0;
										$allaid = 0 ?>

										<div class="col-sm-4">
											<input id="<?php echo $app_config[$i]['pageid']; ?>" onclick="update_role(<?php echo $app_config[$i]['pageid']; ?>)" <?php if (in_array($app_config[$i]['pageid'], $user_rights)) {
																																										echo 'checked';
																																									} ?> value="<?php echo $app_config[$i]['pageid'] ?>" type="checkbox" name="checkbox[]" placeholder="Page Name" class="col-xs-4 col-sm-2 page_checkbox app_config" title="Only Letters Allowed" />
											<?php echo $app_config[$i]['pagename'] ?>

										</div>

										&nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp;
										<?php foreach ($rights as $key => $value) {
											if ($app_config[$i]['pageid'] == $value['pageid']) {
												if ($value['add'] == 1) {
													$addid = 1;
												}
											}
										} ?>

										<input type="checkbox" id="add_<?php echo $app_config[$i]['pageid']; ?>" class="all_<?php echo $app_config[$i]['pageid']; ?>" onclick="update_role(<?php echo $app_config[$i]['pageid']; ?>)" <?php if ($addid == 1) {
																																																											echo 'checked';
																																																										} ?> value="<?php echo $app_config[$i]['add'] ?>"> Add &nbsp; &nbsp;

										<?php foreach ($rights as $key => $value) {
											if ($app_config[$i]['pageid'] == $value['pageid']) {
												if ($value['edit'] == 1) {
													$editid = 1;
												}
											}
										} ?>
										<input type="checkbox" id="edit_<?php echo $app_config[$i]['pageid']; ?>" class="all_<?php echo $app_config[$i]['pageid']; ?>" onclick="update_role(<?php echo $app_config[$i]['pageid']; ?>)" <?php if ($editid == 1) {
																																																											echo 'checked';
																																																										} ?> value="<?php echo $app_config[$i]['edit'] ?>"> Edit &nbsp; &nbsp;

										<?php foreach ($rights as $key => $value) {
											if ($app_config[$i]['pageid'] == $value['pageid']) {
												if ($value['delete'] == 1) {
													$deleteid = 1;
												}
											}
										} ?>
										<input type="checkbox" id="del_<?php echo $app_config[$i]['pageid']; ?>" class="all_<?php echo $app_config[$i]['pageid']; ?>" onclick="update_role(<?php echo $app_config[$i]['pageid']; ?>)" <?php if ($deleteid == 1) {
																																																											echo 'checked';
																																																										} ?> value="<?php echo $app_config[$i]['delete'] ?>"> Delete &nbsp; &nbsp;

										<?php foreach ($rights as $key => $value) {
											if ($app_config[$i]['pageid'] == $value['pageid']) {
												if ($value['add'] == 1 && $value['edit'] == 1 && $value['delete'] == 1 && $value['print'] && $value['view']) {
													$allaid = 1;
												}
											}
										} ?>
										<input type="checkbox" id="alli_<?php echo $app_config[$i]['pageid']; ?>" onclick="all_role(<?php echo $app_config[$i]['pageid']; ?>)" <?php if ($allaid == 1) {
																																													echo 'checked';
																																												} ?> value="<?php echo $app_config[$i]['pageid'] ?>"> All

										<br>
									<?php } ?>
								</div>

								<div class="row" class="row" style="background:#dadada;">

									<div class="col-sm-4">
										<div class="col-sm-8">
											<h5 style="margin-left: 8px;margin-top: -1px;color: #2679B5;">Order Management</h5>
										</div>
										<div class="col-sm-2">

											<input onclick="select_all(this.id)" style="margin-left: -43px;margin-top: 2px;" type="checkbox" name="app_order_management" id="app_order_management">
										</div>

									</div>
								</div>

								<div class="form-group">

									<?php foreach ($app_order_management as $value) { ?>
										<?php
										$addid = 0;
										$editid = 0;
										$deleteid = 0;
										$printid = 0;
										$viewid = 0;
										$allaid = 0;
										?>

										<div class="col-sm-4">
											<input id="<?php echo $value['pageid']; ?>" onclick="update_role(<?php echo $value['pageid']; ?>)" <?php if (in_array($value['pageid'], $user_rights)) {
																																					echo 'checked';
																																				} ?> value="<?php echo $value['pageid'] ?>" type="checkbox" name="checkbox[]" placeholder="Page Name" class="col-xs-4 col-sm-2 page_checkbox app_order_management" title="Only Letters Allowed" />
											<?php echo $value['pagename'] ?>
										</div>

										&nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp;

										<?php foreach ($rights as $key => $right) {
											if ($value['pageid'] == $right['pageid']) {
												if ($right['add'] == 1) {
													$addid = 1;
												}
											}
										} ?>

										<input type="checkbox" id="add_<?php echo $value['pageid']; ?>" class="all_<?php echo $value['pageid']; ?>" onclick="update_role(<?php echo $value['pageid']; ?>)" <?php if ($addid == 1) {
																																																				echo 'checked';
																																																			} ?> value="<?php echo $value['add'] ?>"> Add &nbsp; &nbsp;

										<?php foreach ($rights as $key => $right) {
											if ($value['pageid'] == $right['pageid']) {
												if ($right['edit'] == 1) {
													$editid = 1;
												}
											}
										} ?>
										<input type="checkbox" id="edit_<?php echo $value['pageid']; ?>" class="all_<?php echo $value['pageid']; ?>" onclick="update_role(<?php echo $value['pageid']; ?>)" <?php if ($editid == 1) {
																																																				echo 'checked';
																																																			} ?> value="<?php echo $value['edit'] ?>"> Edit &nbsp; &nbsp;

										<?php foreach ($rights as $key => $right) {
											if ($value['pageid'] == $right['pageid']) {
												if ($right['delete'] == 1) {
													$deleteid = 1;
												}
											}
										} ?>
										<input type="checkbox" id="del_<?php echo $value['pageid']; ?>" class="all_<?php echo $value['pageid']; ?>" onclick="update_role(<?php echo $value['pageid']; ?>)" <?php if ($deleteid == 1) {
																																																				echo 'checked';
																																																			} ?> value="<?php echo $value['delete'] ?>"> Delete &nbsp; &nbsp;

										<?php foreach ($rights as $key => $right) {
											if ($value['pageid'] == $right['pageid']) {
												if ($right['add'] == 1 && $right['edit'] == 1 && $right['delete'] == 1 && $right['print'] == 1 && $right['view'] == 1) {
													$allaid = 1;
												}
											}
										} ?>
										<input type="checkbox" id="alli_<?php echo $value['pageid']; ?>" onclick="all_role(<?php echo $value['pageid']; ?>)" <?php if ($allaid == 1) {
																																									echo 'checked';
																																								} ?> value="<?php echo $value['pageid'] ?>"> All

										<br>
									<?php } ?>
								</div>

								<div class="row" class="row" style="background:#dadada;">

									<div class="col-sm-4">
										<div class="col-sm-8">
											<h5 style="margin-left: 8px;margin-top: -1px;color: #2679B5;">Notifications Management</h5>
										</div>
										<div class="col-sm-2">

											<input onclick="select_all(this.id)" style="margin-left: -43px;margin-top: 2px;" type="checkbox" name="app_notific_management" id="app_notific_management">
										</div>

									</div>
								</div>

								<div class="form-group">

									<?php foreach ($app_notific_management as $value) { ?>
										<?php
										$addid = 0;
										$editid = 0;
										$deleteid = 0;
										$printid = 0;
										$viewid = 0;
										$allaid = 0;
										?>

										<div class="col-sm-4">
											<input id="<?php echo $value['pageid']; ?>" onclick="update_role(<?php echo $value['pageid']; ?>)" <?php if (in_array($value['pageid'], $user_rights)) {
																																					echo 'checked';
																																				} ?> value="<?php echo $value['pageid'] ?>" type="checkbox" name="checkbox[]" placeholder="Page Name" class="col-xs-4 col-sm-2 page_checkbox app_notific_management" title="Only Letters Allowed" />
											<?php echo $value['pagename'] ?>
										</div>

										&nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp;

										<?php foreach ($rights as $key => $right) {
											if ($value['pageid'] == $right['pageid']) {
												if ($right['add'] == 1) {
													$addid = 1;
												}
											}
										} ?>

										<input type="checkbox" id="add_<?php echo $value['pageid']; ?>" class="all_<?php echo $value['pageid']; ?>" onclick="update_role(<?php echo $value['pageid']; ?>)" <?php if ($addid == 1) {
																																																				echo 'checked';
																																																			} ?> value="<?php echo $value['add'] ?>"> Add &nbsp; &nbsp;

										<?php foreach ($rights as $key => $right) {
											if ($value['pageid'] == $right['pageid']) {
												if ($right['edit'] == 1) {
													$editid = 1;
												}
											}
										} ?>
										<input type="checkbox" id="edit_<?php echo $value['pageid']; ?>" class="all_<?php echo $value['pageid']; ?>" onclick="update_role(<?php echo $value['pageid']; ?>)" <?php if ($editid == 1) {
																																																				echo 'checked';
																																																			} ?> value="<?php echo $value['edit'] ?>"> Edit &nbsp; &nbsp;

										<?php foreach ($rights as $key => $right) {
											if ($value['pageid'] == $right['pageid']) {
												if ($right['delete'] == 1) {
													$deleteid = 1;
												}
											}
										} ?>
										<input type="checkbox" id="del_<?php echo $value['pageid']; ?>" class="all_<?php echo $value['pageid']; ?>" onclick="update_role(<?php echo $value['pageid']; ?>)" <?php if ($deleteid == 1) {
																																																				echo 'checked';
																																																			} ?> value="<?php echo $value['delete'] ?>"> Delete &nbsp; &nbsp;

										<?php foreach ($rights as $key => $right) {
											if ($value['pageid'] == $right['pageid']) {
												if ($right['add'] == 1 && $right['edit'] == 1 && $right['delete'] == 1 && $right['print'] == 1 && $right['view'] == 1) {
													$allaid = 1;
												}
											}
										} ?>
										<input type="checkbox" id="alli_<?php echo $value['pageid']; ?>" onclick="all_role(<?php echo $value['pageid']; ?>)" <?php if ($allaid == 1) {
																																									echo 'checked';
																																								} ?> value="<?php echo $value['pageid'] ?>"> All

										<br>
									<?php } ?>
								</div>

								<div class="row" class="row" style="background:#dadada;">

									<div class="col-sm-4">
										<div class="col-sm-8">
											<h5 style="margin-left: 8px;margin-top: -1px;color: #2679B5;">Feedback</h5>
										</div>
										<div class="col-sm-2">

											<input onclick="select_all(this.id)" style="margin-left: -43px;margin-top: 2px;" type="checkbox" name="feedback" id="feedback">
										</div>

									</div>
								</div>

								<div class="form-group">

									<?php foreach ($feedback as $value) { ?>
										<?php
										$addid = 0;
										$editid = 0;
										$deleteid = 0;
										$printid = 0;
										$viewid = 0;
										$allaid = 0;
										?>

										<div class="col-sm-4">
											<input id="<?php echo $value['pageid']; ?>" onclick="update_role(<?php echo $value['pageid']; ?>)" <?php if (in_array($value['pageid'], $user_rights)) {
																																					echo 'checked';
																																				} ?> value="<?php echo $value['pageid'] ?>" type="checkbox" name="checkbox[]" placeholder="Page Name" class="col-xs-4 col-sm-2 page_checkbox feedback" title="Only Letters Allowed" />
											<?php echo $value['pagename'] ?>
										</div>

										&nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp;

										<?php foreach ($rights as $key => $right) {
											if ($value['pageid'] == $right['pageid']) {
												if ($right['add'] == 1) {
													$addid = 1;
												}
											}
										} ?>

										<input type="checkbox" id="add_<?php echo $value['pageid']; ?>" class="all_<?php echo $value['pageid']; ?>" onclick="update_role(<?php echo $value['pageid']; ?>)" <?php if ($addid == 1) {
																																																				echo 'checked';
																																																			} ?> value="<?php echo $value['add'] ?>"> Add &nbsp; &nbsp;

										<?php foreach ($rights as $key => $right) {
											if ($value['pageid'] == $right['pageid']) {
												if ($right['edit'] == 1) {
													$editid = 1;
												}
											}
										} ?>
										<input type="checkbox" id="edit_<?php echo $value['pageid']; ?>" class="all_<?php echo $value['pageid']; ?>" onclick="update_role(<?php echo $value['pageid']; ?>)" <?php if ($editid == 1) {
																																																				echo 'checked';
																																																			} ?> value="<?php echo $value['edit'] ?>"> Edit &nbsp; &nbsp;

										<?php foreach ($rights as $key => $right) {
											if ($value['pageid'] == $right['pageid']) {
												if ($right['delete'] == 1) {
													$deleteid = 1;
												}
											}
										} ?>
										<input type="checkbox" id="del_<?php echo $value['pageid']; ?>" class="all_<?php echo $value['pageid']; ?>" onclick="update_role(<?php echo $value['pageid']; ?>)" <?php if ($deleteid == 1) {
																																																				echo 'checked';
																																																			} ?> value="<?php echo $value['delete'] ?>"> Delete &nbsp; &nbsp;

										<?php foreach ($rights as $key => $right) {
											if ($value['pageid'] == $right['pageid']) {
												if ($right['add'] == 1 && $right['edit'] == 1 && $right['delete'] == 1 && $right['print'] == 1 && $right['view'] == 1) {
													$allaid = 1;
												}
											}
										} ?>
										<input type="checkbox" id="alli_<?php echo $value['pageid']; ?>" onclick="all_role(<?php echo $value['pageid']; ?>)" <?php if ($allaid == 1) {
																																									echo 'checked';
																																								} ?> value="<?php echo $value['pageid'] ?>"> All

										<br>
									<?php } ?>
								</div>

								<div class="row" class="row" style="background:#dadada;">

									<div class="col-sm-4">
										<div class="col-sm-8">
											<h5 style="margin-left: 8px;margin-top: -1px;color: #2679B5;">Customer</h5>
										</div>
										<div class="col-sm-2">

											<input onclick="select_all(this.id)" style="margin-left: -43px;margin-top: 2px;" type="checkbox" name="app_customer_management" id="app_customer_management">
										</div>

									</div>
								</div>

								<div class="form-group">

									<?php foreach ($app_customer_management as $value) { ?>
										<?php
										$addid = 0;
										$editid = 0;
										$deleteid = 0;
										$printid = 0;
										$viewid = 0;
										$allaid = 0;
										?>

										<div class="col-sm-4">
											<input id="<?php echo $value['pageid']; ?>" onclick="update_role(<?php echo $value['pageid']; ?>)" <?php if (in_array($value['pageid'], $user_rights)) {
																																					echo 'checked';
																																				} ?> value="<?php echo $value['pageid'] ?>" type="checkbox" name="checkbox[]" placeholder="Page Name" class="col-xs-4 col-sm-2 page_checkbox app_customer_management" title="Only Letters Allowed" />
											<?php echo $value['pagename'] ?>
										</div>

										&nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp;

										<?php foreach ($rights as $key => $right) {
											if ($value['pageid'] == $right['pageid']) {
												if ($right['add'] == 1) {
													$addid = 1;
												}
											}
										} ?>

										<input type="checkbox" id="add_<?php echo $value['pageid']; ?>" class="all_<?php echo $value['pageid']; ?>" onclick="update_role(<?php echo $value['pageid']; ?>)" <?php if ($addid == 1) {
																																																				echo 'checked';
																																																			} ?> value="<?php echo $value['add'] ?>"> Add &nbsp; &nbsp;

										<?php foreach ($rights as $key => $right) {
											if ($value['pageid'] == $right['pageid']) {
												if ($right['edit'] == 1) {
													$editid = 1;
												}
											}
										} ?>
										<input type="checkbox" id="edit_<?php echo $value['pageid']; ?>" class="all_<?php echo $value['pageid']; ?>" onclick="update_role(<?php echo $value['pageid']; ?>)" <?php if ($editid == 1) {
																																																				echo 'checked';
																																																			} ?> value="<?php echo $value['edit'] ?>"> Edit &nbsp; &nbsp;

										<?php foreach ($rights as $key => $right) {
											if ($value['pageid'] == $right['pageid']) {
												if ($right['delete'] == 1) {
													$deleteid = 1;
												}
											}
										} ?>
										<input type="checkbox" id="del_<?php echo $value['pageid']; ?>" class="all_<?php echo $value['pageid']; ?>" onclick="update_role(<?php echo $value['pageid']; ?>)" <?php if ($deleteid == 1) {
																																																				echo 'checked';
																																																			} ?> value="<?php echo $value['delete'] ?>"> Delete &nbsp; &nbsp;

										<?php foreach ($rights as $key => $right) {
											if ($value['pageid'] == $right['pageid']) {
												if ($right['add'] == 1 && $right['edit'] == 1 && $right['delete'] == 1 && $right['print'] == 1 && $right['view'] == 1) {
													$allaid = 1;
												}
											}
										} ?>
										<input type="checkbox" id="alli_<?php echo $value['pageid']; ?>" onclick="all_role(<?php echo $value['pageid']; ?>)" <?php if ($allaid == 1) {
																																									echo 'checked';
																																								} ?> value="<?php echo $value['pageid'] ?>"> All

										<br>
									<?php } ?>
								</div>
								<div class="row" class="row" style="background:#dadada;">

									<div class="col-sm-4">
										<div class="col-sm-8">
											<h5 style="margin-left: 8px;margin-top: -1px;color: #2679B5;">Reports</h5>
										</div>
										<div class="col-sm-2">

											<input onclick="select_all(this.id)" style="margin-left: -43px;margin-top: 2px;" type="checkbox" name="app_customer_management" id="app_customer_management">
										</div>

									</div>
								</div>
								<div class="form-group">

									<?php foreach ($reports as $value) { ?>
										<?php
										$addid = 0;
										$editid = 0;
										$deleteid = 0;
										$printid = 0;
										$viewid = 0;
										$allaid = 0;
										?>

										<div class="col-sm-4">
											<input id="<?php echo $value['pageid']; ?>" onclick="update_role(<?php echo $value['pageid']; ?>)" <?php if (in_array($value['pageid'], $user_rights)) {
																																					echo 'checked';
																																				} ?> value="<?php echo $value['pageid'] ?>" type="checkbox" name="checkbox[]" placeholder="Page Name" class="col-xs-4 col-sm-2 page_checkbox app_customer_management" title="Only Letters Allowed" />
											<?php echo $value['pagename'] ?>
										</div>

										&nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp;

										<?php foreach ($rights as $key => $right) {
											if ($value['pageid'] == $right['pageid']) {
												if ($right['add'] == 1) {
													$addid = 1;
												}
											}
										} ?>

										<input type="checkbox" id="add_<?php echo $value['pageid']; ?>" class="all_<?php echo $value['pageid']; ?>" onclick="update_role(<?php echo $value['pageid']; ?>)" <?php if ($addid == 1) {
																																																				echo 'checked';
																																																			} ?> value="<?php echo $value['add'] ?>"> Add &nbsp; &nbsp;

										<?php foreach ($rights as $key => $right) {
											if ($value['pageid'] == $right['pageid']) {
												if ($right['edit'] == 1) {
													$editid = 1;
												}
											}
										} ?>
										<input type="checkbox" id="edit_<?php echo $value['pageid']; ?>" class="all_<?php echo $value['pageid']; ?>" onclick="update_role(<?php echo $value['pageid']; ?>)" <?php if ($editid == 1) {
																																																				echo 'checked';
																																																			} ?> value="<?php echo $value['edit'] ?>"> Edit &nbsp; &nbsp;

										<?php foreach ($rights as $key => $right) {
											if ($value['pageid'] == $right['pageid']) {
												if ($right['delete'] == 1) {
													$deleteid = 1;
												}
											}
										} ?>
										<input type="checkbox" id="del_<?php echo $value['pageid']; ?>" class="all_<?php echo $value['pageid']; ?>" onclick="update_role(<?php echo $value['pageid']; ?>)" <?php if ($deleteid == 1) {
																																																				echo 'checked';
																																																			} ?> value="<?php echo $value['delete'] ?>"> Delete &nbsp; &nbsp;

										<?php foreach ($rights as $key => $right) {
											if ($value['pageid'] == $right['pageid']) {
												if ($right['add'] == 1 && $right['edit'] == 1 && $right['delete'] == 1 && $right['print'] == 1 && $right['view'] == 1) {
													$allaid = 1;
												}
											}
										} ?>
										<input type="checkbox" id="alli_<?php echo $value['pageid']; ?>" onclick="all_role(<?php echo $value['pageid']; ?>)" <?php if ($allaid == 1) {
																																									echo 'checked';
																																								} ?> value="<?php echo $value['pageid'] ?>"> All

										<br>
									<?php } ?>
								</div>
								<div class="row" class="row" style="background:#dadada;">

									<div class="col-sm-4">
										<div class="col-sm-8">
											<h5 style="margin-left: 8px;margin-top: -1px;color: #2679B5;">Notification</h5>
										</div>
										<div class="col-sm-2">

											<input onclick="select_all(this.id)" style="margin-left: -43px;margin-top: 2px;" type="checkbox" name="notification_list" id="notification_list">
										</div>

									</div>
								</div>
								<div class="form-group">

									<?php foreach ($notification as $value) { ?>
										<?php
										$addid = 0;
										$editid = 0;
										$deleteid = 0;
										$printid = 0;
										$viewid = 0;
										$allaid = 0;
										?>

										<div class="col-sm-4">
											<input id="<?php echo $value['pageid']; ?>" onclick="update_role(<?php echo $value['pageid']; ?>)" <?php if (in_array($value['pageid'], $user_rights)) {
																																					echo 'checked';
																																				} ?> value="<?php echo $value['pageid'] ?>" type="checkbox" name="checkbox[]" placeholder="Page Name" class="col-xs-4 col-sm-2 page_checkbox notification_list" title="Only Letters Allowed" />
											<?php echo $value['pagename'] ?>
										</div>

										&nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp;

										<?php foreach ($rights as $key => $right) {
											if ($value['pageid'] == $right['pageid']) {
												if ($right['add'] == 1) {
													$addid = 1;
												}
											}
										} ?>

										<input type="checkbox" id="add_<?php echo $value['pageid']; ?>" class="all_<?php echo $value['pageid']; ?>" onclick="update_role(<?php echo $value['pageid']; ?>)" <?php if ($addid == 1) {
																																																				echo 'checked';
																																																			} ?> value="<?php echo $value['add'] ?>"> Add &nbsp; &nbsp;

										<?php foreach ($rights as $key => $right) {
											if ($value['pageid'] == $right['pageid']) {
												if ($right['edit'] == 1) {
													$editid = 1;
												}
											}
										} ?>
										<input type="checkbox" id="edit_<?php echo $value['pageid']; ?>" class="all_<?php echo $value['pageid']; ?>" onclick="update_role(<?php echo $value['pageid']; ?>)" <?php if ($editid == 1) {
																																																				echo 'checked';
																																																			} ?> value="<?php echo $value['edit'] ?>"> Edit &nbsp; &nbsp;

										<?php foreach ($rights as $key => $right) {
											if ($value['pageid'] == $right['pageid']) {
												if ($right['delete'] == 1) {
													$deleteid = 1;
												}
											}
										} ?>
										<input type="checkbox" id="del_<?php echo $value['pageid']; ?>" class="all_<?php echo $value['pageid']; ?>" onclick="update_role(<?php echo $value['pageid']; ?>)" <?php if ($deleteid == 1) {
																																																				echo 'checked';
																																																			} ?> value="<?php echo $value['delete'] ?>"> Delete &nbsp; &nbsp;

										<?php foreach ($rights as $key => $right) {
											if ($value['pageid'] == $right['pageid']) {
												if ($right['add'] == 1 && $right['edit'] == 1 && $right['delete'] == 1 && $right['print'] == 1 && $right['view'] == 1) {
													$allaid = 1;
												}
											}
										} ?>
										<input type="checkbox" id="alli_<?php echo $value['pageid']; ?>" onclick="all_role(<?php echo $value['pageid']; ?>)" <?php if ($allaid == 1) {
																																									echo 'checked';
																																								} ?> value="<?php echo $value['pageid'] ?>"> All

										<br>
									<?php } ?>
								</div>

								<div class="row" style="background:#dadada;">

									<div class="col-sm-4">
										<div class="col-sm-8">
											<h5 style="margin-left: 8px;margin-top: -1px;color: #2679B5;">Miscellaneous Menu &nbsp; </h5>
										</div>
										<div class="col-sm-2">

											<input onclick="select_all(this.id)" style="margin-left: -43px;margin-top: 2px;" type="checkbox" name="misc_list" id="misc_list">
										</div>

									</div>
								</div>


								<div class="form-group">

									<?php for ($i = 0; $i < count($misc_list); $i++) { ?>
										<?php $addid = 0;
										$editid = 0;
										$deleteid = 0;
										$printid = 0;
										$viewid = 0;
										$allaid = 0 ?>

										<div class="col-sm-4">
											<input id="<?php echo $misc_list[$i]['pageid']; ?>" onclick="update_role(<?php echo $misc_list[$i]['pageid']; ?>)" <?php if (in_array($misc_list[$i]['pageid'], $user_rights)) {
																																									echo 'checked';
																																								} ?> value="<?php echo $misc_list[$i]['pageid'] ?>" type="checkbox" name="checkbox[]" placeholder="Page Name" class="col-xs-4 col-sm-2 page_checkbox misc_list" title="Only Letters Allowed" />
											<?php echo $misc_list[$i]['pagename'] ?>

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
		function all_role(pageid) {
			if ($('#alli_' + pageid).is(':checked')) {
				$('.all_' + pageid).prop('checked', true);
				if ($('#' + pageid).is(':checked')) {
					//alert("1");
					var status = 1;
				} else {
					var status = 0;
				}

				if ($('#edit_' + pageid).is(':checked')) {
					//alert("1");

					var edit = 1;
				} else {
					//alert("0");

					var edit = 0;
				}
				if ($('#del_' + pageid).is(':checked')) {
					//alert("1");

					var del = 1;
				} else {
					//alert("0");

					var del = 0;
				}
				if ($('#add_' + pageid).is(':checked')) {
					//alert("1");

					var add = 1;
				} else {
					//alert("0");

					var add = 0;
				}
				if ($('#print_' + pageid).is(':checked')) {
					//alert("1");

					var print = 1;
				} else {
					//alert("0");

					var print = 0;
				}
				if ($('#view_' + pageid).is(':checked')) {
					//alert("1");

					var view = 1;
				} else {
					//alert("0");

					var view = 0;
				}

				var uid = $('#uid').val();

				var request = $.ajax({
					url: "<?php echo SURL . "app/user/update_role/" ?>" + pageid,
					type: "POST",
					data: {
						pageid: pageid,
						uid: uid,
						status: status,
						edit: edit,
						add: add,
						print: print,
						view: view,
						del: del
					},
					dataType: "html"
				});
				request.done(function(msg) {
					//$('#tbody_id').html(msg);
				});
				request.fail(function(jqXHR, textStatus) {
					alert("Request failed: " + textStatus);
				});
			} else {
				$('.all_' + pageid).prop('checked', false);
				if ($('#' + pageid).is(':checked')) {
					//alert("1");
					var status = 1;
				} else {
					var status = 0;
				}

				if ($('#edit_' + pageid).is(':checked')) {
					//alert("1");

					var edit = 1;
				} else {
					//alert("0");

					var edit = 0;
				}
				if ($('#del_' + pageid).is(':checked')) {
					//alert("1");

					var del = 1;
				} else {
					//alert("0");

					var del = 0;
				}
				if ($('#add_' + pageid).is(':checked')) {
					//alert("1");

					var add = 1;
				} else {
					//alert("0");

					var add = 0;
				}
				if ($('#print_' + pageid).is(':checked')) {
					//alert("1");

					var print = 1;
				} else {
					//alert("0");

					var print = 0;
				}
				if ($('#view_' + pageid).is(':checked')) {
					//alert("1");

					var view = 1;
				} else {
					//alert("0");

					var view = 0;
				}

				var uid = $('#uid').val();

				var request = $.ajax({
					url: "<?php echo SURL . "app/user/update_role/" ?>" + pageid,
					type: "POST",
					data: {
						pageid: pageid,
						uid: uid,
						status: status,
						edit: edit,
						add: add,
						print: print,
						view: view,
						del: del
					},
					dataType: "html"
				});
				request.done(function(msg) {
					//$('#tbody_id').html(msg);
				});
				request.fail(function(jqXHR, textStatus) {
					alert("Request failed: " + textStatus);
				});
			}
		}

		function select_all(pageid) {

			if ($('#' + pageid).is(':checked')) {
				var status = 1;
			} else {
				var status = 0;
			}

			var uid = $('#uid').val();

			$('.' + pageid).each(function(index) {


				var page_id = $(this).val();

				if (status == 1) {
					$(this).prop('checked', true);
				} else {
					$(this).prop('checked', false);
				}


				var request = $.ajax({
					url: "<?php echo SURL . "app/user/update_role/" ?>" + page_id,
					type: "POST",
					data: {
						pageid: page_id,
						uid: uid,
						status: status
					},
					dataType: "html"
				});
				request.done(function(msg) {
					//$('#tbody_id').html(msg);
				});
				request.fail(function(jqXHR, textStatus) {
					alert("Request failed: " + textStatus);
				});

			});

		}

		function update_role(pageid) {
			//alert("1");
			if ($('#' + pageid).is(':checked')) {
				//alert("1");
				var status = 1;
			} else {
				var status = 0;
			}

			if ($('#edit_' + pageid).is(':checked')) {
				//alert("1");

				var edit = 1;
			} else {
				//alert("0");

				var edit = 0;
			}
			if ($('#del_' + pageid).is(':checked')) {
				//alert("1");

				var del = 1;
			} else {
				//alert("0");

				var del = 0;
			}
			if ($('#add_' + pageid).is(':checked')) {
				//alert("1");

				var add = 1;
			} else {
				//alert("0");

				var add = 0;
			}
			if ($('#print_' + pageid).is(':checked')) {
				//alert("1");

				var print = 1;
			} else {
				//alert("0");

				var print = 0;
			}
			if ($('#view_' + pageid).is(':checked')) {
				//alert("1");

				var view = 1;
			} else {
				//alert("0");

				var view = 0;
			}

			var uid = $('#uid').val();

			var request = $.ajax({
				url: "<?php echo SURL . "app/user/update_role/" ?>" + pageid,
				type: "POST",
				data: {
					pageid: pageid,
					uid: uid,
					status: status,
					edit: edit,
					add: add,
					print: print,
					view: view,
					del: del
				},
				dataType: "html"
			});
			request.done(function(msg) {
				//$('#tbody_id').html(msg);
			});
			request.fail(function(jqXHR, textStatus) {
				alert("Request failed: " + textStatus);
			});

		}
	</script>
</body>

</html>