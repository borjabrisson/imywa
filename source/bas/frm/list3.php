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
class bas_frm_list3{
/* Nueva modificación el bas_frm_list2 para que se mantenga el tamaño, orden y visibilidad de las columnas */
 	
	
	public $id;
	protected $tbdef;
	protected $toolbar=array();
	protected $header;
	protected $subforms=array();
	protected $footer;
	protected $menu;
	private $list;
	
	
	public function __construct($id = '', $actions = '*'){
		global $CONFIG;
		
		$this->id = $id;
		if ($actions == '*'){
			$actions = 'mostrar,filtros';
			if ($CONFIG['LSTTYPE'] == constant('LT_PAGE_LIST')) $toolactions = "primero,anterior,siguiente,ultimo,$actions";
		}
		$xid = $this->id ? "{$this->id}#" : '';
		foreach(explode(',',$actions) as $action) $this->toolbar[] = array('id'=>$xid.$action, 'image'=>$action, 'description'=>''); //Todo poner de alguna forma las descripciones estandar
		
		$this->header = new bas_htm_elements(); 
		$this->footer = new bas_htm_elements();

		if (class_exists("appmainmenu")) $this->menu = new appmainmenu();
		else $this->menu = new bas_htm_mainmenu();
		
	}

	public function addsubform($frm){
		$this->subforms[$frm->id] = $frm;
	}	

/*
			$this->toolbar[] = array('id'=>"{$id}primero", 'image'=>'primero', 'description'=>'Ir al primero.');
			$this->toolbar[] = array('id'=>"{$id}anterior", 'image'=>'anterior', 'description'=>'Ir al anterior.');
			$this->toolbar[] = array('id'=>"{$id}siguiente", 'image'=>'siguiente', 'description'=>'Ir al siguiente.');
			$this->toolbar[] = array('id'=>"{$id}ultimo", 'image'=>'ultimo', 'description'=>'Ir al último.');
			$this->toolbar[] = array('id'=>"{$id}mostrar", 'image'=>'mostrar', 'description'=>'Seleccionar las columnas del listado.');
			$this->toolbar[] = array('id'=>"{$id}filtros", 'image'=>'filtros', 'description'=>'Aplicar diferentes filtros por columnas.');
			$this->toolbar[] = array('id'=>"{$id}orden", 'image'=>'orden', 'description'=>'Ordenar el listados por alguna de sus columnas.');
 */	
	
	public function OnLoad(){
		foreach($this->subforms as $frm) if (method_exists($frm, 'OnLoad')) $frm->OnLoad();
	}
	
	
	
	
	public function OnPaint(&$form = false){
		if (method_exists($this->tbdef, 'refresh')) $this->tbdef->refresh();
		
		$createform = ! is_object($form);  
		if ($createform) {
			
			$cfg = new bas_sys_frmconfig();
			$loadform = $cfg->load(get_class($this));
			if (!$loadform){
				//TODO: crear un nuevo formato.
				$loadform = "{tableid:'{$this->tbdef->id}', heights:[25,32,5], cols:[";
				$i=0;
				foreach($this->tbdef->getcols() as $col){
					if ($i) $loadform.= ",";
					$loadform.= "{id:'{$col['id']}', width:100, visible:true, order:$i}";
					$i++;
				}
				$loadform.=	"]}";
			} else {
				//Ahora el select lo hace el usuario. De momento tenemos que cambiar el orden de las columnas a lo que haya decidido el usuario
				$order='';
				for($icol=0; $icol<count($loadform->cols); $icol++){
					if ($order) $order.=','; $order.=$loadform->cols[$icol]->id;				
				}
				$this->tbdef->setcolorder($order);
				$loadform= htmlentities(json_encode($loadform));
			}

			$frm = new bas_htm_form2("LISTA DE ".strtoupper($this->tbdef->caption), $this->menu, $this->toolbar, 'list3', $loadform);
			
		}
		else $frm =& $form;
		
		$frm->opendiv('header');
		// Cabecera de filtros automáticos
		$cols = $this->tbdef->getcolsbyproperty('keyfiltered',true);
		$countcols = count($cols);
		if ($countcols){
			$frm->opendiv('autofilters');
			$fdef = new bas_dat_tabledef();
			$i=0;
			foreach($cols as $col){
				$i++;
				$fdef->addcol($col['caption'],$col['id']); 
				if (isset($col['lookup'])) $fdef->setproperty('lookup',$col['lookup']);
				elseif ($i == $countcols) $fdef->setproperty('lookup');
				$fdef->settemplate('hidden');
			}
			$autofilter = $this->tbdef->getautokeyfilter();
			$card = new bas_htm_card($fdef, $autofilter);
			$frm->add($card);
			$frm->closediv();
		}
		// Cabecera del formulario
		$frm->add($this->header);
		$frm->closediv(); //header
		
		
		// Detalle de la query.
		//$frm->opendiv('list_body');
		if (isset($this->dataset)) $dataset =& $this->dataset;
		elseif (method_exists($this->tbdef,'query')) $dataset = new bas_sql_myqrydataset($this->tbdef);
		else $dataset = new bas_dat_arraydataset(); 
		$table = new bas_htm_list3($this->tbdef, $dataset);
		
		$frm->add($table);
		//$frm->closediv(); //list_body
		
		// Subformularios
		foreach($this->subforms as $subform) $subform->OnPaint($frm);
		
		// Pie
		$frm->opendiv('footer');
		$frm->add($this->footer);
		$frm->closediv();
		
		if ($createform) $frm->printme();
	}
	
