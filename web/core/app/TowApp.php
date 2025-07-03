<?php 

namespace DronesMaritime\Core\App;

use DronesMaritime\Core\Models\Vehiculo;
use Defuse\Crypto\Crypto;
use Defuse\Crypto\Key;

final class TowApp extends Controller
{

	public function get_list()
	{
		$id_section = uniqid();


		echo $this->render('vehiculo/vehiculo_lista.html.twig', ['id_section' => $id_section, 'msgSystem'=>'', 'action_system'=>'dont']);

	}

	public function render_list(string $search,array $order, int $length, int $start):array
	{
		$data = array();

		$vehiculo = new Vehiculo();

		$result  = $vehiculo->listVehiculo($search,$order,$length,$start);

		$total_reg = $vehiculo->countVehiculo($search);

		$contar = 0;
		$clave = "";
		$idCifrado = "";
		// se guarda la clave en un txt
		//$key = Key::createNewRandomKey();
		//echo $key->saveToAsciiSafeString(), "\n";
		//$contenido = file_get_contents("web\core\app\clave.txt");
		foreach ($result as $row) {
			$clave = Key::loadFromAsciiSafeString($_SESSION['key_crypto']);
			$idCifrado = Crypto::encrypt($row['id_vehiculo'], $clave);

			$data['data'][] = array(
					'id' 	  => $row['id_vehiculo'],
					'modelo' => $row['modelo'],
					'matricula' => $row['matricula'],
					'responsable' => $row['responsable'],
					'opcion'  => '<div data-id="'.$idCifrado.'"> <a href="#" class="modify_tow" ><i class="zmdi zmdi-border-color zmdi-hc-fw" style="color:#6b8df6;font-size: 1.6rem;"></i> </a> <a href="#" class="delete_tow"><i class="zmdi zmdi-close-circle zmdi-hc-fw" style="color:#de8080;margin-left:1.2rem;font-size: 1.6rem;"></i> </a></div>'
				);
			$contar++;
		}
		if ($contar==0)
		{
			$data['data'][]=array('id'=>'','modelo'=>'','matricula'=>'','responsable'=>'','opcion'=>'');
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
	public function new_tow()
	{
		$token_id = uniqid();

		$_SESSION['token_form'] = $token_id;

		echo $this->render('vehiculo/vehiculo_incluir.html.twig', ['token_id' => $token_id, 'msgSystem'=>'', 'action_system'=>'dont']);

	}
	public function set_tow()
	{
		extract($_POST);

	
		$vehiculo = new Vehiculo();

		$fechareg = new  \DateTime(date());

		$msgSystem = '';
		$actionSystem = 'dont';
		if ($token_id==$_SESSION['token_form'])
		{
			try {
				$data = array(
						'id_vehiculo'=>'',
						'modelo'=>$modelo,
						'matricula'=>$matricula,
						'responsable'=>$responsable,
						'fecha_reg'=>$fechareg
					);

				if (!$vehiculo->writeVehiculo($data,'new')){
					$msgSystem = $vehiculo->get_msgErr();
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
			echo $this->render('vehiculo/vehiculo_incluir.html.twig', ['token_id' => $token_id, 'msgSystem'=>$msgSystem, 'action_system'=>$actionSystem]);

		} else {
			// se redirecciona a la pantalla anterior
			header("Location: /vehiculos");
			die();
		}
	}
	public function del_tow(string $id_tow):array 
	{
		$data = array();

		$vehiculo = new Vehiculo();

		$clave = Key::loadFromAsciiSafeString($_SESSION['key_crypto']);
		$id_vehiculo = Crypto::decrypt($id_tow, $clave);

		$data['id_vehiculo'] = $id_vehiculo;

		if ($vehiculo->writeVehiculo($data,'delete'))
		{
			$data['ok']='01';
			$data['msg']='Correct!!';
		} else {
			$data['ok']='15';
			$data['msg']='Error '.$vehiculo->get_msgErr();
		}

		return $data;	
	}
	public function edit_tow()
	{
		$token_id = uniqid();

		$_SESSION['token_form'] = $token_id;

		$vehiculo = new Vehiculo();


		$clave = Key::loadFromAsciiSafeString($_SESSION['key_crypto']);
		$id_vehiculo = Crypto::decrypt($_GET['data'], $clave);

		$data = $vehiculo->get_dataVehiculo($id_vehiculo)[0];

		echo $this->render('vehiculo/vehiculo_editar.html.twig', ['token_id' => $token_id, 'msgSystem'=>'', 'action_system'=>'dont', 'vehiculo' => $data, 'idTow' => $_GET['data']]);

	}	
	public function update_tow()
	{
		extract($_POST);

		$data = array();

		$clave = Key::loadFromAsciiSafeString($_SESSION['key_crypto']);
		$id_vehiculo = Crypto::decrypt($_GET['data'], $clave);

		$vehiculo = new Vehiculo();

		$data = $vehiculo->get_dataVehiculo($id_vehiculo)[0];

		//unset($data['id_vehiculo']);
		//unset($data['user']);

		$data['modelo'] 		= $modelo;
		$data['matricula']		= $matricula;
		$data['responsable']	= $responsable;


		$msgSystem = '';
		$actionSystem = 'dont';

		if ($token_id==$_SESSION['token_form'])
		{
			try {

				if (!$vehiculo->writeVehiculo($data,'edit')){
					$msgSystem = $vehiculo->get_msgErr();
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
			$data['id_vehiculo'] 	= $id_vehiculo;
			echo $this->render('vehiculo/vehiculo_editar.html.twig', ['token_id' => $token_id,'msgSystem'=>$msgSystem,
				'action_system'=>$actionSystem, 'tipomision' => $data,'idTipom' => $_GET['data']]);

		} else {
			// se redirecciona a la pantalla anterior
			header("Location: /vehiculos");
			die();
		}
	}

	public function render_searchList(string $search,array $order, int $length, int $start):array
	{
		$data = array();

		$vehiculo = new Vehiculo();

		$result  = $vehiculo->listVehiculo($search,$order,$length,$start);

		$total_reg = $vehiculo->countVehiculo($search);

		$contar = 0;
		$clave = "";
		$idCifrado = "";
		// se guarda la clave en un txt
		//$key = Key::createNewRandomKey();
		//echo $key->saveToAsciiSafeString(), "\n";
		//$contenido = file_get_contents("web\core\app\clave.txt");
		foreach ($result as $row) {
			$clave = Key::loadFromAsciiSafeString($_SESSION['key_crypto']);
			$idCifrado = Crypto::encrypt($row['id_vehiculo'], $clave);

			$data['data'][] = array(
					'id' 	  		=> $row['id_vehiculo'],
					'modelo' 		=> $row['modelo'],
					'matricula' 	=> $row['matricula'],
					'midata'		=> $row
				);
			$contar++;
		}
		if ($contar==0)
		{
			$data['data'][]=array('id'=>'','modelo'=>'','matricula'=>'','midata'=>array('id_vehiculo'=>'','modelo'=>''));
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