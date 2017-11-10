/* KRY CHANTO
 * for checking check box have label all
 * if check all 
 *sub element of checkbox all will check auto
 *if user check all return value -1
 * 
 */
//only number ric
function checkAll(len)
{
	var chk=document.getElementById("division_0").checked;
	for(var i=0;i<=len;i++)
		document.getElementById("division_"+i).checked = chk;
					
}
/*
 * 
 * for getting value from user select radio list if 
 * if select return value id in list
 * else return -1 mean user non-select in list
 */
function listSelect(){
	
	var count_rdl=document.frmlist.rdl.length;	
	var id=-1;
	if(typeof count_rdl  == "undefined"){
	    if(document.frmlist.rdl.checked)
			id=document.frmlist.rdl.value;
	}
	else
	for(var i=0;i<count_rdl;i++)
		if(document.frmlist.rdl[i].checked)
		{	
			id=document.frmlist.rdl[i].value;		
			break;
		}					
	return id;
}
/*
 * 
 * for checking user select radio list or not
 * if select confirm message 
 * else alert message 
 */

function confirmDelete(url)
{
	var id=listSelect();
	if(id==-1)
		alert('non-select element');	
	else
		if(confirm('do you want to delete id='+id+'?')){			
			window.location.replace(url+'/id/'+id);			
		}
}
/*
 * 
 * for checking user select radio list or not
 * if select link to page edit
 * else alert message
 */

function confirmEdit(url)
{
	var id=listSelect();
	if(id==-1)
		alert('non-select element');		
	else					
		window.location.replace(url+'/id/'+id);			
		
}
/*
 * 
 */
function confirmUrl(url)
{
					
	window.location.replace(url);			
		
}
function confirms(url)
{
	if(confirm('do you want to delete?')){			
		window.location.replace(url);			
	}
}
function redirector(url)
{
				
	window.location.replace(url);			
	
}
/*
 * 
 * 
*/
function toggleview(element1) {  
	  
	   element1 = document.getElementById(element1);  
	  
	   if (element1.style.display == 'block' || element1.style.display == '')  
	      element1.style.display = 'none';  
	   else  
	      element1.style.display = 'block';  	
	   return;  
}

/*
 *Format number
 * 
*/
function deleteCommas(nStr)
{
	x = nStr.split('.');
	nStr = x[1] == 0 ? x[0] : nStr;
	for(var i=0;i<10;i++)
		nStr=nStr.replace(',','');
	return nStr;
}
function addCommas(nStr)//addCommas(nStr,t=0)
{
	nStr = parseFloat(nStr).toFixed(2);
	nStr += '';
	x = nStr.split('.');
	x1 = x[0];
	x2 = x.length > 1 ? '.' + x[1] : '';
	var rgx = /(\d+)(\d{3})/;
	while (rgx.test(x1)) {
		x1 = x1.replace(rgx, '$1' + ',' + '$2');
	}
	return x1 + x2;
}
/* format number in khmer reil
 * 
 */
function deleteSpaces(nStr)
{
	
	for(var i=0;i<10;i++)
		nStr=nStr.replace('  ','');
	return nStr;
}
function addSpaces(nStr)
{
	nStr += '';
	x = nStr.split('.');
	x1 = x[0];
	x2 = x.length > 1 ? '.' + x[1] : '';
	var rgx = /(\d+)(\d{3})/;
	while (rgx.test(x1)) {
		x1 = x1.replace(rgx, '$1' + '  ' + '$2');
	}
	return x1 + x2;
}
/*
 * 	Display value
*/
function display(id,value)
{
	document.getElementById(id).value=value;
	document.getElementById(id).innerHTML=value;
}
function replaceAll(Source,stringToFind,stringToReplace){

	  var temp = Source;

	    var index = temp.indexOf(stringToFind);

	        while(index != -1){

	            temp = temp.replace(stringToFind,stringToReplace);

	            index = temp.indexOf(stringToFind);

	        }
	        return temp;
}
/*
 * set focus control
 */
function textfocus(text)
{
	var val=document.getElementById(text).value;
	if(val=='' || val==null)
	{
		var currentdate_date= new Date();
		var month=currentdate_date.getMonth()+1;
		var currentdate=currentdate_date.getDate();
		var year=currentdate_date.getFullYear();
		var cur_date=addZeroBefore(currentdate)+"-"+addZeroBefore(month)+"-"+year;
		document.getElementById(text).value=cur_date;
	}	
	//alert('hello');
}

