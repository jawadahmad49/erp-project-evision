<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Sale_comp_report extends CI_Controller {
	public function __construct() {
        parent::__construct();

        $this->load->model(array(
           "mod_common","mod_salelpg","mod_admin"
        ));
        
    }

	public function index()
	{
	$login_user=$this->session->userdata('id');
        $sale_point_id = $this->db->query("select location from tbl_admin where id='$login_user'")->row_array()['location'];
     $fix_code = $this->db->query("select * from tbl_sales_point where sale_point_id='$sale_point_id'")->row_array();
             $data['sale_point_id']=$sale_point_id=$fix_code['sale_point_id'];
if($sale_point_id !=''){ $where_sale_point_id= "where sale_point_id='$sale_point_id'  "; }else{ $where_sale_point_id =""; }
       
        $fix_code = $this->db->query("select * from tbl_code_mapping where sale_point_id='$sale_point_id'")->row_array();
        $customer_code=$fix_code['customer_code'];
         
          if($customer_code !=''){ $where_customer= " and tblacode.general='$customer_code'  "; }else{ $where_customer ="and tblacode.general in('2004001000','2004002000')"; }
		$data['supplier_list'] = $this->db->query("select * from tblacode where atype='Child' $where_customer")->result_array();
		//$data['supplier_list'] = $this->db->query("select * from tblacode where  atype='child'")->result_array();
		$data['location']=$this->db->query("select * from tbl_sales_point $where_sale_point_id")->result_array();

		$data["title"] = "Sale Comparison Report";	
		$this->load->view($this->session->userdata('language')."/Sale_comp_report/search",$data);
	}

	public function report()
	{

			$login_user=$this->session->userdata('id');
        //////////////////////Sale Point Location geting of user////////////////////////
        $sale_point_id = $this->db->query("select location from tbl_admin where id='$login_user'")->row_array()['location'];

///////////////////////////////////////////////////////////////////////////////////////////////////
//////////////Giving Sale_Point_id for user from tbl_sale_point//////////////////////////////////

     $fix_code = $this->db->query("select * from tbl_sales_point where sale_point_id='$sale_point_id'")->row_array();
   
             $data['sale_point_id']=$sale_point_id=$fix_code['sale_point_id'];

               /////////////////////////////////////////////////////////////////////////////////////////////

if($sale_point_id !=''){ $where_sale_point_id= "where sale_point_id='$sale_point_id'  "; }else{ $where_sale_point_id =""; }
       
        $fix_code = $this->db->query("select * from tbl_code_mapping where sale_point_id='$sale_point_id'")->row_array();
        $customer_code=$fix_code['customer_code'];
        /////////////////////////Getting Customer Code from tbl_code_mapping.............
         
          if($customer_code !=''){ $where_customer= " and tblacode.general='$customer_code'  "; }else{ $where_customer ="and tblacode.general in('2004001000','2004002000')"; }
 
 $data['supplier']=$supplier = $this->input->post("supplier");

if($supplier !='All' ){ $where_acode = "and tblacode.acode='$supplier'  "; }else{ $where_acode =""; }
		
		

		$data['supplier_list'] = $this->db->query("select * from tblacode where atype='Child' $where_customer $where_acode")->result_array();


		if($this->input->server('REQUEST_METHOD') == 'POST'){
			
		///////////////////////////FOR HELP PURPOSE////////////////////////////////////////////////
			$month=$data['month']=$this->input->post("month");
			if($this->input->post("month") == "January"){
				$mnth = "01";
			}else if($this->input->post("month") == "February"){
				$mnth = "02";
			}
			else if($this->input->post("month") == "March"){
				$mnth = "03";
			}
			else if($this->input->post("month") == "April"){
				$mnth = "04";
			}
			else if($this->input->post("month") == "May"){
				$mnth = "05";
			}
			else if($this->input->post("month") == "June"){
				$mnth = "06";
			}
			else if($this->input->post("month") == "July"){
				$mnth = "07";
			}
			else if($this->input->post("month") == "August"){
				$mnth = "08";
			}
			else if($this->input->post("month") == "September"){
				$mnth = "09";
			}
			else if($this->input->post("month") == "October"){
				$mnth = "10";
			}
			else if($this->input->post("month") == "November"){
				$mnth = "11";
			}
			else if($this->input->post("month") == "December"){
				$mnth = "12";
			}

		$year=$data['year']=$this->input->post("year");
	$from=$data['from'] = $this->input->post("year")."-".$mnth."-"."01";
	$to=$data['to'] = date("Y-m-t",strtotime($from));
	$data['mnth'] =$mnth;
////////////////////////////////////////////////////////////////////////////
		$month_c=$data['month_c']=$this->input->post("month_c");
			if($this->input->post("month_c") == "January"){
				$mnth_c = "01";
			}else if($this->input->post("month_c") == "February"){
				$mnth_c = "02";
			}
			else if($this->input->post("month_c") == "March"){
				$mnth_c = "03";
			}
			else if($this->input->post("month_c") == "April"){
				$mnth_c = "04";
			}
			else if($this->input->post("month_c") == "May"){
				$mnth_c = "05";
			}
			else if($this->input->post("month_c") == "June"){
				$mnth_c = "06";
			}
			else if($this->input->post("month_c") == "July"){
				$mnth_c = "07";
			}
			else if($this->input->post("month_c") == "August"){
				$mnth_c = "08";
			}
			else if($this->input->post("month_c") == "September"){
				$mnth_c = "09";
			}
			else if($this->input->post("month_c") == "October"){
				$mnth_c = "10";
			}
			else if($this->input->post("month_c") == "November"){
				$mnth_c = "11";
			}
			else if($this->input->post("month_c") == "December"){
				$mnth_c = "12";
			}

		$year_c=$data['year_c']=$this->input->post("year_c");
		$data['mnth_c']=$mnth_c;
	$from_c=$data['from_c'] = $this->input->post("year")."-".$mnth_c."-"."01";

	$to_c=$data['to_c'] = date("Y-m-t",strtotime($from_c));


		 $data['sale_point_id']=$sale_point_id=$this->input->post('location');
		  $plant=$data['plant'] = $this->input->post("plant");

		 /////////////////////////////////////////////////////////////////////////////////////////////
		  
       // pm($data['report'] );exit();
			$table='tbl_company';       
       		$data['company'] = $this->mod_common->get_all_records($table,"*");
			if ($data['supplier_list']) {
			 $data["title"] = "Sale Comparison Report";	
	            $this->load->view($this->session->userdata('language')."/Sale_comp_report/single",$data);
	        } else {
	            $this->session->set_flashdata('err_message', 'No Record Found.');
	            redirect(SURL . 'Sale_comp_report/');
	          
	        }
	    }
	}
			public function newpdf(){
				$login_user=$this->session->userdata('id');
        //////////////////////Sale Point Location geting of user////////////////////////
        $sale_point_id = $this->db->query("select location from tbl_admin where id='$login_user'")->row_array()['location'];

///////////////////////////////////////////////////////////////////////////////////////////////////
//////////////Giving Sale_Point_id for user from tbl_sale_point//////////////////////////////////

     $fix_code = $this->db->query("select * from tbl_sales_point where sale_point_id='$sale_point_id'")->row_array();
   
             $data['sale_point_id']=$sale_point_id=$fix_code['sale_point_id'];

               /////////////////////////////////////////////////////////////////////////////////////////////

if($sale_point_id !=''){ $where_sale_point_id= "where sale_point_id='$sale_point_id'  "; }else{ $where_sale_point_id =""; }
       
        $fix_code = $this->db->query("select * from tbl_code_mapping where sale_point_id='$sale_point_id'")->row_array();
        $customer_code=$fix_code['customer_code'];
        /////////////////////////Getting Customer Code from tbl_code_mapping.............
         
          if($customer_code !=''){ $where_customer= " and tblacode.general='$customer_code'  "; }else{ $where_customer ="and tblacode.general in('2004001000','2004002000')"; }
 
 $data['supplier']=$supplier = $this->input->post("supplier");

if($supplier !='All' ){ $where_acode = "and tblacode.acode='$supplier'  "; }else{ $where_acode =""; }
		
		

		$data['supplier_list'] = $this->db->query("select * from tblacode where atype='Child' $where_customer $where_acode")->result_array();

	
		if($this->input->server('REQUEST_METHOD') == 'POST'){
			
		///////////////////////////FOR HELP PURPOSE////////////////////////////////////////////////
			$month=$data['month']=$this->input->post("month");
			if($this->input->post("month") == "January"){
				$mnth = "01";
			}else if($this->input->post("month") == "February"){
				$mnth = "02";
			}
			else if($this->input->post("month") == "March"){
				$mnth = "03";
			}
			else if($this->input->post("month") == "April"){
				$mnth = "04";
			}
			else if($this->input->post("month") == "May"){
				$mnth = "05";
			}
			else if($this->input->post("month") == "June"){
				$mnth = "06";
			}
			else if($this->input->post("month") == "July"){
				$mnth = "07";
			}
			else if($this->input->post("month") == "August"){
				$mnth = "08";
			}
			else if($this->input->post("month") == "September"){
				$mnth = "09";
			}
			else if($this->input->post("month") == "October"){
				$mnth = "10";
			}
			else if($this->input->post("month") == "November"){
				$mnth = "11";
			}
			else if($this->input->post("month") == "December"){
				$mnth = "12";
			}

		$year=$data['year']=$this->input->post("year");
	$from=$data['from'] = $this->input->post("year")."-".$mnth."-"."01";
	$to=$data['to'] = date("Y-m-t",strtotime($from));
////////////////////////////////////////////////////////////////////////////
		$month_c=$data['month_c']=$this->input->post("month_c");
			if($this->input->post("month_c") == "January"){
				$mnth_c = "01";
			}else if($this->input->post("month_c") == "February"){
				$mnth_c = "02";
			}
			else if($this->input->post("month_c") == "March"){
				$mnth_c = "03";
			}
			else if($this->input->post("month_c") == "April"){
				$mnth_c = "04";
			}
			else if($this->input->post("month_c") == "May"){
				$mnth_c = "05";
			}
			else if($this->input->post("month_c") == "June"){
				$mnth_c = "06";
			}
			else if($this->input->post("month_c") == "July"){
				$mnth_c = "07";
			}
			else if($this->input->post("month_c") == "August"){
				$mnth_c = "08";
			}
			else if($this->input->post("month_c") == "September"){
				$mnth_c = "09";
			}
			else if($this->input->post("month_c") == "October"){
				$mnth_c = "10";
			}
			else if($this->input->post("month_c") == "November"){
				$mnth_c = "11";
			}
			else if($this->input->post("month_c") == "December"){
				$mnth_c = "12";
			}

		$year_c=$data['year_c']=$this->input->post("year_c");
	$from_c=$data['from_c'] = $this->input->post("year")."-".$mnth_c."-"."01";

	$to_c=$data['to_c'] = date("Y-m-t",strtotime($from_c));


		 $data['sale_point_id']=$sale_point_id=$this->input->post('location');
		  $plant=$data['plant'] = $this->input->post("plant");

		 /////////////////////////////////////////////////////////////////////////////////////////////
		  
       // pm($data['report'] );exit();
			$table='tbl_company';       
       		$data['company'] = $this->mod_common->get_all_records($table,"*");
	
	    }

	  
	    	 $profilename =  $from_date;
	    	 // $profilename1 =  $to_date;
	    	 // $profilename2 =  $type;
	  
	  //pm($data);


		$this->load->view($this->session->userdata('language')."/Sale_comp_report/pdffile",$data);

		$this->load->library('pdf');
			 $html = $this->output->get_output();
			 $this->dompdf->loadHtml($html);
			 $this->dompdf->setPaper('A4', 'landscape');
	        $this->dompdf->render();


	        
	        $this->dompdf->stream( $profilename.".pdf", array("Attachment"=>0));	
	}
	public function get_chart() {

		$data['monthly_stock']=  $this->mod_admin->getmonthly_stock_customer_wise($this->input->post());
		$data['monthly_stock_sec']=  $this->mod_admin->getmonthly_stock_customer_wise_sec($this->input->post());
		//pm($data['monthly_stock']);exit;


            $month=$this->input->post('chart_month');
            $month_c=$this->input->post('chart_month_sec');
            $year=$this->input->post('chart_year');
        
            $timestamp    = strtotime("$month" . "$year");


            // $start_date = date('Y-m-01', $timestamp);
            // $end_date  = date('Y-m-t', $timestamp); 
            $start_date = $year."-".$month."-"."01";
              $end_date  = $year."-".$month."-"."31"; 

 

	
	?>
	<div id="chartContainer" style="height: 300px; width: 83%;margin-left: 5%;"></div>
<div class="over" style="height: 20px;margin-top: -14px;width: 60px;background-color: white;position: absolute;"></div>
	<?php

		array_multisort( array_column($data['monthly_stock'], "issuedate"), SORT_ASC, $data['monthly_stock'] );

		 ?>
	<script type="text/javascript">

		var chart = new CanvasJS.Chart("chartContainer", {
			axisY:{
		   viewportMinimum: 0,
		   viewportMaximum: 3,
		   title: "Tonnage" ,
		   interval: .25         
		
		 },
		axisX:{
		  title : "Days",
		  interval: 1

		 },
		data: [
		{
			// Change type to "doughnut", "line", "splineArea", etc.
			type: "line",
			dataPointWidth: 20,
			click: onClick,
			dataPoints: [
				<?php foreach ($data['monthly_stock'] as $key => $value) { 
					$timestamp = strtotime($value['issuedate']);
					?>

				{ label:'<?php echo date("d", $timestamp);  ?>', y: <?php echo $value['totala']; ?>  },
			<?php } ?>

			]
		},{
			// Change type to "doughnut", "line", "splineArea", etc.
			type: "line",
			dataPointWidth: 20,
			click: onClick,
			dataPoints: [
				<?php foreach ($data['monthly_stock_sec'] as $key => $value) { 
					$timestamp = strtotime($value['issuedate']);
					?>

				{ label:'<?php echo date("d", $timestamp);  ?>', y: <?php echo $value['totala_sec']; ?>  },
			<?php } ?>

			]
		},
		]
	});
	chart.render();
	function onClick(e) {

  	var month =$('#chart_month').val();
  	var year =$('#chart_year').val();

    var url = "<?php echo SURL ?>SaleDateReport/item_report_detail";
	var form = $('<form target="_blank" action="' + url + '" method="post">' +
 
  	'<input type="hidden" name="day" value="' + e.dataPoint.x + '" />' +
  	'<input type="hidden" name="month" value="' +month + '" />' +
  	'<input type="hidden" name="year" value="' +year+ '" />' +

		  '</form>');
		$('body').append(form);
		form.submit();
	}
</script>

<?php
 }
 public function get_bar_chart() {

		$data['monthly_stock']=  $this->mod_admin->getmonthly_stock_customer_wise($this->input->post());
		$data['monthly_stock_sec']=  $this->mod_admin->getmonthly_stock_customer_wise_sec($this->input->post());
		//pm($data['monthly_stock']);exit;


            $month=$this->input->post('chart_month');
            $month_c=$this->input->post('chart_month_sec');
            $year=$this->input->post('chart_year');
        
            $timestamp    = strtotime("$month" . "$year");


            // $start_date = date('Y-m-01', $timestamp);
            // $end_date  = date('Y-m-t', $timestamp); 
            $start_date = $year."-".$month."-"."01";
              $end_date  = $year."-".$month."-"."31"; 

 

	
	?>
	<div id="chartContainer" style="height: 300px; width: 83%;margin-left: 5%;"></div>
<div class="over" style="height: 20px;margin-top: -14px;width: 60px;background-color: white;position: absolute;"></div>
	<?php

		array_multisort( array_column($data['monthly_stock'], "issuedate"), SORT_ASC, $data['monthly_stock'] );

		 ?>
	<script type="text/javascript">

		var chart = new CanvasJS.Chart("chartContainer", {
			axisY:{
		   viewportMinimum: 0,
		   viewportMaximum: 3,
		   title: "Tonnage" ,
		   interval: .25         
		
		 },
		axisX:{
		  title : "Days",
		  interval: 1

		 },
		data: [
		{
			// Change type to "doughnut", "line", "splineArea", etc.
			type: "column",
			dataPointWidth: 20,
			click: onClick,
			dataPoints: [
				<?php foreach ($data['monthly_stock'] as $key => $value) { 
					$timestamp = strtotime($value['issuedate']);
					?>

				{ label:'<?php echo date("d", $timestamp);  ?>', y: <?php echo $value['totala']; ?>  },
			<?php } ?>

			]
		},{
			// Change type to "doughnut", "line", "splineArea", etc.
			type: "column",
			dataPointWidth: 20,
			click: onClick,
			dataPoints: [
				<?php foreach ($data['monthly_stock_sec'] as $key => $value) { 
					$timestamp = strtotime($value['issuedate']);
					?>

				{ label:'<?php echo date("d", $timestamp);  ?>', y: <?php echo $value['totala_sec']; ?>  },
			<?php } ?>

			]
		},
		]
	});
	chart.render();
	function onClick(e) {

  	var month =$('#chart_month').val();
  	var year =$('#chart_year').val();

    var url = "<?php echo SURL ?>SaleDateReport/item_report_detail";
	var form = $('<form target="_blank" action="' + url + '" method="post">' +
 
  	'<input type="hidden" name="day" value="' + e.dataPoint.x + '" />' +
  	'<input type="hidden" name="month" value="' +month + '" />' +
  	'<input type="hidden" name="year" value="' +year+ '" />' +

		  '</form>');
		$('body').append(form);
		form.submit();
	}
</script>

<?php
 }

}
