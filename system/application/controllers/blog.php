<?php

class Blog extends Controller {
	
	function Blog(){
		parent::Controller();
		$this->user_model->logStatus();
	}
	function index(){
		$this->load->model('blog_posts_model');
		$arr=array();
				
		$arr['posts']=$this->blog_posts_model->getPostDetail(); //print_r($ret);
		
		$this->template->write_view('content','blog/main',$arr);
		$this->template->render();
	}
	function add($id=null){
		$this->load->helper('form');
		$this->load->library('validation');
		$this->load->model('blog_posts_model');
		$arr=array();
		
		$fields=array(
			'title'		=> 'Entry Title',
			'story'		=> 'Entry Content',
			'post_mo'	=> 'Post Month',
			'post_day'	=> 'Post Day',
			'post_yr'	=> 'Post Year',
			'post_hr'	=> 'Post Hour',
			'post_mi'	=> 'Post Minute'
		);
		$rules=array(
			'title'		=> 'required',
			'story'		=> 'required',
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
				'content'	 => $this->input->post('story'),
				'date_posted'=> $post_date,
				'author_id'	 => ''
			);
			echo '<pre>'; print_r($arr); echo '</pre>';
			if($id){
				$this->db->where('ID',$id);	
				$this->db->update('blog_posts',$arr);
			}else{ $this->db->insert('blog_posts',$arr); }
			
			$arr=array('msg'=>'Post inserted successfully!');
		}else{
			if($id){
				$det=$this->blog_posts_model->getPostDetail(); //print_r($det);
				$this->validation->title=$det[0]->title;
				$this->validation->story=$det[0]->content;

				$this->validation->post_mo	= date('m',$det[0]->date_posted);
				$this->validation->post_day	= date('d',$det[0]->date_posted);
				$this->validation->post_year= date('Y',$det[0]->date_posted);

				$this->validation->post_hr	= date('H',$det[0]->date_posted);
				$this->validation->post_mi	= date('i',$det[0]->date_posted);
			}
		}
		$arr['edit_id']=($id) ? $id : null;
		
		$this->template->write_view('content','blog/add',$arr);
		$this->template->render();
	}
	function edit($id){
		//if(!$this->user_model->isSiteAdmin()){ redirect(); }
		$this->add($id);
	}
	function view(){
		$this->load->model('blog_posts_model');
		
		$arr=array();
		$this->template->write_view('content','blog/main',$arr);
		$this->template->render();
	}
}

?>