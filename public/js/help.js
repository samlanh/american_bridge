/**
 * @author Sovann
 * @date	07/11/2011
 * @time 	9:28:00 AM
 * to help get data to work with JSON data (front End) 
 */

/**
 * Get data form JSON dara to ItemFileWriteStore
 * @param identifier_name
 * @param label_name
 * @param data
 * @returns {JSON data}
 */

function getDatafromJSON(identifier_name,label_name, data ){
	if (data == null){
		var __data = {
				identifier: identifier_name,
		        label: label_name,
		        items: []
			};
	}else{
		var __data = {
				identifier: identifier_name,
		        label: label_name,
		        items: data
			};
	}
		
	return __data;
}

/**
 * get data store form JSON 
 * @param identifier_name
 * @param label_name
 * @param data
 * @returns {dojo.data.ItemFileWriteStore}
 */
function getDataStorefromJSON(identifier_name,label_name, data ){
	if (data == null){
		var __data = {
				identifier: identifier_name,
		        label: label_name,
		        items: []
			};
	}else{
		var __data = {
				identifier: identifier_name,
		        label: label_name,
		        items: data
			}
	}
	var __store = new dojo.data.ItemFileWriteStore({
	       data: __data
	 });
	return __store;
}

/**
 * add new data to data grid view and refresh data store 
 * @param grid
 * @param datastore
 * @param newitem
 */
function addDataToGrid(grid, datastore, newitem){
	datastore.newItem(newitem);
	datastore.save();
	grid.setStore(datastore);	
}

/**
 * add new data to select box and auto select value
 * @param sbid
 * @param datastore
 * @param newitem
 * @param selectd_id
 */
function addDataToSelectbox(sbid, datastore, newitem, selectd_id){
	datastore.newItem(newitem);	
	datastore.save();
	sbid.set('store',datastore);
	if (selectd_id != null){
		sbid.set('value',selectd_id);
	}
}

/**
 * Calculate percent value
 * @param amount
 * @param percent
 * @returns {Number}
 */
function getValueByPercent(amount, percent){
	return amount * percent;
}

/**
 * total - commision, amount and cut service
 * @param amount
 * @param commsion
 * @param sign
 * @param total
 * @param gave
 */
function getTotalAmount(amount, commission, sign, total, gave){	
	tt=0;
	gv=0;
	if (sign){
		tt = amount;
		gv = amount - commission;
	}
	else{
		gv = amount;
		tt = amount + commission;
	}

	total.attr('value',tt);	
	gave.attr('value', gv);	
}

/**
 * Clear text from in input type=text
 * @param name
 */
function clearText(name){
	for(var i=0;i<name.length;i++)
	{
		dijit.byId(name[i]).reset();
	}	
}

//function convert all items to json
function itemToJSON(widget) {
  var jsons = {};  
  var store = widget.get('store');
  var rowcount = widget.rowCount;  
  var k;  
  for(k = 0; k < rowcount; k++){	  	  
	  var item = widget.getItem(k);	  
	  if (item && store) {
	    //Determine the attributes we need to process.
	    var attributes = store.getAttributes(item);	    
	    if (attributes && attributes.length > 0) {
	      var i;
	      var json = {};
	      for (i = 0; i < attributes.length; i++) {    	  
	        var values = store.getValues(item, attributes[i]);
	        if (values) {	          
	           json[attributes[i]] = values[0];
	        }
	      }
	    }
	    jsons[k] = json;
	  }	  
   }  
  return dojo.toJson(jsons);
}


//set result of amont by rate
function convertAmountGetTotal(amount, rate, result, type){
	if (type == 'us'){
		var _help = amount.get('value') * rate.get('value');
	}
	else if(type == 'kh'){
		var _help =  amount.get('value') / rate.get('value');
	}
	result.attr('value',  dojo.number.round(_help));
}

//set amiount of result by rate 
function convertTotalGetAmount(total, rate, result, type){
	if (type == 'us'){
		var _help = total.get('value') / rate.get('value');
	}
	else if(type == 'kh'){
		var _help =  dojo.number.round(total.get('value') * rate.get('value'));
	}
	result.attr('value', _help);
}

//change number sign to unsign
function abs(number){	
	if(number < 0){		
		return number * -1;
	}
	return number;
}

//covert number <0 to add prefix
function numberprefix(number, currency, prefix){
	return prefix + " " + dojo.number.format(abs(number)) + "  " + currency;
}

//format account number formater XXX.X.XXXXX.X
function formatAccountNumber(evt, id){
	// keyCode = 8 is backspace
	if (evt.keyCode !== 8){
		var str = dijit.byId(id).get('value');
		if (str.length == 3 || str.length == 5 || str.length == 11){
			dijit.byId(id).set('value', str + ".");		
		}	
	}
}

//format date * need dojo.require("dojo.date.locale");
function format_date(date){
	var _date = new Date(date + "");	
	str = dojo.date.locale.format(_date, {
        selector: "date",
        datePattern: 'dd/MM/yyyy'
    });	    
    return str;
};