<!doctype html>
<html lang="en" data-layout="vertical" data-topbar="light" data-sidebar="dark" data-sidebar-size="lg" data-sidebar-image="none" data-preloader="disable">


<head>

	<meta charset="utf-8" />
	<title>Request For User Deletion</title>
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<meta content="Premium Multipurpose Admin & Dashboard Template" name="description" />
	<meta content="Themesbrand" name="author" />
	<!-- App favicon -->
	<link rel="shortcut icon" href="<?php echo SURL; ?>assets/images/logo-light.png">

	<!-- Layout config Js -->
	<script src="<?php echo SURL; ?>assets/js/layout.js"></script>
	<!-- Bootstrap Css -->
	<link href="<?php echo SURL; ?>assets/css/bootstrap2.min.css" rel="stylesheet" type="text/css" />
	<!-- Icons Css -->
	<link href="<?php echo SURL; ?>assets/css/icons2.min.css" rel="stylesheet" type="text/css" />
	<!-- App Css-->
	<link href="<?php echo SURL; ?>assets/css/app.min.css" rel="stylesheet" type="text/css" />
	<!-- custom Css-->
	<link href="<?php echo SURL; ?>assets/css/custom2.min.css" rel="stylesheet" type="text/css" />

</head>

<body>
	<div class="auth-page-wrapper pt-5">
		<!-- auth page bg -->
		<div class="auth-one-bg-position auth-one-bg" id="auth-particles">
			<div class="bg-overlay"></div>

			<div class="shape">
				<svg xmlns="http://www.w3.org/2000/svg" version="1.1" xmlns:xlink="http://www.w3.org/1999/xlink" viewBox="0 0 1440 120">
					<path d="M 0,36 C 144,53.6 432,123.2 720,124 C 1008,124.8 1296,56.8 1440,40L1440 140L0 140z"></path>
				</svg>
			</div>
		</div>

		<!-- auth page content -->
		<div class="auth-page-content">
			<div class="container">
				<div class="row">
					<div class="col-lg-12">
						<div class="text-center mt-sm-5 mb-4 text-white-50">
							<div>
								<a href="https://opigas.com/" class="d-inline-block auth-logo">
									<img src="<?php echo SURL; ?>assets/images/logo-light.png" alt="" height="150">
								</a>
							</div>
						</div>
					</div>
				</div>
				<!-- end row -->
				<div id="message-container"></div>

				<div class="row justify-content-center">
					<div class="col-md-8 col-lg-6 col-xl-5">
						<div class="card mt-4">

							<div class="card-body p-4">
								<div class="text-center mt-2">
									<h5 class="text-primary">User delete request !</h5>
									<p class="text-muted">To proceed with deleting this user, please complete all required fields in the form. This action is permanent, and once deleted, the user’s information cannot be recovered.</p>
								</div>
								<div class="p-2 mt-4">
									<!-- <form id="loginForm" class="form-horizontal" onsubmit="return false;"> -->
									<form id="formID" class="form-horizontal" role="form" method="post" action="<?php echo SURL . 'app/Delete_user/add_request'; ?>" enctype="multipart/form-data">
										<!-- Form fields here -->
										<div class="mb-3">
											<label for="name" class="form-label">User Name</label>
											<input type="text" class="form-control" id="name" name="name" placeholder="Enter User Name" required>
										</div>
										<div class="mb-3">
											<label for="email" class="form-label">User Email</label>
											<input type="email" class="form-control" id="email" name="email" placeholder="Enter User Email" required>
										</div>
										<div class="mb-3">
											<label for="number" class="form-label">Phone Number</label>
											<input type="text" class="form-control" id="number" name="number" placeholder="Enter Phone Number" required maxlength="13">
										</div>
										<div class="mb-3">
											<label for="password" class="form-label">Password</label>
											<input type="password" class="form-control" id="password" name="password" placeholder="Enter Password" required>
										</div>
										<div class="mb-3">
											<label class="form-label">Reason</label>
											<div class="d-flex justify-content-between" style="gap: 15px; flex-wrap: wrap;">
												<div>
													<input type="checkbox" id="reason1" name="reason[]" value="Reason 1">
													<label for="reason1">Reason 1</label>
												</div>
												<div>
													<input type="checkbox" id="reason2" name="reason[]" value="Reason 2">
													<label for="reason2">Reason 2</label>
												</div>
												<div>
													<input type="checkbox" id="reason3" name="reason[]" value="Reason 3">
													<label for="reason3">Reason 3</label>
												</div>
												<div>
													<input type="checkbox" id="reasonOther" name="reason[]" value="Other">
													<label for="reasonOther">Other</label>
												</div>
											</div>
											<div id="otherReasonContainer" style="display: none; margin-top: 10px;">
												<label for="reasonTextarea" class="form-label">Please specify</label>
												<textarea class="form-control" id="reasonTextarea" name="otherReason" placeholder="Enter other reason"></textarea>
											</div>
										</div>
										<div class="mb-4">
											<p class="mb-0 fs-12 text-muted fst-italic">
												<input class="form-check-input" type="checkbox" value="" id="auth-remember-check" required>
												By clicking on this, you confirm that you have read and agree to abide by our
												<a href="https://lpginsight.com/GasablePK/app/Privacy_policy/view" class="text-primary text-decoration-underline fst-normal fw-medium">Privacy Policy</a>
											</p>
										</div>
										<div class="mt-4">
											<!-- <button class="btn btn-success w-100 btnsubmit" type="submit">Submit</button> -->
											<input id="myButton" type="submit" class="btn btn-success w-100 btnsubmit" value="Submit">
										</div>
									</form>

									<div id="message-container"></div>


								</div>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
		<footer class="footer">
			<div class="container">
				<div class="row">
					<div class="col-lg-12">
						<div class="text-center">
							<p class="mb-0 text-muted">&copy;
								<script>
									document.write(new Date().getFullYear())
								</script> Evision. Crafted by <a href="https://evisionsystem.com/">Evision</a>
							</p>
						</div>
					</div>
				</div>
			</div>
		</footer>
	</div>
	<script src="<?php echo SURL; ?>assets/libs/bootstrap/js/bootstrap.bundle.min.js"></script>
	<script src="<?php echo SURL; ?>assets/libs/simplebar/simplebar.min.js"></script>
	<script src="<?php echo SURL; ?>assets/libs/node-waves/waves.min.js"></script>
	<script src="<?php echo SURL; ?>assets/libs/feather-icons/feather.min.js"></script>
	<script src="<?php echo SURL; ?>assets/js/pages/plugins/lord-icon-2.1.0.js"></script>
	<script src="<?php echo SURL; ?>assets/js/plugins.js"></script>

	<!-- particles js -->
	<script src="<?php echo SURL; ?>assets/libs/particles.js/particles.js"></script>
	<!-- particles app js -->
	<script src="<?php echo SURL; ?>assets/js/pages/particles.app.js"></script>
	<!-- password-addon init -->
	<script src="<?php echo SURL; ?>assets/js/pages/password-addon.init.js"></script>
	<script src="https://code.jquery.com/jquery-3.7.1.js" integrity="sha256-eKhayi8LEQwp4NKxN+CfCh+3qOVUtJn3QNZ0TciWLP4=" crossorigin="anonymous"></script>
	<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
	<script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
	<script src="<?php echo SURL; ?>assets/js/pages/passowrd-create.init.js"></script>
	<script>
		// Toggle other reason textarea visibility
		document.getElementById('reasonOther').addEventListener('change', function () {
			var otherReasonContainer = document.getElementById('otherReasonContainer');
			otherReasonContainer.style.display = this.checked ? 'block' : 'none';
		});
		$('#formID').on('submit', function (e) {
			e.preventDefault();

			let valid = true;
			let errorMessages = [];

			const name = $('#name').val().trim();
			if (name === '') {
				valid = false;
				errorMessages.push('Name is required.');
			}

			const email = $('#email').val().trim();
			if (email === '') {
				valid = false;
				errorMessages.push('Email is required.');
			}

			const number = $('#number').val().trim();
			const phonePattern = /^\+92\d{10}$/;

			if (!phonePattern.test(number)) {
				valid = false;
				errorMessages.push('Enter a valid phone number in the format +923123456789.');
			}

			const password = $('#password').val().trim();
			if (password === '') {
				valid = false;
				errorMessages.push('Password is required.');
			}

			const reasons = $('input[name="reason[]"]:checked');
			const reasonTextarea = $('#reasonTextarea').val().trim();
			if (reasons.length === 0) {
				valid = false;
				errorMessages.push('Please select at least one reason.');
			} else if ($('#reasonOther').prop('checked') && reasonTextarea === '') {
				valid = false;
				errorMessages.push('Please specify your reason in the "Other" field.');
			}

			const privacyPolicy = $('#auth-remember-check').prop('checked');
			if (!privacyPolicy) {
				valid = false;
				errorMessages.push('You must agree to the Privacy Policy.');
			}

			if (valid) {
				var formData = new FormData(this);
				$.ajax({
					url: $(this).attr('action'),
					type: 'POST',
					data: formData,
					processData: false,
					contentType: false,
					dataType: 'text',
					cache: false,
					success: function (response) {
						if (typeof response === 'string') {
							response = JSON.parse(response);
						}

						if (response.status === 'success') {
							$('#message-container').html('<div class="alert alert-success">' + response.message + '</div>');
							$('input[type="text"], input[type="email"], input[type="password"], textarea').val('');
							$('input[type="checkbox"], input[type="radio"]').prop('checked', false);
							$('select').prop('selectedIndex', 0);
						} else {
							$('#message-container').html('<div class="alert alert-danger">' + response.message + '</div>');
						}
					},
					error: function () {
						$('#message-container').html('<div class="alert alert-danger">An error occurred. Please try again.</div>');
					}
				});


				// if (valid) {
				// 	$.ajax({
				// 		url: "<?php echo SURL . 'app/Delete_user/add_request'; ?>",
				// 		cache: false,
				// 		type: "POST",
				// 		data: {
				// 			name: name,
				// 			email: email,
				// 			number: number,
				// 			password: password,
				// 			reasons: reasons,
				// 			reasonTextarea: reasonTextarea,
				// 		},
				// 		success: function (response) {
				// 			if (response.status === 'success') {
				// 				$('#message-container').html('<div class="alert alert-success">' + response.message + '</div>');
				// 			} else {
				// 				$('#message-container').html('<div class="alert alert-danger">' + response.message + '</div>');
				// 			}
				// 		},
				// 		error: function () {
				// 			$('#message-container').html('<div class="alert alert-danger">An error occurred. Please try again.</div>');
				// 		}
				// 	});
				// } else {
				// 	alert(errorMessages.join('\n'));
				// }
			} else {
				alert(errorMessages.join('\n'));
			}
		});

	</script>
</body>

</html>