<?php 


namespace DronesMaritime\Core\Models;

use DronesMaritime\Core\App\ConnectionDB;
use DronesMaritime\Core\App\FunctionsApp;

class TmpPilot extends ConnectionDB
{

	public function __construct()
	{

	}
	public function writeData(array $data_w, string $type_w):bool
	{
		$ret_val = false;
		try {
			
			if ($type_w=='new'){
				$sql = FunctionsApp::createQuery($data_w,'insert','tmp_pilot');

			} elseif($type_w=='delete'){
				$sql = FunctionsApp::createQuery($data_w,'delete','tmp_pilot') . " WHERE id_piloto='".$data_w['id_piloto']."'";
			} elseif($type_w=='edit'){
				$sql = FunctionsApp::createQuery($data_w,'update','tmp_pilot') . " WHERE id_piloto='".$data_w['id_piloto']."'";

			}
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
	public function listItemsPilot(string $search,array $order, int $length, int $start,string $tmp_id):array
	{
		$resDev = array();

		$columnOrder = $this->getColumnName($order[0]['column']);
		$typeOrder = $order[0]['dir'];

		if ($this->openDB()){
			$sql = sprintf("SELECT u.id_usuario,u.nombres,d.id_drone,
						d.modelo,p.id_piloto,p.tipo,p.labor
					FROM tmp_pilot AS p 
					INNER JOIN usuario AS u ON u.id_usuario=p.id_usuario
					INNER JOIN drone AS d ON d.id_drone = p.id_drone
					WHERE p.id_plan = '%s'
					ORDER BY %s %s LIMIT %s,%s",
					$tmp_id,$columnOrder,$typeOrder,$start,$length);

			$resDev = $this->get_array($sql);
		} 
		$this->closeDB();
		return $resDev;
	}
	public function countItemsPilot(string $search, $tmp_id):int
	{
		$resDev = 0;
		if ($this->openDB()){

			$sql = sprintf("SELECT count(p.id_plan) as total 
				FROM tmp_pilot AS p 
				WHERE p.id_plan = '%s'
				", $tmp_id);

			$resDev = $this->get_unique($sql);
		}
		$this->closeDB();
		return $resDev;
	}
	public function validPilot(string $id_user,string $id_plain):bool
	{
		$varRet = false;
		if ($this->openDB()){

			$sql = sprintf("
					SELECT count(id_usuario) as total FROM tmp_pilot WHERE id_usuario='%s' AND id_plan='%s'
				", $id_user,$id_plain);

			$resDev = $this->get_unique($sql);

			if ($resDev>0)
			{
				/*El usuario se encuentra registrado*/
				$varRet = true;
			}
		}
		$this->closeDB();

		return $varRet;
	}
	public function get_msgErr()
	{
		return $this->msgErr;
	}
	private function getColumnName(int $column)
	{
		return 'p.id_piloto';
	}

	public function loadItemsPilot(string $tmp_id):array
	{
		$resDev = array();


		if ($this->openDB()){
			$sql = sprintf("SELECT p.id_piloto,p.id_plan,p.id_usuario,p.id_drone,p.tipo,p.labor 
					FROM tmp_pilot AS p 
					WHERE p.id_plan = '%s'
					",
					$tmp_id);

			$resDev = $this->get_array($sql);
		} 
		$this->closeDB();
		return $resDev;
	}
}
