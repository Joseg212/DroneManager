<?php 

namespace DronesMaritime\Core\App;

use DronesMaritime\Core\Models\Usuarios;
use Defuse\Crypto\Crypto;
use Defuse\Crypto\Key;

final class UserApp extends Controller
{

	public function get_list()
	{
		$id_section = uniqid();


		echo $this->render('usuario/usuario_lista.html.twig', ['id_section' => $id_section, 'msgSystem'=>'', 'action_system'=>'dont']);

	}

	public function render_list(string $search,array $order, int $length, int $start):array
	{
		$data = array();

		$usuarios = new Usuarios();

		$result  = $usuarios->listUsuario($search,$order,$length,$start);

		$total_reg = $usuarios->countUsuario($search);

		$contar = 0;
		$clave = "";
		$idCifrado = "";
		// se guarda la clave en un txt
		//$key = Key::createNewRandomKey();
		//echo $key->saveToAsciiSafeString(), "\n";
		//$contenido = file_get_contents("web\core\app\clave.txt");
		foreach ($result as $row) {
			$clave = Key::loadFromAsciiSafeString($_SESSION['key_crypto']);
			$idCifrado = Crypto::encrypt($row['id_usuario'], $clave);

			var_dump($idCifrado);

			$data['data'][] = array(
					'id' 	  => $row['id_usuario'],
					'nombres' => $row['nombres'],
					'usuario' => $row['user'],
					'roll'	  => $row['role'],
					'opcion'  => '<div data-id="'.$idCifrado.'"> <a href="#" class="modify_user" ><i class="zmdi zmdi-border-color zmdi-hc-fw" style="color:#6b8df6;font-size: 1.6rem;"></i> </a> <a href="#" class="delete_user"><i class="zmdi zmdi-close-circle zmdi-hc-fw" style="color:#de8080;margin-left:1.2rem;font-size: 1.6rem;"></i> </a></div>'
				);
			$contar++;
		}
		if ($contar==0)
		{
			$data['data'][]=array('id'=>'','nombres'=>'','usuario'=>'','option'=>'','roll'=>'');
		}
		if ($total_reg>$length){
			$data['recordsTotal']=(integer)$total_reg;
			$data['recordsFiltered']=(integer)$length;
		} else {
			$data['recordsTotal']=(integer)$total_reg;
			$data['recordsFiltered']=(integer)$total_reg;
		}

		return $data;
	}
	public function new_user()
	{
		$token_id = uniqid();

		$_SESSION['token_form'] = $token_id;

		echo $this->render('usuario/usuario_incluir.html.twig', ['token_id' => $token_id, 'msgSystem'=>'', 'action_system'=>'dont']);

	}
	public function set_user()
	{
		extract($_POST);

		$passwCriptor =  hash('sha1', $userPassw);

		$usuarios = new Usuarios();

		$fechareg = new  \DateTime(date());

		$msgSystem = '';
		$actionSystem = 'dont';
		if ($token_id==$_SESSION['token_form'])
		{
			try {
				$data = array(
						'id_usuario'=>'',
						'nombres'=>$nombres,
						'telf_movil'=>$telfm,
						'email'=>$email,
						'cargo'=>$cargo,
						'user' =>$userName,
						'password'=>$passwCriptor,
						'fecha_reg'=>$fechareg,
						'role'=>$roll
					);

				if (!$usuarios->writeUser($data,'new')){
					$msgSystem = $usuarios->get_msgErr();
					$actionSystem = 'display';
				}

			} catch (Exception $err) {
				$msgSystem = 'Sistema: '.$err->getMessage();
				$actionSystem = 'display';
			}
		} else {
			$msgSystem = 'Token not is valid!!!';
			$actionSystem = 'display';
		}
		if ($actionSystem=='display'){
			// reporta el error al usuario 
			echo $this->render('usuario/usuario_incluir.html.twig', ['token_id' => $token_id, 'msgSystem'=>$msgSystem, 'action_system'=>$actionSystem]);

		} else {
			// se redirecciona a la pantalla anterior
			header("Location: /usuarios");
			die();
		}
	}
	public function del_user(string $id_user):array 
	{
		$data = array();

		$usuarios = new Usuarios();

		$clave = Key::loadFromAsciiSafeString($_SESSION['key_crypto']);
		$id_usuario = Crypto::decrypt($id_user, $clave);

		$data['id_usuario'] = $id_usuario;

		if ($usuarios->writeUser($data,'delete'))
		{
			$data['ok']='01';
			$data['msg']='Correct!!';
		} else {
			$data['ok']='15';
			$data['msg']='Error '.$usuarios->get_msgErr();
		}

		return $data;	
	}
	public function edit_user()
	{
		$token_id = uniqid();

		$_SESSION['token_form'] = $token_id;

		$usuario = new Usuarios();


		$clave = Key::loadFromAsciiSafeString($_SESSION['key_crypto']);
		$id_usuario = Crypto::decrypt($_GET['data'], $clave);

		$data = $usuario->get_dataUser($id_usuario)[0];

		echo $this->render('usuario/usuario_editar.html.twig', ['token_id' => $token_id, 'msgSystem'=>'', 'action_system'=>'dont', 'usuario' => $data, 'idUser' => $_GET['data']]);

	}	
	public function update_user()
	{
		extract($_POST);

		$data = array();

		$clave = Key::loadFromAsciiSafeString($_SESSION['key_crypto']);
		$id_usuario = Crypto::decrypt($_GET['data'], $clave);

		$usuarios = new Usuarios();

		$data = $usuarios->get_dataUser($id_usuario)[0];

		if (!empty($userPassw)){
			$passwCriptor =  hash('sha1', $userPassw);
			$data['password'] = $passwCriptor;
		} else {
			unset($data['password']);
		}

		//unset($data['id_usuario']);
		//unset($data['user']);

		$data['nombres'] 	= $nombres;
		$data['email']		= $email;
		$data['cargo']		= $cargo;
		$data['telf_movil'] = $telfm;
		$data['role']		= $roll;


		$msgSystem = '';
		$actionSystem = 'dont';
		if ($token_id==$_SESSION['token_form'])
		{
			try {

				if (!$usuarios->writeUser($data,'edit')){
					$msgSystem = $usuarios->get_msgErr();
					$actionSystem = 'display';
				}

			} catch (Exception $err) {
				$msgSystem = 'Sistema: '.$err->getMessage();
				$actionSystem = 'display';
			}
		} else {
			$msgSystem = 'Token not is valid!!!';
			$actionSystem = 'display';
		}
		if ($actionSystem=='display'){
			// reporta el error al usuario 
			$data['id_usuario'] 	= $id_usuario;
			echo $this->render('usuario/usuario_editar.html.twig', ['token_id' => $token_id,'msgSystem'=>$msgSystem,
				'action_system'=>$actionSystem, 'usuario' => $data,'idUser' => $_GET['data']]);

		} else {
			// se redirecciona a la pantalla anterior
			header("Location: /usuarios");
			die();
		}
	}

