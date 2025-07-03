<?php 


namespace DronesMaritime\Core\Models;

use DronesMaritime\Core\App\ConnectionDB;
use DronesMaritime\Core\App\FunctionsApp;

class Pilotos extends ConnectionDB
{

	public function __construct()
	{

	}

	public function writePilot(array $data_w, string $type_w):bool
	{
		$ret_val = false;
		try {
			
			if ($type_w=='new'){
				$sql = FunctionsApp::createQuery($data_w,'insert','pilotos');

			} elseif($type_w=='delete'){
				$sql = FunctionsApp::createQuery($data_w,'delete','pilotos') . " WHERE id_piloto='".$data_w['id_piloto']."'";
			} elseif($type_w=='edit'){
				$sql = FunctionsApp::createQuery($data_w,'update','pilotos') . " WHERE id_piloto='".$data_w['id_piloto']."'";

			}
			//var_dump($sql);
			if ($this->openDB()){
				
				$this->actionQuery($sql);

				$this->closeDB();
				//$this->msgErr = $sql;
				$ret_val=true;
			} else {
				$this->msgErr = $this->msgServer;
			}			
		} catch (Exception $err) {
			$this->msgErr = $err->getMessage();
		}
		return $ret_val;
	}	
	public function listItemsPilot(string $id_plan):array
	{
		$resDev = array();

		if ($this->openDB())
		{
			$sql = sprintf("SELECT u.id_usuario,u.nombres,d.id_drone,
						d.modelo,p.id_piloto,p.tipo,p.labor
					FROM pilotos AS p 
					INNER JOIN usuario AS u ON u.id_usuario=p.id_usuario
					INNER JOIN drone AS d ON d.id_drone = p.id_drone
					WHERE p.id_plan = '%s'
					ORDER BY p.id_plan limit 0,3",
					$id_plan);

			$resDev = $this->get_array($sql);
		} 

		$this->closeDB();
		return $resDev;
	}
	public function listPilots(string $search,array $order, int $length, int $start,string $id_plan, array $usuario):array
	{
		$resDev = array();

		$columnOrder = $this->getColumnName($order[0]['column']);
		$typeOrder = $order[0]['dir'];

		if ($usuario['role']=='Piloto')
		{
			$htmlPiloto = " p.id_usuario = '".$usuario['id_usuario']."' AND ";
		} else {
			$htmlPiloto = "";
		}

		if ($this->openDB()){
			$sql = sprintf("
					SELECT u.id_usuario,u.nombres,d.id_drone,
					d.modelo,p.id_piloto,p.tipo,p.labor,p.finalizado
				FROM pilotos AS p 
				INNER JOIN usuario AS u ON u.id_usuario=p.id_usuario
				INNER JOIN drone AS d ON d.id_drone = p.id_drone
				WHERE $htmlPiloto p.id_plan = '%s'
				ORDER BY %s %s limit %s,%s					
				",$id_plan,$columnOrder,$typeOrder,$start,$length);

			$resDev = $this->get_array($sql);
		} 
		$this->closeDB();
		return $resDev;
	}
	public function countPilots(string $search,string $id_plan,array $usuario):int
	{
		$resDev = 0;
		if ($usuario['role']=='Piloto')
		{
			$htmlPiloto = " p.id_usuario = '".$usuario['id_usuario']."' AND ";
		} else {
			$htmlPiloto = "";
		}

		if ($this->openDB()){

			$sql = sprintf("
				SELECT count(id_drone) as total
				FROM pilotos AS p  
				INNER JOIN usuario AS u ON u.id_usuario=p.id_usuario
				INNER JOIN drone AS d ON d.id_drone = p.id_drone
				WHERE $htmlPiloto p.id_plan = '%s'
				",$id_plan);

			$resDev = $this->get_unique($sql);


		}
		$this->closeDB();
		return $resDev;
	}
	private function getColumnName(int $columnOrder):string
	{
		$nameColumn = '';
		switch ($columnOrder) {
			case 0:
				$nameColumn = 'p.id_piloto';
				break;
			case 1:
				$nameColumn = 'u.nombres';
				break;
			case 2:
				$nameColumn = 'p.tipo';
				break;
			case 3:
				$nameColumn = 'p.labor';
				break;
			default:
				$nameColumn = 'p.id_piloto';
				break;
		}
		return $nameColumn;

	}
	public function get_dataPiloto(string $idPiloto):array
	{
		$data_ret = array();

		$sql =sprintf("SELECT p.id_piloto,p.id_usuario,u.nombres,p.tipo,p.labor,
							p.id_drone, d.modelo,p.num_vuelo, p.total_hrs,
							p.hora_final,p.finalizado, p.id_plan, p.observ_final
						FROM pilotos AS p 
						INNER JOIN usuario AS u ON p.id_usuario = u.id_usuario
						INNER JOIN drone AS d ON d.id_drone = p.id_drone
						WHERE id_piloto = '%s'",$idPiloto);
		try {
			if ($this->openDB())
			{
				$data_ret = $this->get_array($sql);
			} else {
				$this->msgErr = $this->msgServer;
			}
			
		} catch (Exception $err) {
			$this->msgErr = $err->getMessage();
		}
		if (empty($data_ret)){
			$data_ret[] = array('id_piloto'=>'','id_usuario'=>'','nombres'=>'');
		}
		return $data_ret;
	}
	public function listItemsPilotAll(string $id_plan):array
	{
		$resDev = array();

		if ($this->openDB())
		{
			$sql = sprintf("SELECT u.id_usuario,u.nombres,d.id_drone,
						d.modelo,p.id_piloto,p.tipo,p.labor,p.num_vuelo,p.total_hrs,
						p.hora_final,p.observ_final,p.finalizado
					FROM pilotos AS p 
					INNER JOIN usuario AS u ON u.id_usuario=p.id_usuario
					INNER JOIN drone AS d ON d.id_drone = p.id_drone
					WHERE p.id_plan = '%s'
					ORDER BY p.id_piloto",
					$id_plan);

			$resDev = $this->get_array($sql);
		} 

		$this->closeDB();
		return $resDev;
	}


}