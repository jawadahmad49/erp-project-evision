<!DOCTYPE html>
<html>

<?php

$this->load->view('en/include/head'); ?>

<head>
	<meta charset="utf-8" />
	<title>Tax Invoice</title>
	<link rel="shortcut icon" type="image/png" href="./favicon.png" />
	<style>
		* {
			box-sizing: border-box;
		}

		.table-bordered td,
		.table-bordered th {
			border: 1px solid #ddd;
			padding: 10px;
			word-break: break-all;
		}

		body {
			font-family: Arial;
			margin: 0;
			padding: 0;
			font-size: 16px;
			background-color: #E4E6E9 !important;
		}

		.h4-14 h4 {
			font-size: 12px;
			margin-top: 0;
			margin-bottom: 5px;
		}

		.img {
			margin-left: "auto";
			margin-top: "auto";
			height: 30px;
		}

		pre,
		p {
			/* width: 99%; */
			/* overflow: auto; */
			/* bpicklist: 1px solid #aaa; */
			padding: 0;
			margin: 0;
			font-family: Arial;

		}

		table {
			font-family: arial;
			width: 100%;
			border-collapse: collapse;
			padding: 1px;
		}

		.hm-p p {
			text-align: left;
			padding: 1px;
			padding: 5px 4px;
		}

		td,
		th {
			text-align: left;
			padding: 8px 6px;
		}

		.table-b td,
		.table-b th {
			border: 1px solid #ddd;
		}

		.hm-p td,
		.hm-p th {
			padding: 3px 0px;
		}

		.cropped {
			float: right;
			margin-bottom: 20px;
			height: 100px;
			/* height of container */
			overflow: hidden;
		}

		.cropped img {
			width: 400px;
			margin: 8px 0px 0px 80px;
		}

		.main-pd-wrapper {
			/* box-shadow: 0 0 10px #ddd; */
			background-color: #fff;
			border-radius: 10px;
			padding: 15px;
		}

		.table-bordered td,
		.table-bordered th {
			border: 1px solid #ddd;
			padding: 10px;
			font-size: 14px;
		}

		.invoice-items {
			font-size: 14px;
			border-top: 1px dashed #ddd;
		}

		.invoice-items td {
			padding: 14px 0;

		}

		@media print {
			.button {
				display: none !important;
			}
		}
	</style>
</head>
<?php
$qresult = $this->db->query("select * from tbl_company where id=1")->row_array();

$hosp_id = $qresult['id'];
$hosp_name = $qresult['business_name'];
$hosp_address_1 = $qresult['address'];
$hosp_nums = $qresult['phone'];
$hosp_email = $qresult['email'];
$hosp_img = $qresult['logo'];
?>
<?php

$order_detail = $this->db->query("SELECT delivery_charges,userid,address,area_id,area_name,city_id,deliveryType,date,sale_point_id,deliveryStatus,gst,reject_reason FROM `tbl_place_order` where id='$id'")->row_array();
$userid = $order_detail['userid'];
$address = $order_detail['address'];
$area_name = $order_detail['area_name'];
$area_id = $order_detail['area_id'];
$city_id = $order_detail['city_id'];
$date = $order_detail['date'];
$sale_point_id = $order_detail['sale_point_id'];
$zone_id = $this->db->query("SELECT zone_id FROM `tbl_zone_detail` where id='$area_id'")->row_array()['zone_id'];
$zone_name = $this->db->query("SELECT zone_name FROM `tbl_zone` where id='$zone_id'")->row_array()['zone_name'];

$user_detail = $this->db->query("SELECT * FROM `tbl_user` where id='$userid'")->row_array();
$city_name = $this->db->query("SELECT city_name FROM `tbl_city` where city_id='$city_id'")->row_array()['city_name'];
if ($user_detail['dp']) {
	$dp = $user_detail['dp'];
} else {
	$dp = "default.JPG";
}
$delivery = $this->db->query("SELECT standard_range,express_range,night_range FROM `tbl_delivery_charges` where sale_point_id='$sale_point_id' and zone='$zone_id' and e_date<='$date'ORDER BY e_date DESC LIMIT 1")->row_array();
$gst = $order_detail['gst'];
if ($order_detail['deliveryType'] == 'Standard') {
	$delivery_gst = ($delivery['standard_range'] * $gst) / 100;
	$delivery_charges = $delivery['standard_range'] + $delivery_gst;
}
if ($order_detail['deliveryType'] == 'Express') {
	$delivery_gst = ($delivery['express_range'] * $gst) / 100;
	$delivery_charges = $delivery['express_range'] + $delivery_gst;
}
if ($order_detail['deliveryType'] == 'Night') {
	$delivery_gst = ($delivery['night_range'] * $gst) / 100;
	$delivery_charges = $delivery['night_range'] + $delivery_gst;
}
?>
<!-- <div class="col-xs-12 button align-center" style="display: contents;">
	<div class="form-group form-control" style="height: auto;width: min-content;margin: auto;">
		<a href="<?php echo SURL . "app/All_orders/" ?>" class="btn btn-sm btn-info">Back</a>
	</div>
