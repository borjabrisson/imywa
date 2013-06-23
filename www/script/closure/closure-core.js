//This is for all global functions declared in frmx/form.js

function select_item(){  
  var row_selected = document.getElementsByClassName("ia_selected_box");
  //¿Que frame? se busca en sus padres .ia_frame y se toma su id
  //alert(row_selected);
    if (row_selected.length != 0){    
		return row_selected[0].id;
// 		addhidden("selected",row_selected[0].id);//row_selected.id);
   // alert(row_selected[0].id);
  }
  else
	  return undefined;
  
};


function frameAction(action,form,lookup){
	

	var values = {};
	if (form != "ALLFRAME"){
		var $inputs = $('form[name="form_'+form+'"] :input');//.filter('input[type="text"]');
		if (lookup != undefined) values["lookup"] = lookup;
		$inputs.each(function() {
			switch (this.type){
				case "checkbox":
	// 				alert(this.checked);
					values[this.name] = this.checked;
					break;
				case "file":
					values[this.name] = this.files[0];
					break;
				case "select-one":
					values[this.name] = this.value;
				break;
				case "textarea":
				case "password":
				case "text":
					values[this.name] = this.value;
					break;			
			}
		});
	}
	else{ // debemos recorrer todos los formularios con cla clase ia_Form
		var $forms = $(".ia_Form");
		$forms.each(function(){
			for (var elem=0;elem<this.length;elem++){
				switch (this[elem].type){
					case "checkbox":
		// 				alert(this.checked);
						values[this[elem].name] = this[elem].checked;
						break;
					case "file":
						values[this[elem].name] = this[elem].files[0];
						break;
					case "select-one":
						values[this[elem].name] = this[elem].value;
					break;
					case "password":
					case "text":
						values[this[elem].name] = this[elem].value;
						break;			
				}
			}

		});
		
	}
	currentForm.sendAction(action,values); 
}

function ajaxaction(action,params){ 
	currentForm.sendAction(action,params); 
};
	


function submitlookup(idForm,lookupForm){
	
	if ($(".ia_filterbox").length == 0){
		frameAction("lookup",idForm,lookupForm);
		if (console != undefined) console.log('lookup.NO.dialog');
		
	} else {
		var dialogId = $(".ia_filterbox").attr("id");
		currentForm.dialogAction(dialogId, 'lookup', {lookup: lookupForm});
		if (console != undefined) console.log('lookup.dialog');
	}
	
};



function addhidden(hidname, hidvalue, hidform){
	var newinput = document.createElement("input");
	
	newinput.setAttribute("type", "hidden");
	newinput.setAttribute("name", hidname);
	newinput.setAttribute("value", hidvalue);
	if (hidform == undefined)	document.forms["form_"+hidvalue].appendChild(newinput);
	else	document.forms["form_"+hidform].appendChild(newinput);
};

function submitaction(action){
	addhidden('action', action);
	var item = select_item();
	if (item != undefined) addhidden('selected', item,action);
	document.forms["form_"+action].submit();
	
};

