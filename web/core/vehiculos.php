<?php 

// Permite ingresar los usuario al sistema
namespace DronesMaritime\Core;

use DronesMaritime\Core\App\TowApp;


if ($objInitApp->access_token())
{
	$objTowApp = new TowApp();

	$action = filter_input(INPUT_GET, 'action');

	if (strlen($action)>0)
	{
		switch ($action) {
			case 'modificar':
				$functions->registerAction($arrUser,'Modificando datos de Vehículo');
				$objTowApp->edit_tow();
				break;
			case 'incluir':
				$functions->registerAction($arrUser,'Agregando nuevo vehículo');
				$objTowApp->new_tow();

				break;
			case 'salvar':
				$objTowApp->set_tow();
				break;
			case 'actualizar':
				$objTowApp->update_tow();
				break;
		}

	} else {
		// Se asume por defecto la lista de clientes.
		$functions->registerAction($arrUser,'En el Modulo de Vehículo');
		$objTowApp->get_list();
	}

} else {
	header("Location: /");
	die();
}


