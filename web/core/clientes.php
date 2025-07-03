<?php 
// Permite ingresar los usuario al sistema
namespace DronesMaritime\Core;

use DronesMaritime\Core\App\ClientApp;


if ($objInitApp->access_token())
{
	$objClientApp = new ClientApp();

	$action = filter_input(INPUT_GET, 'action');

	if (strlen($action)>0)
	{
		switch ($action) {
			case 'modificar':
				$functions->registerAction($arrUser,'Modificar datos del Cliente');
				$objClientApp->edit_client();
				break;
			case 'incluir':
				$functions->registerAction($arrUser,'Anexando nuevo cliente');
				$objClientApp->new_client();
				break;
			case 'salvar':
				$objClientApp->set_client();
				break;
			case 'actualizar':
				$objClientApp->update_client();
				break;
		}

	} else {
		// Se asume por defecto la lista de clientes.
		$objClientApp->get_list();
		$functions->registerAction($arrUser,'En el Modulo de Cliente');
	}

} else {
	header("Location: /");
	die();
}