</div> -->

<body>

	<section class="main-pd-wrapper form-group" style="width: 400px; margin: auto">
		<div style="
				  text-align: center;
				  margin: auto;
				  line-height: 1.5;
				  font-size: 14px;
				  color: #4a4a4a;
				">
			<img src="<?php echo IMG . "company/" . $hosp_img ?>" width="200" height="150">
			<p style="font-weight: bold; color: #000; font-size: 18px;">
				<?php print $hosp_name; ?>
			</p>
			<!-- style="margin: 15px auto;" -->
			<p style="font-weight: 500; color: #000; font-size: 15px;">
				<?php print $hosp_address_1;
				if ($hosp_address_2) {
					print '<br>' . $hosp_address_2;
				}
				?>
			</p>
			<p style="font-weight: 500; color: #000; font-size: 15px;">
				<?php print $hosp_nums; ?>
			</p>
			<p style="font-weight: 500; color: #000; font-size: 15px;">
				<?php print $hosp_email; ?>
			</p>

		</div>
		<div style="text-align: center;"><span><b>Order # <?= $id; ?></span></b></div>

		<table style="margin: 03px 25px auto auto;width: 100%; table-layout: fixed">
			<tr class="invoice-items">
				<th><strong>Customer :</strong></th>
				<td>&nbsp;<?= $user_detail['name']; ?></td>
				<th><strong>Date :</strong></th>
				<td>&nbsp;<?= $date; ?></td>
			</tr>
			<tr class="invoice-items">
				<th><strong>Phone :</strong></th>
				<td>&nbsp;<?= $user_detail['phone']; ?></td>
			</tr>
			<tr class="invoice-items">
				<th><strong>E-Mail :</strong></th>
				<td>&nbsp;<?= $user_detail['email']; ?></td>
			</tr>
		</table>
		<hr style="border: 1px dashed rgb(131, 131, 131); margin: 25px auto">

		<table style="width: 100%; table-layout: fixed">
			<thead>
				<tr>
					<th style="width: 50px;text-align: left; padding-left: 0;">Sn.</th>
					<th style="width: 120px;">Item Name</th>
					<th style="width: 50px;text-align: left; padding-right: 0; padding-left: 0;">Type</th>
					<th style="text-align: right; padding-right: 0;">Quantity</th>
					<th style="text-align: right; padding-right: 0;">Amount</th>
				</tr>
			</thead>
			<tbody width="100%" ;>
				<?php
				// Fetch order details
				$order_list_details = $this->db->query("SELECT * FROM tbl_place_order_detail WHERE order_id = '$id'")->result_array();
				$order_status = $order_detail['deliveryStatus'];

				$count = 0;
				$total_qty = 0;
				$lpg_amount = 0;
				$ttl_accessories = 0;
				$ttl_security_charges = 0;
				$ttl_delivery_charge = 0;
				$ttl_gst = 0;

				foreach ($order_list_details as $value) {
					$count++;
					$materialcode = $value['materialcode'];

					// Fetch item details
					$item_detail = $this->db->query("SELECT itemname, CONCAT('https://lpginsight.com/GasablePK/assets/images/items/', image_path) AS image_path, catcode, itemnameint, security_price FROM tblmaterial_coding WHERE materialcode = '$materialcode'")->row_array();
					$date = $order_detail['date'];

					// Fetch sale price and security charges
					// $price = $this->db->query("SELECT saleprice, security_charges FROM tbl_price_fluctuation WHERE edate <= '$date' and item_id='$materialcode' and sale_point_id='$sale_point_id' order by id desc")->row_array();
					$saleprice = $value['price'];
					$security_charges = $value['security_charges'];


					if ($value['type'] != 'Swap') {
						$gst = ($saleprice * $order_detail['gst']) / 100;
						$ttl_gst += $value['quantity'] * $gst;

						$total_amount = ($saleprice + round($gst)) * $value['quantity'];
					}


					// Calculate total amount (with security charges if type is New)
					if ($value['type'] == 'New') {
						$total_amount += $security_charges * $value['quantity'];  // Add security charges if type is 'New'
						$ttl_security_charges += $security_charges * $value['quantity'];  // Add security charges if type is 'New'
					}

					$catcode = $item_detail['catcode'];
					$area_id = $order_detail['area_id'];
					$zone_id = $this->db->query("SELECT zone_id FROM `tbl_zone_detail` where id='$area_id'")->row_array()['zone_id'];
					$delivery = $this->db->query("SELECT standard_range,express_range,night_range FROM `tbl_delivery_charges` where sale_point_id='$sale_point_id' and zone='$zone_id' and e_date<='$date'ORDER BY e_date DESC LIMIT 1")->row_array();

					if ($order_detail['deliveryType'] == 'Standard') {
						$delivery_gst = ($delivery['standard_range'] * $order_detail['delivery_gst']) / 100;
						$delivery_charges = $delivery['standard_range'] + $delivery_gst;
					}
					if ($order_detail['deliveryType'] == 'Express') {
						$delivery_gst = ($delivery['express_range'] * $order_detail['delivery_gst']) / 100;
						$delivery_charges = $delivery['express_range'] + $delivery_gst;
					}
					if ($order_detail['deliveryType'] == 'Night') {
						$delivery_gst = ($delivery['night_range'] * $order_detail['delivery_gst']) / 100;
						$delivery_charges = $delivery['night_range'] + $delivery_gst;
					}

					if ($catcode == 1) {
						if ($value['type'] != 'Swap') {
							$ttl_delivery_charge += (int) $value['quantity'] * (float) $delivery_charges;
							$lpg_amount += $value['quantity'] * $saleprice;
						}
					} else {
						$ttl_accessories += $value['quantity'] * $saleprice;
					}
					$total_qty += $value['quantity'];

					if (
						$value['type'] == 'Swap'
					) {
						$ttl_swap_charges += $value['swap_charges'] * $value['quantity'];
						$total_amount = $value['swap_charges'] * $value['quantity'];
					}
					$brand_name = $this->db->query("SELECT brand_name FROM tbl_brand WHERE brand_id = '$value[cylinder_brand]'")->row_array()['brand_name'];

					?>
					<tr class="invoice-items">
						<td><?php echo $count ?></td>
						<td><?php echo $item_detail['itemname'] ?></td>
						<td style="text-align-last: start;"><?php echo $value['type'] ?></td>
						<td style="text-align-last: end;">
							<?php echo $value['quantity'] ?>
						</td>
						<td style="text-align-last: end;">
							<?php echo number_format($total_amount) ?>
						</td>
					</tr>
				<?php } ?>
			</tbody>
		</table>

		<table style="width: 100%;
			  background: #fcbd024f;
			  border-radius: 4px;">
			<thead>
				<!-- <tr>
					<th width='75%'>Grand Total</th>
					<th style="text-align: left;"></th>
					<th style="text-align: right;"></th>
				</tr> -->
			</thead>

		</table>

		<table style="width: 100%;
			  margin-top: 15px;
			  border: 1px dashed #00cd00;
			  border-radius: 3px;">
			<thead>
				<?php if ($total_qty > 0) { ?>
					<tr>
						<td>Total Quantity </td>
						<td style="text-align: right;"><?php echo number_format($total_qty) ?></td>
					</tr>
				<?php } ?>
				<?php if ($lpg_amount > 0) { ?>
					<tr>
						<td>LPG Amount</td>
						<td style="text-align: right;"><?php echo number_format($lpg_amount) ?></td>
					</tr>
				<?php } ?>
				<?php if ($order_detail['gst'] > 0) { ?>
					<tr>
						<td>GST Percentage </td>
						<td style="text-align: right;"><?php echo number_format($order_detail['gst']) . " %" ?></td>
					</tr>
				<?php } ?>
				<?php if ($ttl_gst > 0) { ?>
					<tr>
						<td>GST Amount </td>
						<td style="text-align: right;"><?php echo number_format($ttl_gst) ?></td>
					</tr>
				<?php } ?>
				<?php if ($ttl_security_charges > 0) { ?>
					<tr>
						<td>Total Security Charges </td>
						<td style="text-align: right;"><?php echo number_format($ttl_security_charges) ?></td>
					</tr>
				<?php } ?>
				<?php if ($ttl_accessories > 0) { ?>
					<tr>
						<td>Accessories Amount </td>
						<td style="text-align: right;"><?php echo number_format($ttl_accessories) ?></td>
					</tr>
				<?php } ?>
				<?php if ($order_detail['delivery_charges'] > 0) { ?>
					<tr>
						<td>Total Delivery Charges </td>
						<td style="text-align: right;"><?php echo number_format($order_detail['delivery_charges']) ?></td>
					</tr>
				<?php } ?>
				<?php if ($ttl_swap_charges > 0) { ?>
					<tr>
						<td>Total Swap Credits </td>
						<td style="text-align: right;"><?php echo number_format($ttl_swap_charges) ?></td>
					</tr>
				<?php } ?>
				<tr style="background: #fcbd024f;">
					<td>Grand Total </td>
					<td style="text-align: right;"><?php echo number_format($lpg_amount + $ttl_accessories + $ttl_security_charges + $order_detail['delivery_charges'] + $ttl_gst + $ttl_swap_charges) ?></td>
				</tr>
			</thead>

		</table>
		<hr style="border: 1px dashed rgb(131, 131, 131); margin: 10px auto">

		<table width="350px" border="0" cellspacing="0" cellpadding="0" align="center">
			<tr style="border: 1px solid;
	background: #80808045;">
				<td width="10%" height="23" style="text-align: center;" colspan="2"><i>Thank You for visiting us</i></td>



			</tr>
			<tr>

				<!-- <td width="10%"><span class="style7"><u>Sale By: </u></span></td>
				<td><strong><?php
				$res2 = $this->db->query("select * from tbl_admin where id='$issuedby'")->row_array();
				$res_2 = $res2;
				$approve_by_name = $res_2['admin_name'];

				echo $approve_by_name; ?></strong></td> -->
			</tr>
			<tr>
				<td width="10%"><span class="style7"><u>Print&nbsp;Date/Time: </u></span></td>
				<td> <strong><?php echo date('Y-m-d H:i:s'); ?></strong></td>
			</tr>
			<tr style="border: 1px solid;">
				<td width="10%" height="23" style="text-align: center;" colspan="2">Software by www.evisionsystem.com</td>
			</tr>
		</table>
		<hr style="border: 1px dashed rgb(131, 131, 131); margin: 25px auto">
		<table width="350px" border="0" cellspacing="0" cellpadding="0" align="center">
			<tr>
				<td width="10%" height="23" style="text-align: left;" colspan="2"><b>Terms and Condition</b></td>
			</tr>
			<tr>
				<td width="10%" height="23" align="center" colspan="2">1. Inspection: OPI Gas Pvt Ltd ensures cylinders are sealed and inspected for leaks before dispatch; customers must inspect on delivery and report any issues immediately.</td>
			</tr>
			<tr>
				<td width="10%" height="23" align="center" colspan="2">2. Safety: Customers must follow provided safety guidelines, use cylinders in ventilated, hazard-free areas, and handle with care.</td>
			</tr>
			<tr>
				<td width="10%" height="23" align="center" colspan="2">3. Liability: OPI Gas is liable only for replacement or refund due to manufacturing defects or delivery mishandling; damages from misuse or accidents post-delivery are not covered.</td>
			</tr>
			<tr>
				<td width="10%" height="23" align="center" colspan="2">By accepting delivery, customers agree to these terms, assuming responsibility for safe usage.</td>
			</tr>

		</table>
		<input type="hidden" id="order_id" value="<?php echo $id ?>">

	</section>
	<script src="https://cdnjs.cloudflare.com/ajax/libs/html2pdf.js/0.10.1/html2pdf.bundle.min.js" integrity="sha512-GsLlZN/3F2ErC5ifS5QtgpiJtWd43JWSuIgh7mbzZ8zBps+dvLusV+eNQATqgA/HdeKFVgA5v3S/cIrLF7QnIg==" crossorigin="anonymous" referrerpolicy="no-referrer"></script>
	<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.4.1/jquery.min.js"></script>

	<script>
		convert();


		function convert() {
			var order_id = $("#order_id").val();
			document.body.style.width = '100%';
			var filename = document.getElementById("order_id").value;
			var opt = {
				filename: filename + '.pdf',
				pagebreak: {
					mode: ['avoid-all', 'css', 'legacy']
				}
			};
			html2pdf().set(opt).from(document.body).output('datauristring').then(function (pdfAsString) {
				// Make an AJAX call to the server
				$.ajax({
					url: '<?php echo SURL . "app/Order_confirmation/small_invoice/" ?>',
					type: 'POST',
					data: {
						pdf: pdfAsString,
						name: filename + '.pdf',
						id: order_id
					},
					success: function (response) {
						// var res = prompt("Copy to clipboard: Ctrl+C, Enter", response);
						// if (res != null) {
						// 	window.open(response, "_blank");
						// }
					}
				});
			});
		}
	</script>
</body>

</html>