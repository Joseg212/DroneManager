<?php
namespace DronesMaritime\Core\Ajax;


session_start();

require_once __DIR__ . '/../..'.'/config.php';
require_once __DIR__ . '/../../..'.'/vendors/autoload.php';


// Validar si el acceso de usuario es autorizado al sistema 
$program = filter_input(INPUT_GET, 'program');

if (strlen($program)>0)
{
	require_once "ajax_".$program.".php";
}