<?php 

// Permite ingresar los usuario al sistema
namespace DronesMaritime\Core;

use DronesMaritime\Core\App\ReportesApp;


if ($objInitApp->access_token())
{
	$objReportesApp = new ReportesApp();

	$action = filter_input(INPUT_GET, 'action');

	if (strlen($action)>0)
	{
		switch ($action) {
			case 'resultado':
				$functions->registerAction($arrUser,'Genera el resultado de la mision Reporte');
				$objReportesApp->report_resultado();
				break;
			case 'rpt01':
				$objReportesApp->report_rpt01();
				break;
		}

	} else {
		// Se asume por defecto la lista de clientes.
		header("Location: /");
		die();
	}

} else {
	header("Location: /");
	die();
}