/*
 * set focus control
 * @author: BUN Seak Leang
 */
function textFocusSpace(text)
{
	var val=document.getElementById(text).value;
	if(val=='' || val==null)
	{
		document.getElementById(text).value='00-00-0000';
	}	
	//alert('hello');
}

function addZeroBefore(value)
{
	if(value<10) return '0'+value;
	return value;
}

function loadNewWindow(url) {
	window.open(url,'','scrollbars=no,menubar=no,addressbar=no,height=600,width=800,resizable=yes,toolbar=no,location=no,status=no');
}
/**
 * 
 * @param id get value from control
 * @param param assign value from control to param
 * @param url recove url
 * template param=id
 * return value |param=id
 */
function reportParamClient(id)
{	
	var value=document.getElementById(id).value;
	if(value=='') value=null;
	var template='|'+id+'='+value;
	//var template=param+'='+value;
	var url=window.location.href;	
	var arr=url.split("|");	
	var feed='';	
	if(arr.length != 1)
	{
		var b=false;		
		for(var i=0;i<arr.length;i++)
		{
			var index=arr[i].indexOf(id);
			if(index != -1){
				var temp1=arr[i].split("=");
				var temp2=arr[i];
				arr[i]=temp1[0]+"="+value;
				url=url.replace(temp2, arr[i]);
				b=true;
			}//if index != -1			
		}//supply for i=0 to arr.length
		if(b) feed=url;
		else{
			feed=url+template;
		}//if not b 	
	}//if arr is array
	else{	
		feed=url+'#'+template;
	}//arr is not array	
	return feed;
}
function urlParamClient(id)
{	
	url=window.location.href;
	//url=replaceParam(param,url);	
	url=reportParamClient(id);	
	window.location.href=url;	
}
/**
 * @param paramadd for add paramter if necessary default = ''
 * for replace string | to /, or #| to /
 * template param=id
 * return /param/id
 */
function reportParamServer(param_add)
{
	var url=window.location.href;	
//	url=url.replace('#|', '/');	
//	url=replaceAll(url,"|","/");
//	url=replaceAll(url,"=","/");
	url=url.replace('#', '/report_param/');
	var index=url.indexOf(param_add);
	if(index == -1)
		return url+param_add;
	else return url;
}
function urlParamServer(param_add)
{	
	window.location.href=reportParamServer(param_add);
}
//---------------------------------------------------
/*function urlChange(){
var value=document.getElementById('start_date').value+'#report';
var url=window.location.href;
var index=url.indexOf('date_select');
if(index != -1 ) url = url.substr(0,index-1) + '/date_select/' + value;
else url += '/process/view/date_select/'+value;
if(value != '' )										
	window.location.href = url;		
}*/		
/**
 * @param param
 */
