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
		$this->load->helper('reqkey');
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
			
			$query = 'q:'.urlencode($this->input->post('search_term'));
			echo 'query: '.$query;
			
			$start_mo=$this->input->post('start_mo');
			$end_mo=$this->input->post('end_mo');			
			if(!empty($start_mo)){
				/*$start=mktime(
					0,0,0,
					$this->input->post('start_mo'),
					$this->input->post('start_day'),
					$this->input->post('start_yr')
				);*/
				$start = sprintf(
				    '%04d-%02d-%02d',
				    $this->input->post('start_yr'),
				    $this->input->post('start_mo'),
				    $this->input->post('start_day')
				);
				$query .= '/start:' . $start;
			}else{ $start=0; }
			if(!empty($end_mo)){
				/*$end=mktime(
					23,59,59,
					$this->input->post('end_mo'),
					$this->input->post('end_day'),
					$this->input->post('end_yr')
				);*/
			    $end = sprintf(
				    '%04d-%02d-%02d',
				    $this->input->post('end_yr'),
				    $this->input->post('end_mo'),
				    $this->input->post('end_day')
				);
				$query .= '/end:' . $end;
			}else{ $end=0; }
			//echo $start.' - '.$end;

			redirect('search/' . $query, 'location', 302);
		}

		$results = null;
		
		$rsegments = $this->uri->rsegments;
		array_shift($rsegments); // Remove controller
		array_shift($rsegments); // Remove action
		
		if (count($rsegments) > 0) {
    		$rsegments = array_slice($rsegments, 0, 3);

    		$search_term = null;
    		$start = null;
    		$end = null;
    		
    		foreach ($rsegments as $val) {
    		    if (false !== ($pos = strpos($val, 'q:'))) {
    		        $search_term = substr($val, 2);
    		        continue;
    		    }
    		    if (false !== ($pos = strpos($val, 'start:'))) {
    		        $start = substr($val, 6);
    		        continue;
    		    }
    		    if (false !== ($pos = strpos($val, 'end:'))) {
    		        $end = substr($val, 4);
    		        continue;
    		    }
    		}
    
    		if (!empty($search_term)) {
    		    $this->validation->search_term = urldecode($search_term);

    		    if (null !== $start) {
    		        $start = max(0, @strtotime($start));

    		        $this->validation->start_mo = date('m', $start);
    		        $this->validation->start_day = date('d', $start);
    		        $this->validation->start_yr = date('Y', $start);
    		    }
    		    if (null !== $end) {
    		        $end = max(0, @strtotime($end));
    		        
    		        $this->validation->end_mo = date('m', $end);
    		        $this->validation->end_day = date('d', $end);
    		        $this->validation->end_yr = date('Y', $end);
    		    }
    
    		    //check to see if they entered a date and set that first
    			$results = array(
    				'talks'	=> $this->talks_model->search($search_term, $start, $end),
    				'events'=> $this->event_model->search($search_term, $start, $end),
				'users'	=> $this->user_model->search($search_term, $start, $end)
    			);
    		}
		}
		
		$reqkey = buildReqKey();
		
		$arr=array(
			'results'=>$results,
		    'reqkey' => $reqkey,
			'seckey' => buildSecFile($reqkey)
		);	
		
		$this->template->write_view('content','search/main',$arr,TRUE);
		$this->template->render();
	}

}
	
?>