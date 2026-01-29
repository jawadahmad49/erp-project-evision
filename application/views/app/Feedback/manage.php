<!DOCTYPE html>
<html lang="en">
<?php
$this->load->view('app/include/head');
$this->load->view('app/include/header');

//include("restaurant_ajax.php");

?>

<body class="no-skin">
	<style>
		.feedback {
			--normal: #ECEAF3;
			--normal-shadow: #D9D8E3;
			--normal-mouth: #9795A4;
			--normal-eye: #595861;
			--active: #F8DA69;
			--active-shadow: #F4B555;
			--active-mouth: #F05136;
			--active-eye: #313036;
			--active-tear: #76b5e7;
			--active-shadow-angry: #e94f1d;
			margin: 0;
			padding: 0;
			list-style: none;
			display: flex;

			li {
				position: relative;
				border-radius: 50%;
				background: var(--sb, var(--normal));
				box-shadow: inset 3px -3px 4px var(--sh, var(--normal-shadow));
				transition: background .4s, box-shadow .4s, transform .3s;
				-webkit-tap-highlight-color: transparent;

				&:not(:last-child) {
					margin-right: 20px;
				}

				div {
					width: 40px;
					height: 40px;
					position: relative;
					transform: perspective(240px) translateZ(4px);

					svg,
					&:before,
					&:after {
						display: block;
						position: absolute;
						left: var(--l, 9px);
						top: var(--t, 13px);
						width: var(--w, 8px);
						height: var(--h, 2px);
						transform: rotate(var(--r, 0deg)) scale(var(--sc, 1)) translateZ(0);
					}

					svg {
						fill: none;
						stroke: var(--s);
						stroke-width: 2px;
						stroke-linecap: round;
						stroke-linejoin: round;
						transition: stroke .4s;

						&.eye {
							--s: var(--e, var(--normal-eye));
							--t: 17px;
							--w: 7px;
							--h: 4px;

							&.right {
								--l: 23px;
							}
						}

						&.mouth {
							--s: var(--m, var(--normal-mouth));
							--l: 11px;
							--t: 23px;
							--w: 18px;
							--h: 7px;
						}
					}

					&:before,
					&:after {
						content: '';
						z-index: var(--zi, 1);
						border-radius: var(--br, 1px);
						background: var(--b, var(--e, var(--normal-eye)));
						transition: background .4s;
					}
				}

				&.angry {
					--step-1-rx: -24deg;
					--step-1-ry: 20deg;
					--step-2-rx: -24deg;
					--step-2-ry: -20deg;

					div {
						&:before {
							--r: 20deg;
						}

						&:after {
							--l: 23px;
							--r: -20deg;
						}

						svg {
							&.eye {
								stroke-dasharray: 4.55;
								stroke-dashoffset: 8.15;
							}
						}
					}

					&.active {
						animation: angry 1s linear;

						div {
							&:before {
								--middle-y: -2px;
								--middle-r: 22deg;
								animation: toggle .8s linear forwards;
							}

							&:after {
								--middle-y: 1px;
								--middle-r: -18deg;
								animation: toggle .8s linear forwards;
							}
						}
					}
				}

				&.sad {
					--step-1-rx: 20deg;
					--step-1-ry: -12deg;
					--step-2-rx: -18deg;
					--step-2-ry: 14deg;

					div {

						&:before,
						&:after {
							--b: var(--active-tear);
							--sc: 0;
							--w: 5px;
							--h: 5px;
							--t: 15px;
							--br: 50%;
						}

						&:after {
							--l: 25px;
						}

						svg {
							&.eye {
								--t: 16px;
							}

							&.mouth {
								--t: 24px;
								stroke-dasharray: 9.5;
								stroke-dashoffset: 33.25;
							}
						}
					}

					&.active {
						div {

							&:before,
							&:after {
								animation: tear .6s linear forwards;
							}
						}
					}
				}

				&.ok {
					--step-1-rx: 4deg;
					--step-1-ry: -22deg;
					--step-1-rz: 6deg;
					--step-2-rx: 4deg;
					--step-2-ry: 22deg;
					--step-2-rz: -6deg;

					div {
						&:before {
							--l: 12px;
							--t: 17px;
							--h: 4px;
							--w: 4px;
							--br: 50%;
							box-shadow: 12px 0 0 var(--e, var(--normal-eye));
						}

						&:after {
							--l: 13px;
							--t: 26px;
							--w: 14px;
							--h: 2px;
							--br: 1px;
							--b: var(--m, var(--normal-mouth));
						}
					}

					&.active {
						div {
							&:before {
								--middle-s-y: .35;
								animation: toggle .2s linear forwards;
							}

							&:after {
								--middle-s-x: .5;
								animation: toggle .7s linear forwards;
							}
						}
					}
				}

				&.good {
					--step-1-rx: -14deg;
					--step-1-rz: 10deg;
					--step-2-rx: 10deg;
					--step-2-rz: -8deg;

					div {
						&:before {
							--b: var(--m, var(--normal-mouth));
							--w: 5px;
							--h: 5px;
							--br: 50%;
							--t: 22px;
							--zi: 0;
							opacity: .5;
							box-shadow: 16px 0 0 var(--b);
							filter: blur(2px);
						}

						&:after {
							--sc: 0;
						}

						svg {
							&.eye {
								--t: 15px;
								--sc: -1;
								stroke-dasharray: 4.55;
								stroke-dashoffset: 8.15;
							}

							&.mouth {
								--t: 22px;
								--sc: -1;
								stroke-dasharray: 13.3;
								stroke-dashoffset: 23.75;
							}
						}
					}

					&.active {
						div {
							svg {
								&.mouth {
									--middle-y: 1px;
									--middle-s: -1;
									animation: toggle .8s linear forwards;
								}
							}
						}
					}
				}

				&.happy {
					div {
						--step-1-rx: 18deg;
						--step-1-ry: 24deg;
						--step-2-rx: 18deg;
						--step-2-ry: -24deg;

						&:before {
							--sc: 0;
						}

						&:after {
							--b: var(--m, var(--normal-mouth));
							--l: 11px;
							--t: 23px;
							--w: 18px;
							--h: 8px;
							--br: 0 0 8px 8px;
						}

						svg {
							&.eye {
								--t: 14px;
								--sc: -1;
							}
						}
					}

					&.active {
						div {
							&:after {
								--middle-s-x: .95;
								--middle-s-y: .75;
								animation: toggle .8s linear forwards;
							}
						}
					}
				}

				&:not(.active) {
					cursor: pointer;

					&:active {
						transform: scale(.925);
					}
				}

				&.active {
					--sb: var(--active);
					--sh: var(--active-shadow);
					--m: var(--active-mouth);
					--e: var(--active-eye);

					div {
						animation: shake .8s linear forwards;
					}
				}
			}
		}

		@keyframes shake {
			30% {
				transform: perspective(240px) rotateX(var(--step-1-rx, 0deg)) rotateY(var(--step-1-ry, 0deg)) rotateZ(var(--step-1-rz, 0deg)) translateZ(10px);
			}

			60% {
				transform: perspective(240px) rotateX(var(--step-2-rx, 0deg)) rotateY(var(--step-2-ry, 0deg)) rotateZ(var(--step-2-rz, 0deg)) translateZ(10px);
			}

			100% {
				transform: perspective(240px) translateZ(4px);
			}
		}

		@keyframes tear {
			0% {
				opacity: 0;
				transform: translateY(-2px) scale(0) translateZ(0);
			}

			50% {
				transform: translateY(12px) scale(.6, 1.2) translateZ(0);
			}

			20%,
			80% {
				opacity: 1;
			}

			100% {
				opacity: 0;
				transform: translateY(24px) translateX(4px) rotateZ(-30deg) scale(.7, 1.1) translateZ(0);
			}
		}

		@keyframes toggle {
			50% {
				transform: translateY(var(--middle-y, 0)) scale(var(--middle-s-x, var(--middle-s, 1)), var(--middle-s-y, var(--middle-s, 1))) rotate(var(--middle-r, 0deg));
			}
		}

		@keyframes angry {
			40% {
				background: var(--active);
			}

			45% {
				box-shadow: inset 3px -3px 4px var(--active-shadow), inset 0 8px 10px var(--active-shadow-angry);
			}
		}

		html {
			box-sizing: border-box;
			-webkit-font-smoothing: antialiased;
		}

		* {
			box-sizing: inherit;

			&:before,
			&:after {
				box-sizing: inherit;
			}
		}

		// Center & dribbble
		body {
			min-height: 100vh;
			display: flex;
			font-family: 'Roboto', Arial;
			justify-content: center;
			align-items: center;
			flex-direction: column;
			background: #F9F9FC;

			.dribbble {
				position: fixed;
				display: block;
				right: 20px;
				bottom: 20px;

				img {
					display: block;
					height: 28px;
				}
			}

			.twitter {
				position: fixed;
				display: block;
				right: 64px;
				bottom: 14px;

				svg {
					width: 32px;
					height: 32px;
					fill: #1da1f2;
				}
			}
		}
	</style>

	<div class="main-container ace-save-state" id="main-container">

		<?php $this->load->view('app/include/sidebar');
		?>

		<div class="main-content">
			<div class="main-content-inner">

				<div class="breadcrumbs ace-save-state" id="breadcrumbs" style="font-weight: bold;">
					<ul class="breadcrumb">
						<li>
							<i class="ace-icon fa fa-home home-icon"></i>
							<a href="<?php echo SURL . "Module/app"; ?>">Home</a>
						</li>

						<li class="active">Manage Feedback <?php if ($arabic_check == 'Yes') { ?>(إدارة العنصر
							)<?php } ?></li>
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

					<!-- <div class="page-header">
							<h1>
								Tables
								<small>
									<i class="ace-icon fa fa-angle-double-right"></i>
									Static &amp; Dynamic Tables
								</small>
							</h1>
						</div> -->
					<!-- /.page-header -->

					<div class="row">
						<div class="col-xs-12">
							<!-- PAGE CONTENT BEGINS -->


							<div class="row">
								<div class="col-xs-12">
									<h3 class="header smaller lighter blue">Manage Feedback </h3>

									<?php
									if ($this->session->flashdata('err_message')) {
									?>

										<div class="alert alert-danger">
											<button type="button" class="close" data-dismiss="alert">
												<i class="ace-icon fa fa-times"></i>
											</button>

											<strong>
												<i class="ace-icon fa fa-times"></i>

											</strong>

											<?php echo $this->session->flashdata('err_message'); ?>
											<br>
										</div>

									<?php
									}
									if ($this->session->flashdata('ok_message')) {
									?>

										<div class="alert alert-block alert-success">
											<button type="button" class="close" data-dismiss="alert">
												<i class="ace-icon fa fa-times"></i>
											</button>

											<p>
												<strong>
													<i class="ace-icon fa fa-check"></i>
													Well done!
												</strong>
												<?php echo $this->session->flashdata('ok_message'); ?>
											</p>
										</div>

									<?php
									}
									?>
									<form role="form" method="post" action="<?php echo SURL . "app/Feedback/filter" ?>" style="background-color:#fafafa;">


										<div class="form-group">
											<label class="col-sm-1 control-label no-padding-right" for="form-field-1" style="padding-top:6px;">From Date</label>
											<div class="col-sm-2">
												<div class="input-group">
													<input name="from" class="form-control date-picker" id="datepicker" type="text" data-date-format="yyyy-mm-dd" data-date-start-date="<?php echo $comp['financial_start_date']; ?>" data-date-end-date="<?php echo $comp['financial_end_date']; ?>" readonly required value="<?php if (empty($from_date)) {
																																																																																	echo date('Y-m-d');
																																																																																} else {
																																																																																	echo $from_date;
																																																																																} ?>">
													<span class="input-group-addon">
														<i class="fa fa-calendar bigger-110"></i>
													</span>
												</div>
											</div>

										</div>
										<div class="form-group">
											<label class="col-sm-1 control-label no-padding-right" for="form-field-1" style="padding-top:6px;">To Date</label>

											<div class="col-sm-2">
												<div class="input-group">
													<input name="to" class="form-control date-picker form_date" id="id-date-picker-1" type="text" data-date-format="yyyy-mm-dd" required="" value="<?php if (empty($to_date)) {
																																																		echo date('Y-m-d');
																																																	} else {
																																																		echo $to_date;
																																																	} ?>">
													<span class="input-group-addon">
														<i class="fa fa-calendar bigger-110"></i>
													</span>
												</div>
											</div>
										</div>
										<button type="submit" value="submit" name="submit" class="btn btn-sm btn-info">
											Search
										</button>

									</form>
									<div class="clearfix" style="margin-top: 10px;">

										<div class="pull-right tableTools-container">
											<style>
												.btn-success:hover {
													background: green !important;
												}

												.btn-success:focus {
													background: green !important;
												}
											</style>
										</div>
									</div>
									<div class="table-header" style="font-weight: bold;">
										Results for "Feedback"
									</div>

									<!-- div.table-responsive -->

									<!-- div.dataTables_borderWrap -->
									<div>
										<table id="dynamic-table" class="table table-striped table-bordered table-hover">
											<thead>
												<tr>
													<th>Sr No </th>
													<th>User Name</th>
													<th>user Mobile No</th>
													<th>Order no</th>
													<th>Rating</th>
													<th>Feedback</th>

												</tr>
											</thead>

											<tbody>

												<?php $count = 0;
												foreach ($item as $key => $value) {
													$count++; ?>
													<tr>
														<td>
															<?php echo $count ?>
														</td>

														<td>
															<?php echo $value['uname'] ?>
														</td>
														<td>
															<?php echo $value['uphone'] ?>
														</td>
														<td>
															<?php echo $value['id'] ?>
														</td>
														<td>
															<div class="form-group">

																<div class="col-sm-6">
																	<ul class="feedback">

																		<li class="happy <?php if ($value['rating'] == '5') { ?> active <?php } ?>">
																			<div>
																				<svg class="eye left">
																					<use xlink:href="#eye">
																				</svg>
																				<svg class="eye right">
																					<use xlink:href="#eye">
																				</svg>
																			</div>
																		</li>
																		<li class="good <?php if ($value['rating'] == '4') { ?> active <?php } ?>">
																			<div>
																				<svg class="eye left">
																					<use xlink:href="#eye">
																				</svg>
																				<svg class="eye right">
																					<use xlink:href="#eye">
																				</svg>
																				<svg class="mouth">
																					<use xlink:href="#mouth">
																				</svg>
																			</div>
																		</li>
																		<li class="ok <?php if ($value['rating'] == '3') { ?> active <?php } ?>">
																			<div></div>
																		</li>
																		<li class="sad <?php if ($value['rating'] == '2') { ?> active <?php } ?>">
																			<div>
																				<svg class="eye left">
																					<use xlink:href="#eye">
																				</svg>
																				<svg class="eye right">
																					<use xlink:href="#eye">
																				</svg>
																				<svg class="mouth">
																					<use xlink:href="#mouth">
																				</svg>
																			</div>
																		</li>
																		<li class="angry <?php if ($value['rating'] == '1') { ?> active <?php } ?>">
																			<div>
																				<svg class="eye left">
																					<use xlink:href="#eye">
																				</svg>
																				<svg class="eye right">
																					<use xlink:href="#eye">
																				</svg>
																				<svg class="mouth">
																					<use xlink:href="#mouth">
																				</svg>
																			</div>
																		</li>
																	</ul>

																	<svg xmlns="http://www.w3.org/2000/svg" style="display: none;">
																		<symbol xmlns="http://www.w3.org/2000/svg" viewBox="0 0 7 4" id="eye">
																			<path d="M1,1 C1.83333333,2.16666667 2.66666667,2.75 3.5,2.75 C4.33333333,2.75 5.16666667,2.16666667 6,1"></path>
																		</symbol>
																		<symbol xmlns="http://www.w3.org/2000/svg" viewBox="0 0 18 7" id="mouth">
																			<path d="M1,5.5 C3.66666667,2.5 6.33333333,1 9,1 C11.6666667,1 14.3333333,2.5 17,5.5"></path>
																		</symbol>
																	</svg>

																	<!-- dribbble - twitter -->
																	<a class="dribbble" href="https://dribbble.com/ai" target="_blank"><img src="https://cdn.dribbble.com/assets/dribbble-ball-mark-2bd45f09c2fb58dbbfb44766d5d1d07c5a12972d602ef8b32204d28fa3dda554.svg" alt=""></a>

																</div>
															</div>

														</td>
														<td>
															<?php echo $value['feedback'] ?>
														</td>
													</tr>
												<?php } ?>

											</tbody>
										</table>
									</div>
								</div>
							</div>



							<!-- PAGE CONTENT ENDS -->
						</div><!-- /.col -->
					</div><!-- /.row -->
				</div><!-- /.page-content -->
			</div>
		</div><!-- /.main-content -->

		<?php

		$this->load->view('app/include/footer');
		?>
	</div>
	<script type="text/javascript">
		if ('ontouchstart' in document.documentElement) document.write("<script src='<?php echo SURL; ?>assets/js/jquery.mobile.custom.min.js'>" + "<" + "/script>");
	</script>
	<script src="<?php echo SURL; ?>assets/js/bootstrap.min.js"></script>

	<!-- page specific plugin scripts -->
	<script src="<?php echo SURL; ?>assets/js/jquery.dataTables.min.js"></script>
	<script src="<?php echo SURL; ?>assets/js/jquery.dataTables.bootstrap.min.js"></script>
	<script src="<?php echo SURL; ?>assets/js/dataTables.buttons.min.js"></script>
	<script src="<?php echo SURL; ?>assets/js/buttons.flash.min.js"></script>
	<script src="<?php echo SURL; ?>assets/js/buttons.html5.min.js"></script>
	<script src="<?php echo SURL; ?>assets/js/buttons.print.min.js"></script>
	<script src="<?php echo SURL; ?>assets/js/buttons.colVis.min.js"></script>
	<script src="<?php echo SURL; ?>assets/js/dataTables.select.min.js"></script>

	<!-- ace scripts -->
	<script src="<?php echo SURL; ?>assets/js/ace-elements.min.js"></script>
	<script src="<?php echo SURL; ?>assets/js/ace.min.js"></script>

	<!-- inline scripts related to this page -->
	<script type="text/javascript">
		jQuery(function($) {
			//initiate dataTables plugin
			var myTable =
				$('#dynamic-table')
				//.wrap("<div class='dataTables_borderWrap' />")   //if you are applying horizontal scrolling (sScrollX)
				.DataTable({
					bAutoWidth: false,
					// "aoColumns": [
					//   { "bSortable": false },
					//   null, null,
					//   { "bSortable": false }
					// ],
					"aaSorting": [],


					//"bProcessing": true,
					//"bServerSide": true,
					//"sAjaxSource": "http://127.0.0.1/table.php"	,

					//,
					//"sScrollY": "200px",
					//"bPaginate": false,

					//"sScrollX": "100%",
					//"sScrollXInner": "120%",
					//"bScrollCollapse": true,
					//Note: if you are applying horizontal scrolling (sScrollX) on a ".table-bordered"
					//you may want to wrap the table inside a "div.dataTables_borderWrap" element

					//"iDisplayLength": 50


					select: {
						style: 'multi'
					}
				});



			$.fn.dataTable.Buttons.defaults.dom.container.className = 'dt-buttons btn-overlap btn-group btn-overlap';

			new $.fn.dataTable.Buttons(myTable, {
				buttons: [{
						"extend": "colvis",
						"text": "<i class='fa fa-search bigger-110 blue'></i> <span class='hidden'>Show/hide columns</span>",
						"className": "btn btn-white btn-primary btn-bold",
						columns: ':not(:first):not(:last)'
					},
					{
						"extend": "copy",
						"text": "<i class='fa fa-copy bigger-110 pink'></i> <span class='hidden'>Copy to clipboard</span>",
						"className": "btn btn-white btn-primary btn-bold"
					},
					{
						"extend": "csv",
						"text": "<i class='fa fa-database bigger-110 orange'></i> <span class='hidden'>Export to CSV</span>",
						"className": "btn btn-white btn-primary btn-bold"
					},
					{
						"extend": "excel",
						"text": "<i class='fa fa-file-excel-o bigger-110 green'></i> <span class='hidden'>Export to Excel</span>",
						"className": "btn btn-white btn-primary btn-bold"
					},
					{
						"extend": "pdf",
						"text": "<i class='fa fa-file-pdf-o bigger-110 red'></i> <span class='hidden'>Export to PDF</span>",
						"className": "btn btn-white btn-primary btn-bold"
					},
					{
						"extend": "print",
						"text": "<i class='fa fa-print bigger-110 grey'></i> <span class='hidden'>Print</span>",
						"className": "btn btn-white btn-primary btn-bold",
						autoPrint: false,
						//message: 'This print was produced using the Print button for DataTables'
						exportOptions: {
							columns: [0, 1, 2]
						}
					}
				]
			});
			myTable.buttons().container().appendTo($('.tableTools-container'));

			//style the message box
			var defaultCopyAction = myTable.button(1).action();
			myTable.button(1).action(function(e, dt, button, config) {
				defaultCopyAction(e, dt, button, config);
				$('.dt-button-info').addClass('gritter-item-wrapper gritter-info gritter-center white');
			});


			var defaultColvisAction = myTable.button(0).action();
			myTable.button(0).action(function(e, dt, button, config) {

				defaultColvisAction(e, dt, button, config);


				if ($('.dt-button-collection > .dropdown-menu').length == 0) {
					$('.dt-button-collection')
						.wrapInner('<ul class="dropdown-menu dropdown-light dropdown-caret dropdown-caret" />')
						.find('a').attr('href', '#').wrap("<li />")
				}
				$('.dt-button-collection').appendTo('.tableTools-container .dt-buttons')
			});

			////

			setTimeout(function() {
				$($('.tableTools-container')).find('a.dt-button').each(function() {
					var div = $(this).find(' > div').first();
					if (div.length == 1) div.tooltip({
						container: 'body',
						title: div.parent().text()
					});
					else $(this).tooltip({
						container: 'body',
						title: $(this).text()
					});
				});
			}, 500);





			myTable.on('select', function(e, dt, type, index) {
				if (type === 'row') {
					$(myTable.row(index).node()).find('input:checkbox').prop('checked', true);
				}
			});
			myTable.on('deselect', function(e, dt, type, index) {
				if (type === 'row') {
					$(myTable.row(index).node()).find('input:checkbox').prop('checked', false);
				}
			});




			/////////////////////////////////
			//table checkboxes
			$('th input[type=checkbox], td input[type=checkbox]').prop('checked', false);

			//select/deselect all rows according to table header checkbox
			$('#dynamic-table > thead > tr > th input[type=checkbox], #dynamic-table_wrapper input[type=checkbox]').eq(0).on('click', function() {
				var th_checked = this.checked; //checkbox inside "TH" table header

				$('#dynamic-table').find('tbody > tr').each(function() {
					var row = this;
					if (th_checked) myTable.row(row).select();
					else myTable.row(row).deselect();
				});
			});

			//select/deselect a row when the checkbox is checked/unchecked
			$('#dynamic-table').on('click', 'td input[type=checkbox]', function() {
				var row = $(this).closest('tr').get(0);
				if (this.checked) myTable.row(row).deselect();
				else myTable.row(row).select();
			});



			$(document).on('click', '#dynamic-table .dropdown-toggle', function(e) {
				e.stopImmediatePropagation();
				e.stopPropagation();
				e.preventDefault();
			});



			//And for the first simple table, which doesn't have TableTools or dataTables
			//select/deselect all rows according to table header checkbox
			var active_class = 'active';
			$('#simple-table > thead > tr > th input[type=checkbox]').eq(0).on('click', function() {
				var th_checked = this.checked; //checkbox inside "TH" table header

				$(this).closest('table').find('tbody > tr').each(function() {
					var row = this;
					if (th_checked) $(row).addClass(active_class).find('input[type=checkbox]').eq(0).prop('checked', true);
					else $(row).removeClass(active_class).find('input[type=checkbox]').eq(0).prop('checked', false);
				});
			});

			//select/deselect a row when the checkbox is checked/unchecked
			$('#simple-table').on('click', 'td input[type=checkbox]', function() {
				var $row = $(this).closest('tr');
				if ($row.is('.detail-row ')) return;
				if (this.checked) $row.addClass(active_class);
				else $row.removeClass(active_class);
			});



			/********************************/
			//add tooltip for small view action buttons in dropdown menu
			$('[data-rel="tooltip"]').tooltip({
				placement: tooltip_placement
			});

			//tooltip placement on right or left
			function tooltip_placement(context, source) {
				var $source = $(source);
				var $parent = $source.closest('table')
				var off1 = $parent.offset();
				var w1 = $parent.width();

				var off2 = $source.offset();
				//var w2 = $source.width();

				if (parseInt(off2.left) < parseInt(off1.left) + parseInt(w1 / 2)) return 'right';
				return 'left';
			}




			/***************/
			$('.show-details-btn').on('click', function(e) {
				e.preventDefault();
				$(this).closest('tr').next().toggleClass('open');
				$(this).find(ace.vars['.icon']).toggleClass('fa-angle-double-down').toggleClass('fa-angle-double-up');
			});
			/***************/





			/**
			//add horizontal scrollbars to a simple table
			$('#simple-table').css({'width':'2000px', 'max-width': 'none'}).wrap('<div style="width: 1000px;" />').parent().ace_scroll(
			  {
				horizontal: true,
				styleClass: 'scroll-top scroll-dark scroll-visible',//show the scrollbars on top(default is bottom)
				size: 2000,
				mouseWheelLock: true
			  }
			).css('padding-top', '12px');
			*/


		})
	</script>
	<script src="<?php echo SURL; ?>assets/js/bootbox.js"></script>
	<script type="text/javascript">
		function confirmDelete(delUrl) {
			bootbox.confirm("Are you sure you want to delete?", function(result) {
				if (result) {
					document.location = delUrl;
				}
			});

		}
	</script>
	<script src="<?php echo SURL ?>assets/js/jquery-2.1.4.min.js"></script>
	<script src="<?php echo SURL ?>assets/js/bootstrap-datepicker.min.js"></script>
	<script src="<?php echo SURL ?>assets/js/moment.min.js"></script>
	<script type="text/javascript">
		var test = jQuery.noConflict();
		jQuery(function($) {
			//datepicker plugin
			//link
			$('.date-picker').datepicker({
				autoclose: true,
				todayHighlight: true
			})
		});
	</script>
</body>

</html>