<?php 

// Permite ingresar los usuario al sistema
namespace DronesMaritime\Core;

use DronesMaritime\Core\App\MissionApp;


if ($objInitApp->access_token())
{
	$objMissionApp = new MissionApp();

	$action = filter_input(INPUT_GET, 'action');

	if (strlen($action)>0)
	{
		switch ($action) {
			case 'crearplan':
				$functions->registerAction($arrUser,'Ingresando un nuevo plan');
				$objMissionApp->get_plainSelect();
				break;
			case 'incluir':
				$functions->registerAction($arrUser,'Realizando un nuevo plan');
				$objMissionApp->new_plain();
				break;
			case 'salvar':
				$objMissionApp->set_plain();
				break;
		}

	} else {
		// Se asume por defecto la lista de clientes.
		$functions->registerAction($arrUser,'En el modulo de Planificación');
		$objMissionApp->get_plainList();
	}

} else {
	header("Location: /");
	die();
}