//JavaScript Document
/*function read_money(value)
{
		return value;
}*/
//JavaScript Document
/*function read_money(value)
{
		return value;
}*/
function read_in_khmer_from_0_to_9(value)
{
	var read='';
	var calc=value;
	if(calc<10){
		if(calc==0) read='';
		else if(calc==1) read='មួយ';
		else if(calc==2) read='ពីរ';
		else if(calc==3) read='បី';
		else if(calc==4) read='បួន';
		else if(calc==5) read='ប្រាំ';
		else if(calc==6) read='ប្រាំមួយ';
		else if(calc==7) read='ប្រាំពីរ';
		else if(calc==8) read='ប្រាំបី';
		else if(calc==9) read='ប្រាំបួន';
	}
	return read;	
}
function read_in_khmer_from_10_to_99(value)
{
	var calc=value;
	var read='';
	if(calc>=10 && calc<100){
		if(calc==10) read='ដប់';
		else if(calc==11) read='ដប់មួយ';
		else if(calc==12) read='ដប់ពីរ';
		else if(calc==13) read='ដបបី់';
		else if(calc==14) read='ដប់បួន';
		else if(calc==15) read='ដប់ប្រាំ';
		else if(calc==16) read='ដប់ប្រាំមួយ';
		else if(calc==17) read='ដប់ប្រាំពីរ';
		else if(calc==18) read='ដប់ប្រាំបី';
		else if(calc==19) read='ដប់ប្រាំបួន';		
		else if(calc>=20 && calc<30) read='ម្ភៃ';
		else if(calc>=30 && calc<40) read='សាមសិប';
		else if(calc>=40 && calc<50) read='សែសិប';
		else if(calc>=50 && calc<60) read='ហាសិប';
		else if(calc>=60 && calc<70) read='ហុកសិប';
		else if(calc>=70 && calc<80) read='ជិតសិប';
		else if(calc>=80 && calc<90) read='ប៉ែតសិប';		
		else if(calc>=90 && calc<100) read='កៅសិប';
	}
	return read;
}
function checkZero(value)
{	
	if(value==0) return 0;
	
	for(var i=0;i<10;i++)
	{
		if(value.substr(i,1)!=0){
			return value.substr(i);
		}
		//value=value.replace(/^0*/, "");	
	}
	return value;
}
function read_in_khmer_by_len(value)
{
	var len=value.length;
	var read='';
	if(len==1) read=read_in_khmer_from_0_to_9(value);
	else if(len==2) read=read_in_khmer_from_10_to_99(value);
	else if(len==3) read='រយ';
	else if(len==4) read='ពាន់';
	else if(len==5 || len==6) read='ពាន់';
	else if(len>=7 && len<10) read='លាន';
	else if(len>=10 && len<=12) read='កោត';
	else if(len>=13) read='កោត';
	return read;
}
function read_number_in_khmer_by_len_1_to_5(value)
{
	value=value.replace(/^\s+|\s+$/g, '');
	value=checkZero(value);
	var len=value.length;
	if(len<=5)
	{
		if(value<20){			
			return read_in_khmer_by_len(value);		
		}//1
		else if(value>=20 && value<100){
			return read_in_khmer_by_len(value)+read_in_khmer_by_len(value.substr(1,1))+'';		
		}//2
		else{
			var	calc=value;
			var read='';				
			var amount='',number='';				
			var b=false;
			for(var i=0;i<value.length;i++){		
				var n=0;
				var reader='';
				if(calc<20){					
					reader=read_in_khmer_by_len(calc);								
					b=true;
				}//if calc<20
				else if(calc>=20 && calc<100){					
					reader=read_in_khmer_by_len(calc)+read_in_khmer_by_len(calc.substr(1,1))+'';		
					b=true;
				}//if calc>=20 and <100
				else{				
					var len=calc.length;
					if(len==3 || len==4){
						number=read_in_khmer_by_len(calc.substr(0,1));
						amount=read_in_khmer_by_len(calc);								
						reader=number+amount+'';
					}//if len=[3,4]
					else if(len==5){
						number=read_in_khmer_by_len(calc.substr(0,calc.length-3));
						amount=read_in_khmer_by_len(calc);			
						n=calc.substr(0,calc.length-3).length-1;
						i=n;
						reader=number+amount+'';
					}//len=[5,6]				
				}//if calc>=100						
				read=read+reader+'';
				if(b) break; 
				else calc=value.substr(i+1);
				calc=checkZero(calc);
			}//3
			return read;
		}//for
	}//len<=5
}//1 to 5
function reverst(read)
{
	var alr=read.split('#');
	var result='';
	for(var i=alr.length-1;i>=0;i--) result=result+alr[i]+'  ';
	return result;
}
function read_money_in_khmer(value)
{
	value=value.replace(/^\s+|\s+$/g, '');
	value=checkZero(value);
	var val=value;
	val=addCommas(val);	
	var dval =val.split('.');
	var extract=dval[0].split(',');
	var read='';
	var amount='';	
	var len=extract.length;
	for(var i=0;i<extract.length;i++){				
		var temp=read_number_in_khmer_by_len_1_to_5(extract[i]);				
		
			switch(len){			
				case 1:
					if(temp==undefined){ amount=''; break;}
					amount=temp;
					break;
				case 2:
					if(temp==undefined){ amount=''; break;}
					amount=temp + 'ពាន់';
					break;
				case 3:
					if(temp==undefined){ amount=''; break;}
					amount=temp + 'លាន';
					break;
				case 4:
					if(temp==undefined){ amount=''; break;}					
					amount=temp + 'កោត';					
					break;					
				default:
					return '-1';	
					break;
			}		
		len=len-1;
		read=amount+'#'+read;		
	}
	//return read;		
	return reverst(read);	
}

