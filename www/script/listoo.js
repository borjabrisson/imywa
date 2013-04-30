/*
 * GESTIÓN DEL FORMULARIO DE LISTA VERSION OBJECT ORIENTED.
 * Implementación de los tamaños, visibilidad y orden de las columnas por identificador.
 */


var downpoint= {x:0, y:0, pos:0};
var uppoint= {x:0, y:0};
var dragging= {status:false, originid:''};

var tabledef= {}; // Sólo hay una tabla => solo hay un wheelObject.
var wheel= {}; // Sólo se utiliza para que funcione el scroll del ratón.

function loadform(config){
	
	tabledef= config;
	createcolstyles();
	adjustmenus();
	adjustform();
	
	//Events
	var formtable = document.getElementById('list_'.concat(tabledef.tableid));
	wheel= new class_wheel('list_'.concat(tabledef.tableid), 100);
	formtable.addEventListener('DOMMouseScroll', wheel);
	//document.getElementById("mouseWheelTest").onmousewheel = wheelme; //For all the browsers except Firefox
	formtable.getElementsByClassName('vscroll')[0].onscroll = vscrollme;
	formtable.getElementsByClassName('hscroll')[0].onscroll = hscrollme;
	window.onresize = adjustform;
	window.onmousedown = mousedownevent;
	window.onmouseup = mouseupevent;
	window.ondragstart = returnfalse;
	window.onselectstart = returnfalse;

}

function class_wheel(scrollBar, maxScroll){

	function handleEvent(event){

		switch(event.axis){
		case 1: //Desplazamiento horizontal. Se copiará del vertical cuando este funcione.
				break;
		case 2:	
				var mynewtop= this.mytop + event.detail*2;
				if (mynewtop<-this.maxScroll && wheelme.mytop>-this.maxScroll) mynewtop=-this.maxScroll;
				if (mynewtop>0 && this.mytop<0) mynewtop=0;
				if (mynewtop >= -this.maxScroll && mynewtop <=0) {
					this.mytop= mynewtop;
					document.getElementById(this.scrollBar).scrollTop =  - this.mytop;
				}
				break;
		}
		this.counter++;
	}

	// CONSTRUCTOR . function wheelClass()
	// Attributes
	this.counter= 0;
	this.mytop= 0;
	this.scrollBar= scrollBar; //mytablevscroll
	this.maxScroll= maxScroll; //440
	// Methods
	this.handleEvent= handleEvent;

}

function returnfalse(){
	return false;
}


function mousedownevent(event){
	//if (event.explicitOriginalTarget.className == 'widthPull'){ **** Esto estaba para FireFox
	if (event.target.className == 'widthPull'){
		var colNode = event.target.parentNode;
		dragging.status = true;
		dragging.originid = colNode.id;
		downpoint.x = event.clientX;
		downpoint.y = event.clientY;
		document.childNodes[0].style.cursor = 'ew-resize';
	}
	return false;
}


function mouseupevent(event){
	if (dragging.status) {
		document.childNodes[0].style.cursor = 'auto';
		uppoint.x = event.clientX;
		uppoint.y = event.clientY;
		dragging.status = false;
		var colNode = document.getElementById(dragging.originid);
		var newwidth = colNode.offsetWidth + uppoint.x - downpoint.x;
		if (newwidth < 16) newwidth = 16;
		colNode.style.width = newwidth;
		adjustandsendcolwidths();
	}
	return false;
}

function sendxhr(data){
	var rq = new XMLHttpRequest();
	rq.open('POST','xhrindex.php', true);
	rq.onreadystatechange = function(aEvt){
		if (rq.readyState == 4 && rq.status == 200) {} //rq.responseText contiene la respuesta.
	}
/* 	------ METODO POST
    ------ Para los nuevos Firefox
 	var data = new FormData();
	data.append('Nombre','Domingo');
	data.append('Apellidos', 'Melián Cárdenes');
	------ Para los antiguos
*/
	
	var post = encodeURI("sessionno=" + document.getElementsByName('sessionno')[0].value
			+ "&installationid=" + document.getElementsByName('installationid')[0].value
			+ "&sequenceno=" + document.getElementsByName('sequenceno')[0].value
			+ "&" + data
			);
	rq.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
//	rq.setRequestHeader("Content-length", post.length); // De esto se queja el Chrome
//	rq.setRequestHeader("Connection", "close"); // Y de esto también.
	
	rq.send(post);
}

