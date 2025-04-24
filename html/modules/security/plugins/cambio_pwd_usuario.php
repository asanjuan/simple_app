<?php 

PluginManager::RegisterPlugin(new my_new_plugin());

class my_new_plugin extends PluginInterface {
	

	public function onCustomButton($operation, $item, $datos){ 
	    
	    
	    if (!empty($datos['id_usuario']) && !empty($datos['pwd']) 
	        && $datos['pwd']==$datos['pwd2']){
	            
	        $new_pwd = md5($datos['pwd']);
	        $r = [];
	        $r['id'] = $datos['id_usuario'];
	        $r['pwd'] =  $new_pwd;
	        dbupdate("usuarios",$r);
	        $this->showMessage("Contraseña cambiada correctamente");
	        
	    }else {
	        $this->showMessage("Faltan datos o las contraseñas no coinciden");
	    }
		
	}

}
