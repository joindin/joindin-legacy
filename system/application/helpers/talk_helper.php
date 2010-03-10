<?php

function buildClaimData($talk_detail,$talk_claims,&$ftalk){
	$speaker=array();
	foreach($talk_claims as $k=>$claim){
		// Be sure we're only looking at the ones we need
		if($claim->rid!=$talk_detail->ID){ continue; }else{ $ftalk++; }

		// Get the claim code
		$cd=$claim->rcode;

		// Break up the speakers
		$sp=explode(',',$claim->tdata['speaker']);

		// Now, check to see if any of the codes match the $cd
		$ct=0;
		$matched=array();
		foreach($claim->tdata['codes'] as $ck=>$claim_code){
			// This was so that, if there's one speaker claim, so ahead and link it...
			// seems to have backfired a little
			//$iscl=(count($sp)==1 && count($v->tdata['codes'])==1) ? true : false;
			$iscl=false;
			
		    if($claim_code==$cd || $iscl){
			   $speaker[$sp[$ct]]='<a href="/user/view/'.$claim->uid.'">'.$sp[$ct].'</a>';
		    }else{
				if(!isset($speaker[$sp[$ct]])){ $speaker[$sp[$ct]]=$sp[$ct]; }
		    }
		    $ct++;
		}
	}
	return $speaker;
}

?>