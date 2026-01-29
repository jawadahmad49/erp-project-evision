

<?php $actual_url_final = 'http://'. $_SERVER["SERVER_NAME"].":".$_SERVER["SERVER_PORT"].$_SERVER["REQUEST_URI"];



		 	$CI =& get_instance();

			$CI->load->model('mod_user');



			$result = $CI->mod_user->get_language();



			 $result['lang_opt'];





//Check User Right

// $seg1 = $this->uri->segment(1);

// $seg2 = $this->uri->segment(2);







// $userID = $this->session->userdata('id');



// if(!empty($seg1) && $seg1!="admin" && $seg2==""){

//   	$seg1Data = $this->db->get_where("tbl_menu",array("linkname"=>$seg1))->row();

// 	$seg1ID = $seg1Data->pageid;

// 	$checkAccess = $this->db->get_where('tbl_user_rights',array('uid'=>$userID,'pageid'=>$seg1ID))->num_rows();



// 	if($checkAccess=="" || $checkAccess==0){

// 		$this->session->set_flashdata('err_message',"Sorry! You are not allowed to access this panel.");

// 		redirect(SURL);

// 	}

// }elseif($seg1!="" && $seg2!=""){

// 	$linkname = $seg1."/".$seg2;



// 	$linkData = $this->db->get_where("tbl_menu",array("linkname"=>$linkname))->row();

// 	$totalRows = count($linkData);



// 	if($totalRows >0){

// 		$pageid = $linkData->pageid;



// 		$checkAccess = $this->db->get_where('tbl_user_rights',array('uid'=>$userID,'pageid'=>$pageid))->num_rows();



// 		if($checkAccess=="" || $checkAccess==0){

// 			$this->session->set_flashdata('err_message',"Sorry! You are not allowed to access this panel.");

// 			redirect(SURL);

// 		}



// 	}elseif($totalRows==0 || $totalRows==""){

// 		$seg1Data = $this->db->get_where("tbl_menu",array("linkname"=>$seg1))->row();

// 		$seg1ID = $seg1Data->pageid;

// 		$checkAccess = $this->db->get_where('tbl_user_rights',array('uid'=>$userID,'pageid'=>$seg1ID))->num_rows();



// 		if($checkAccess=="" || $checkAccess==0){

// 			$this->session->set_flashdata('err_message',"Sorry! You are not allowed to access this panel.");

// 			redirect(SURL);

// 		}

// 	}

// }



$companyname = $this->db->query("select * from tbl_company")->result_array()[0]['business_name'];

?>

		<div id="navbar" class="navbar navbar-default          ace-save-state">

			<div class="navbar-container ace-save-state" id="navbar-container">

				<button type="button" class="navbar-toggle menu-toggler pull-left" id="menu-toggler" data-target="#sidebar">

					<span class="sr-only">Toggle sidebar</span>



					<span class="icon-bar"></span>



					<span class="icon-bar"></span>



					<span class="icon-bar"></span>

				</button>



				<div class="navbar-header pull-left">

					<a href="<?php echo SURL; ?>admin" class="navbar-brand">

						<small>

							<i class="glyphicon glyphicon-oil"></i>

							<?php


 

								$year=date('Y');
 




							 ?>

						    <?php echo $companyname;?> (Financial Year : <?php echo $year;?>)

						</small>

					</a>

				</div>



				<div class="navbar-buttons navbar-header pull-right" role="navigation">

					<ul class="nav ace-nav">





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

			</div><!-- /.navbar-container -->

		</div>







		<?php





		//////////////////check of posting last 3 days//////////////////////////////////////////////

			$result = $CI->mod_user->get_last_posted();

		  	  $last_posted=$result[0]['post_date'];



	$ci = & get_instance();

	$current_controler=$ci->uri->segment(1);



			// if($last_posted){



			// 	$today_is=date('Y-m-d');

			// 	$max_three_days= date("Y-m-d", strtotime($today_is ."-180 days" ));

			// 	$dt1=  strtotime($max_three_days);

			// 	$dt2=  strtotime($last_posted);

			// 	if($current_controler!=''){

			// 	 if($current_controler!='day_closing'){

			// 		if($dt2<$dt1){

			// 			$this->session->set_flashdata('err_message', 'Please Post First, Posting can not be delayed for more then 30 days !!');

			// 			redirect(SURL . 'day_closing/');

			// 			}

			// 		}

			// 	}else{





			// 			if($dt2<$dt1){

			// 			$this->session->set_flashdata('err_message', 'Please Post First, Posting can not be delayed for more then 30 days !!');

			// 			//redirect(SURL . 'day_closing/');

			// 			}

			// 		//$this->session->set_flashdata('err_message', 'Please Post First, Posting can not be delayed for more then 10 days !!');

			// 		// redirect(SURL . 'day_closing/');





			// 	}

			// }

////////////////////////////////////////////////////////////////////////////////////////////



		//////////////////check for last backup //////////////////////////////////////////////

			$result = $CI->mod_user->get_last_backupdate();

			  $last_backup_date=$result[0]['dt'];



			if($last_backup_date){



				$today_is=date('Y-m-d');

				$max_three_days= date("Y-m-d", strtotime($today_is ."-3 days" ));

				$dt1=  strtotime($max_three_days);

				$dt2=  strtotime($last_backup_date);



					if($dt2<$dt1){

						$this->session->set_flashdata('err_message', 'Alert! Please take database backup First, backup is not taken for last three days, Maintain proper log of database backups in case of any lost!');



						}





			}else{



				$this->session->set_flashdata('err_message', 'Database backup is not taken yet , please backup your data first !!');





			}

////////////////////////////////////////////////////////////////////////////////////////////



		?>