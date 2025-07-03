<?php 

namespace DronesMaritime\Core;

use DronesMaritime\Core\App\InitApp;

$nameUser  = $_POST['nameUser'];
$passUser = $_POST['passwUser'];
$id_token = $_POST['id_token'];


if ($objInitApp->access_token()){
	$functions->registerAction($arrUser,'Inicio sesión de usuario');	
	header("Location: /");
	die();
} else {

	$objInitApp->set_access_system($nameUser,$passUser,$id_token);
}








