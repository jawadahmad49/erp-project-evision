<?php

class Mod_voucher extends CI_Model {

    function __construct() {

        parent::__construct();
        error_reporting(0);
    
    }


    function get_all_transaction($table = "",  $fields = "*",$where = "") {
 
		
        $this->db->select('tbltrans_detail.*,tblacode.aname as aname');
        $this->db->join('tblacode', 'tbltrans_detail.acode = tblacode.acode'); 
         
        $this->db->where($where);

        $get = $this->db->get('tbltrans_detail');
	 
        return $get->result_array();
    }

    function get_all_transaction_day($tdate) {
 
	  	 $sql="SELECT `tbltrans_detail`.*, `tblacode`.`aname` as `aname` FROM `tbltrans_detail` JOIN `tblacode` ON `tbltrans_detail`.`acode` = `tblacode`.`acode` WHERE `tbltrans_detail`.`vtype` = 'CP' AND `tbltrans_detail`.`vdate` = '$tdate' and `tbltrans_detail`.`acode` != '2003013001'";
        $query = $this->db->query($sql);
         $sub_count=1; 
		 
		 
		 
		 ?>
		 
		 
										<table id="data_table1" class="table  table-bordered table-hover" >
											<thead>
														<tr>
																	<th>واؤچر نمبر</th>
														<th>بنام</th>
														
														<th class="hidden-480">تاریخ</th>
														<th class="hidden-480">کل </th>

														<th></th>
												</tr>
												
											</thead>
											</tbody>
		 
		 <?php
            foreach($query->result_array() as $key => $value) {
                $vno = $value['vno'];
                $aname = $value['aname'];
                $vdate = $value['vdate'];
                $damount = $value['damount'];
        
				 
				 
			?>									
			  

											 
													<tr> 
														<td>
															<?php echo  ucwords($vno); ?>
														</td>
													
														<td><?php 	 echo  ucwords($aname); 
																	 ?>
															
														</td>

														
														<td class="hidden-480">
															<?php echo  $vdate; ?>
														</td>
														<td class="hidden-480">
															<?php echo  number_format($damount); ?>
														</td>
														
														<td>
															<div class="hidden-sm hidden-xs action-buttons">
																

																<a class="green" href="<?php echo SURL."PaymentReceipt/edit/".$vno?>">
																	<i class="ace-icon fa fa-pencil bigger-130"></i>
																</a>

																<a id="bootbox-confirm" href="javascript:void(0)" class="red" onClick="confirmDelete('<?php echo SURL; ?>PaymentReceipt/delete/<?php echo $vno;?>');">
																	<i class="ace-icon fa fa-trash-o bigger-130"></i>
																</a>

															<a class="" href="<?php echo SURL."PaymentReceipt/detail/".$vno?>">
																	<i class="ace-icon fa fa-print bigger-130"></i>
																</a>	
															</div>

															<div class="hidden-md hidden-lg">
																<div class="inline pos-rel">
																	<button class="btn btn-minier btn-yellow dropdown-toggle" data-toggle="dropdown" data-position="auto">
																		<i class="ace-icon fa fa-caret-down icon-only bigger-120"></i>
																	</button>

																	<ul class="dropdown-menu dropdown-only-icon dropdown-yellow dropdown-menu-right dropdown-caret dropdown-close">
																		

																		<li>
																			<a href="<?php echo SURL."PaymentReceipt/edit/".$vno?>" class="tooltip-success" data-rel="tooltip" title="Edit">
																				<span class="green">
																					<i class="ace-icon fa fa-pencil-square-o bigger-120"></i>
																				</span>
																			</a>
																		</li>

																		<li>
																			<a  onClick="confirmDelete('<?php echo SURL; ?>PaymentReceipt/delete/<?php echo $vno;?>');" class="tooltip-error" data-rel="tooltip" title="Delete">
																				<span class="red">
																					<i class="ace-icon fa fa-trash-o bigger-120"></i>
																				</span>
																			</a>
																		</li>


																	</ul>
																</div>
															</div>
														</td>
													</tr>
													


													
                <?php

				}

				?>
					</tbody>
</table>						 
<?php
    }



