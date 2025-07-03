<?php 

// Permite ingresar los usuario al sistema
namespace DronesMaritime\Core;

use DronesMaritime\Core\App\DroneApp;


if ($objInitApp->access_token())
{
	$objDroneApp = new DroneApp();

	$action = filter_input(INPUT_GET, 'action');

	if (strlen($action)>0)
	{
		switch ($action) {
			case 'modificar':
				$functions->registerAction($arrUser,'Modificando los dato del drone');
				$objDroneApp->edit_drone();
				break;
			case 'incluir':
				$functions->registerAction($arrUser,'Ingresando un nuevo drone');
				$objDroneApp->new_drone();

				break;
			case 'salvar':
				$objDroneApp->set_drone();
				break;
			case 'actualizar':
				$objDroneApp->update_drone();
				break;
		}

	} else {
		// Se asume por defecto la lista de clientes.
		$functions->registerAction($arrUser,'En el Modulo de Drones');
		$objDroneApp->get_list();
	}

} else {
	header("Location: /");
	die();
}


