<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Categories extends AdminController {



	 public function __construct() {
        parent::__construct();

        $this->load->model(array(
            "admin/mod_categories"
        ));
        
    }
    
	public function index($pagenumber=1){
		
		$this->mod_common->is_page_accessible(221);
		#------- count records----------#
        $table = "business_categories";
        $rows=$this->mod_common->get_all_records_nums($table);
        
		#----------- pagination--------------#
        $config["base_url"] = AURL . "categories/index/";
        $config['total_rows'] = $rows;
         $ppage = $config["per_page"] = 25;
        $config['use_page_numbers'] = TRUE;
        $config['uri_segment'] = 4;


        $config['full_tag_open'] = '<ul class="pagination">';
        $config['full_tag_close'] = '</ul>';
        $config['first_link'] = false;
        $config['last_link'] = false;
        $config['first_tag_open'] = '<li>';
        $config['first_tag_close'] = '</li>';
        $config['prev_link'] = '&laquo';
        $config['prev_tag_open'] = '<li class="prev">';
        $config['prev_tag_close'] = '</li>';
        $config['next_link'] = '&raquo';
        $config['next_tag_open'] = '<li>';
        $config['next_tag_close'] = '</li>';
        $config['last_tag_open'] = '<li>';
        $config['last_tag_close'] = '</li>';
        $config['cur_tag_open'] = '<li class="active"><a href="#">';
        $config['cur_tag_close'] = '</a></li>';
        $config['num_tag_open'] = '<li>';
        $config['num_tag_close'] = '</li>';


        $this->pagination->initialize($config);

        $data['numcounter'] = 1;
        if ($pagenumber > 0) {
            $pagenumber = $pagenumber - 1;
            $data['numcounter'] = ($pagenumber * ITEMS_PER_PAGE) + 1;
            $llimit = ($ppage * $pagenumber);
            $ulimit = ($ppage * $pagenumber) + $ppage;
        }
        if ($pagenumber == 0) {
            $llimit = 0;
            $ulimit = $ppage;
        }

        $data["links"] = $this->pagination->create_links();
        
		#------- get record with limit apply---------#   
        $data['categories_list'] = $this->mod_common->get_all_records($table,"*",$llimit,$ppage);
		$data["filter"] = '';
		#----load view----------#
		$data["title"] = "Manage Business Categories";
		$data["subview"] = "admin/category/index";
	  	$this->load->view('admin_layout', $data);
	}
	
	public function add() {

        $this->mod_common->is_page_accessible(220);        
        #------------- if post--------------#
        if ($this->input->post("add_new_category_submit")) {


		// get rules set in common model 		
		$set_rules = $this->mod_common->set_rules();
		
		// validate by login rules
	  	$this->form_validation->set_rules($set_rules['category']);
     	  
	  	if ($this->form_validation->run() == FALSE){
                   $this->session->set_flashdata('err_message', validation_errors());
					redirect(AURL . 'categories/add');
		  }else{
			#---------- add company record---------------#
			 $add_category =  $this->mod_categories->add_category();
			 
            
				if ($add_category) {
					$this->session->set_flashdata('ok_message', '- Category added successfully!');
					redirect(AURL . 'categories');
				} else {
                $this->session->set_flashdata('err_message', '- Error in adding category please try again!');
                redirect(AURL . 'categories/add');
            	}
            }
        }

		#--------- load view----------------#
		$data["title"] = "Add Category";
		$data["subview"] = "admin/category/add";
	  	$this->load->view('admin_layout', $data);
    }
	
	public function edit($cid) {
        
        $this->mod_common->is_page_accessible(222);
		
		#------------ get record------------#        
        $data['category_edit'] = $this->mod_categories->edit_record($cid);

        if (count($data['category_edit']) == 0) {

            redirect("nopage");
        } else {			
			
			#------------- load view-------#
			$data["title"] = "Edit Category";
			$data["subview"] = "admin/category/edit";
			$this->load->view('admin_layout', $data);
        }
    }
	
	public function update($cid) {

        $this->mod_common->is_page_accessible(222);        
        #------------- if post--------------#
        if ($this->input->post("update_category_submit")) {
			#---------- update company record---------------#
			 $update_ccategory =  $this->mod_categories->update_ccategory($cid);
            
				if ($update_ccategory) {
					$this->session->set_flashdata('ok_message', '- Category updated successfully!');
					redirect(AURL . 'categories');
				} else {
                $this->session->set_flashdata('err_message', '- Error in adding category please try again!');
                redirect(AURL . 'categories/edit');
            	}
        }       		

    }
	
	public function delete($id) {

       $this->mod_common->is_page_accessible(223);
		#-------------delete record--------------#
        $table = "business_categories";
        $where = "id = '" . $id . "'";
        $delete_user = $this->mod_common->delete_record($table, $where);

        if ($delete_user) {
            $this->session->set_flashdata('ok_message', '- Category deleted successfully!');
            redirect(AURL . 'categories/');
        } else {
            $this->session->set_flashdata('err_message', '- Error in deleteting category please try again!');
            redirect(AURL . 'categories/');
        }
    }


}
