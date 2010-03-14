<?php

class Error extends Controller {
	
	function error_404(){
		$arr=array('msg'=>"404 - File not found");
		$this->template->write_view('content','error/404',$arr);
		$this->template->render();
	}
	
}


?>