function convertEnglishToKhmerNumber(num) {
    var str = '';        
    for (var i = 0; i < num.length; i++) {
        str = str + convertEngToKh(num.charAt(i));
    }
    return str;
}
function convertEngToKh(num) {	    
	if (num == '.') return '.';
	if( num == ' ') return ' ';
	else if (num == ',') return ',';	
	else if (num == '0') return '០';
    else if (num == '1') return '១';
    else if (num == '2') return '២';
    else if (num == '3') return '៣';
    else if (num == '4') return '៤';
    else if (num == '5') return '៥';
    else if (num == '6') return '៦';
    else if (num == '7') return '៧';
    else if (num == '8') return '៨';
    else if (num == '9') return '៩';	     
}
function convertKhmerNumberToEnglish(num) {
    var str = '';        
    for (var i = 0; i < num.length; i++) {    	
        str = str + converter(num.charAt(i));
    }
    return str;
}
function convertKhToEng(kh_num) {
	var num=kh_num;
	if (num == '.') return '.';
	if (num == ',') return ',';		
	else if (num == '០') return '0' ;
    else if (num == '១') return '1';
    else if (num == '២') return '2';
    else if (num == '៣') return '3';
    else if (num == '៤') return '4';
    else if (num == '៥') return '5';
    else if (num == '៦') return '6';
    else if (num == '៧') return '7';
    else if (num == '៨') return '8';
    else if (num == '៩') return '9';	    
	return 'val';
}
function converter(kh_num) {
	var num=kh_num;
	if (num == '.') return '.';
	if (num == ',') return ',';	
	else if (num == '០') return '0' ;
    else if (num == '១') return '1';
    else if (num == '២') return '2';
    else if (num == '៣') return '3';
    else if (num == '៤') return '4';
    else if (num == '៥') return '5';
    else if (num == '៦') return '6';
    else if (num == '៧') return '7';
    else if (num == '៨') return '8';
    else if (num == '៩') return '9';	    
    else if (num == 'ខ') return 'x';
    else if (num == '៏') return '*';
    else if (num == '។') return '.';
	return kh_num;
}
/*function replaceParam(param,url)
{
	//var url=window.location.href;
	//query of # sign 
	var sap=url.indexOf("#|");			
	var index=url.indexOf("/"+param+"/");	
	if(index == -1) return url;	
	else{
		if(sap == -1){
			url=url.replace("/"+param+"/","#|"+param+"=");
		}//if sap = -1
		else{
			url=url.replace("/"+param+"/","/"+param+"/");
		}
	}
	return url;
}*/
//---------------------------------------------------

function onlyNumber(id)
{
	regex = /^[0-9]{1}$/;
	var val=document.getElementById(id).value;
	var temp=convertKhToEng(val);
	if(temp != 'val'){
		document.getElementById(id).value=temp;
		val=temp;
	}
	if(!val.match(regex) && val != ''){
		document.getElementById(id).value='';		
	}	
	
}

//format currency
function convertCurrencyKH(id,currency){
	var val=$('#'+id).val();
	if(val!=undefined){
	    val=deleteCommas(val);			    
	    val=deleteSpaces(val);
		val=val.replace(/^\s+|\s+$/g, '');
		if(isNaN(val)) val=0;
		if(currency==77)
			$('#'+id).val(addSpaces(val));
		else
			$('#'+id).val(addCommas(val));
	}
}


//-------------------end calcualte------------------------------------------------
function disableEnterKey(e)
{
     var key;

     if(window.event)
          key = window.event.keyCode;     //IE
     else
          key = e.which;     //firefox

     if(key == 13)
          return false;
     else
          return true;
}
//-------------------min and max row of record-------------------------------------
function maxRow(add,remove,max,index)
{	
	if(index<max){
		$('#'+add).attr('style','');
		$('#'+remove).attr('style','');
		if(index==0){
			$('#'+add).attr('style','');
			$('#'+remove).attr('style','display:none');
		}
	}else{
		$('#'+add).attr('style','display:none');
		$('#'+remove).attr('style','');
	}
}
//-------------------------print report in i frame----------------------------------