	public function render_searchList(string $search,array $order, int $length, int $start, string $type):array
	{
		$data = array();

		$usuarios = new Usuarios();

		$result  = $usuarios->listUsuarioSel($search,$order,$length,$start,$type);

		$total_reg = $usuarios->countUsuarioSel($search, $type);

		$contar = 0;
		$clave = "";
		$idCifrado = "";
		// se guarda la clave en un txt
		//$key = Key::createNewRandomKey();
		//echo $key->saveToAsciiSafeString(), "\n";
		//$contenido = file_get_contents("web\core\app\clave.txt");
		foreach ($result as $row) {
			$clave = Key::loadFromAsciiSafeString($_SESSION['key_crypto']);
			$idCifrado = Crypto::encrypt($row['id_usuario'], $clave);

			var_dump($idCifrado);

			$data['data'][] = array(
					'id' 	  => $row['id_usuario'],
					'nombres' => $row['nombres'],
					'usuario' => $row['user'],
					'midata'  => $row
				);
			$contar++;
		}
		if ($contar==0)
		{
			$data['data'][]=array('id'=>'','nombres'=>'','usuario'=>'','midata'=>array('id_usuario'=>'','nombres'=>''));
		}
		if ($total_reg>$length){
			$data['recordsTotal']=(integer)$total_reg;
			$data['recordsFiltered']=(integer)$length;
		} else {
			$data['recordsTotal']=(integer)$total_reg;
			$data['recordsFiltered']=(integer)$total_reg;
		}

		return $data;
	}
}