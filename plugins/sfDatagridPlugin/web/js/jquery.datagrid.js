function dg_send(form, datagridName, type, url, freeze_after)
{
	
	
    var oForm = $("form#"+form);
    
    switch(type)
    {
        case 'search':
        	dg_hide_show(datagridName);
        	$.ajax({
        		url: url,
        		global: false,
        		type: 'POST',
        		data: (oForm.serialize()),
        		success: function(msg){
        			$('div#'+datagridName).empty().append(msg);
        		 	$('div#loader-' + datagridName).hide();	
        		 	if(freeze_after){
        		 		freezePanes(datagridName);
        		 	}
        		 }
        	});
            break;
        
        case 'reset':
        	clearForm(oForm)
        	url+='&reset=1';
        	dg_send(form, datagridName, 'search', url, freeze_after);
            break;
           
        case 'action':
        	dg_hide_show(datagridName);
        	$.ajax({
        		url: $('#'+datagridName + '_select').val(),
        		global: false,
        		type: 'POST',
        		data: (oForm.serialize()),
        		success: function(msg){
        			$('div#'+datagridName).empty().append(msg);
        		 	$('div#loader-' + datagridName).hide();
        		 	if(freeze_after){
        		 		freezePanes(datagridName);
        		 	}	
        		 }
        	});
           
            
            break;
    }
}

function dg_check_all(chk){
    var checked_status = chk.checked;
    $(chk).parent().parent().parent().find("input.gridline_chk[type='checkbox']").attr('checked',checked_status);
}
function dg_keydown(form, datagridName, type, url, e, freeze_after)
{
    if(e.keyCode == 13)
    {
        dg_send(form, datagridName, type, url, freeze_after);
    }
    
    return false;
}

function dg_hide_show(name)
{    
    if($('div#loader-' + name))
    {
        $('div#loader-' + name).show();
    }
}

function function_exists (function_name) {
    if (typeof function_name == 'string'){
        return (typeof this.window[function_name] == 'function');
    } else{
        return (function_name instanceof Function);
    }
}

/**
@see: http://www.learningjquery.com/2007/08/clearing-form-data
**/
function clearForm(form) {
  // iterate over all of the inputs for the form
  // element that was passed in
  $(':input', form).each(function() {
 var type = this.type;
 var tag = this.tagName.toLowerCase(); // normalize case
 // it's ok to reset the value attr of text inputs,
 // password inputs, and textareas
 if (type == 'text' || type == 'password' || tag == 'textarea')
   this.value = "";
 // checkboxes and radios need to have their checked state cleared
 // but should *not* have their 'value' changed
 else if (type == 'checkbox' || type == 'radio')
   this.checked = false;
 // select elements need to have their 'selectedIndex' property set to -1
 // (this works for both single and multiple select elements)
 else if (tag == 'select')
   this.selectedIndex = -1;
  });
};



