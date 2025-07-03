<?php
// Inicio de Session del sistema
namespace DronesMaritime\Core\App;

use DronesMaritime\Core\Models\Usuarios;
use DronesMaritime\Core\Models\LogSistema;
use Defuse\Crypto\Key;
//use DronesMaritime\Modules\Model\Usuario;
//use DronesMaritime\Modules\App\ConectionDB;


final class InitApp extends Controller
{
	public function access_token():bool
	{
		$ret = false;
		//$_SESSION["id_section"] = "jose";
		//unset($_SESSION['id_section']);

		if (!isset($_SESSION["id_section"]) || $_SESSION["id_section"]==null){
			$ret = false;

		} else {
			$ret = true;
		}
		return $ret;
	}

	public function get_login_form()
	{

		$id_section = uniqid();

		$_SESSION['id_token'] = $id_section;

		echo $this->render('login/login_user.html.twig', ['id_section' => $id_section, 'msgSystem'=>'', 'action_system'=>'dont']);
	}

	public function set_access_system(string $nameUser, string $passUser, string $token)
	{

		$passwCriptor =  hash('sha1', $passUser);
		$usuario = new Usuarios();
		// Determina si tiene acceso el usuario al sistema 
		if ($_SESSION['id_token']==$token){
			// Verifica que exista el usuario 
			if ($usuario->validUser($nameUser, $passwCriptor))
			{
				// Acceso consedido al usuario 
				$_SESSION['id_section'] = $token.'-'.uniqid();
				$_SESSION['idUser'] = $usuario->get_idUser();
				//var_dump("id Usuario " . $usuario->get_idUser());
				// Para aplicar un concepto de seguridad en los datos
				$key = Key::createNewRandomKey();
				$_SESSION['key_crypto'] =  $key->saveToAsciiSafeString();

				// Se pasa al Dashboard
				header("Location: /");
				die();
			} else {
				$id_section = uniqid();
				$_SESSION['id_token'] = $id_section;
				echo $this->render('login/login_user.html.twig', ['id_section' => $id_section, 'msgSystem'=>'Nombre de usuario o contraseña incorrecto!!','action_system'=>'alert']);
			}
		} else {
			$id_section = uniqid();
			$_SESSION['id_token'] = $id_section;
			//header("Location: http://www.dronesmaritime.com");
			echo $this->render('login/login_user.html.twig', ['id_section' => $id_section, 'msgSystem'=>'Formulario no valido para procesar!!!','action_system'=>'alert']);
		}

	}
	public function get_home_system()
	{
		echo $this->render('home/dashboard.html.twig', ['id_section' => '123456', 'msgSystem'=>'','action_system'=>'']);
	}
	public function logout_system()
	{
		unset($_SESSION['id_section']);
		header("Location: /");
		die();
	}
	public function view_log()
	{
		$id_section = uniqid();

		echo $this->render('home/view_log_sistema.html.twig', ['id_section' => $id_section, 'msgSystem'=>'', 'action_system'=>'dont']);

	}
	public function render_viewLogList():array
	{
		extract($_POST);

		$data = array();

		$logSistema = new LogSistema();


		$txtSearch= "";
        $order = array(0=>['dir'=>'asc','column'=>0]);
        $length= 5;
        $start =  0;
		/*$filterData=array(
				'fecha_inicio' 	=> $fecha_inicio,
				'fecha_final'  	=> $fecha_final,
				'estatus'		=> $estatus,
				'id_usuario'	=> $id_usuario,
				'id_cliente'	=> $id_cliente
			);*/

		//$logSistema->set_filterData($filterData);

		$result  = $logSistema->listLogSis($txtSearch,$order,$length,$start);

		$total_reg = $logSistema->countLogSis($txtSearch);

		$contar = 0;
		$clave = "";
		$idCifrado = "";
		// se guarda la clave en un txt
		//$key = Key::createNewRandomKey();
		//echo $key->saveToAsciiSafeString(), "\n";
		//$contenido = file_get_contents("web\core\app\clave.txt");
		$html = "";
		foreach ($result as $row) {
			$html ='<p>ID: '.$row['id_secuen'];
			$html .=' ACCIÓN: '.$row['accion'];
			$html .=' USUARIO: '.$row['id_usuario'].'/'.$row['nombres'].'/'.$row['alias'];
			$html .=' ROL: '.$row['role'];
			$html .=' FECHA : '.$row['fecha_hora'].'</p>';

			$data['data'][] = array(
					'contenido' => $html
				);
			$contar++;
		}
		if ($contar==0)
		{
			$data['data'][]=array('contenido'=>'');
		}
		if ($total_reg>$length){
			$data['recordsTotal']=(integer)$total_reg;
			$data['recordsFiltered']=(integer)$length;
		} else {
			$data['recordsTotal']=(integer)1;
			$data['recordsFiltered']=(integer)1;
		}

		return $data;
	}	

}

?>