    public function add_voucher($data){
        //pm($data);
        $ins_array = array(
            "vno" =>$data['transcode'],
            "vtype" =>"JVD",
            "damount" =>$data['d_total'],
            "camount" =>$data['c_total'],
             "damount_dollar" =>$data['fc_d_total'],
            "camount_dollar" =>$data['fc_c_total'],
            'printed' =>$data['save_print'],
            
            'authenticate'=>"Yes",
            'svtype'=>"JVD",
            "created_by"=>$this->session->userdata('id'),
            "created_date" =>$data['pdt'],
            'billno'=>0,
            'net_payment'=>0,
            'discount'=>0
        );
        #----------- add record---------------#
        $table = "tbltrans_master";
        $add_goods = $this->mod_common->insert_into_table($table, $ins_array);
        $insert_id = $add_goods;
            if($add_goods){
                return $this->multiple_voucher_againstid($data,$insert_id,'tbltrans_detail');
            }else{
                return false;
        }
    }

    public function multiple_voucher_againstid($data,$itnid,$table){
        $datas = array();
        //pm($data);
     $this->db->trans_start();

     $credit = "";
     $debit = "";
     $number = 2;
        foreach($data['customer'] as $key=>$value) {


        $datas[] = array(
            'vno' => $data['transcode'],
            'acode' => $data['customer'][$key],
            'srno' =>$data['srno'][$key],
            'expense_type'=>0,
            'scode'=>0,
            'chequedate'=>date('Y-m-d'),
            'chequeno'=>$data['cno'][$key],
            'damount'=>$data['debit'][$key],
            'camount'=>$data['credit'][$key],
            'remarks'=>$data['nar'][$key],
            'vtype'=>"JVD",
            'svtype'=>"JVD",
            'billno'=>0,
            'vdate'=>$data['pdt'],
            'damount_dollar'=>$data['fc_debit'][$key], 
            'camount_dollar'=>$data['fc_credit'][$key] 
           
           );
        }

        //pm($datas);
        $this->db->insert_batch($table, $datas);
        $this->db->trans_complete();
    }

    public function update_voucher($data){
        // pm($data);
        //$this->db->trans_start();
        $ins_array = array(
            "damount" =>$data['d_total'],
            "camount" =>$data['c_total'],
            "damount_dollar" =>$data['fc_d_total'],
            "camount_dollar" =>$data['fc_c_total'],
            "modify_by"=>$this->session->userdata('id'),
            "created_date" =>$data['pdt'],
            "modify_date" =>date('Y-m-d')
        );
        #----------- Update record---------------#
         //pm($ins_array);
        $id = $data['transcode'];
        $where = "vno= '$id'";
        $table = "tbltrans_master";
        $add_goods = $this->mod_common->update_table($table,$where,$ins_array);
       $insert_id = $id;
       // die;
        //$this->db->trans_complete();
            if($add_goods){
                return $this->multiple_update_voucher_againstid($data,$insert_id,'tbltrans_detail');
            }else{
                return false;
        }
    }

