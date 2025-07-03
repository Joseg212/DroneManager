<?php 

namespace DronesMaritime\Core\App;

use DronesMaritime\Core\Models\Mision;
use DronesMaritime\Core\Models\TmpPilot;
use DronesMaritime\Core\Models\Plan;
use DronesMaritime\Core\Models\Pilotos;
use DronesMaritime\Core\Models\Archivos;
use Defuse\Crypto\Crypto;
use Defuse\Crypto\Key;

final class ConsultApp extends Controller
{

	public function get_list()
	{
		$id_section = uniqid();

		echo $this->render('consulta/consulta_inicial.html.twig', ['id_section' => $id_section, 'msgSystem'=>'', 'action_system'=>'dont']);

	}	

	public function render_list():array
	{
		extract($_POST);

		$data = array();

		$mision = new Mision();

		$filterData=array(
				'fecha_inicio' 	=> $fecha_inicio,
				'fecha_final'  	=> $fecha_final,
				'estatus'		=> $estatus,
				'id_usuario'	=> $id_usuario,
				'id_cliente'	=> $id_cliente
			);

		$mision->set_filterData($filterData);
		$mision->set_activeFilter(true);

		$result  = $mision->listMission($txtSearch,$order,$length,$start);

		$total_reg = $mision->countMission($txtSearch);

		$contar = 0;
		$clave = "";
		$idCifrado = "";
		// se guarda la clave en un txt
		//$key = Key::createNewRandomKey();
		//echo $key->saveToAsciiSafeString(), "\n";
		//$contenido = file_get_contents("web\core\app\clave.txt");
		foreach ($result as $row) {
			$clave = Key::loadFromAsciiSafeString($_SESSION['key_crypto']);
			$idCifrado = Crypto::encrypt($row['id_mision'], $clave);


			$data['data'][] = array(
					'mision' => '<p>'.$row['objetivo'].'</p>',
					'compania' => '<p>'.$row['compania'].'</p><p>Id Misión:'.$row['id_mision'].'</p>',
					'opcion'  => '<div data-id="'.$idCifrado.'"> <a href="#" class="view_detail" ><i class="zmdi zmdi-eye zmdi-hc-fw" style="color:#6b8df6;font-size: 1.6rem;"></i></a></div>'
				);
			$contar++;
		}
		if ($contar==0)
		{
			$data['data'][]=array('mision'=>'','compania'=>'','opcion'=>'');
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
	public function view_mission(string $idMission)
	{

		$id_section = uniqid();

		$clave = Key::loadFromAsciiSafeString($_SESSION['key_crypto']);
		$id_mision = Crypto::decrypt($idMission, $clave);

		$data = array();

		$mision = new Mision();
		$plan = new Plan();
		$pilotos = new Pilotos();
		$archivos = new Archivos();

		

		$data['mision'] = $mision->get_dataMision($id_mision)[0];
		
		$list = array('Planificado','Reportado','Finalizado');

		if (in_array($data['mision']['estatus'],$list))
		{
			$data['plan'] = $plan->get_dataPlan($id_mision)[0];


			$data['pilotos'] = $pilotos->listItemsPilotAll($data['plan']['id_plan']);

		}

		// determinar el promedio de los valor indicado por los pilotos
		$cantidad = 0;
		$sumTotalVuelo = 0;
		$sumTotalHoras = 0;
		$timeMax = 0;
		foreach ($data['pilotos'] as $fila) {
			$timeMax = max($fila['hora_final'],$timeMax);

			$cantidad++;
			$sumTotalVuelo += $fila['num_vuelo'];
			$sumTotalHoras += $fila['total_hrs'];
		}
		$data['promdNumVuelos'] = strval(round(($sumTotalVuelo/$cantidad),0)).' Vuelos';
		$data['promdHrsVuelos'] = strval(round(($sumTotalHoras/$cantidad),0)).' Horas';
		$data['horaMaxima'] = $timeMax;

		// archivos items. 
		$data['archivos'] = $archivos->get_dataFiles($id_mision);

		$nroArchivos=0;
		$totalMegas=0;

		foreach ($data['archivos'] as $fila) {
			$nroArchivos++;
			$totalMegas += $fila['tamanio'];
		}
		$data['totalMegas'] = $totalMegas;
		$data['total_arch'] = $nroArchivos;

		echo $this->render('consulta/consulta_view.html.twig', ['id_section' => $id_section, 'msgSystem'=>'', 'action_system'=>'dont','render'=>$data]);		
	}

}