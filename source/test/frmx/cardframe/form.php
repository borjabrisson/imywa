<?php

class test_frmx_cardframe_form extends bas_frmx_form{
	
	public function OnLoad(){
		parent::OnLoad();
		$this->toolbar= new bas_frmx_toolbar('pdf,csv,config,close');
		$this->title= 'Card Frame Test';
		
		$this->buttonbar = new bas_frmx_buttonbar();
		$this->buttonbar->addAction("aceptar");
		$this->buttonbar->addAction("eliminar");
		$this->buttonbar->addAction("nuevo");
		$this->buttonbar->addAction("salir");
		
		$menu = new bas_frmx_menubox("prueba");
		$menu->addElement("primero");
		$menu->addElement("segundo");
		$menu->addElement("tercero");
		$aux = new bas_frmx_menubox("sub");
		$aux->addElement("primero");
		$aux->addElement("segundo");
		$aux->addElement("tercero");
		
		$menu->addElement("cuarto","",$aux);
		
		$this->buttonbar->addMenu("prueba",$menu);
// 		$this->buttonbar->addMenu("eliminar",array("primero","segundo","tercero"));
// 		$this->buttonbar->addMenu("eliminar > primero",array("A","B","C"));
        $card = new bas_frmx_gridFrame("grid",array("grid"));
        
        $grid = new bas_frmx_panelGridQuery("pepe");
        
//         $gid->addComponent(1,1,"AMIGO");
        $grid->query->add("grid","test");
        $grid->query->addcol("id","ID","grid",true,"test");
        $grid->classMain="id";
        $grid->setEvent("ENVIO");
        $grid->setRecord();
        
        $card->addComponent("pepe",$grid,1,1, 2,2);
        $card->addComponent("pepe2",new bas_frmx_panelGrid("pepe"),1,3, 2,2);
        
        $grid = new bas_frmx_panelGridQuery("pepe3",array('width'=>4,'height'=>1));
        $grid->query->add("grid","test");
        $grid->query->addcol("id","ID","grid",true,"test");
        $grid->classMain="id";
        $grid->setEvent("ENVIO");

        $grid->setRecord();
        
        $card->addComponent("pepe3",$grid,3,1, 4,2);


        $this->addFrame($card);
	}
	
	public function OnAction($action, $data=""){
		parent::OnAction($action,$data);
		switch($action){
			case 'salir': case 'close': return array('close');
			case 'edit':
// 			case 'nextGrid':
// 					echo '{"command": "void",'. substr(json_encode($this),1);
           
			break;
			case 'prevGrid':case 'nextGrid':
                $this->frames[$data["idFrame"]]->OnAction($action,$data);
                $this->OnPaint("jscommand");
			break;
		}
	}
	
}

?>
