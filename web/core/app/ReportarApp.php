<?php 

namespace DronesMaritime\Core\App;

use DronesMaritime\Core\Models\Mision;
use DronesMaritime\Core\Models\TmpPilot;
use DronesMaritime\Core\Models\Plan;
use DronesMaritime\Core\Models\Pilotos;
use DronesMaritime\Core\Models\Archivos;
use DronesMaritime\Core\App\FunctionsApp;
use Defuse\Crypto\Crypto;
use Defuse\Crypto\Key;

final class ReportarApp extends Controller
{

	public function get_list()
	{
		$id_section = uniqid();

		echo $this->render('mision/reportar_select.html.twig', ['id_section' => $id_section, 'msgSystem'=>'', 'action_system'=>'dont']);

	}
	public function render_plainList(string $search,array $order, int $length, int $start):array
	{
		$data = array();

		$plan = new Plan();
		$pilotos = new Pilotos();

		$usuario = $this->get_user($_SESSION['idUser']);

		$idUserGestor = $usuario['id_usuario'];
		$role = $usuario['role'];

		$result  = $plan->listPlains($search,$order,$length,$start,$idUserGestor, $role,"Reportado");

		$total_reg = $plan->countPlains($search,$idUserGestor, $role,"Reportado");

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
			$row['pilotos'] = $pilotos->listItemsPilot($row['id_plan']);


			$htmlCode = $this->render('mision/item_plan_02.html.twig', ['plan' => $row, 'sec'=>strval($sec),'idMision'=>$idCifrado]);

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
	public function select_plain()
	{
		$token_id = uniqid();

		$tmp_id = 'c'.substr(uniqid(),1,10);

		$_SESSION['token_form'] = $token_id;

		$plan = new Plan();

		$pilotos = new Pilotos();
		$archivos = new Archivos();


		$clave = Key::loadFromAsciiSafeString($_SESSION['key_crypto']);
		$id_mision = Crypto::decrypt($_GET['data'], $clave);

		$data = $plan->get_dataPlan($id_mision)[0];


		$usuario = $this->get_user($_SESSION['idUser']);

		$Completed = 'Not';

		$piloto  =  $pilotos->listItemsPilotAll($data['id_plan']);
		$contarNot = 0;

		foreach ($piloto as $row) 
		{
			if ($row['finalizado']=='N')
			{
				$contarNot++;
			}
		}
		$nroArchivo = $archivos->countFiles("",$id_mision);

		if ($contarNot==0 && $nroArchivo>0)
		{
			$Completed = 'Yes';
		}
		echo $this->render('mision/reportar_form.html.twig',['token_id' => $token_id, 'msgSystem'=>'', 'action_system'=>'dont', 'plan' => $data, 'idMision' => $_GET['data'], 'usuario'=>$usuario, 'tmp_id'=>$tmp_id,'Completed'=>$Completed,'piloto'=>$piloto]);
	}

	public function reportar_itemsPilots(string $search,array $order, int $length, int $start,string $id_plan):array
	{
		$data = array();

		$pilotos = new Pilotos();

		$idMision = $_POST['idMision'];

		$usuario = $this->get_user($_SESSION['idUser']);

		$result  = $pilotos->listPilots($search,$order,$length,$start,$id_plan, $usuario);

		$total_reg = $pilotos->countPilots($search,$id_plan,$usuario);

		$contar = 0;
		$clave = "";
		$idCifrado = "";
		$check = "";
		// se guarda la clave en un txt
		//$key = Key::createNewRandomKey();
		//echo $key->saveToAsciiSafeString(), "\n";
		//$contenido = file_get_contents("web\core\app\clave.txt");
		foreach ($result as $row) {
			$clave = Key::loadFromAsciiSafeString($_SESSION['key_crypto']);
			$idCifrado = Crypto::encrypt($row['id_piloto'], $clave);

			if ($row['finalizado']=='S'){
				$check = '<div><i class="zmdi zmdi-check-circle check-md-repo"></i></div>';
			} else {
				$check = '<div><i class="zmdi zmdi-circle-o check-md-repo"></i></div>';

			}


			$data['data'][] = array(
					'id' 	  	=> $row['id_piloto'],
					'nombres' 	=> $row['nombres'],
					'tipo' 		=> $row['tipo'],
					'labor'		=> $row['labor'],
					'opcion'  	=> '<div data-id="'.$idCifrado.'" data-mision="'.$idMision.'"><a href="#" class="btn btn-dark reportar_piloto">Reportar</a></div>',
					'estado'	=> $check
				);
			$contar++;
		}
		if ($contar==0)
		{
			$data['data'][]=array('id'=>'','nombres'=>'','tipo'=>'','labor'=>'','opcion'=>'','estado'=>'');
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
	public function piloto_reporte()
	{
		$token_id = uniqid();

		$_SESSION['token_form'] = $token_id;

		$pilotos = new Pilotos();

		$arrCodificado = explode("_", $_GET['data']);

		$clave = Key::loadFromAsciiSafeString($_SESSION['key_crypto']);
		$id_piloto = Crypto::decrypt($arrCodificado[0], $clave);

		//var_dump($id_piloto);

		$data = $pilotos->get_dataPiloto($id_piloto)[0];

		echo $this->render('mision/reportar_piloto.html.twig', ['token_id' => $token_id, 'msgSystem'=>'', 'action_system'=>'dont', 'piloto' => $data, 'idPiloto' => $arrCodificado[0], 'idMision'=>$arrCodificado[1]]);

	}	
	public function piloto_ReportarUpdate()
	{
		extract($_POST);

		$data = array();

		$clave = Key::loadFromAsciiSafeString($_SESSION['key_crypto']);
		$id_piloto = Crypto::decrypt($_GET['data'], $clave);

		$pilotos = new Pilotos();

		$data = $pilotos->get_dataPiloto($id_piloto)[0];

		//unset($data['id_drone']);
		//unset($data['user']);
		$dataw = array();
		$dataw['num_vuelo'] 	= $num_vuelo;
		$dataw['total_hrs']	= $total_hrs;
		$dataw['hora_final'] = $hora_final;
		$dataw['observ_final'] = $observ_final;
		$dataw['finalizado']  = 'S';
		$dataw['id_piloto'] = $id_piloto;

		$msgSystem = '';
		$actionSystem = 'dont';
		if ($token_id==$_SESSION['token_form'])
		{
			try {

				if (!$pilotos->writePilot($dataw,'edit')){
					$msgSystem = $pilotos->get_msgErr();
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
			$data['id_piloto'] 	= $id_piloto;
			echo $this->render('mision/reportar_piloto.html.twig', ['token_id' => $token_id,'msgSystem'=>$msgSystem,
				'action_system'=>$actionSystem, 'piloto' => $data,'idPiloto' => $_GET['data']]);

		} else {
			// se redirecciona a la pantalla anterior
			header("Location: /reportar/selecionar/".$idMision);
			die();
		}
	}
	public function upload_fileGDRIVER()
	{
		extract($_POST);

		$data = array();

		$clave = Key::loadFromAsciiSafeString($_SESSION['key_crypto']);
		$id_mision = Crypto::decrypt($_GET['data'], $clave);
		
		$fecha_subida = new \DateTime(date("Y/m/d"));

		$archivos = new Archivos();



		// Proceso de subida de archivo a google driver
 		//$documento = '/../../temporal/' . htmlspecialchars($_FILES['file_document']['name']);
 		$baseDir = dirname(__DIR__);
 		$baseDir = dirname($baseDir);

 		$poscPoint = strpos($_FILES['file_document']['name'],".");
 		$extFile =  substr($_FILES['file_document']['name'], $poscPoint,5);

 		$documento = $baseDir.'\\temporal\\' . htmlspecialchars($name_file).$extFile;
 		$nameFile = htmlspecialchars($name_file).$extFile;
 		$documento02 = 'http://www.dronesmaritime.com/web/temporal/' . htmlspecialchars($name_file).$extFile;

		$usuario = $this->get_user($_SESSION['idUser']);

 		$objFunctions = new FunctionsApp();

	  	if(move_uploaded_file($_FILES['file_document']['tmp_name'], $documento)){
	        //echo "1.- Fichero subido al servidor. ";

	        $file_id_drive = $objFunctions->uploadDocumentGoogleDriver($documento,htmlspecialchars($descrip_arch),$baseDir,$id_mision,$nameFile);
	        
	        unlink($documento);
	    } 	

		$dataw = array(
				'id_archivo' 	=> '',
				'id_mision'		=> $id_mision,
				'name_file'		=> $name_file,
				'arch_orig'		=> $fileName,
				'tamanio'		=> $fileSize,
				'extension'		=> $fileType,
				'tipo_archivo'	=> $tipo_archivo,
				'descrip_arch'	=> $descrip_arch,
				'fecha_subida'	=> $fecha_subida,
				'file_id_drive'	=> $file_id_drive,
				'id_usuario'	=> $usuario['id_usuario']
			);

		$msgSystem = '';
		$actionSystem = 'dont';
		if ($token_id==$_SESSION['token_form'])
		{
			try {

				if (!$archivos->writeArchivo($dataw,'new')){
					$msgSystem = $archivos->get_msgErr();
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

		header("Location: /reportar/selecionar/".$_GET['data']);
		die();
	}	

	public function gestor_reporte(string $token_id, string $fecha_final,string $observg_final, string $idMision):array
	{
		$plan = new Plan();
		$mision = new Mision();


		$fecha_final = new \DateTime($fecha_final);

		$clave = Key::loadFromAsciiSafeString($_SESSION['key_crypto']);
		$id_mision = Crypto::decrypt($idMision, $clave);

		$dataw = array(
			'id_mision'=>$id_mision,
			'fecha_final'=>$fecha_final,
			'observg_final'=>$observg_final,
		);

		$msgSystem = '';
		$actionSystem = 'dont';
		if ($token_id==$_SESSION['token_form'])
		{
			try {

				if (!$plan->writePlain($dataw,'editMision')){
					$msgSystem = $plan->get_msgErr();
					$actionSystem = 'display';
				} else {
					// Actualizar estatus de la misión
					$mision->writeMision(['id_mision'=>$id_mision, 'estatus'=>'Reportado'],'editPlain');
				}

			} catch (Exception $err) {
				$msgSystem = 'Sistema: '.$err->getMessage();
				$actionSystem = 'display';
			}
		} else {
			$msgSystem = 'Token not is valid!!!';
			$actionSystem = 'display';
		}
		// reportar el error al usuario 
		if ($actionSystem=='display')
		{
			$dataw['ok']='15';
			$dataw['msg']=$msgSystem;
		} else {
			$dataw['ok']='01';
			$dataw['msg']='Correct!!';
		}
		return $dataw;
	}

	public function upload_itemsArchivos(string $search,array $order, int $length, int $start,string $idMision):array
	{
		$data = array();

		$archivos = new Archivos();

		$clave = Key::loadFromAsciiSafeString($_SESSION['key_crypto']);
		$id_mision = Crypto::decrypt($idMision, $clave);

		$result  = $archivos->listFiles($search,$order,$length,$start,$id_mision);

		$total_reg = $archivos->countFiles($search,$id_mision);

		$contar = 0;
		$clave = "";
		$idCifrado = "";
		$check = "";
		// se guarda la clave en un txt
		//$key = Key::createNewRandomKey();
		//echo $key->saveToAsciiSafeString(), "\n";
		//$contenido = file_get_contents("web\core\app\clave.txt");
		foreach ($result as $row) {
			$clave = Key::loadFromAsciiSafeString($_SESSION['key_crypto']);
			$idArchivo = Crypto::encrypt($row['id_archivo'], $clave);

			$data['data'][] = array(
					'archivo' 	  	=> $row['name_file'],
					'descrip' 	=> $row['descrip_arch'],
					'tipo' 		=> $row['tipo_archivo'],
					'tamanio'		=> strval($row['tamanio']).' Mb',
					'opcion'  	=> '<div data-id="'.$idArchivo.'" data-fileid ="'.$row['name_file'].'"><a href="#" class="btn btn-dark delete_archivo">Eliminar</a></div>'
				);
			$contar++;
		}
		if ($contar==0)
		{
			$data['data'][]=array('archivo'=>'','descrip'=>'','tipo'=>'','tamanio'=>'','opcion'=>'');
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
	public function del_archivo(string $idArchivo, string $name_file):array 
	{
		$data = array();

		$archivos = new Archivos();
		$functionsApp = new FunctionsApp();

		$clave = Key::loadFromAsciiSafeString($_SESSION['key_crypto']);
		$id_archivo = Crypto::decrypt($idArchivo, $clave);

		$baseDir = dirname(__DIR__);
 		$baseDir = dirname($baseDir);

		$functionsApp->deleteFileInGoogleDrive($name_file,$baseDir);

		//$result = (string)$FunctionsApp->objResult;

		$data['id_archivo'] = $id_archivo;

		if ($archivos->writeArchivo($data,'delete'))
		{
			$data['ok']='01';
			$data['msg']='Correct!!';
		} else {
			$data['ok']='15';
			$data['msg']='Error '.$archivos->get_msgErr();
		}
		$data['resultDrive'] = $result;
		return $data;	
	}
	public function view_gd(){
		$functionsApp = new FunctionsApp();

		$baseDir = dirname(__DIR__);
 		$baseDir = dirname($baseDir);

		//$functionsApp->deleteFileInGoogleDrive('myarchivo',$baseDir);
		return;
	}
	public function get_finishList()
	{
		$id_section = uniqid();

		echo $this->render('mision/finalizar_select.html.twig', ['id_section' => $id_section, 'msgSystem'=>'', 'action_system'=>'dont']);

	}

	public function render_finishList(string $search,array $order, int $length, int $start):array
	{
		$data = array();

		$plan = new Plan();
		$pilotos = new Pilotos();

		$usuario = $this->get_user($_SESSION['idUser']);

		$idUserGestor = $usuario['id_usuario'];
		$role = $usuario['role'];

		$result  = $plan->listPlains($search,$order,$length,$start,$idUserGestor, $role,"Reportado");

		$total_reg = $plan->countPlains($search,$idUserGestor, $role,"Reportado");

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
			if ($row['estatus']=='Reportado'){
				$clave = Key::loadFromAsciiSafeString($_SESSION['key_crypto']);
				$idCifrado = Crypto::encrypt($row['id_mision'], $clave);

				//$row['descrip'] = utf8_encode($row['descrip']);
				$row['pilotos'] = $pilotos->listItemsPilot($row['id_plan']);


				$htmlCode = $this->render('mision/item_reportado.html.twig', ['plan' => $row, 'sec'=>strval($sec),'idMision'=>$idCifrado]);

				$data['data'][] = array(
						'contenido' => $htmlCode
					);

				$contar++;
			}
		} // fin del for 
		if ($contar==0)
		{
			$data['data'][]=array('contenido'=>'<h4>No hay misiones reportadas.</h4>');
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
	public function select_report()
	{
		$token_id = uniqid();

		$tmp_id = 'c'.substr(uniqid(),1,10);

		$_SESSION['token_form'] = $token_id;

		$plan = new Plan();

		$pilotos = new Pilotos();
		
		$clave = Key::loadFromAsciiSafeString($_SESSION['key_crypto']);
		$id_mision = Crypto::decrypt($_GET['data'], $clave);

		$data = $plan->get_dataPlan($id_mision)[0];


		$usuario = $this->get_user($_SESSION['idUser']);

		$piloto  =  $pilotos->listItemsPilotAll($data['id_plan']);

		echo $this->render('mision/finalizar_form.html.twig',['token_id' => $token_id, 'msgSystem'=>'', 'action_system'=>'dont', 'plan' => $data, 'idMision' => $_GET['data'], 'usuario'=>$usuario, 'tmp_id'=>$tmp_id,'piloto'=>$piloto]);
	}
	public function update_misionFinish()
	{
		extract($_POST);

		$data = array();

		$clave = Key::loadFromAsciiSafeString($_SESSION['key_crypto']);
		$id_mision = Crypto::decrypt($_GET['data'], $clave);

		$mision = new Mision();

		$data = array(
				'id_mision' 		=> $id_mision,
				'fecha_finalizado' 	=> $fecha_finalizado,
				'informacion'		=> $informacion,
				'estatus'			=> 'Finalizado',
			);

		$msgSystem = '';
		$actionSystem = 'dont';
		if ($token_id==$_SESSION['token_form'])
		{
			try {

				if (!$mision->writeMision($data,'editFinish')){
					$msgSystem = $mision->get_msgErr();
					$actionSystem = 'display';
				} else {

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
			
			$plan = new Plan();
			$pilotos = new Pilotos();

			$data = $plan->get_dataPlan($id_mision)[0];
			$usuario = $this->get_user($_SESSION['idUser']);
			$piloto  =  $pilotos->listItemsPilotAll($data['id_plan']);

			$tmp_id = 'c'.substr(uniqid(),1,10);

			echo $this->render('mision/finalizar_form.html.twig',['token_id' => $token_id, 'msgSystem'=>'', 'action_system'=>'dont', 'plan' => $data, 'idMision' => $_GET['data'], 'usuario'=>$usuario, 'tmp_id'=>$tmp_id,'piloto'=>$piloto]);

		} else {
			// se redirecciona a la pantalla anterior
			header("Location: /finalizar");
			die();
		}

	}

}