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
			case 'modificar':
				$functions->registerAction($arrUser,'Modificando datos de una misión');
				$objMissionApp->edit_mission();
				break;
			case 'incluir':
				$functions->registerAction($arrUser,'Ingresando una nueva misión');
				$objMissionApp->new_mission();

				break;
			case 'salvar':
				$objMissionApp->set_mission();
				break;
			case 'actualizar':
				$objMissionApp->update_mission();
				break;
		}

	} else {
		// Se asume por defecto la lista de clientes.
		$functions->registerAction($arrUser,'En el modulo de Nueva Misión');
		$objMissionApp->get_list();
	}

} else {
	header("Location: /");
	die();
}