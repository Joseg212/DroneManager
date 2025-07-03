<?php

namespace DronesMaritime\Core\App;

use DronesMaritime\Core\Models\Usuarios;
use DronesMaritime\Core\App\GlobalTwig;
use DronesMaritime\Core\Models\Drone;


abstract class controller
{
	private  $twig=null;

	public function __construct(string $uri = '/')
	{
		## Inicia la petición de contraseña de usuario 
		$loader = new \Twig\Loader\FilesystemLoader(__DIR__.'/../..'.'/views/');

		$this->twig = new \Twig\Environment($loader);
		
		$this->twig->addExtension(new GlobalTwig());
	}

	public function render($filename,$arrvar)
	{
		return $this->twig->render($filename,$arrvar);
	}

	public function get_user($id_user):array
	{
		$objUser = new Usuarios();
		$arrUser = $objUser->get_dataUser($id_user)[0];
		return $arrUser;
	}

}