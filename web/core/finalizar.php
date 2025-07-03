<?php 

// Permite ingresar los usuario al sistema
namespace DronesMaritime\Core;

use DronesMaritime\Core\App\ReportarApp;


if ($objInitApp->access_token())
{
	$objReporteApp = new ReportarApp();

	$action = filter_input(INPUT_GET, 'action');

	if (strlen($action)>0)
	{
		switch ($action) {
			case 'selecionar':
				$objReporteApp->select_report();
				break;
			case 'procesar':
				$functions->registerAction($arrUser,'Finiquito la misión');
				$objReporteApp->update_misionFinish();
				break;

		}

	} else {
		// Se asume por defecto la lista de clientes.
		$functions->registerAction($arrUser,'Finalizar la misión');
		$objReporteApp->get_finishList();
	}

} else {
	header("Location: /");
	die();
}