    public function multiple_update_voucher_againstid($data,$itnid,$table){
        $datas = array();
  
        $this->db->trans_start();
        $this->db->where(array('vno'=>$itnid));
        $this->db->delete($table);

        foreach($data['customer'] as $key=>$value) {

        $datas[] = array(
            'vno' => $data['transcode'],
            'acode' => $data['customer'][$key],
            'srno' =>$data['srno'][$key],
            'expense_type'=>0,
            'scode'=>0,
            'chequedate'=>$data['cdate'][$key],
            'chequeno'=>$data['cno'][$key],
            'damount'=>$data['debit'][$key],
            'camount'=>$data['credit'][$key],
            'remarks'=>$data['nar'][$key],
            'vtype'=>"JVD",
            'svtype'=>"JVD",
            'billno'=>0,
            'vdate'=>$data['pdt'],
           'damount_dollar'=>$data['fc_debit'][$key], 
            'camount_dollar'=>$data['fc_credit'][$key]           
           );
        }

        //pm($datas);
        $this->db->insert_batch($table, $datas);
        $this->db->trans_complete();
    }
     public function add_bank_receipt_voucher($data){
        //pm($data);
        $ins_array = array(
            "vno" =>$data['transcode'],
            "vtype" =>"BR",
            "damount" =>$data['d_total'],
            "camount" =>$data['c_total'],
            'printed' =>$data['save_print'],
            
            'authenticate'=>"Yes",
            'svtype'=>"BR",
            "created_by"=>$this->session->userdata('id'),
            "created_date" =>$data['pdt'],
            'billno'=>0,
            'net_payment'=>0,
            'discount'=>0
        );
        #----------- add record---------------#
        $table = "tbltrans_master";
        $add_goods = $this->mod_common->insert_into_table($table, $ins_array);
        $insert_id = $add_goods;
            if($add_goods){
                return $this->multiple_voucher_againstid_bank_receipt($data,$insert_id,'tbltrans_detail');
            }else{
                return false;
        }
    }
     public function update_bank_receipt_voucher($data){
        //pm($data);
        //$this->db->trans_start();
        $ins_array = array(
            "damount" =>$data['d_total'],
            "camount" =>$data['c_total'],
            "modify_by"=>$this->session->userdata('id'),
            "created_date" =>$data['pdt'],
            "modify_date" =>date('Y-m-d')
        );
        #----------- Update record---------------#
         //pm($ins_array);
        $id = $data['transcode'];
        $where = "vno= '$id'";
        $table = "tbltrans_master";
        $add_goods = $this->mod_common->update_table($table,$where,$ins_array);
       $insert_id = $id;
       // die;
        //$this->db->trans_complete();
            if($add_goods){
                return $this->multiple_voucher_againstid_bank_receipt($data,$insert_id,'tbltrans_detail');
            }else{
                return false;
        }
    }

    public function multiple_voucher_againstid_bank_receipt($data,$itnid,$table){
        $datas = array();
        $this->db->trans_start();
        $this->db->where(array('vno'=>$itnid));
        $this->db->delete($table);
        foreach($data['customer'] as $key=>$value) {

        $datas[] = array(
            'vno' => $data['transcode'],
            'acode' => $data['customer'][$key],
            'srno' =>$data['srno'][$key],
             'chequeno' =>$data['chequeno'][$key],
              'chequedate' =>$data['chequedate'][$key],
            'expense_type'=>0,
            'scode'=>0,
             'damount'=>$data['debit'][$key],
            'camount'=>$data['credit'][$key],
            'remarks'=>$data['nar'][$key],
            'vtype'=>"BR",
            'svtype'=>"BR",
            'billno'=>0,
            'vdate'=>$data['pdt']            
           );
        }

        //pm($datas);
        $this->db->insert_batch($table, $datas);
        $this->db->trans_complete();
    }
  public function add_bank_payment_voucher($data){
        //pm($data);
        $ins_array = array(
            "vno" =>$data['transcode'],
            "vtype" =>"BP",
            "damount" =>$data['d_total'],
            "camount" =>$data['c_total'],
            'printed' =>$data['save_print'],
            
            'authenticate'=>"Yes",
            'svtype'=>"BP",
            "created_by"=>$this->session->userdata('id'),
            "created_date" =>$data['pdt'],
            'billno'=>0,
            'net_payment'=>0,
            'discount'=>0
        );
        #----------- add record---------------#
        $table = "tbltrans_master";
        $add_goods = $this->mod_common->insert_into_table($table, $ins_array);
        $insert_id = $add_goods;
            if($add_goods){
                return $this->multiple_voucher_againstid_bank_payment($data,$insert_id,'tbltrans_detail');
            }else{
                return false;
        }
    }
     public function update_bank_payment_voucher($data){
        //pm($data);
        //$this->db->trans_start();
        $ins_array = array(
            "damount" =>$data['d_total'],
            "camount" =>$data['c_total'],
            "modify_by"=>$this->session->userdata('id'),
            "created_date" =>$data['pdt'],
            "modify_date" =>date('Y-m-d')
        );
        #----------- Update record---------------#
         //pm($ins_array);
        $id = $data['transcode'];
        $where = "vno= '$id'";
        $table = "tbltrans_master";
        $add_goods = $this->mod_common->update_table($table,$where,$ins_array);
       $insert_id = $id;
       // die;
        //$this->db->trans_complete();
            if($add_goods){
                return $this->multiple_voucher_againstid_bank_payment($data,$insert_id,'tbltrans_detail');
            }else{
                return false;
        }
    }

