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
//-------------------------
function apiRequest(rtype,raction,data){
	var xml_str='';
	$.each(data,function(k,v){
		xml_str+='<'+k+'>'+v+'</'+k+'>';
	});
	xml_str='<request><action type="'+raction+'" output="json">'+xml_str+'</action></request>';
	gt_url="/api/"+rtype+'?reqk='+reqk+'&seck='+seck;
	
	$.ajax({
		type: "POST",
		url	: gt_url,
		data: xml_str,
		contentType: "text/xml",
		processData: false,
		success: function(rdata){
			//alert(rdata);
			obj=eval('('+rdata+')'); //alert(obj.msg);
			
			//check for the redirect
			if(obj.msg.match('redirect:')){
				goto=obj.msg.replace(/redirect:/,'');
				document.location.href=goto;
			}else{
				//maybe add some callback method here 
				//alert('normal'); 
			}
		}
		
	});
}
//-------------------------


function markAttending(eid,showt){
	var obj=new Object();
	obj.eid=eid;
	apiRequest('event','attend',obj);
	switch(showt){
		case 1: link_txt='Were you there?'; break;
		case 2: link_txt='Will you be there?'; break;
		case 3: link_txt='I was there!'; break;
		case 4: link_txt="I'll be there!"; break;
	}
	$('#attend_link').html(link_txt);
	alert('Thanks for letting us know!');
}

