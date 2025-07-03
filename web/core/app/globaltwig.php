<?php
namespace DronesMaritime\Core\App;

use DronesMaritime\Core\Models\Usuarios;


class GlobalTwig extends \Twig\Extension\AbstractExtension implements \Twig\Extension\GlobalsInterface
{
    public function getGlobals():array
    {

    	if (isset($_SESSION['idUser'])){
        	$objUser = new Usuarios();
        	$arrUser = $objUser->get_dataUser($_SESSION['idUser'])[0];
    	} else {
    		$arrUser = array('id_usuario'=>'','nombres'=>'','role'=>'','user'=>'');
    	}

        return ['myname' => 'Jose Gregorio','user'=>$arrUser];
    }

    // ...
}