    public function multiple_voucher_againstid_bank_payment($data,$itnid,$table){
        $datas = array();
        $this->db->trans_start();
        $this->db->where(array('vno'=>$itnid));
        $this->db->delete($table);
        foreach($data['customer'] as $key=>$value) {

        $datas[] = array(
            'vno' => $data['transcode'],
            'acode' => $data['customer'][$key],
            'srno' =>$data['srno'][$key],
             'chequeno' =>$data['chequeno'][$key],
              'chequedate' =>$data['chequedate'][$key],
            'expense_type'=>0,
            'scode'=>0,
             'damount'=>$data['debit'][$key],
            'camount'=>$data['credit'][$key],
            'remarks'=>$data['nar'][$key],
            'vtype'=>"BP",
            'svtype'=>"BP",
            'billno'=>0,
            'vdate'=>$data['pdt']            
           );
        }

        //pm($datas);
        $this->db->insert_batch($table, $datas);
        $this->db->trans_complete();
    }
    public function add_voucher_simple($data){
        //pm($data);
        $ins_array = array(
            "vno" =>$data['transcode'],
            "vtype" =>"JV",
            "damount" =>$data['d_total'],
            "camount" =>$data['c_total'],
            'printed' =>$data['save_print'],
            
            'authenticate'=>"Yes",
            'svtype'=>"JV",
            "created_by"=>$this->session->userdata('id'),
            "created_date" =>$data['pdt'],
            'billno'=>0,
            'net_payment'=>0,
            'discount'=>0
        );
        #----------- add record---------------#
        $table = "tbltrans_master";
        $add_goods = $this->mod_common->insert_into_table($table, $ins_array);
        $insert_id = $add_goods;
            if($add_goods){
                return $this->multiple_voucher_againstid_simple($data,$insert_id,'tbltrans_detail');
            }else{
                return false;
        }
    }

    public function multiple_voucher_againstid_simple($data,$itnid,$table){
        $datas = array();

        $this->db->trans_start();

        foreach($data['customer'] as $key=>$value) {

        $datas[] = array(
            'vno' => $data['transcode'],
            'acode' => $data['customer'][$key],
            'srno' =>$data['srno'][$key],
            'expense_type'=>0,
            'scode'=>0,
            'chequedate'=>date('Y-m-d'),
            'chequeno'=>$data['cno'][$key],
            'damount'=>$data['debit'][$key],
            'camount'=>$data['credit'][$key],
            'remarks'=>$data['nar'][$key],
            'vtype'=>"JV",
            'svtype'=>"JV",
            'billno'=>0,
            'vdate'=>$data['pdt']            
           );
        }

        //pm($datas);
        $this->db->insert_batch($table, $datas);
        $this->db->trans_complete();
    }

