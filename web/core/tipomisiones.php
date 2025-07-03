<?php 

// Permite ingresar los usuario al sistema
namespace DronesMaritime\Core;

use DronesMaritime\Core\App\MissionTypeApp;


if ($objInitApp->access_token())
{
	$objMissionTypeApp = new MissionTypeApp();

	$action = filter_input(INPUT_GET, 'action');

	if (strlen($action)>0)
	{
		switch ($action) {
			case 'modificar':
				$functions->registerAction($arrUser,'Modificando el tipo de misión');
				$objMissionTypeApp->edit_mtype();
				break;
			case 'incluir':
				$functions->registerAction($arrUser,'Incluyendo un tipo de misión');
				$objMissionTypeApp->new_mtype();

				break;
			case 'salvar':
				$objMissionTypeApp->set_mtype();
				break;
			case 'actualizar':
				$objMissionTypeApp->update_mtype();
				break;
		}

	} else {
		// Se asume por defecto la lista de clientes.
		$functions->registerAction($arrUser,'En el Modulo de Tipo Misión');
		$objMissionTypeApp->get_list();
	}

} else {
	header("Location: /");
	die();
}