function adjustandsendcolwidths(){
	// get the new column widths.
	var coldefs = new Array();
	var headercols = document.getElementById('list_'.concat(tabledef.tableid)).getElementsByClassName('tableHeader')[0].getElementsByClassName('tableCell');
	for (var icol = 0, left = 0; icol < headercols.length; icol++ ) {
		coldefs[headercols[icol].id] = {left: left, width: headercols[icol].offsetWidth};
		left+= headercols[icol].offsetWidth;
	}
	// set the widths and lefts
	var rules = document.styleSheets[0].cssRules;
	for (var irule=0; irule < rules.length; irule++){
		
		var divid=/div#list_.*div#(col_\S+)/.exec(rules[irule].selectorText);
		if (divid != null) {
			rules[irule].style.left = coldefs[divid[1]].left;
			rules[irule].style.width = coldefs[divid[1]].width;
		}
	}
	
	//Send the new information.
	for (var icol = 0; icol < tabledef.cols.length; icol++) {
		if (tabledef.cols[icol].visible){
			tabledef.cols[icol].width = coldefs['col_'.concat(tabledef.cols[icol].id)].width;
		}
	}
	
	sendxhr('tabledef='.concat(JSON.stringify(tabledef)));

	
	// Adjusts the void cols
	adjustform();
}


function createcolstyles(){
	style = document.styleSheets[0];
	rulecount = style.cssRules.length;
	
	// Header 
	var fixedselector = 'div#list_'.concat(tabledef.tableid, ' div.tableHeader');
	var rule = '%fs div.tableRow { height: %1px; overflow: hidden; color: white; background-color: silver; background-image: url(\'../image/titleback2.png\'); }';
	style.insertRule(rule.replace('%fs',fixedselector).replace('%1', tabledef.heights[0]), rulecount++);
	
	var left = 0; var top = 0; var colWidth = 0;
	for (var col=0; col<tabledef.cols.length; col++){
		colWidth= tabledef.cols[col].visible?tabledef.cols[col].width:0;
		rule = '%fs div#col_%1 { position: relative; top: %2px; left: %3px; width: %4px; height: %5px;}';
		style.insertRule(rule.replace('%fs',fixedselector).replace('%1',tabledef.cols[col].id)
			.replace('%2',top).replace('%3',left).replace('%4',colWidth).replace('%5',tabledef.heights[0])
			, rulecount++);
		left += colWidth;
		top -= tabledef.heights[0];
	}

	// Content
	
	fixedselector = 'div#list_'.concat(tabledef.tableid, ' div.tableContent');
	rule = '%fs div.tableRow { height: %1px; overflow: hidden; color: black; background-color: white; }';
	style.insertRule(rule.replace('%fs',fixedselector).replace('%1', tabledef.heights[1]), rulecount++);
	rule = '%fs div.tableRowAlt { height: %1px; overflow: hidden; color: black; background-color: whitesmoke; }';
	style.insertRule(rule.replace('%fs',fixedselector).replace('%1', tabledef.heights[1]), rulecount++);
	
	var left = 0; var top = 0;
	for (var col=0; col<tabledef.cols.length; col++){
		colWidth= tabledef.cols[col].visible?tabledef.cols[col].width:0;
		rule = '%fs div#col_%1 { position: relative;	top: %2px; left: %3px; width: %4px; height: %5px;}';
		style.insertRule(rule.replace('%fs',fixedselector).replace('%1',tabledef.cols[col].id)
			.replace('%2',top).replace('%3',left).replace('%4',colWidth).replace('%5',tabledef.heights[1])
			, rulecount++); //Aquí tenemos el rulecount de cada columna para usarlo a la hora de cambiar el estilo de la misma.
		left += colWidth;
		top -= tabledef.heights[1];
		}

	// Footer
	
	fixedselector = 'div#list_'.concat(tabledef.tableid, ' div.tableFooter');
	var rule = '%fs div.tableRow { height: %1px; overflow: hidden; color: black; background-color: whitesmoke; background-image: url(\'../image/titleback2.png\'); }';
	style.insertRule(rule.replace('%fs',fixedselector).replace('%1', tabledef.heights[2]), rulecount++);
	
	var left = 0; var top = 0;
	for (var col=0; col<tabledef.cols.length; col++){
		colWidth= tabledef.cols[col].visible?tabledef.cols[col].width:0;
		rule = '%fs div#col_%1 { position: relative;	top: %2px; left: %3px; width: %4px; height: %5px;}';
		style.insertRule(rule.replace('%fs',fixedselector).replace('%1',tabledef.cols[col].id)
			.replace('%2',top).replace('%3',left).replace('%4',colWidth).replace('%5',tabledef.heights[2])
			, rulecount++);
		left += colWidth;
		top -= tabledef.heights[2];
	}

	
}


