<?php 

namespace DronesMaritime\Core;


if ($objInitApp->access_token()){
	$functions->registerAction($arrUser,'Ha cerrado Sesión');
	$objInitApp->logout_system();
} else {
	header("Location: /");
	die();
}