	public function OnPdf(&$pdf = false){
		
		if (method_exists($this->tbdef, 'refresh')) $this->tbdef->refresh();
		
		$createpdf = ! is_object($pdf);  
		if ($createpdf) $frm = new bas_pdf_page(); else $frm =& $pdf;
		
		$frm->opendiv('header');
		// Cabecera de filtros automáticos
		$cols = $this->tbdef->getcolsbyproperty('keyfiltered',true);
		$countcols = count($cols);
		if ($countcols){
			$frm->opendiv('autofilters');
			$fdef = new bas_dat_tabledef();
			$i=0;
			foreach($cols as $col){
				$i++;
				$fdef->addcol($col['caption'],$col['id']); 
				if (isset($col['lookup'])) $fdef->setproperty('lookup',$col['lookup']);
				elseif ($i == $countcols) $fdef->setproperty('lookup');
				$fdef->settemplate('hidden');
			}
			$autofilter = $this->tbdef->getautokeyfilter();
			//$card = new bas_htm_card($fdef, $autofilter);
			//$frm->add($card); TODO: bas_pdf_card
			$frm->closediv();
		}
		// Cabecera del formulario
		//$frm->add($this->header); TODO: Los header y footer deben poseer la funcion PdfMe
		$frm->closediv(); //header
		
		
		// Detalle de la query.
		//$frm->opendiv('list_body');
		if (isset($this->dataset)) $dataset =& $this->dataset;
		elseif (method_exists($this->tbdef,'query')) $dataset = new bas_sql_myqrydataset($this->tbdef);
		else $dataset = new bas_dat_arraydataset(); 
		$table = new bas_pdf_list($this->tbdef, $dataset);
		
		$frm->add($table);
		//$frm->closediv(); //list_body
		
		// Subformularios
		foreach($this->subforms as $subform) $subform->OnPdf($frm); //TODO: LOS SUBFORMULARIOS
		
		// Pie
		$frm->opendiv('footer');
		//$frm->add($this->footer); TODO: Los footer
		$frm->closediv();
		
		if ($createpdf) $frm->pdfme();
	}
		
	private function auxusortcallback($col1, $col2){
		if ($col1->order == $col2->order)return 0;
		else return $col1->order > $col2->order ? 1: -1;
	}
	
	public function OnAction($action, $data){
		if ($ret = $this->menu->OnAction($action,$data)) return $ret;
		
		
		if (strpos($action,'#') !== false){
			list($id, $action) = explode('#',$action);
			$this->subforms[$id]->OnAction($action, $data);
			
		} else {
			
			if (isset($data['selected']) && method_exists($this->tbdef, 'setkeyselected')){
				$this->tbdef->setkeyselected($data);			
			}
			
			switch($action){
				case 'primero': 	$this->tbdef->go('first'); break;
				case 'anterior':	$this->tbdef->go('previouspage'); break;
				case 'siguiente':	$this->tbdef->go('nextpage'); break;
				case 'ultimo':		$this->tbdef->go('last'); break;
				
				case 'mostrar':
					$cfg = new bas_sys_frmconfig();
					if ($frmUsrDef = $cfg->load(get_class($this))) {
						$cols= $this->tbdef->getcols('*');
						for($icol=0; $icol<count($frmUsrDef->cols); $icol++){
							if (isset($cols[$frmUsrDef->cols[$icol]->id])){
								$cols[$frmUsrDef->cols[$icol]->id]['selected'] = $frmUsrDef->cols[$icol]->visible;
							} 
						}
						return array('open', 'bas_dlg_select', 'initdlgselect', array('cols'=>$cols));												
					} else {
						//TODO: Crear una configuración básica del formulario en el caso que no existiera
						return array('open', 'bas_dlg_msgbox', 'warning', 'No se ha generado la configuración inicial para el usuario.');						
					}
				case 'selectset':
					$cfg = new bas_sys_frmconfig();
					if ($frmUsrDef = $cfg->load(get_class($this))) {
						for($icol=0; $icol<count($frmUsrDef->cols); $icol++){
							if (isset($data[$frmUsrDef->cols[$icol]->id])){

								$frmUsrDef->cols[$icol]->visible= $data[$frmUsrDef->cols[$icol]->id]['selected'];
								$frmUsrDef->cols[$icol]->order= $data[$frmUsrDef->cols[$icol]->id]['order'];
							} 
						}
						usort($frmUsrDef->cols, array('bas_frm_list3','auxusortcallback'));
						$cfg->save(get_class($this), $frmUsrDef);
					}
					/*
					$this->tbdef->setcolorder($order);
					$this->tbdef->select(false, '*');
					$this->tbdef->select(true, $selected);
					*/
					break;
					
				case 'filtros':
					return array('open', 'bas_dlg_filter', 'initdlgfilter', $this->tbdef->getcols('*'));
				case 'filterset':
					foreach($data as $key => $filter){
						if (is_array($filter)) $this->tbdef->setfilter($filter['filter'], $filter['id']);
						else $this->tbdef->setfilter($filter, $key);
					}
					break;
					
				case 'orden': case 'orderset':
					break;
	
					
				// Para el autokeyfilter en las listas.	
				case 'lookup':
					if (method_exists($this->tbdef,'getautokeyfilter')) $filter = $this->tbdef->getautokeyfilter(); else $filter = array(); 
					return (array('open',"frm_${data['lookup']}lookup", "seek", $filter));
					
				case 'seek': case 'setvalues':
					if (method_exists($this->tbdef,'setkeyfromrecord')) $this->tbdef->setkeyfromrecord($data);
					break;
					
				case 'saveconfig':
					if (isset($data['tabledef'])){ 
						$cfg = new bas_sys_frmconfig();
						$cfg->saveAsObject(get_class($this), $data['tabledef']);
					}
					break;
					
				
			}
		}
	}
	
}
?>
