<?php 

// Permite ingresar los usuario al sistema
namespace DronesMaritime\Core;

use DronesMaritime\Core\App\UserApp;

if ($objInitApp->access_token())
{
	$objUserApp = new UserApp();

	$action = filter_input(INPUT_GET, 'action');

	if (strlen($action)>0)
	{
		switch ($action) {
			case 'modificar':
				$functions->registerAction($arrUser,'Modificando datos de usuario');
				$objUserApp->edit_user();
				break;
			case 'incluir':
				$functions->registerAction($arrUser,'Modificar datos del Cliente');
				$objUserApp->new_user();
				break;
			case 'salvar':
				$objUserApp->set_user();
				break;
			case 'actualizar':
				$objUserApp->update_user();
				break;
		}

	} else {
		// Se asume por defecto la lista de clientes.
		$functions->registerAction($arrUser,'En el Modulo de Usuario');
		$objUserApp->get_list();
	}

} else {
	header("Location: /");
	die();
}