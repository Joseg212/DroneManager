<?php 

namespace DronesMaritime\Core\App;

use DronesMaritime\Core\Models\TipoMision;
use Defuse\Crypto\Crypto;
use Defuse\Crypto\Key;

final class MissionTypeApp extends Controller
{

	public function get_list()
	{
		$id_section = uniqid();


		echo $this->render('tipomision/tipomision_lista.html.twig', ['id_section' => $id_section, 'msgSystem'=>'', 'action_system'=>'dont']);

	}

	public function render_list(string $search,array $order, int $length, int $start):array
	{
		$data = array();

		$tipomision = new TipoMision();

		$result  = $tipomision->listTipom($search,$order,$length,$start);

		$total_reg = $tipomision->countTipom($search);

		$contar = 0;
		$clave = "";
		$idCifrado = "";
		// se guarda la clave en un txt
		//$key = Key::createNewRandomKey();
		//echo $key->saveToAsciiSafeString(), "\n";
		//$contenido = file_get_contents("web\core\app\clave.txt");
		foreach ($result as $row) {
			$clave = Key::loadFromAsciiSafeString($_SESSION['key_crypto']);
			$idCifrado = Crypto::encrypt($row['id_tipom'], $clave);

			$data['data'][] = array(
					'id' 	  => $row['id_tipom'],
					'denominacion' => $row['denominacion'],
					'descripcion' => $row['descripcion'],
					'opcion'  => '<div data-id="'.$idCifrado.'"> <a href="#" class="modify_mtype" ><i class="zmdi zmdi-border-color zmdi-hc-fw" style="color:#6b8df6;font-size: 1.6rem;"></i> </a> <a href="#" class="delete_mtype"><i class="zmdi zmdi-close-circle zmdi-hc-fw" style="color:#de8080;margin-left:1.2rem;font-size: 1.6rem;"></i> </a></div>'
				);
			$contar++;
		}
		if ($contar==0)
		{
			$data['data'][]=array('id'=>'','denominacion'=>'','descripcion'=>'','opcion'=>'');
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
	public function new_mtype()
	{
		$token_id = uniqid();

		$_SESSION['token_form'] = $token_id;

		echo $this->render('tipomision/tipomision_incluir.html.twig', ['token_id' => $token_id, 'msgSystem'=>'', 'action_system'=>'dont']);

	}
	public function set_mtype()
	{
		extract($_POST);

	
		$tipomision = new TipoMision();

		$fechareg = new  \DateTime(date());

		$msgSystem = '';
		$actionSystem = 'dont';
		if ($token_id==$_SESSION['token_form'])
		{
			try {
				$data = array(
						'id_tipom'=>'',
						'denominacion'=>$denominacion,
						'descripcion'=>$descripcion,
						'fecha_reg'=>$fechareg
					);

				if (!$tipomision->writeTipom($data,'new')){
					$msgSystem = $tipomision->get_msgErr();
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
			echo $this->render('tipomision/tipomision_incluir.html.twig', ['token_id' => $token_id, 'msgSystem'=>$msgSystem, 'action_system'=>$actionSystem]);

		} else {
			// se redirecciona a la pantalla anterior
			header("Location: /tipomisiones");
			die();
		}
	}
	public function del_mtype(string $id_mtype):array 
	{
		$data = array();

		$tipomision = new TipoMision();

		$clave = Key::loadFromAsciiSafeString($_SESSION['key_crypto']);
		$id_tipom = Crypto::decrypt($id_mtype, $clave);

		$data['id_tipom'] = $id_tipom;

		if ($tipomision->writeTipom($data,'delete'))
		{
			$data['ok']='01';
			$data['msg']='Correct!!';
		} else {
			$data['ok']='15';
			$data['msg']='Error '.$tipomision->get_msgErr();
		}

		return $data;	
	}
	public function edit_mtype()
	{
		$token_id = uniqid();

		$_SESSION['token_form'] = $token_id;

		$tipomision = new TipoMision();


		$clave = Key::loadFromAsciiSafeString($_SESSION['key_crypto']);
		$id_tipom = Crypto::decrypt($_GET['data'], $clave);

		$data = $tipomision->get_dataTipom($id_tipom)[0];

		echo $this->render('tipomision/tipomision_editar.html.twig', ['token_id' => $token_id, 'msgSystem'=>'', 'action_system'=>'dont', 'tipomision' => $data, 'idTipom' => $_GET['data']]);

	}	
	public function update_mtype()
	{
		extract($_POST);

		$data = array();

		$clave = Key::loadFromAsciiSafeString($_SESSION['key_crypto']);
		$id_tipom = Crypto::decrypt($_GET['data'], $clave);

		$tipomision = new TipoMision();

		$data = $tipomision->get_dataTipom($id_tipom)[0];

		//unset($data['id_tipom']);
		//unset($data['user']);

		$data['denominacion'] 	= $denominacion;
		$data['descripcion']	= $descripcion;


		$msgSystem = '';
		$actionSystem = 'dont';
		if ($token_id==$_SESSION['token_form'])
		{
			try {

				if (!$tipomision->writeTipom($data,'edit')){
					$msgSystem = $tipomision->get_msgErr();
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
			$data['id_tipom'] 	= $id_tipom;
			echo $this->render('tipomision/tipomision_editar.html.twig', ['token_id' => $token_id,'msgSystem'=>$msgSystem,
				'action_system'=>$actionSystem, 'tipomision' => $data,'idTipom' => $_GET['data']]);

		} else {
			// se redirecciona a la pantalla anterior
			header("Location: /tipomisiones");
			die();
		}
	}

	public function render_searchList(string $search,array $order, int $length, int $start):array
	{
		$data = array();

		$tipomision = new TipoMision();

		$result  = $tipomision->listTipom($search,$order,$length,$start);

		$total_reg = $tipomision->countTipom($search);

		$contar = 0;
		$clave = "";
		$idCifrado = "";
		foreach ($result as $row) {

			$data['data'][] = array(
					'id_tipom' 	  	=> $row['id_tipom'],
					'denominacion' 	=> $row['denominacion'],
					'midata'		=> $row
 				);
			$contar++;
		}
		if ($contar==0)
		{
			$data['data'][]=array('id_tipom'=>'','denominacion'=>'','midata'=>array('id_tipom'=>''));
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