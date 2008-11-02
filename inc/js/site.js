function setVote(rate){
	$("input[name='rating']").val(rate);
	$.each($("img[id^='rate_']"),function(k,v){
		mk=k+1;
		if(mk==rate){ 
			v.style.border='1px solid #000000';
		}else{ v.style.border='0px'; }
	});
	return false;
}
function toggleAnon(tid){
	lnk=$('#anonLink').html();
	$.each($("tr[id^='com_anon_"+tid+"']"),function(k,v){
		if(v.style.display=='none'){
			v.style.display='block';
			$('#anonLink').html(lnk.replace(/show/,'hide'));
		}else{
			v.style.display='none';			
			$('#anonLink').html(lnk.replace(/hide/,'show'));
		}
	});
}