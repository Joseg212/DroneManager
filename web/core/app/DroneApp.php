<?php 

namespace DronesMaritime\Core\App;

use DronesMaritime\Core\Models\Drone;
use Defuse\Crypto\Crypto;
use Defuse\Crypto\Key;

final class DroneApp extends Controller
{

	public function get_list()
	{
		$id_section = uniqid();


		echo $this->render('drone/drone_lista.html.twig', ['id_section' => $id_section, 'msgSystem'=>'', 'action_system'=>'dont']);

	}

	public function render_list(string $search,array $order, int $length, int $start):array
	{
		$data = array();

		$drone = new Drone();

		$result  = $drone->listDrone($search,$order,$length,$start);

		$total_reg = $drone->countDrone($search);

		$contar = 0;
		$clave = "";
		$idCifrado = "";
		// se guarda la clave en un txt
		//$key = Key::createNewRandomKey();
		//echo $key->saveToAsciiSafeString(), "\n";
		//$contenido = file_get_contents("web\core\app\clave.txt");
		foreach ($result as $row) {
			$clave = Key::loadFromAsciiSafeString($_SESSION['key_crypto']);
			$idCifrado = Crypto::encrypt($row['id_drone'], $clave);


			$data['data'][] = array(
					'id' 	  => $row['id_drone'],
					'modelo' => $row['modelo'],
					'numserie' => $row['num_serie'],
					'opcion'  => '<div data-id="'.$idCifrado.'"> <a href="#" class="modify_drone" ><i class="zmdi zmdi-border-color zmdi-hc-fw" style="color:#6b8df6;font-size: 1.6rem;"></i> </a> <a href="#" class="delete_drone"><i class="zmdi zmdi-close-circle zmdi-hc-fw" style="color:#de8080;margin-left:1.2rem;font-size: 1.6rem;"></i> </a></div>'
				);
			$contar++;
		}
		if ($contar==0)
		{
			$data['data'][]=array('id'=>'','modelo'=>'','numserie'=>'','opcion'=>'');
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
	public function new_drone()
	{
		$token_id = uniqid();

		$_SESSION['token_form'] = $token_id;

		echo $this->render('drone/drone_incluir.html.twig', ['token_id' => $token_id, 'msgSystem'=>'', 'action_system'=>'dont']);

	}
	public function set_drone()
	{
		extract($_POST);

		$drone = new Drone();

		$fechareg = new  \DateTime(date());

		$msgSystem = '';
		$actionSystem = 'dont';
		if ($token_id==$_SESSION['token_form'])
		{
			try {
				$data = array(
						'id_drone'=>'',
						'modelo'=>$modelo,
						'num_serie'=>$num_serie,
						'fecha_reg'=>$fechareg
					);

				if (!$drone->writeDrone($data,'new')){
					$msgSystem = $drone->get_msgErr();
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
			echo $this->render('drone/drone_incluir.html.twig', ['token_id' => $token_id, 'msgSystem'=>$msgSystem, 'action_system'=>$actionSystem]);

		} else {
			// se redirecciona a la pantalla anterior
			header("Location: /drones");
			die();
		}
	}
	public function del_drone(string $id_mtype):array 
	{
		$data = array();

		$drone = new Drone();

		$clave = Key::loadFromAsciiSafeString($_SESSION['key_crypto']);
		$id_drone = Crypto::decrypt($id_mtype, $clave);

		$data['id_drone'] = $id_drone;

		if ($drone->writeDrone($data,'delete'))
		{
			$data['ok']='01';
			$data['msg']='Correct!!';
		} else {
			$data['ok']='15';
			$data['msg']='Error '.$drone->get_msgErr();
		}

		return $data;	
	}
	public function edit_drone()
	{
		$token_id = uniqid();

		$_SESSION['token_form'] = $token_id;

		$drone = new Drone();


		$clave = Key::loadFromAsciiSafeString($_SESSION['key_crypto']);
		$id_drone = Crypto::decrypt($_GET['data'], $clave);

		$data = $drone->get_dataDrone($id_drone)[0];

		echo $this->render('drone/drone_editar.html.twig', ['token_id' => $token_id, 'msgSystem'=>'', 'action_system'=>'dont', 'drone' => $data, 'idDrone' => $_GET['data']]);

	}	
	public function update_drone()
	{
		extract($_POST);

		$data = array();

		$clave = Key::loadFromAsciiSafeString($_SESSION['key_crypto']);
		$id_drone = Crypto::decrypt($_GET['data'], $clave);

		$drone = new Drone();

		$data = $drone->get_dataDrone($id_drone)[0];

		//unset($data['id_drone']);
		//unset($data['user']);

		$data['modelo'] 	= $modelo;
		$data['num_serie']	= $num_serie;


		$msgSystem = '';
		$actionSystem = 'dont';
		if ($token_id==$_SESSION['token_form'])
		{
			try {

				if (!$drone->writeDrone($data,'edit')){
					$msgSystem = $drone->get_msgErr();
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
			$data['id_drone'] 	= $id_drone;
			echo $this->render('drone/drone_editar.html.twig', ['token_id' => $token_id,'msgSystem'=>$msgSystem,
				'action_system'=>$actionSystem, 'drone' => $data,'idDrone' => $_GET['data']]);

		} else {
			// se redirecciona a la pantalla anterior
			header("Location: /drones");
			die();
		}
	}

	public function render_searchList(string $search,array $order, int $length, int $start):array
	{
		$data = array();

		$drone = new Drone();

		$result  = $drone->listDrone($search,$order,$length,$start);

		$total_reg = $drone->countDrone($search);

		$contar = 0;
		$clave = "";
		$idCifrado = "";
		// se guarda la clave en un txt
		//$key = Key::createNewRandomKey();
		//echo $key->saveToAsciiSafeString(), "\n";
		//$contenido = file_get_contents("web\core\app\clave.txt");
		foreach ($result as $row) {
			$clave = Key::loadFromAsciiSafeString($_SESSION['key_crypto']);
			$idCifrado = Crypto::encrypt($row['id_drone'], $clave);


			$data['data'][] = array(
					'id' 	  => $row['id_drone'],
					'modelo' => $row['modelo'],
					'numserie' => $row['num_serie'],
					'midata'  => $row
				);
			$contar++;
		}
		if ($contar==0)
		{
			$data['data'][]=array('id'=>'','modelo'=>'','numserie'=>'','midata'=>array('id_drone'=>'','modelo'=>''));
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