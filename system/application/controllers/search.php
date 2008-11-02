<?php

class Search extends Controller {
	
	function Search(){
		parent::Controller();
		$this->user_model->logStatus();
	}
	function index(){
		$this->load->helper('form');
		$this->load->library('validation');
		$this->load->model('talks_model');
		$this->load->model('event_model');
		$results=array();
		
		$rules=array(
			'search_term'=>'required'
		);
		$fields=array(
			'search_term'=>'Search Term',
			'start_mo'	=>'Start Month',
			'start_day'	=>'Start Day',
			'start_yr'	=>'Start Year',
			'end_mo'	=>'End Month',
			'end_day'	=>'End Day',
			'end_yr'	=>'End Year',
		);
		
		$this->validation->set_rules($rules);
		$this->validation->set_fields($fields);
		
		if($this->validation->run()==TRUE){
			//success! search the talks and events
			
			$start_mo=$this->input->post('start_mo');
			$end_mo=$this->input->post('end_mo');			
			if(!empty($start_mo)){
				$start=mktime(
					0,0,0,
					$this->input->post('start_mo'),
					$this->input->post('start_day'),
					$this->input->post('start_yr')
				);
			}else{ $start=0; }
			if(!empty($end_mo)){
				$end=mktime(
					23,59,59,
					$this->input->post('end_mo'),
					$this->input->post('end_day'),
					$this->input->post('end_yr')
				);
			}else{ $end=0; }
			echo $start.' - '.$end;
			
			//check to see if they entered a date and set that first
			$results=array(
				'talks'	=> $this->talks_model->search($this->input->post('search_term'),$start,$end),
				'events'=> $this->event_model->search($this->input->post('search_term'),$start,$end)
			);
		}
		
		$this->template->write_view('content','search/main',array('results'=>$results),TRUE);
		$this->template->render();
	}

}
	
?>