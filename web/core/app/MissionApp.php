<?php 

namespace DronesMaritime\Core\App;

use DronesMaritime\Core\Models\Mision;
use DronesMaritime\Core\Models\TmpPilot;
use DronesMaritime\Core\Models\Plan;
use DronesMaritime\Core\Models\Pilotos;
use Defuse\Crypto\Crypto;
use Defuse\Crypto\Key;

final class MissionApp extends Controller
{

	public function get_list()
	{
		$id_section = uniqid();

		echo $this->render('mision/mision_inicial.html.twig', ['id_section' => $id_section, 'msgSystem'=>'', 'action_system'=>'dont']);

	}

	public function render_list(string $search,array $order, int $length, int $start):array
	{
		$data = array();

		$mision = new Mision();

		$result  = $mision->listMission($search,$order,$length,$start);

		$total_reg = $mision->countMission($search);

		$contar = 0;
		$clave = "";
		$idCifrado = "";
		// se guarda la clave en un txt
		//$key = Key::createNewRandomKey();
		//echo $key->saveToAsciiSafeString(), "\n";
		//$contenido = file_get_contents("web\core\app\clave.txt");
		$sec = 0;
		$htmlCode="";
		foreach ($result as $row) {
			$clave = Key::loadFromAsciiSafeString($_SESSION['key_crypto']);
			$idCifrado = Crypto::encrypt($row['id_mision'], $clave);

			//$row['descrip'] = utf8_encode($row['descrip']);

			$htmlCode = $this->render('mision/item_mision.html.twig', ['mision' => $row, 'sec'=>strval($sec),'idMission'=>$idCifrado,'typeButton'=>'action']);


			$data['data'][] = array(
					'contenido' => $htmlCode
				);
			$contar++;
		} // fin del for 
		if ($contar==0)
		{
			$data['data'][]=array('contenido'=>'No hay misiones activas debe ingresar una para gestionar en esta pantalla');
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
	public function new_mission()
	{
		$token_id = uniqid();

		$_SESSION['token_form'] = $token_id;

		$fechareg = new  \DateTime(date());

		ob_clean();

		$fechaRango = $fechareg->format("d/m/Y").' - '.$fechareg->format("d/m/Y");		

		echo $this->render('mision/mision_nueva.html.twig', ['token_id' => $token_id, 'msgSystem'=>'', 'action_system'=>'dont', 'fechaRango' => $fechaRango]);

	}

	public function set_mission()
	{
		extract($_POST);

		$mision = new Mision();

		$arrFecha = explode($rango_fecha, ' - ');
		try {
			$fechareg = new  \DateTime(date());
		} catch(Exception $err) {

		}

		$fechaInicio = new  \DateTime(date('d/m/Y',$arrFecha[0]));
		$fechaFinal = new  \DateTime(date('d/m/Y',$arrFecha[0]));

		$msgSystem = '';
		$ok = 'dont';
		if ($token_id==$_SESSION['token_form'])
		{
			try {
				$data = array(
						'id_mision'=>'',
						'id_cliente'=>$id_cliente,
						'id_tipom'=>$id_tipom,
						'ciudad'  =>$ciudad,
						'descrip'=>$descrip,
						'objetivo'=>$objetivo,
						'coord' =>$coord,
						'fecha_inicio'=>$fechaInicio,
						'fecha_final'=>$fechaFinal,
						'fecha_reg'=>$fechareg,
						'estatus'=>'Activo'
					);

				if (!$mision->writeMision($data,'new')){
					$msgSystem = $mision->get_msgErr();
					$ok = 'display';
				}

			} catch (Exception $err) {
				$msgSystem = 'Sistema: '.$err->getMessage();
				$ok = 'display';
			}
		} else {
			$msgSystem = 'Token not is valid!!!';
			$ok = 'display';
		}
		if ($ok=='display'){
			// reporta el error al usuario 
			echo $this->render('mision/mision_nueva.html.twig', ['token_id' => $token_id, 'msgSystem'=>'', 'action_system'=>'dont', 'fechaRango' => $rango_fecha]);

		} else {
			// se redirecciona a la pantalla anterior
			header("Location: /misiones");
			die();
		}
	}
	public function del_mission(string $id_mission):array 
	{
		$data = array();

		$mision = new Mision();

		$clave = Key::loadFromAsciiSafeString($_SESSION['key_crypto']);
		$id_mission = Crypto::decrypt($id_mission, $clave);

		$data['id_mision'] = $id_mission;

		if ($mision->writeMision($data,'delete'))
		{
			$data['ok']='01';
			$data['msg']='Correct!!';
		} else {
			$data['ok']='15';
			$data['msg']='Error '.$mision->get_msgErr();
		}

		return $data;
	}	
	public function edit_mission()
	{
		$token_id = uniqid();

		$_SESSION['token_form'] = $token_id;

		$mision = new Mision();


		$clave = Key::loadFromAsciiSafeString($_SESSION['key_crypto']);
		$id_mision = Crypto::decrypt($_GET['data'], $clave);

		$data = $mision->get_dataMision($id_mision)[0];

		$data['rango_fecha'] = $data['fecha_inicio'].' - '.$data['fecha_final'];

		$data['infoClient'] = 'La información del cliente es: Teléfono ' . $data['telf_cia'] . '; Dirección '.			$data['direccion'] . '; Contacto ' . $data['contacto'] . ' ' . $data['telf_contact'];

		
		echo $this->render('mision/mision_editar.html.twig',['token_id' => $token_id, 'msgSystem'=>'', 'action_system'=>'dont', 'mision' => $data, 'idMission' => $_GET['data']]);
	}

	public function update_mission()
	{
		extract($_POST);

		$mision = new Mision();


		$clave = Key::loadFromAsciiSafeString($_SESSION['key_crypto']);
		$id_mision = Crypto::decrypt($_GET['data'], $clave);

		$data = array();
		//$data = $mision->get_dataMision($id_mision)[0];

		$arrFecha = explode($rango_fecha, ' - ');

		$fechaInicio = new  \DateTime(date('d/m/Y',$arrFecha[0]));
		$fechaFinal = new  \DateTime(date('d/m/Y',$arrFecha[0]));

		$msgSystem = '';
		$ok = 'dont';
		if ($token_id==$_SESSION['token_form'])
		{
			try {
				$data['id_mision'] 	 = $id_mision;
				$data['id_cliente']	 =$id_cliente;
				$data['id_tipom']	 =$id_tipom;
				$data['ciudad']		 =$ciudad;
				$data['descrip']	 =$descrip;
				$data['objetivo']	 =$objetivo;
				$data['coord'] 		 =$coord;
				$data['fecha_inicio']=$fechaInicio;
				$data['fecha_final'] =$fechaFinal;

				if (!$mision->writeMision($data,'edit')){
					$msgSystem = $mision->get_msgErr();
					$ok = 'display';
				}

			} catch (Exception $err) {
				$msgSystem = 'Sistema: '.$err->getMessage();
				$ok = 'display';
			}
		} else {
			$msgSystem = 'Token not is valid!!!';
			$ok = 'display';
		}
		if ($ok=='display'){
			// reporta el error al usuario 
			echo $this->render('mision/mision_editar.html.twig', ['token_id' => $token_id, 'msgSystem'=>'', 'action_system'=>'dont', 'fechaRango' => $rango_fecha]);

		} else {
			// se redirecciona a la pantalla anterior
			header("Location: /misiones");
			die();
		}
	}	
	public function get_plainList()
	{
		$id_section = uniqid();

		echo $this->render('mision/plan_lista.html.twig', ['id_section' => $id_section, 'msgSystem'=>'', 'action_system'=>'dont']);

	}
	public function get_plainSelect()
	{
		$id_section = uniqid();

		echo $this->render('mision/plan_seleccion.html.twig', ['id_section' => $id_section, 'msgSystem'=>'', 'action_system'=>'dont']);

	}
	public function render_plainSelect01(string $search,array $order, int $length, int $start):array
	{
		$data = array();

		$mision = new Mision();

		$result  = $mision->listMission($search,$order,$length,$start);

		$total_reg = $mision->countMission($search);

		$contar = 0;
		$clave = "";
		$idCifrado = "";
		// se guarda la clave en un txt
		//$key = Key::createNewRandomKey();
		//echo $key->saveToAsciiSafeString(), "\n";
		//$contenido = file_get_contents("web\core\app\clave.txt");
		$sec = 0;
		$htmlCode="";
		foreach ($result as $row) {
			$clave = Key::loadFromAsciiSafeString($_SESSION['key_crypto']);
			$idCifrado = Crypto::encrypt($row['id_mision'], $clave);

			//$row['descrip'] = utf8_encode($row['descrip']);

			$htmlCode = $this->render('mision/item_mision.html.twig', ['mision' => $row, 'sec'=>strval($sec),'idMission'=>$idCifrado,'typeButton'=>'select']);


			$data['data'][] = array(
					'contenido' => $htmlCode
				);
			$contar++;
		} // fin del for 
		if ($contar==0)
		{
			$data['data'][]=array('contenido'=>'No hay misiones por planificar');
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
	public function new_plain()
	{
		$token_id = uniqid();

		$tmp_id = 'c'.substr(uniqid(),1,10);

		$_SESSION['token_form'] = $token_id;

		$mision = new Mision();


		$clave = Key::loadFromAsciiSafeString($_SESSION['key_crypto']);
		$id_mision = Crypto::decrypt($_GET['data'], $clave);

		$data = $mision->get_dataMision($id_mision)[0];

		$arrCoord = explode(",", $data['coord']);

		$data['rango_fecha'] = $data['fecha_inicio'].' - '.$data['fecha_final'];

		$data['infoClient'] = 'La información del cliente es: Teléfono ' . $data['telf_cia'] . '; Dirección '. 
						$data['direccion'] . '; Contacto ' . $data['contacto'] . ' ' . $data['telf_contact'];

		$data['lat'] = $arrCoord[0];
		$data['long'] = $arrCoord[1];

		$usuario = $this->get_user($_SESSION['idUser']);

		
		echo $this->render('mision/plan_nuevo.html.twig',['token_id' => $token_id, 'msgSystem'=>'', 'action_system'=>'dont', 'mision' => $data, 'idMission' => $_GET['data'], 'usuario'=>$usuario, 'tmp_id'=>$tmp_id]);
	}
	public function add_pilot():array
	{
		extract($_POST);

		if (!isset($_SESSION[$tmp_id])){
			$_SESSION[$tmp_id] = 1;
		} else {
			$_SESSION[$tmp_id] += 1;
		}

		if ($_SESSION[$tmp_id]==1){
			$tipo_piloto = 'Principal';
		} else {
			$tipo_piloto = 'Auxiliar';
		}

		$tmpPilot = new TmpPilot();

		try {
			$fechareg = new  \DateTime(date('Y-m-d'));
		} catch(Exception $err) {
			ob_clean();
		}

		$msgSystem = '';
		$ok = 'dont';
		if ($token_id==$_SESSION['token_form'])
		{
			try {
				if (!$tmpPilot->validPilot($id_usuario,$tmp_id)){
					$dataWrite = array(
						'id_piloto'=>'',
						'id_plan'=>$tmp_id,
						'id_usuario'=>$id_usuario,
						'id_drone'  =>$id_drone,
						'labor'=>$labor,
						'tipo'=>$tipo_piloto,
						'fecha_reg' => $fechareg
					);
					if (!$tmpPilot->writeData($dataWrite,'new')){
						$msgSystem = $tmpPilot->get_msgErr();
						$ok = '10';
					} else {
						$msgSystem = 'Correct!!!';
						$ok = '01';
					}

				} else {
					$msgSystem = "Ya se encuentra registrado!!!";
					$ok = '16';
				}
			} catch (Exception $err) {
				$msgSystem = 'Sistema: '.$err->getMessage();
				$ok = '12';
			}
		} else {
			$msgSystem = 'Token not is valid!!!';
			$ok = '15';
		}

		$data = array('ok'=>$ok, 'msg'=>$msgSystem);

		return $data;
	}

	public function load_itemsPilot(string $search,array $order, int $length, int $start, string $id_plain): array
	{
		$data = array();

		$mision = new TmpPilot();

		$result  = $mision->listItemsPilot($search,$order,$length,$start, $id_plain);

		$total_reg = $mision->countItemsPilot($search, $id_plain);

		$contar = 0;
		$clave = "";
		$idCifrado = "";
		// se guarda la clave en un txt
		//$key = Key::createNewRandomKey();
		//echo $key->saveToAsciiSafeString(), "\n";
		//$contenido = file_get_contents("web\core\app\clave.txt");
		$sec = 0;
		$htmlCode="";
		foreach ($result as $row) {
			$clave = Key::loadFromAsciiSafeString($_SESSION['key_crypto']);
			$idCifrado = Crypto::encrypt($row['id_piloto'], $clave);

			//$row['descrip'] = utf8_encode($row['descrip']);

			$htmlCode = $this->render('mision/item_pilot.html.twig', ['piloto' => $row, 'sec'=>strval($sec),'idPiloto'=>$idCifrado,'typeButton'=>'action']);


			$data['data'][] = array(
					'contenido' => $htmlCode
				);
			$contar++;
		} // fin del for 
		if ($contar==0)
		{
			$data['data'][]=array('contenido'=>'');
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
	public function del_plainPilotItem(string $id_piloto,string $tmp_id)
	{

		$data = array();

		$tmpPilot = new TmpPilot();

		$clave = Key::loadFromAsciiSafeString($_SESSION['key_crypto']);
		$id_piloto2 = Crypto::decrypt($id_piloto, $clave);

		$data['id_piloto'] = $id_piloto2;

		if ($tmpPilot->writeData($data,'delete'))
		{
			$_SESSION[$tmp_id] -= 1;

			$data['ok']='01';
			$data['msg']='Correct!!';
		} else {
			$data['ok']='15';
			$data['msg']='Error '.$tmpPilot->get_msgErr();
		}

		return $data;		
	}
	public function set_plain()
	{

		extract($_POST);

		$plan = new Plan();


		try {
			$fechareg = new  \DateTime(date('Y-m-d'));
			$fechaComienzo = new  \DateTime($fecha_comienzo);
		} catch(Exception $err) {

		}
		$clave = Key::loadFromAsciiSafeString($_SESSION['key_crypto']);
		$id_mision = Crypto::decrypt($_GET['data'], $clave);

		$mision = new Mision();
		$tmpPilot = new TmpPilot();
		$pilotos = new Pilotos();

		$dataMission = $mision->get_dataMision($id_mision)[0];

		$arrCoord = explode(",", $dataMission['coord']);

		$dataMission['rango_fecha'] = $dataMission['fecha_inicio'].' - '.$dataMission['fecha_final'];

		$dataMission['infoClient'] = 'La información del cliente es: Teléfono ' . $dataMission['telf_cia'] . '; Dirección '. 
						$dataMission['direccion'] . '; Contacto ' . $dataMission['contacto'] . ' ' . $dataMission['telf_contact'];

		$dataMission['lat'] = $arrCoord[0];
		$dataMission['long'] = $arrCoord[1];

		$usuario = $this->get_user($_SESSION['idUser']);


		$msgSystem = '';
		$ok = 'dont';
		if ($token_id==$_SESSION['token_form'])
		{
			try {
				$data = array(
						'id_plan'   	=>'',
						'id_mision'		=>$id_mision,
						'id_usuario'	=>$id_usuario,
						'id_vehiculo'	=>$id_vehiculo,
						'fecha_comienzo'=>$fechaComienzo,
						'hora_comienzo' =>$hora_comienzo,
						'descrip'		=>$descrip,
						'tipo_recop'	=>$tipo_recop,
						'tiempo_hrs'	=>$tiempo_hrs,
						'fecha_reg'		=>$fechareg,
					);

				if ($plan->writePlain($data,'new')){
					// Proceso de grabado de los pilotos
					$secuen = $plan->get_idLastInsert();
					$id_plan = $plan->lastIdPlan($secuen);


					$itemsProcess = $tmpPilot->loadItemsPilot($tmp_id);
					$dataPilot = array();
					$process = false;

					foreach ($itemsProcess as $row) {
						$dataPilot = array(
								'id_piloto'	=> '',
								'id_plan'	=> $id_plan,
								'id_usuario'=> $row['id_usuario'],
								'id_drone'	=> $row['id_drone'],
								'tipo'		=> $row['tipo'],
								'labor'		=> $row['labor'],
								'fecha_reg' => $fechareg
							);
						if (!$pilotos->writePilot($dataPilot,'new'))
						{
							$process = true;
							break;
						} else {
							// limpiar el temporal 
							$tmpPilot->writeData(array('id_piloto'=>$row['id_piloto']),'delete');
						}
					}
					if ($process==true)
					{
						$msgSystem = 'No se pudo guardar los pilotos';
						$ok = 'diplay';
					} 
					$msgSystem = '';
					$ok = 'notdiplay';
					// cambiar el status de la misión 
					$data = array(
							'id_mision' => $id_mision,
							'estatus'	=> 'Planificado'
						);
					$mision->writeMision($data, 'edit');

				} else {
						$msgSystem = 'No se pudo guardar la información del plan';
						$ok = 'diplay';
				}

			} catch (Exception $err) {
				$msgSystem = 'Sistema: '.$err->getMessage();
				$ok = 'display';
			}
		} else {
			$msgSystem = 'Token not is valid!!!';
			$ok = 'display';
		}
		if ($ok=='display'){
			// reporta el error al usuario 
			echo $this->render('mision/plan_nueva.html.twig', ['token_id' => $token_id, 'msgSystem'=>'', 'action_system'=>'dont', 'mision' => $dataMission, 'idMission' => $_GET['data'], 'usuario'=>$usuario, 'tmp_id'=>$tmp_id]);

		} else {
			// se redirecciona a la pantalla anterior
			header("Location: /planes");
			die();
		}
	}
	public function render_plainList(string $search,array $order, int $length, int $start):array
	{
		$data = array();

		$plan = new Plan();
		$pilotos = new Pilotos();

		$usuario = $this->get_user($_SESSION['idUser']);

		$idUserGestor = $usuario['id_usuario'];
		$role = $usuario['role'];

		$result  = $plan->listPlains($search,$order,$length,$start,$idUserGestor, $role);

		$total_reg = $plan->countPlains($search,$idUserGestor, $role);

		$contar = 0;
		$clave = "";
		$idCifrado = "";
		// se guarda la clave en un txt
		//$key = Key::createNewRandomKey();
		//echo $key->saveToAsciiSafeString(), "\n";
		//$contenido = file_get_contents("web\core\app\clave.txt");
		$sec = 0;
		$htmlCode="";
		foreach ($result as $row) {
			$clave = Key::loadFromAsciiSafeString($_SESSION['key_crypto']);
			$idCifrado = Crypto::encrypt($row['id_plan'], $clave);

			//$row['descrip'] = utf8_encode($row['descrip']);
			$row['pilotos'] = $pilotos->listItemsPilot($row['id_plan']);


			$htmlCode = $this->render('mision/item_plan.html.twig', ['plan' => $row, 'sec'=>strval($sec),'idPlain'=>$idCifrado]);

			$data['data'][] = array(
					'contenido' => $htmlCode
				);
			$contar++;
		} // fin del for 
		if ($contar==0)
		{
			$data['data'][]=array('contenido'=>'<h4>No hay planificaciones activas.</h4>');
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
	public function del_plain(string $id_plain):array 
	{
		$data = array();

		$mision = new Mision();
		$plan = new Plan();
		$pilotos = new Pilotos();


		$clave = Key::loadFromAsciiSafeString($_SESSION['key_crypto']);
		$id_plain = Crypto::decrypt($id_plain, $clave);

		$id_mision = $plan->get_IdMission($id_plain);

		$data['id_plan'] = $id_plain;

		if ($pilotos->writePilot($data,'allDelete'))
		{
			// Si elimina todos los pilotos 
			if ($plan->writePlain($data,'delete'))
			{
				$data = array('id_mision'=>$id_mision,'estatus'=>'Activo');

				$mision->writeMision($data,'editPlain');

				$data['ok']='01';
				$data['msg']='Correct!!';
				// Se reversa el estatus de la misión

			} else {
				$data['ok']='15';
				$data['msg']='Error '.$plan->get_msgErr();
			}
		} else {
			$data['ok']='15';
			$data['msg']='Error '.$pilotos->get_msgErr();
		}


		return $data;
	}
}

