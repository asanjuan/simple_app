<?php

PluginManager::RegisterPlugin(new consultas_plugin());

class consultas_plugin extends PluginInterface {
	protected $datos; //resultado de la consulta
	
	public function postUpdate($item, $datos){}
	public function postInsert($item, $datos){ }
	public function preDuplicate($item, &$datos){ }
	public function postDuplicate($item, $new_item){ }
	public function customContent($item){ 
		$listado_html = "";
		if ($this->datos != null){
			$listado_html = generarTablaHTML($this->datos);
		}
		
		return $listado_html;
	}
	public function setDefaultValues(&$datos){}
	public function preRenderform($item, &$datos){ 
		
		//$this->form->addCustomButton("ejecutar", "Ejecutar Query", "form");
		//$datos['query']=eval('return '. $datos["query"] );
		//$this->showMessage("dato modificado");
			
	}
	public function onCustomButton($operation, $item, $datos){ 
		if ($operation == "ejecutar" ){
			try{
			    
			    $consulta_json = sqlToJson($datos["query"]);
                $consulta_reprocesada = jsonToSql($consulta_json);
                //echo $consulta_reprocesada;
                
				$r = ["id" => $item, "query" =>$datos["query"], "query_json" => $consulta_json ];
				dbupdate("consultas",$r);
				
				$this->datos = query($datos["query"]); 
				$this->showMessage("Consulta ejecutada");
		
			} catch (PDOException $e) {
				$this->showMessage("Error en la consulta: " . $e->getMessage());
			}
		}
	}
	public function postUploadFile($filedata){ $this->showMessage("postUploadFile");}

}
