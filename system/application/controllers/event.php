<?php

class Event extends Controller {
	
	function Event(){
		parent::Controller();
		$this->user_model->logStatus();
	}
	function cust($in){
		$this->load->helper('url');
		$this->load->model('event_model');
		$id=$this->event_model->getEventIdByName($in);
		//print_r($id); echo $id[0]->ID;
		if(isset($id[0]->ID)){ 
			redirect('event/view/'.$id[0]->ID); 
		}else{ echo 'error'; }
	}
	//--------------------
	function index(){
		$prefs = array (
			'show_next_prev'  => TRUE,
			'next_prev_url'   => '/event'
		);
		
		$this->load->helper('form');
		//$this->load->library('calendar',$prefs);
		$this->load->model('event_model');
		
		$events=$this->event_model->getEventDetail();
		$arr=array(
			'events' =>$events,
			//'admin'	 =>($this->user_model->isAdminEvent($id)) ? true : false
		);	
		$this->template->write_view('content','event/main',$arr,TRUE);
		$this->template->render();
		
		//$this->load->view('event/main',array('events'=>$events));
	}
	function add($id=null){
		//check for admin
		if(!$this->user_model->isSiteAdmin()){ redirect(); }
		
		if($id){ $this->edit_id=$id; }
		$this->load->helper('form');
		$this->load->library('validation');
		$this->load->model('event_model');
		
		$rules=array(
			'event_name'=>'required',
			'event_loc'	=>'required',
			'start_mo'	=>'callback_start_mo_check',
			'end_mo'	=>'callback_end_mo_check'
		);
		$this->validation->set_rules($rules);
		
		$fields=array(
			'event_name'=>'Event Name',
			'start_mo'	=>'Start Month',
			'start_day'	=>'Start Day',
			'start_yr'	=>'Start Year',
			'end_mo'	=>'End Month',
			'end_day'	=>'End Day',
			'end_yr'	=>'End Year',
			'event_loc'	=>'Event Location',
			'event_desc'=>'Event Description'
		);
		$this->validation->set_fields($fields);
		
		if($this->validation->run()==FALSE){
			if($id){
				//we're editing here...
				$ret=$this->event_model->getEventDetail($id);
				foreach($ret[0] as $k=>$v){
					if($k=='event_start'){
						$this->validation->start_mo	= date('m',$v);
						$this->validation->start_day= date('d',$v);
						$this->validation->start_yr	= date('Y',$v);
					}elseif($k=='event_end'){
						$this->validation->end_mo	= date('m',$v);
						$this->validation->end_day	= date('d',$v);
						$this->validation->end_yr	= date('Y',$v);
					}else{ $this->validation->$k=$v; }
				}
			}
			$this->template->write_view('content','event/add');
			$this->template->render();
		}else{ 
			//success...
			$arr=array(
				'event_name'	=>$this->input->post('event_name'),
				'event_start'	=>mktime(
					0,0,0,
					$this->input->post('start_mo'),
					$this->input->post('start_day'),
					$this->input->post('start_yr')
				),
				'event_end'		=>mktime(
					23,59,59,
					$this->input->post('end_mo'),
					$this->input->post('end_day'),
					$this->input->post('end_yr')
				),
				'event_loc'		=>$this->input->post('event_loc'),
				'event_desc'	=>$this->input->post('event_desc'),
				'active'		=>'1'
			);
			if($id){
				//edit...
				$this->db->where('id',$this->edit_id);
				$this->db->update('events',$arr);
			}else{ $this->db->insert('events',$arr); }
			
			$arr=array('msg'=>'Data saved! <a href="/event/view/'.$id.'">View event</a>');
			$this->template->write_view('content','event/add',$arr);
			$this->template->render();
		}
	}
	function edit($id){
		if(!$this->user_model->isSiteAdmin()){ redirect(); }
		$this->add($id);
	}
	function view($id){
		$this->load->helper('form');
		$this->load->library('validation');
		$this->load->model('event_model');
		$talks	= $this->event_model->getEventTalks($id);
		$events	= $this->event_model->getEventDetail($id);	
		$arr=array(
			'events' =>$events,
			'talks'  =>$talks,
			'admin'	 =>($this->user_model->isAdminEvent($id)) ? true : false
		);
		
		$this->template->write_view('content','event/detail',$arr,TRUE);
		$this->template->render();
		//$this->load->view('event/detail',$arr);
	}
	function delete($id){
		if(!$this->user_model->isSiteAdmin()){ redirect(); }
		$this->load->helper('form');
		$this->load->library('validation');
		$this->load->model('event_model');
		
		$arr=array(
			'eid'		=> $id,
			'details'	=> $this->event_model->getEventDetail($id)
		);
		if(isset($_POST['answer']) && $_POST['answer']=='yes'){
			$this->event_model->deleteEvent($id);
			$arr=array();
		}
		
		$this->template->write_view('content','event/delete',$arr,TRUE);
		$this->template->render();
		//$this->load->view('event/delete',$arr);
	}
	function codes($id){
		$this->load->helper('form');
		$this->load->library('validation');
		$this->load->library('events');
		$this->load->helper('url');
		
		if($this->user_model->isSiteAdmin() || $this->user_model->isAdminEvent($id)){ 
			//they're okay
		}else{ redirect(); }
				
		$rules=array();
		$fields=array();
		
		//make our code list for the talks
		$this->load->model('event_model');
		$codes=array();
		$talks=$this->event_model->getEventTalks($id);
		foreach($talks as $k=>$v){
			$str='ec'.str_pad($v->ID,2,0,STR_PAD_LEFT).str_pad($v->event_id,2,0,STR_PAD_LEFT);
			$str.=substr(md5($v->talk_title),5,5);
			
			$codes[]=$str;
			
			//$rules['email_'.$v->ID]='trim|valid_email';
			$rules['email_'.$v->ID]	='callback_chk_email_check';
			$fields['email_'.$v->ID]='speaker email';
		}
		$this->validation->set_rules($rules);
		$this->validation->set_fields($fields);
		
		$arr=array(
			'talks'		=> $talks,
			'codes'		=> $codes,
			'details'	=> $this->event_model->getEventDetail($id)
		);
		if($this->validation->run()!=FALSE){
			foreach($talks as $k=>$v){
				$pv=$this->input->post('email_'.$v->ID);
				$chk=$this->input->post('email_chk_'.$v->ID);
				if(!empty($pv) && $chk==1){
					//these are the ones we need to send the email to these
					$this->events->sendCodeEmail($pv,$codes[$k]);
				}
			}
		}else{ /*echo 'fail';*/ }
		$this->template->write_view('content','event/codes',$arr,TRUE);
		$this->template->render();
	}
	//----------------------
	function start_mo_check($str){
		$t=mktime(
			0,0,0,
			$this->validation->start_mo,
			$this->validation->start_day,
			$this->validation->start_yr
		);
		if($t<=time()){
			$this->validation->set_message('start_mo_check','Start date must be in the future!');
			return false;
		}else{ return true; }
	}
	function end_mo_check($str){
		$st=mktime(
			0,0,0,
			$this->validation->start_mo,
			$this->validation->start_day,
			$this->validation->start_yr
		);
		$et=mktime(
			23,59,59,
			$this->validation->end_mo,
			$this->validation->end_day,
			$this->validation->end_yr
		);
		if($et<$st){
			$this->validation->set_message('end_mo_check','End month must be past the start date!');
			return false;
		}else{ return true; }
	}
	function chk_email_check($str){
		$chk_str=str_replace('_','_chk_',$this->validation->_current_field);
		$val=$this->input->post($chk_str);
		if($val==1 && !$this->validation->valid_email($str)){
			$this->validation->set_message('chk_email_check','Email address invalid!');
			return false;
		}else{ return true; }
	}
	//----------------------
}

?>
