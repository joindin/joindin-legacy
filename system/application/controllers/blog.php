<?php

class Blog extends Controller {
	
	function Blog(){
		parent::Controller();
		$this->user_model->logStatus();
	}
	function index(){
		$this->load->model('blog_posts_model','bpm');
		$arr=array();
				
		$arr=array(
			'posts'		=> $this->bpm->getPostDetail(),
			'is_admin'	=> $this->user_model->isSiteAdmin()
		);
		
		$this->template->write('feedurl','/feed/blog');
		$this->template->write_view('content','blog/main',$arr);
		$this->template->render();
	}
	function add($id=null){
		if(!$this->user_model->isSiteAdmin()){ redirect(); }

		$this->load->helper('form');
		$this->load->library('validation');
		$this->load->model('blog_posts_model');
		$this->load->model('blog_cats_model');
		$this->load->model('blog_post_cat_model','bpcat');
		$arr=array();
		
		$fields=array(
			'title'		=> 'Entry Title',
			'story'		=> 'Entry Content',
			'post_mo'	=> 'Post Month',
			'post_day'	=> 'Post Day',
			'post_yr'	=> 'Post Year',
			'post_hr'	=> 'Post Hour',
			'post_mi'	=> 'Post Minute',
			'category'	=> 'Category'
		);
		$rules=array(
			'title'		=> 'required',
			'story'		=> 'required',
			'post_mo'	=> 'required',
			'post_day'	=> 'required',
			'post_yr'	=> 'required',
			'post_hr'	=> 'required',
			'post_mi'	=> 'required',
			'category'	=> 'required'
		);
		$this->validation->set_rules($rules);
		$this->validation->set_fields($fields);
				
		if($this->validation->run()!=FALSE){
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
			//echo '<pre>'; print_r($arr); echo '</pre>';
			if($id){
				$this->db->where('ID',$id);	
				$this->db->update('blog_posts',$arr);
			}else{ 
				$arr['views']=0;
				$this->db->insert('blog_posts',$arr); 
				$id=$this->db->insert_id();
			}
			$this->bpcat->setPostCat($id,$this->input->post('category'));
			
			$arr=array('msg'=>'Post inserted successfully! <a href="/blog/view/'.$id.'">View post</a>');
		}else{
			if($id){
				$det=$this->blog_posts_model->getPostDetail($id); //print_r($det);
				$this->validation->title=$det[0]->title;
				$this->validation->story=$det[0]->content;

				$this->validation->post_mo	= date('m',$det[0]->date_posted);
				$this->validation->post_day	= date('d',$det[0]->date_posted);
				$this->validation->post_year= date('Y',$det[0]->date_posted);

				$this->validation->post_hr	= date('H',$det[0]->date_posted);
				$this->validation->post_mi	= date('i',$det[0]->date_posted);
			}
		}
		$arr['edit_id']	= ($id) ? $id : null;
		$arr['cats']	= $this->blog_cats_model->getCategories();
		
		$this->template->write_view('content','blog/add',$arr);
		$this->template->render();
	}
	function edit($id){
		if(!$this->user_model->isSiteAdmin()){ redirect(); }
		$this->add($id);
	}
	function view($id){
		$this->load->helper('form');
		$this->load->library('validation');
		$this->load->library('akismet');
		$this->load->library('defensio');
		$this->load->helper('reqkey');
		$this->load->model('blog_posts_model','bpm');
		$this->load->model('blog_comments_model','bcm');
		
		$this->bpm->updatePostViews($id);
		$reqkey=buildReqKey();
		
		$fields=array(
			'title'		=> 'Title',
			'comment'	=> 'Comment',
			'name'		=> 'Name'
		);
		$rules=array(
			'title'		=> 'required',
			'comment'	=> 'required',
			'name'		=> 'required'
		);
		$this->validation->set_rules($rules);
		$this->validation->set_fields($fields);
		
		if($this->validation->run()!=FALSE){
			$arr=array(
				'comment_type'			=>'comment',
				'comment_content'		=>$this->input->post('comment')
			);
			$ret=$this->akismet->send('/1.1/comment-check',$arr);
			
			$ec=array();
			$ec['comment']=$this->input->post('comment');
			$def_ret=$this->defensio->check('anonymous',$ec['comment'],false,'/blog/view/'.$id);
			$is_spam=(string)$def_ret->spam;
			
			//passed...;
			$arr=array(
				'title'			=> $this->input->post('title'),
				'author_id'		=> (int)$this->session->userdata('ID'),
				'author_name'	=> $this->input->post('name'),
				'content'		=> $this->input->post('comment'),
				'blog_post_id'	=> $id
			);
			//print_r($arr);
			
			if($is_spam!='true'){
				$this->db->insert('blog_comments',$arr);
			
				$to='enygma@phpdeveloper.org';
				$subj='Blog comment on entry '.$id.' from joind.in';
				$cont= 'Title: '.$this->input->post('title')."\n\n";
				$cont.='Content: '.$this->input->post('comment')."\n\n";
				$cont.='Post: http://joind.in/blog/view/'.$id."\n\n";
				$cont.='Spam check: '.($ret=='false') ? 'not spam' : 'spam caught';

				mail($to,$subj,$cont,'From: feedback@joind.in');
				
				redirect('blog/view/'.$id . '#comments', 'location', 302);
			}
		}else{
			//failed...
		}
		
		$arr=array(
			'details'	=> $this->bpm->getPostDetail($id),
			'is_admin'	=> $this->user_model->isSiteAdmin(),
			'comments'	=> $this->bcm->getPostComments($id),
			'pid'		=> $id,
			'reqkey'	=> $reqkey,
			'seckey' 	=> buildSecFile($reqkey)
		);
		if($this->user_model->isAuth()){ 
			$udata=$this->user_model->getUser($this->session->userdata('ID')); //print_r($udata);
			$arr['full_name']=(!empty($udata[0]->full_name)) ? $udata[0]->full_name : $udata[0]->username;
		}
		$this->template->write('feedurl','/feed/blog');
		$this->template->write_view('content','blog/view',$arr);
		$this->template->render();
	}
}

?>