function print(id,name) {    	
	var value=parent.document.getElementById(id).src;
	//var ObjectPrint=window.open();
	if(value != '' ){													 			 			 
		window.frames[name].window.focus();
		window.frames[name].window.print();
	}
}
//------------------------ chapter account sub account ------------------------------
//set chapter
function setChapter(chapter,index){
	var temp='<option value="">---Chapter---</option>';
	$('#chapter_'+index).append(temp);
	for(var i=0;i<chapter.obj.length;i++){			
		if(chapter.obj[i].chapter != '')	
			$('#chapter_'+index).append('<option value="'+chapter.obj[i].chapter+'">'+chapter.obj[i].chapter+'</option>');
	}
}
//set account
function setAccount(chapter,index){
	$('#account_'+index +' option').each(function(i, option){ $(option).remove(); });
	var temp='<option value="">---Account---</option>';
	$('#account_'+index).append(temp);
	var val=$('#chapter_'+index).val();		
	for(var i=0;i<chapter.obj.length;i++){			
		if(chapter.obj[i].account != ''){
			if(chapter.obj[i].account.substr(0,val.length) == val)	
				$('#account_'+index).append('<option value="'+chapter.obj[i].account+'">'+chapter.obj[i].account+'</option>');
		}
	}
}
//set sub account
function setSubAccount(chapter,index){
	$('#subaccount_'+index +' option').each(function(i, option){ $(option).remove(); });
	var temp='<option value="">---Sub Account---</option>';
	$('#subaccount_'+index).append(temp);
	var val=$('#account_'+index).val();		
	for(var i=0;i<chapter.obj.length;i++){			
		if(chapter.obj[i].sub_account != ''){
			if(chapter.obj[i].sub_account.substr(0,val.length) == val)	
				$('#subaccount_'+index).append('<option value="'+chapter.obj[i].sub_account+'">'+chapter.obj[i].sub_account+'</option>');
		}
	}
}
//set allocation
function setAllocation(allocation,index){		
	$('#fund_allocation_'+index +' option').each(function(i, option){ $(option).remove(); });
	var temp='<option value="">---Select---</option>';
	$('#fund_allocation_'+index).append(temp);														
	for(var i=0;i<allocation.obj.length;i++){
		var b=false;
		for(var j=1;j<=index;j++){
			if(allocation.obj[i].allocation_id==$('#fund_allocation_'+j).val()) b=true;
		}
		if(b==false)																					
			$('#fund_allocation_'+index).append('<option value="'+allocation.obj[i].allocation_id+'">'+allocation.obj[i].full_name+'</option>');
									
	}		 
}
//set financier
function setFinancier(financier,index1,index2){		
	$('#financier_'+index1+'_'+index2+' option').each(function(i, option){ $(option).remove(); });
	var temp='<option value="">---Select---</option>';
	$('#financier_'+index1+'_'+index2).append(temp);														
	for(var i=0;i<financier.obj.length;i++){
		var b=false;
		for(var j=1;j<=20;j++){
			if(financier.obj[i].financier_id==$('#financier_'+index1+'_'+j).val()) b=true;
		}
		if(b==false)																					
			$('#financier_'+index1+'_'+index2).append('<option value="'+financier.obj[i].financier_id+'">'+financier.obj[i].financier_name+'</option>');
									
	}		 
}
//for sorting in json data
function sortJson() {
	   
}
					
//-----------------------------------------------set option groupt--------------------------------------------------------
/*function getOptionGroup(data){
	var opt='<option value="">--------------Select-----------------</option>';		
	if(data){
		var tt=data.obj[0].groups;
		opt+='<optgroup label="'+data.obj[0].groups+'">';						
		for(var i=0;i<data.obj.length;i++){				
			if(tt==data.obj[i].groups){
				opt+='<option value="'+data.obj[i].value+'" >'+data.obj[i].label+'</option>';
			}else{
				opt+='</optgroup>';
				opt+='<optgroup label="'+data.obj[i].groups+'">';
			}
			tt=data.obj[i].groups;
		}
	}
	return opt+'</optgroup>';
}*/
function getOptionGroup(data){
	var opt='<option value="">--------------Select-----------------</option>';
	var grparr = new Array();
	if(data){
		for(var i=0;i<data.obj.length;i++){
			if(jQuery.inArray(data.obj[i].groups,grparr)==-1){
				grparr.push(data.obj[i].groups);				
				opt+='<optgroup label="'+data.obj[i].groups+'">';														
				for(var j=0;j<data.obj.length;j++){
					if(data.obj[i].groups == data.obj[j].groups){
						opt+='<option value="'+data.obj[j].value+'" >'+data.obj[j].label+'</option>';
					}
				}
				opt+='</optgroup>';
			}
		}
	}
	return opt;
}


