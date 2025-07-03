<?php
namespace DronesMaritime\Core;

use DronesMaritime\Core\App\FunctionsApp;
use DronesMaritime\Core\App\InitApp;
use DronesMaritime\Core\Models\Usuarios;


session_start();

require_once __DIR__ . '/..'.'/config.php';
require_once __DIR__ . '/../..'.'/vendors/autoload.php';



// Validar si el acceso de usuario es autorizado al sistema 


$objInitApp = new InitApp();
if (isset($_GET['program']) || !$_GET['program']==null)
{
	if ($_GET['program']=='validform')
	{
		$run_program = $_GET['program'] . ".php";
		require_once $run_program;
	} else {
		$objUser = new Usuarios();
		$arrUser = $objUser->get_dataUser($_SESSION['idUser'])[0];

		$functions = new FunctionsApp();

	    if ($functions->UserAccess($arrUser['role'],$_GET['program']))
	    {
			$run_program = $_GET['program'] . ".php";
			require_once $run_program;
	    } else {
	    	$functions->render_access_denied();
	    }
	}

}