    public function add_voucher_madani($data){
        //pm($data);
        $ins_array = array(
            "vno" =>$data['transcode'],
            "vtype" =>"JVM",
            "damount" =>$data['d_total'],
            "camount" =>$data['c_total'],
            'printed' =>$data['save_print'],
            
            'authenticate'=>"Yes",
            'svtype'=>"JVM",
            "created_by"=>$this->session->userdata('id'),
            "created_date" =>$data['pdt'],
            'billno'=>0,
            'net_payment'=>0,
            'discount'=>0
        );
        #----------- add record---------------#
        $table = "tbltrans_master";
        $add_goods = $this->mod_common->insert_into_table($table, $ins_array);
        $insert_id = $add_goods;
            if($add_goods){
                return $this->multiple_voucher_againstid_madani($data,$insert_id,'tbltrans_detail');
            }else{
                return false;
        }
    }

    public function multiple_voucher_againstid_madani($data,$itnid,$table){
        $datas = array();

        $this->db->trans_start();

        foreach($data['customer'] as $key=>$value) {

        $datas[] = array(
            'vno' => $data['transcode'],
            'acode' => $data['customer'][$key],
            'srno' =>$data['srno'][$key],
            'expense_type'=>0,
            'scode'=>0,
            'chequedate'=>date('Y-m-d'),
            'chequeno'=>$data['cno'][$key],
            'damount'=>$data['debit'][$key],
            'camount'=>$data['credit'][$key],
            'remarks'=>$data['nar'][$key],
            'vtype'=>"JVM",
            'svtype'=>"JVM",
            'billno'=>0,
            'vdate'=>$data['pdt']            
           );
        }

        //pm($datas);
        $this->db->insert_batch($table, $datas);
        $this->db->trans_complete();
    }

    public function update_voucher_simple($data){
        //pm($data);
        //$this->db->trans_start();
        $ins_array = array(
            "damount" =>$data['d_total'],
            "camount" =>$data['c_total'],
            "modify_by"=>$this->session->userdata('id'),
            "created_date" =>$data['pdt'],
            "modify_date" =>date('Y-m-d')
        );
        #----------- Update record---------------#
         //pm($ins_array);
        $id = $data['transcode'];
        $where = "vno= '$id'";
        $table = "tbltrans_master";
        $add_goods = $this->mod_common->update_table($table,$where,$ins_array);
       $insert_id = $id;
       // die;
        //$this->db->trans_complete();
            if($add_goods){
                return $this->multiple_update_voucher_againstid_simple($data,$insert_id,'tbltrans_detail');
            }else{
                return false;
        }
    }

    public function multiple_update_voucher_againstid_simple($data,$itnid,$table){
        $datas = array();
  
        $this->db->trans_start();
        $this->db->where(array('vno'=>$itnid));
        $this->db->delete($table);

        foreach($data['customer'] as $key=>$value) {

        $datas[] = array(
            'vno' => $data['transcode'],
            'acode' => $data['customer'][$key],
            'srno' =>$data['srno'][$key],
            'expense_type'=>0,
            'scode'=>0,
            'chequedate'=>$data['cdate'][$key],
            'chequeno'=>$data['cno'][$key],
            'damount'=>$data['debit'][$key],
            'camount'=>$data['credit'][$key],
            'remarks'=>$data['nar'][$key],
            'vtype'=>"JV",
            'svtype'=>"JV",
            'billno'=>0,
            'vdate'=>$data['pdt']            
           );
        }

        //pm($datas);
        $this->db->insert_batch($table, $datas);
        $this->db->trans_complete();
    }

    public function update_voucher_madani($data){
        //pm($data);
        //$this->db->trans_start();
        $ins_array = array(
            "damount" =>$data['d_total'],
            "camount" =>$data['c_total'],
            "modify_by"=>$this->session->userdata('id'),
            "created_date" =>$data['pdt'],
            "modify_date" =>date('Y-m-d')
        );
        #----------- Update record---------------#
         //pm($ins_array);
        $id = $data['transcode'];
        $where = "vno= '$id'";
        $table = "tbltrans_master";
        $add_goods = $this->mod_common->update_table($table,$where,$ins_array);
       $insert_id = $id;
       // die;
        //$this->db->trans_complete();
            if($add_goods){
                return $this->multiple_update_voucher_againstid_madani($data,$insert_id,'tbltrans_detail');
            }else{
                return false;
        }
    }

