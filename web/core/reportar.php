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
				$objReporteApp->select_plain();
				break;
			case 'piloto':
				$objReporteApp->piloto_reporte();
				break;
			case 'pilotoUpdate':
				$functions->registerAction($arrUser,'Se reporto la misión del piloto');
				$objReporteApp->piloto_ReportarUpdate();
				break;
			case 'docdriver':
				$functions->registerAction($arrUser,'Subió un archivo a Google driver');
				$objReporteApp->upload_fileGDRIVER();
				break;
			case 'view_gd':
				$objReporteApp->view_gd();
				break;
		}

	} else {
		// Se asume por defecto la lista de clientes.
		$functions->registerAction($arrUser,'Reporte de la misión');
		$objReporteApp->get_list();
	}

} else {
	header("Location: /");
	die();
}