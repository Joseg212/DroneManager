<?php



use DronesMaritime\Core\App\InitApp;


const FS_FOLDER = __DIR__;

require_once __DIR__ . '/config.php';
require_once __DIR__ . '/..'.'/vendors/autoload.php';


$objInitApp = new InitApp();

session_start();


if ($objInitApp->access_token()) {
    // Iniciar el Dashboard
    $objInitApp->get_home_system();
} else {
	// Solicita inicio de sesión
    $objInitApp->get_login_form();
}

?>