    public function multiple_update_voucher_againstid_madani($data,$itnid,$table){
        $datas = array();
  
        $this->db->trans_start();
        $this->db->where(array('vno'=>$itnid));
        $this->db->delete($table);

        foreach($data['customer'] as $key=>$value) {

        $datas[] = array(
            'vno' => $data['transcode'],
            'acode' => $data['customer'][$key],
            'srno' =>$data['srno'][$key],
            'expense_type'=>0,
            'scode'=>0,
            'chequedate'=>$data['cdate'][$key],
            'chequeno'=>$data['cno'][$key],
            'damount'=>$data['debit'][$key],
            'camount'=>$data['credit'][$key],
            'remarks'=>$data['nar'][$key],
            'vtype'=>"JVM",
            'svtype'=>"JVM",
            'billno'=>0,
            'vdate'=>$data['pdt']            
           );
        }

        //pm($datas);
        $this->db->insert_batch($table, $datas);
        $this->db->trans_complete();
    }


    public function edit_voucher($id){
        $this->db->select('tbltrans_master.*,tbltrans_detail.*,tblacode.aname');
        $this->db->from('tbltrans_master');
        $this->db->join('tbltrans_detail', 'tbltrans_master.vno = tbltrans_detail.vno');
        $this->db->join('tblacode', 'tbltrans_detail.acode = tblacode.acode');
        //$this->db->join('tbl_sales_point', 'tbl_itn.location_to = tbl_sales_point.sale_point_id');
        $this->db->where('tbltrans_master.vno=',$id);
       // $this->db->order_by("tbl_itn.itnid", "desc");
        $query = $this->db->get();
        return $query->result_array();
    }

    
  

    public function paymentreceipt_vno($type){

        $result = $this->db->select('vno')
                          ->from('tbltrans_master')
                          ->where('vtype',$type)
                          ->get();

        $Sr = array();
        $num_rows = $result->num_rows();
        if($num_rows!=0)
        {
            $line = $result->result_array();
            foreach ($line as $key => $value)
            {
                $parts = explode("-",$value['vno']);
                $Sr[] = $parts[2];
            }
            $billno=max($Sr)+1;
        }
        else{
            $billno = 1;
        }
        if($billno <=9){
            $billno = "00000" . $billno;
        }
        else if($billno <=99){
            $billno = "0000" . $billno;
        }
        else if($billno <=999){
            $billno = "000" . $billno;
        }
        else if($billno <=9999){
            $billno = "00" . $billno;
        }
        else if($billno <=99999){
            $billno = "0" . $billno;
        }
        return $vno=$this->session->userdata('id') . "-" . $type . "-" . $billno;
 
    }

    // UPDATE tbltrans_detail JOIN tbltrans_master ON tbltrans_detail.vno = tbltrans_master.vno SET tbltrans_detail.camount = 14,tbltrans_master.camount = 14 WHERE tbltrans_detail.id = 1

