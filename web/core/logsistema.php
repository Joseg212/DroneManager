<?php 

// Permite ingresar los usuario al sistema
namespace DronesMaritime\Core;

use DronesMaritime\Core\App\InitApp;


if ($objInitApp->access_token())
{
	$objInitApp = new InitApp();

	$action = filter_input(INPUT_GET, 'action');

	if (strlen($action)>0)
	{
		switch ($action) {
			case 'view':
				$objInitApp->view_log();
				break;
		}
	} else {
		header("Location: /");
		die();		
	}

} else {
	header("Location: /");
	die();
}