<?php
class test_frmx_listframe_form_medias extends bas_frmx_listframe{
	
	public $listado= 1;
	public $desde= '2013-5-20';
	public $hasta= '2013-5-30';

    public function __construct($id,$title, $tabs='', $grid=array('width'=>4,'height'=>4)) {
    	global $_LOG;
		parent::__construct($id,$title);
		$query= new alt_sql_groupQuery();
		$query->addTableAs('movLocal', 'movimientos');
		$query->addFieldAs('local','establecimiento','group','text');
		$query->addRelatedTableAs('grupoConcepto', 'grupillo', 'concepto');
		$query->addField('grupo','group','text');
		$query->addFieldAs('valor','importe','sum','double');		
		$query->addField('valor','avg','double');
		$query->addFilter('valor','group','<>100');
		$query->addFilter('sum(valor)','aggregate','123..345');
		$query->addFilter('valor2','group','*juana*');
		$query->addFilter('valor1+valor2+valor3','group','12|42|234|<-30');
		$query->addFieldFilters('establecimiento,grupo,valor');
		$query->sortBy('grupo>,establecimiento<');		
		$_LOG->log(">>> Query:" . $query->getQuery());
		$_LOG->debug("queryobject",$query);
		return;
	//	$this->query = new bas_sqlx_querydef();
		$this->query->add("movLocal",'seguimiento');
		$this->query->addcol("local", "Local","movLocal" ,true,'seguimiento');
			
		$this->query->addrelated('grupoConcepto','concepto','movLocal','seguimiento');	
		$this->query->addcol("grupo", "Concepto","conceptoGrupo" ,true, 'seguimiento');
		
	
		$this->query->addcol("valor", "Valor","movLocal" ,false,'seguimiento');
		$this->query->setAttColum('valor', 'expression', 'sum(movLocal.importe)*grupoConcepto.signo');
		
		$this->query->addrelated('columna', 'grupo', 'grupoConcepto', false, 'seguimiento');
		
		$this->query->addCondition("movLocal.fecha between '{$this->desde}' and '{$this->hasta}'", 'fecha'); //Ver las fechas iniciales y finales de la semana.
		$this->query->addCondition("columna.listado = {$this->listado}", 'listado');
		
		$this->query->setkey(array('local'));
		
	// 	$this->query->db = "seguimiento";
		$this->query->order= array("local"=>"asc",'columna.orden'=>'asc');
		$_LOG->log("medias.query = " . $this->query->query());
		
		$x=$y= $height = 1;
		$width = 80;
		
	// 	$this->setFixed(2);
		
		// field type:   ($id,$type,$name, $caption, $editable, $value,$visible)
		
		$this->addComponent($width, $height,"local");
		
		$qry = "select grupo from columna where listado = {$this->listado} order by orden";
		
		foreach(array(array("grupo"=>'GVTA'),array("grupo"=>'GECPA'),array("grupo"=>'GEOTRGTS'),array("grupo"=>'GRDO'))
				as $rec){
			$this->query->addcol($rec['grupo'],$rec['grupo'],"valordetalle",false,"temp","number");
			$this->setAttr($rec['grupo'],"selected",false);
			$this->addComponent($width,$height,$rec['grupo']);
			$pivotValues[]= $rec['grupo'];
		}
	
		$this->query->setGroup('local'); $this->query->setGroup('grupo');
		$_LOG->log("medias.grouped-query = " . $this->query->query());
		$this->createRecord();
		$this->setPivot("concepto","importe");
		//pivot values =$pivotValues;
		
		$proc = new bas_sql_myextprocedure("seguimiento");
		if ($proc->success){ 
			$proc->call('calcularConceptos', array(null, null, null,null),"seguimiento"); 
			$this->Reload(false,$proc->connection);
			$proc->commit();
		}
		
    }	
    
}