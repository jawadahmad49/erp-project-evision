			<div class="footer">
				<div class="footer-inner">
					<div class="footer-content">
						<span class="bigger-120">
							<span class="blue bolder"> </span>
							&copy; 2018-2019
						</span>

						&nbsp; &nbsp;
						<span class="action-buttons">
							<a href="#">
								<i class="ace-icon fa fa-twitter-square light-blue bigger-150"></i>
							</a>

							<a href="#">
								<i class="ace-icon fa fa-facebook-square text-primary bigger-150"></i>
							</a>

							<a href="#">
								<i class="ace-icon fa fa-rss-square orange bigger-150"></i>
							</a>
						</span>
					</div>
				</div>
			</div>
			<a href="#" id="btn-scroll-up" class="btn-scroll-up btn btn-sm btn-inverse">
				<i class="ace-icon fa fa-angle-double-up icon-only bigger-110"></i>
			</a>
			<script src="<?php echo SURL; ?>assets/js/jquery-2.1.4.min.js"></script>
			<script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
			<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

			<script type="text/javascript">
				$(document).ready(function() {
					$('.select-2').select2();
				});
				flatpickr('.date-picker', {
					dateFormat: "Y-m-d",
					maxDate: "today",
				});
				$(".date-picker").addClass('flat-picker');
				$(".flat-picker").removeClass('date-picker');
				/*$('#icons1').addClass('fa fa-shopping-cart');
				$("#icons2").addClass("glyphicon glyphicon-euro");
				$("#icons3").addClass("fa fa-pencil-square-o");
				$("#icons4").addClass("fa fa-undo");*/
				$('#nav-search').hide();
				$('.date-picker').attr('readonly', true);
			</script>