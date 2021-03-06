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

/**
 * Convierte fitros en clausula SQL where
 * @package sql
 *
 * Es un pequeno parser para generar sentencias where a partir de filtros.
 * 
 * Gramatica
 * 
 * 	<filtro> ::= { <condicion> } .
 *  <condicion> ::= <termino> { "|" <termino> } .
 *  <termino> ::= "operador" "tira" | "tira" { ".." "tira" } | "comodin" .
 *   
 *  "operador" - >, >=, <, <=, =, <> 
 *	"tira" - secuencia de caracteres
 *	"comodin" - secuencia de caracteres que incluyen * o ?
 *
 *
 * Como pcode guardamos en $this->condicion un array de condiciones. 
 * Cada condicion es un array de terminos.
 * Cada termino es un array de factores de tipo:
 * 	- single -> operator + value => <id> <operator> '<value>'
 * 	- range -> ivalue (+ fvalue)  => <id> between '<ivalue>' and '<fvalue>'  |  <id> = '<ivalue>'
 *	- comodin -> expresion(cambiando *? por %_) => <id> like '<expresion>'
 *
 */
class bas_sql_filtertowhere {

		private $lasttoken;
		private $input;
		private $pos;
		private $condicion;
		private $properties;
		public $error;
		public $errormsg= array();

	public function __construct ($fieldprops=''){
		if ($fieldprops) $this->properties = $fieldprops;
	}
		
	/**
	 * GENERA LA CLAUSULA WHERE SEGÚN LOS FILTROS SELECCIONADOS.
	 * como entrada $filtros array de entradas 'id' para el nombre del campo y 'filter' para la expresión del filtro. 
	 */
	public function filtertowhere($filtros=''){
/*		Sólo utilizable a partir de php 5.3.0		
 			return $this->filtertowhereextended(
			function($id, $lpart, $template){
				return str_replace('*LPART*', $lpart, $template);
				}
			, $filtros);
*/
		$callback = create_function('$id, $lpart, $template', 'return str_replace(\'*LPART*\', $lpart, $template);');
		return $this->filtertowhereextended($callback, $filtros);	
		
	}
/*
	static function stamptemplate($id, $lpart, $template){
		return str_replace('*LPART*', $lpart, $template);
	}
*/	
	
	public function filtertowhereextended($stampcallback, $filtros=''){
		$where = "";
		$septrm='';
		if (!$filtros){
			$filtros = array();
			foreach($this->properties as $property){
				if (isset($property['filter'])) $filtros[$property['id']]=$property['filter'];
			}
		}
		foreach ($filtros as $id => $filtro){
			if (strlen(trim($filtro))>0){
			
				$this->pos=0;
				$this->condicion=array();
				array_splice($this->condicion,0);
				$this->input=$filtro;
				$this->filtro();
				$where .= $septrm . '(';
				$sep = '';
				foreach($this->condicion['terminos'] as $termino){
					$lpart = $this->leftpart($id);
					switch ($termino['type']){
						case 'single':
							$value = $this->formatvalue($termino['value']);
							$where .= $stampcallback($id, $lpart, "$sep*LPART* {$termino['operator']} $value");
							#$where .= "$sep$lpart {$termino['operator']} $value"; 
							break;
						case 'range': 
							$ivalue = $this->formatvalue($termino['ivalue']); 
							$fvalue = $this->formatvalue($termino['fvalue']); 
							$where .= $stampcallback($id, $lpart, "$sep*LPART* between $ivalue and $fvalue"); 
							#$where .= "$sep$lpart between $ivalue and $fvalue"; 
							break;
						case 'comodin':
							$where .= $stampcallback($id, $lpart, "$sep*LPART* like '${termino['value']}'"); 
							#$where .= "$sep$lpart like '${termino['value']}'"; 
							break;
					}
					$sep = ' or ';
				}
				$where .= ')';
				$septrm = ' and '; 
			}
		}
	
		return $where;
	}
	
		
	private function formatvalue($value){
		// Esta función mira si es un número entero o decimal.
		// Si lo es, lo retorna talcual, sino le añade comillas. Si tiene comillas las duplica.
		$esfloat = true;
		for ($i=0; $esfloat && $i<strlen($value);$i++){
			$pos = strpos('0123456789.,', $value[$i]);
			if ($pos===false) $esfloat = false;
			elseif ($pos>9) {
				if (isset($decimalpoint)) $esfloat = false;
				else $decimalpoint = $i;
			}
		}
		if ($esfloat){
			if (isset($decimalpoint)) $value[$decimalpoint]='.';
			return $value;
		} else return "'$value'";
	} 
	
	private function leftpart($id){
		if (isset($this->properties)){
			if (isset($this->properties[$id]['expresion'])){
				return $this->properties[$id]['expresion'];
			} else {
				$ret = '';
				if (isset($this->properties[$id]['table'])) $ret .= $this->properties[$id]['table'] .'.';
				if (isset($this->properties[$id]['aliasof'])) {	$ret .= $this->properties[$id]['aliasof'];}
				else { $ret .= $this->properties[$id]['id'];} 
				return $ret;
			}
		} else {
			return $id;
		}	
	}
	
