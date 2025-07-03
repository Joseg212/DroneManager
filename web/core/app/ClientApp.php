<?php 

namespace DronesMaritime\Core\App;

use DronesMaritime\Core\Models\Cliente;
use Defuse\Crypto\Crypto;
use Defuse\Crypto\Key;

final class ClientApp extends Controller
{

	public function get_list()
	{
		$id_section = uniqid();


		echo $this->render('cliente/cliente_lista.html.twig', ['id_section' => $id_section, 'msgSystem'=>'', 'action_system'=>'dont']);

	}

	public function render_list(string $search,array $order, int $length, int $start):array
	{
		$data = array();

		$cliente = new Cliente();

		$result  = $cliente->listCliente($search,$order,$length,$start);

		$total_reg = $cliente->countCliente($search);

		$contar = 0;
		$clave = "";
		$idCifrado = "";
		// se guarda la clave en un txt
		//$key = Key::createNewRandomKey();
		//echo $key->saveToAsciiSafeString(), "\n";
		//$contenido = file_get_contents("web\core\app\clave.txt");
		foreach ($result as $row) {
			$clave = Key::loadFromAsciiSafeString($_SESSION['key_crypto']);
			$idCifrado = Crypto::encrypt($row['id_cliente'], $clave);

			$data['data'][] = array(
					'id' 	   => $row['id_cliente'],
					'compania' => $row['compania'],
					'contacto' => $row['contacto'],
					'email'	   => $row['email'],
					'opcion'   => '<div data-id="'.$idCifrado.'"> <a href="#" class="modify_client" ><i class="zmdi zmdi-border-color zmdi-hc-fw" style="color:#6b8df6;font-size: 1.6rem;"></i> </a> <a href="#" class="delete_client"><i class="zmdi zmdi-close-circle zmdi-hc-fw" style="color:#de8080;margin-left:1.2rem;font-size: 1.6rem;"></i> </a></div>'
				);
			$contar++;
		}
		if ($contar==0)
		{
			$data['data'][]=array('id'=>'','compania'=>'','contacto'=>'','email'=>'','opcion'=>'');
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
	public function new_client()
	{
		$token_id = uniqid();

		$_SESSION['token_form'] = $token_id;

		echo $this->render('cliente/cliente_incluir.html.twig', ['token_id' => $token_id, 'msgSystem'=>'', 'action_system'=>'dont']);

	}
	public function set_client()
	{
		extract($_POST);

		//$passwCriptor =  hash('sha1', $userPassw);

		$cliente = new Cliente();

		$fechareg = new  \DateTime(date());

		$msgSystem = '';
		$actionSystem = 'dont';
		if ($token_id==$_SESSION['token_form'])
		{
			try {
				$data = array(
						'id_cliente'=>'',
						'compania'=>$compania,
						'direccion'=>$direccion,
						'rif_nit'=>$rif_nit,
						'email'=>$email,
						'telf_cia' =>$telf_cia,
						'contacto'=>$contacto,
						'telf_contact'=>$telf_contact,
						'estatus'=>'Activo',
						'fecha_reg'=>$fechareg
					);

				if (!$cliente->writeCliente($data,'new')){
					$msgSystem = $cliente->get_msgErr();
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
			echo $this->render('cliente/cliente_incluir.html.twig', ['token_id' => $token_id, 'msgSystem'=>$msgSystem, 'action_system'=>$actionSystem]);

		} else {
			// se redirecciona a la pantalla anterior
			header("Location: /clientes");
			die();
		}
	}
	public function del_client(string $id_client):array 
	{
		$data = array();

		$cliente = new Cliente();

		$clave = Key::loadFromAsciiSafeString($_SESSION['key_crypto']);
		$id_cliente = Crypto::decrypt($id_client, $clave);

		$data['id_cliente'] = $id_cliente;

		if ($cliente->writeCliente($data,'delete'))
		{
			$data['ok']='01';
			$data['msg']='Correct!!';
		} else {
			$data['ok']='15';
			$data['msg']='Error '.$cliente->get_msgErr();
		}

		return $data;	
	}
	public function edit_client()
	{
		$token_id = uniqid();

		$_SESSION['token_form'] = $token_id;

		$cliente = new Cliente();


		$clave = Key::loadFromAsciiSafeString($_SESSION['key_crypto']);
		$id_cliente = Crypto::decrypt($_GET['data'], $clave);

		$data = $cliente->get_dataCliente($id_cliente)[0];

		echo $this->render('cliente/cliente_editar.html.twig', ['token_id' => $token_id, 'msgSystem'=>'', 'action_system'=>'dont', 'cliente' => $data, 'idClient' => $_GET['data']]);

	}	
	public function update_client()
	{
		extract($_POST);

		$data = array();

		$clave = Key::loadFromAsciiSafeString($_SESSION['key_crypto']);
		$id_cliente = Crypto::decrypt($_GET['data'], $clave);

		$cliente = new Cliente();

		$data = $cliente->get_dataCliente($id_cliente)[0];


		//unset($data['id_cliente']);
		//unset($data['user']);

		$data['compania'] 	= $compania;
		$data['email']		= $email;
		$data['direccion']	= $direccion;
		$data['rif_nit'] 	= $rif_nit;
		$data['telf_cia']	= $telf_cia;
		$data['contacto']	= $contacto;
		$data['telf_contact'] =	$telf_contact;


		$msgSystem = '';
		$actionSystem = 'dont';
		if ($token_id==$_SESSION['token_form'])
		{
			try {

				if (!$cliente->writeCliente($data,'edit')){
					$msgSystem = $cliente->get_msgErr();
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
			$data['id_cliente'] 	= $id_cliente;
			echo $this->render('cliente/cliente_editar.html.twig', ['token_id' => $token_id,'msgSystem'=>$msgSystem,
				'action_system'=>$actionSystem, 'cliente' => $data,'idClient' => $_GET['data']]);

		} else {
			// se redirecciona a la pantalla anterior
			header("Location: /clientes");
			die();
		}
	}



	public function render_searchList(string $search,array $order, int $length, int $start):array
	{
		$data = array();

		$cliente = new Cliente();

		$result  = $cliente->listCliente02($search,$order,$length,$start);

		$total_reg = $cliente->countCliente($search);

		$contar = 0;
		$clave = "";
		$idCifrado = "";
		// se guarda la clave en un txt
		//$key = Key::createNewRandomKey();
		//echo $key->saveToAsciiSafeString(), "\n";
		//$contenido = file_get_contents("web\core\app\clave.txt");
		foreach ($result as $row) {
			$data['data'][] = array(
					'id_cliente'		=> $row['id_cliente'],
					'compania'  => '<p style="white-space: break-spaces;">'.$row['compania'].'</p>',
					'midata' 	=> $row
				);
			$contar++;
		}
		if ($contar==0)
		{
			$data['data'][]=array('compania'=>'','id_cliente'=>'','midata'=>array('id_cliente'=>''));
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