     public function update_transaction($data){ 


        $vno =$data['id'];

$vno_array=explode('-',$vno);

if($vno_array[1]=='CP'){$transaction ='Payment';}else{$transaction ='Receipt'; }

        $this->db->where('vno',$vno);
        $delete = $this->db->delete('tbltrans_master');
        
        $this->db->where('vno',$vno);
        $delete = $this->db->delete('tbltrans_detail');



            ////// delete vno from master and detail


 
        if($transaction=="Payment"){
            $vtype="CP";
            $damount = $data['amount'];
            $camount=0;
            $vno = $this->paymentreceipt_vno($vtype);
            //$acode_payment = $this->acode_forpayment();
            /* add record for CR as well */
            $vtype2="CP";
            $camount2 = $data['amount'];
            $damount2=0;
            $vno2 = $this->paymentreceipt_vno($vtype2);
            //$acode_receipt = $this->acode_forreceipt();

        }else{
            $vtype="CR";
            $camount = $data['amount'];
             $damount=0;
            $vno = $this->paymentreceipt_vno($vtype);
            //$acode_receipt = $this->acode_forreceipt();
            /* add record for CP as well */
            $vtype2="CR";
            $damount2 = $data['amount'];
             $camount2=0;
            $vno2 = $this->paymentreceipt_vno($vtype2);
            //$acode_payment = $this->acode_forpayment();
        }
       $this->db->trans_start();
        #----------- add record trans master---------------#
        $in_array_master = array(
            "vno" =>$vno,
            "vtype" =>$vtype,
            "camount" =>$data['amount'],
            "damount" =>$data['amount'],
            "created_date" =>$data['date'],
            "created_by" =>$this->session->userdata('id')    
        );
        
        $table = "tbltrans_master";
        $add = $this->mod_common->insert_into_table($table, $in_array_master);

         #----------- add record trans detail ---------------#
        $in_array_detail = array(
            "vno" =>$vno,
            "vtype" =>$vtype,
            "camount" =>$damount,
            "damount" =>$camount,
            "vdate" =>$data['date'],
            "remarks" =>$data['remarks'], 
            //"aname" =>$data['name'], 
            "expense_type" =>$data['acodes'], 
            "acode" =>'2003013001',
        );
        $table_detail = "tbltrans_detail";
        $add_detail = $this->mod_common->insert_into_table($table_detail, $in_array_detail);

         #----------- if CR add another entry for CP (vice versa) ---------------#
        $in_array_details = array(
            "vno" =>$vno2,
            "vtype" =>$vtype2,
            "camount" =>$damount2,
            "damount" =>$camount2,
            "vdate" =>$data['date'],
            "remarks" =>$data['remarks'], 
            //"aname" =>$data['name'], 
            "expense_type" =>$data['acodes'],
            "acode" =>$data['acodes'],
        );
        
        $add_details = $this->mod_common->insert_into_table($table_detail, $in_array_details);
        
        $this->db->trans_complete();
            if($add && $add_detail){
                return true;
            }else{
                return false;
            }

     }

     public function acode_forpayment(){

        $rest_creditors_code='2001001';
        $result = $this->db->select('COALESCE(max(right(`acode`,3)),0) as code')
                          ->from('tbltrans_detail')
                          ->where('LEFT(`acode`,7)',$rest_creditors_code)
                          ->get();

        $num_rows = $result->num_rows();
        $Code = 1;

        $line = $result->row_array();

        if(intval($line["code"])==0)
            $Code=1;
        else
            $Code=intval($line["code"])+1;
        if($Code<=99)
            $Code="00".$Code;

        return $rest_creditors_code .= $Code;
        
    }



    function select_trans_print_records($where) {

        $this->db->select('tbltrans_detail.*,tblacode.aname as final_name');
        $this->db->join('tblacode', 'tbltrans_detail.acode = tblacode.acode'); 
         $this->db->group_by('tbltrans_detail.testid');
          $this->db->order_by("srno", "ASC");
        $this->db->where($where);

        $get = $this->db->get('tbltrans_detail');
        return $get->result_array();
    }



     public function acode_forreceipt(){

            $rest_creditors_code='2001002';
            $result = $this->db->select('COALESCE(max(right(`acode`,3)),0) as code')
                          ->from('tbltrans_detail')
                          ->where('LEFT(`acode`,7)',$rest_creditors_code)
                          ->get();

        $num_rows = $result->num_rows();
        $Code = 1;

        $line = $result->row_array();

        if(intval($line["code"])==0)
            $Code=1;
        else
            $Code=intval($line["code"])+1;
        if($Code<=99)
            $Code="00".$Code;

        return $rest_creditors_code .= $Code;
        
    }
}

?>