function getOptionGroupPod(data){
	var opt='<option value="">------ជ្រើសរើសលេខគណនី-------</option>';	
	var grparr = new Array();
	if(data){
		for(var i=0;i<data.obj.length;i++){
			if(jQuery.inArray(data.obj[i].groups,grparr)==-1){
				grparr.push(data.obj[i].groups);				
				opt+='<optgroup label="'+data.obj[i].groups+'">';														
				for(var j=0;j<data.obj.length;j++){
					if(data.obj[i].groups == data.obj[j].groups){
						opt+='<option value="'+data.obj[j].value+'" >'+data.obj[j].label+'</option>';
					}
				}
				opt+='</optgroup>';
			}
		}
	}
	return opt;
}

function setPayeeType(payee,type_in){
	var opt='';		
	if(payee){
		var tt=type_in;
		opt+='<optgroup label="'+type_in+'">';						
		for(var i=0;i<payee.obj.length;i++){				
			if(tt==payee.obj[i].payee_type){
				opt+='<option value="'+payee.obj[i].payee_no+'" >'+payee.obj[i].payee_name+'</option>';
			}
		}
	}
	return opt+'</optgroup>';
}
function setPayeePod(payee,type_in){
	var opt='';		
	if(payee){
		var tt=type_in;
		opt+='<optgroup label="'+type_in+'">';						
		for(var i=0;i<payee.obj.length;i++){				
			if(tt==payee.obj[i].payee_type){
				opt+='<option value="'+payee.obj[i].payee_no+'" >'+payee.obj[i].payee_name_khmer+'</option>';
			}
		}
	}
	return opt+'</optgroup>';
}
//get sql statement
function getSqlStatement(field)
{
	var table=field[0].table;
	var select='';
	for(var i=1;i<field.length;i++){
    	if(i!=field.length-1)select+='`'+field[i].column+'`,';
        else select+='`'+field[i].column+'`';
	}
	return "SELECT "+select+" FROM "+table;
}
//get header of field
function getHeader(field){
	var head='<thead>';
	for(var i=1;i<field.length;i++){            	
		head+='<td class="query-header">'+field[i].column+'</td>';            	
	}
	return head+'</thead>';
}
//------------remove array json------------------
function findAndRemove(array, property, value) {
	   $.each(array, function(index, result) {
	      if(result[property] == value) {
	          //Remove from array
	          array.splice(index, 1);
	      }    
	   });
}
//------------set vehicle-------------------------------
function setVehicle(id,vehicle,type){		
	$('#'+id+' option').each(function(i, option){ $(option).remove(); });
	var temp='<option value="">---Select---</option>';
	$('#'+id).append(temp);
	if(type==1){
		for(var i=0;i<vehicle.types.length;i++){																						
			$('#'+id).append('<option value="'+vehicle.types[i].types+'">'+vehicle.types[i].types+'</option>');									
		}		
	}
	else if(type==2){
		for(var i=0;i<vehicle.sub_types.length;i++){																		
			$('#'+id).append('<option value="'+vehicle.sub_types[i].sub_types+'">'+vehicle.sub_types[i].sub_types+'</option>');									
		}
	}
	else if(type==3){
		for(var i=0;i<vehicle.maker.length;i++){																						
			$('#'+id).append('<option value="'+vehicle.maker[i].maker+'">'+vehicle.maker[i].maker+'</option>');									
		}
	}
}

