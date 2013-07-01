<?php
/*
	Copyright 2009-2012 Domingo Melian

	This file is part of imywa.

	imywa is free software: you can redistribute it and/or modify
	it under the terms of the GNU General Public License as published by
	the Free Software Foundation, either version 3 of the License, or
	(at your option) any later version.

	imywa is distributed in the hope that it will be useful,
	but WITHOUT ANY WARRANTY; without even the implied warranty of
	MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
	GNU General Public License for more details.

	You should have received a copy of the GNU General Public License
	along with imywa.  If not, see <http://www.gnu.org/licenses/>.
*/
class bas_frmx_panelGrid {

	public $jsClass= "bas_frmx_cardframe";
	public $labelwidth;
	
	public $grid;
	public $components=array();
	
	public $id;
	
	protected $mode;
	public $type;
	protected $gnrEvent;
	
	public function __construct($id,$grid="") {
		$this->id = $id;
		if (!$grid) $grid = array('width'=>4,'height'=>5);
		$this->grid= $grid;
		$this->labelwidth = 30;
		$this->mode = "edit";
		$this->type = "gridPanel";
	}
	
// Beware with the cut and paste. We are including ghost code which never are going to be executed.
// setMode, getMode, uploadFile, ...
	public function SetMode($mode="enable"){
		switch ($mode){
			case "disable":
				$this->mode = $mode;
			break;
			case "enable":	
				$this->mode = $mode;
				$this->record->original = $this->record->current = array();
			break;
			default:		
				global $_LOG;
				$_LOG->log("El modo insertado en FRMX_CardFrame::SetMode es incorrecto");
		}
	}
	
	public function GetMode(){
		return $this->mode;		
	}
	
// 	public function uploadFile($id,$name){
// 		global $_SESSION;	
// 		$localDir = "/var/www/apps/upload/".$_SESSION->apps[$_SESSION->currentApp]->source."/docs/";
// 			// Insertamos el fichero en el servidor	
// 		if  ($_FILES[$id]["size"] < 20000)	{
// 			if ($_FILES[$id]["error"] > 0){
// 				return "Return Code: " . $_FILES[$id]["error"] . "<br />";
// 			}
// 			else{ // alamacenamos el fichelos en el directorio indicado.
// 				move_uploaded_file($_FILES[$id]["tmp_name"],
// 				$localDir . $id);			  //### TODO:sustituir el contaluz por la aplicacion actual   // "/var/www/apps/upload/contaluz/docs/"
// 			}
// 		}
// 		else{ return "Invalid file"; }
// 		return NULL;	
// 	}
	
	public function setLabelWidth($width){
		$this->labelwidth = $width;
	}
	
	public function numItems(){
        return $this->grid["width"] * $this->grid["height"];
    }
    
    public function setEvent($event){
        $this->gnrEvent = $event;
    }
	
	public function getComponent($y,$x){
		if (isset($this->components[$y][$x]))	return $this->components[$y][$x]["id"];	
		return NULL;
	}
	
	public function OnPaint($mainGrid=""){ // ¿que es maingrid? Parece ser el identificador del FrameGrid al que pertenece el PanelGrid
		$html = new bas_html_panelGrid($this);
		$html->OnPaint($mainGrid);	
	}
	
	public function addComponent($y=0, $x=0, $field_id,$caption,$event="",$subItem=""){
        if (!$event) $event = $this->gnrEvent;
		$this->components[$y][$x] = array("x"=>$x,"y"=>$y,"id"=>$field_id,"caption"=>$caption,"subItem"=>$subItem,"event"=>$event);
	}
	
	public function setAttrPos($x,$y,$attr,$value){
		if (isset($this->components[$y][$x])) $this->components[$y][$x][$attr] = $value;
	}
	
	public function setAttrId($id,$attr,$value){
		for ($row = 0; $row < $this->grid["height"]; $row++){
			for($column = 0; $column < $this->grid["width"]; $column++){
				if (isset($this->components[$row][$column])){
					if($this->components[$row][$column]["id"] == $id){ 
						$this->components[$row][$column][$attr] = $value;
						break;
					}
				}
			}
		}
	}
	
	public function delComponent($id){
        foreach($this->components as $item => $component){
            if ($component['id'] == $id)  unset($this->components[$item]);
        }
	}
	
	
	public function selecttab($tab){// marcamos como seleccionado el tab indicado
		
	}
	
	public function OnAction($action, $data){
	global $_LOG;
		switch($action){
			case "pdf":  $this->OnPdf(); break;
			case "csv":  $this->OnCsv(); break;
		}
	}
	
	public function Oncsv($csv){
		$csvcard=new bas_csv_card();
		$csvcard->loadcard($this);
		//$csvcard->prepare();
		$csvcard->Onprint($csv);
	}
	
	public function OnPdf($pdf){
		$pdfcard=new bas_pdf_card();
		$pdfcard->OnPdf($pdf,$this);
		
	}
}

?>