function adjustmenus(){
	var lastleft = 100;
	var menus = document.getElementById('menus');
	if (menus != null) {
		var uls = menus.getElementsByTagName('UL');
		for (var i=0; i<uls.length; i++){
			if (uls[i].id.substring(0,4) == 'menu') {
				var anchor = document.getElementById('linkto' + uls[i].id.substring(4));
				uls[i].style.left = anchor.offsetLeft;
				document.getElementById(anchor.id).onmouseover = showmenu;
				document.getElementById(anchor.id).onmouseout = hidemenu;
				document.getElementById(uls[i].id).onmouseover = showmenu;
				document.getElementById(uls[i].id).onmouseout = hidemenu;
			} else {
				uls[i].parentNode.onmouseover = showmenu;
				uls[i].parentNode.onmouseout = hidemenu;
				uls[i].style.top = 0;
				uls[i].style.left = uls[i].parentNode.offsetWidth;
			}
		}
	}	
}

function showmenu(event){
	var element = event.currentTarget;
	
	if (element.tagName == "A") {
		if (element.id.substring(0,6) == "linkto") {
			document.getElementById('menu' + element.id.substring(6)).style.visibility = "visible";
		}
	} else if (element.tagName == "UL") { 
		element.style.visibility = "visible";
	} else if (element.tagName == 'LI') {
		element.getElementsByTagName('UL')[0].style.visibility = "visible";
	}
}

function hidemenu(event){
	var element = event.currentTarget;
	
	if (element.tagName == "A") {
		if (element.id.substring(0,6) == "linkto") {
			document.getElementById('menu' + element.id.substring(6)).style.visibility = "hidden";
		}
	} else if (element.tagName == "UL") { 
		element.style.visibility = "hidden";
	} else if (element.tagName == 'LI') {
		element.getElementsByTagName('UL')[0].style.visibility = "hidden";
	}
}


function vscrollme(event){
	var tablecontainer = document.getElementById('list_'.concat(tabledef.tableid));
	tablecontainer.getElementsByClassName('tableContent')[0].style.top =  "-" + event.target.scrollTop + "px";
}

function hscrollme(event){
	var tablecontainer = document.getElementById('list_'.concat(tabledef.tableid));
	var tableview = tablecontainer.getElementsByClassName('tableView')[0]
	tableview.style.left =  "-" + event.target.scrollLeft + "px";
	tableview.style.width = tableview.style.width + event.target.scrollLeft + "px";
	
}


function submit(){ 
	document.forms[0].submit();
}

function addhidden(hidname, hidvalue){
	var newinput = document.createElement("input");
	
	newinput.setAttribute("type", "hidden");
	newinput.setAttribute("name", hidname);
	newinput.setAttribute("value", hidvalue);

	document.forms[0].appendChild(newinput);
}

function submitlookup(id){
	addhidden('action', 'lookup');
	addhidden('lookup', id);
	submit();
}

function submitaction(id){
	addhidden('action', id);
	submit();
}

function showtab(id, selected){
	var tabs_container = document.getElementById("tab_" + id);
	var tabs = tabs_container.getElementsByTagName("li");
	tabs[selected].className = "selected"; 
	var content = document.getElementById("tabcontent"+id+selected);  
	content.className = "tabcontentSelected";
	for (var i=0; i<tabs.length; i++){
		if (i != selected) {
			tabs[i].className = ''; 
			content =  document.getElementById("tabcontent"+id+i);
			content.className = "tabcontent";
		}
	}
	var hidden_selected = document.getElementById("tabselected_" + id);
	hidden_selected.setAttribute("value", selected);
}