//sopharat 2014 03 20
//-----------------------------convert currency number to word--------------------------
//-------------English -----------
function read_in_english_from_0_to_9(value)
{
	var read='';
	var calc=value;
	if(calc<10){
		if(calc==0) read='';
		else if(calc==1) read=' One';
		else if(calc==2) read=' Two';
		else if(calc==3) read=' Three';
		else if(calc==4) read=' Four';
		else if(calc==5) read=' Five';
		else if(calc==6) read=' Six';
		else if(calc==7) read=' Seven';
		else if(calc==8) read=' Eight';
		else if(calc==9) read=' Nine';
	}
	return read;	
}
function read_in_english_from_10_to_99(value)
{
	var calc=value;
	var read='';
	if(calc>=10 && calc<100){
		if(calc==10) read=' Ten';
		else if(calc==11) read=' Eleven';
		else if(calc==12) read=' Twelve';
		else if(calc==13) read=' Thirteen';
		else if(calc==14) read=' Fourteen';
		else if(calc==15) read=' Fifteen';
		else if(calc==16) read=' Sixteen';
		else if(calc==17) read=' Seventeen';
		else if(calc==18) read=' Eighteen';
		else if(calc==19) read=' Nineteen';		
		else if(calc>=20 && calc<30) read=' Twenty';
		else if(calc>=30 && calc<40) read=' Thirty';
		else if(calc>=40 && calc<50) read=' Fourty';
		else if(calc>=50 && calc<60) read=' Fifty';
		else if(calc>=60 && calc<70) read=' Sixty';
		else if(calc>=70 && calc<80) read=' Seventy';
		else if(calc>=80 && calc<90) read=' Eighty';		
		else if(calc>=90 && calc<100) read=' Ninety';
	}
	return read;
}
function read_in_english_by_len(value)
{
	var len=value.length;
	var read='';
	if(len==1) read=read_in_english_from_0_to_9(value);
	else if(len==2) read=read_in_english_from_10_to_99(value);
	else if(len==3) read=' Hundred';
	else if(len==4) read=' Thousand';
	else if(len==5 || len==6) read=' Thousand';
	else if(len>=7 && len<10) read=' Million';
	else if(len>=10 && len<=12) read=' Billion';
	else if(len>=13) read=' Billion';
	return read;
}
function read_number_in_english_by_len_1_to_5(value)
{
	value=value.replace(/^\s+|\s+$/g, '');
	value=checkZero(value);
	var len=value.length;
	if(len<=5)
	{
		if(value<20){			
			return read_in_english_by_len(value);		
		}//1
		else if(value>=20 && value<100){
			return read_in_english_by_len(value)+read_in_english_by_len(value.substr(1,1))+'';		
		}//2
		else{
			var	calc=value;
			var read='';				
			var amount='',number='';				
			var b=false;
			for(var i=0;i<value.length;i++){		
				var n=0;
				var reader='';
				if(calc<20){					
					reader=read_in_english_by_len(calc);								
					b=true;
				}//if calc<20
				else if(calc>=20 && calc<100){					
					reader=read_in_english_by_len(calc)+read_in_english_by_len(calc.substr(1,1))+'';		
					b=true;
				}//if calc>=20 and <100
				else{				
					var len=calc.length;
					if(len==3 || len==4){
						number=read_in_english_by_len(calc.substr(0,1));
						amount=read_in_english_by_len(calc);								
						reader=number+amount+'';
					}//if len=[3,4]
					else if(len==5){
						number=read_in_english_by_len(calc.substr(0,calc.length-3));
						amount=read_in_english_by_len(calc);			
						n=calc.substr(0,calc.length-3).length-1;
						i=n;
						reader=number+amount+'';
					}//len=[5,6]				
				}//if calc>=100						
				read=read+reader+'';
				if(b) break; 
				else calc=value.substr(i+1);
				calc=checkZero(calc);
			}//3
			return read;
		}//for
	}//len<=5
}//1 to 5
function read_money_in_english(value)
{
	value=value.replace(/^\s+|\s+$/g, '');
	value=checkZero(value);
	var val=value;
	val=addCommas(val);	
	var dval =val.split('.');
	var extract=dval[0].split(',');
	var read='';
	var amount='';	
	var len=extract.length;

	for(var i=0;i<extract.length;i++){				
		var temp=read_number_in_english_by_len_1_to_5(extract[i]);		
			switch(len){			
				case 1:
					if(temp==undefined){ amount=''; break;}
					amount=temp;
					break;
				case 2:
					if(temp==undefined){ amount=''; break;}
					amount=temp + ' Thousand';
					break;
				case 3:
					if(temp==undefined){ amount=''; break;}
					amount=temp + ' Million';
					break;
				case 4:
					if(temp==undefined){ amount=''; break;}					
					amount=temp + ' Billion';					
					break;					
				default:
					return '-1';	
					break;
			}		
		len=len-1;
		read=amount+'#'+read;		
	}
	//return read;		
	return reverst(read);	
}