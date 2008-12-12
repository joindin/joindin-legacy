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
function getArea(field){
	obj = document.getElementsByName(field);
	cont= obj[0][obj[0].selectedIndex].value; //alert(cont);
	$.getJSON(
		'/api/tz/'+cont,
		function(data){ //alert(data);
			obj = document.getElementsByName('event_tz_area');
			//clear it out first...
			obj[0].options.length=-1;
			$.each(data,function(k,v){
				//alert(k+' : '+v['area']);
				area=v['area'].replace(/_/,' ');
				obj[0].options[k]=new Option(area,area);
			});
		}
	);
}