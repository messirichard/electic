<?php 

try{
		
	//-------------------------------------------------------------
	
	if(is_admin()){		//load admin part
		require_once $currentFolder."/unitecreator_admin.php";
		require_once GlobalsUC::$pathProvider . "provider_admin.class.php";
		
		new UniteProviderAdminUC($mainFilepath);
		
	}else{		//load front part
		require_once GlobalsUC::$pathProvider . "provider_front.class.php";
		$UCproductFront = new UniteProviderFrontUC($mainFilepath);
	}

	
	}catch(Exception $e){
		$message = $e->getMessage();
		$trace = $e->getTraceAsString();
		echo "Addon Library Error: <b>".$message."</b>";
	
		if(GlobalsUC::SHOW_TRACE == true)
			dmp($trace);
	}
	
	
?>