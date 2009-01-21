<?php

class Blog extends Controller {
	
	function Blog(){
		parent::Controller();
		$this->user_model->logStatus();
	}
	function index(){
		$arr=array();
		$this->template->write_view('content','blog/main',$arr);
		$this->template->render();
	}
	function add(){
		$this->load->helper('form');
		$this->load->library('validation');
		$arr=array();
		
		$fields=array(
			'title'		=> 'Entry Title',
			'content'	=> 'Entry Content',
			'post_mo'	=> 'Post Month',
			'post_day'	=> 'Post Day',
			'post_yr'	=> 'Post Year',
			'post_hr'	=> 'Post Hour',
			'post_mi'	=> 'Post Minute'
		);
		$rules=array(
			'title'		=> 'required',
			'content'	=> 'required',
			'post_mo'	=> 'required',
			'post_day'	=> 'required',
			'post_yr'	=> 'required',
			'post_hr'	=> 'required',
			'post_mi'	=> 'required'
		);
		$this->validation->set_rules($rules);
		$this->validation->set_fields($fields);
		
		if($this->validation->run()!=FALSE){
			echo 'success!';
			$post_date=mktime(
				$this->input->post('post_hr'),$this->input->post('post_mi'),0,
				$this->input->post('post_mo'),$this->input->post('post_day'),
				$this->input->post('post_yr')
			);
			$arr=array(
				'title'		 => $this->input->post('title'),
				'content'	 => $this->input->post('content'),
				'date_posted'=> $post_date,
				'author_id'	 => ''
			);
			//$this->db->insert('blog_posts',$arr);
		}
		
		$this->template->write_view('content','blog/add',$arr);
		$this->template->render();
	}
	function view(){
		$arr=array();
		$this->template->write_view('content','blog/main',$arr);
		$this->template->render();
	}
}

?>