	private function match($token){
	
		if ($this->lasttoken['token']!=$token) {
			$this->errormsg[] = "Se esperaba \"$token\" en la posición $this->pos.\n"
				. "Se encuentra (" . $this->lasttoken['token'] . "," . $this->lasttoken['value'] . ").\n"; //TODO: COMPROBAR QUE FUNCIONA EL MENSAJE.
			$this->pos=strlen($this->input);
			$this->error=true;
			$this->lexico();
		} else {
			$result = isset($this->lasttoken['value']) ? $this->lasttoken['value']  : 0;
			$this->lexico();
			return $result;
		}
	}

	private function filtro(){
		$this->lexico();
		if ($this->lasttoken['token']!='$'){
			$this->condicion();
		}
		$this->match('$');
	}

	private function condicion(){
		$this->termino();
		while ($this->lasttoken['token']=='|') {
			$this->lexico();
			$this->termino();
		}
	}

	private function termino(){
		if (!isset($this->condicion['terminos'])) $this->condicion['terminos'] = array();
		switch ($this->lasttoken['token']){
			case 'operator':
				$operator = $this->match('operator');
				$value = $this->match('tira');
				$this->condicion['terminos'][]=array('type'=>'single', 'value'=>$value, 'operator'=>$operator);
				break;
			case 'tira':
				$ivalue=$this->match('tira');
				if ($this->lasttoken['token']=='..') {
					$this->lexico();
					$fvalue = $this->match('tira');
					$this->condicion['terminos'][]=array('type'=>'range', 'ivalue'=>$ivalue, 'fvalue'=>$fvalue);
				} else {
					$this->condicion['terminos'][]=array('type'=>'single', 'value'=>$ivalue, 'operator'=>'=');
				}
				break;
			case 'comodin':
				$comodin = $this->match('comodin');
				$this->condicion['terminos'][]=array('type'=>'comodin', 'value'=>$comodin);
				break;
			default:
				$this->errormsg[] = "Se esperaba un operador, valor o comodín en la posición $this->pos.\n"
					. "Se encuentra (" . $this->lasttoken['token'] . "," . $this->lasttoken['value'] . ").\n"; 
					//TODO: COMPROBAR QUE FUNCIONA EL MENSAJE.
				$this->error=true;
				break; 
		}
		
	}

	private function lexico(){
	
		$len=strlen($this->input);
		while ($this->pos<$len && !(strpos(" \t\n",$this->input[$this->pos])===false)) $this->pos++;
		if ($this->pos>=$len){
			$this->lasttoken = array('token'=>'$');
		
		} else {
			switch($this->input[$this->pos]){
				case '|': $this->pos++;  $this->lasttoken = array('token'=>'|');  break;
				case '.': $this->pos+=2; $this->lasttoken = array('token'=>'..'); break;
				case '=': 
					$this->pos++;  
					$this->lasttoken = array('token'=>'operator', 'value'=>'=');  
					break;
				case '<': 
					$this->pos++; 
					$this->lasttoken = array('token'=>'operator', 'value'=>'<');
					if ($this->pos<$len && !(strpos('>=', $this->input[$this->pos])===false)){
						$this->lasttoken['value'] .= $this->input[$this->pos];
						$this->pos++; 
					}
					break;
				case '>': 
					$this->pos++; 
					$this->lasttoken = array('token'=>'operator', 'value'=>'>');
					if ($this->pos<$len && $this->input[$this->pos]=='='){
						$this->lasttoken['value'] .= '=';
						$this->pos++; 
					}
					break;
				case "'": case '"':
					//resolver tira pero de string.
					$i = $this->pos;
					$comilla = $this->input[$i++];
					$inicio = $i; $result = '';
					do {
						while ($i<$len && $this->input[$i] != $comilla) $i++;
						$seguir = $i+1 < $len && $this->input[$i+1] == $comilla;
						if ($seguir) $i=$i+2;	 
					} while ($seguir); 
					$result .= substr($this->input,$inicio,$i-$inicio);
					$this->pos= $i+1;
					if (!(strpos($result,'*')===false) || !(strpos($result,'?')===false)){
						$this->lasttoken = array('token'=>'comodin', 'value'=>strtr($result,'*?','%_'));
					} else {
						$this->lasttoken = array('token'=>'tira', 'value'=>$result);
					}
					break;
					
				default: 
					#tira o comodin
					$i=$this->pos;
					while ($i<$len && (strpos("|.<>= \t\n",$this->input[$i])===false)) $i++;
					$result = substr($this->input,$this->pos,$i-$this->pos);
					if ($i==$this->pos) $this->pos++; else $this->pos=$i;
					if (!(strpos($result,'*')===false) || !(strpos($result,'?')===false)){
						$this->lasttoken = array('token'=>'comodin', 'value'=>strtr($result,'*?','%_'));
					} else {
						$this->lasttoken = array('token'=>'tira', 'value'=>$result);
					}
					break;
			}
		}
	}



}

?>
