<?php 


namespace DronesMaritime\Core\Models;

use DronesMaritime\Core\App\ConnectionDB;
use DronesMaritime\Core\App\FunctionsApp;

class logSistema extends ConnectionDB
{

	public function __construct()
	{

	}

	private $msgErr='';
	public function get_msgErr()
	{
		return $this->msgErr;
	}	
	private $idUser='';
	public function get_idUser()
	{
		return $this->idUser;
	}
	public function writeLog(array $data_w, string $type_w):bool
	{
		$ret_val = false;
		try {
			
			if ($type_w=='new'){
				$sql = FunctionsApp::createQuery($data_w,'insert','log_sistema');

			} elseif($type_w=='delete'){
				$sql = FunctionsApp::createQuery($data_w,'delete','log_sistema') . " WHERE id_secuen='".$data_w['id_secuen']."'";
			} elseif($type_w=='edit'){
				$sql = FunctionsApp::createQuery($data_w,'update','log_sistema') . " WHERE id_secuen='".$data_w['id_secuen']."'";

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

	public function listLogSis(string $search,array $order, int $length, int $start):array
	{
		$resDev = array();

		$columnOrder = $this->getColumnName($order[0]['column']);
		$typeOrder = $order[0]['dir'];

		if ($this->openDB()){
			//$filterData = $this->get_filterData("m.estatus = 'Activo'");

			$sql = sprintf("
				SELECT id_secuen, DATE_FORMAT(fecha_hora,'%s') as fecha_hora,id_usuario,nombres,
						accion,alias,role
					FROM log_sistema
					ORDER BY %s %s LIMIT %s,%s
				",
					"%d\/%m\/%Y %H\:%i\:%s",$columnOrder,$typeOrder,$start,$length);

			$resDev = $this->get_array($sql);

			//$this->msgErr = $sql;
		} 

		$this->closeDB();
		return $resDev;
	}
	public function countLogSis(string $search):int
	{
		$resDev = 0;


		if ($this->openDB()){

			$sql = sprintf("
					SELECT count(*) as total
						FROM log_sistema
					");

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
				$nameColumn = 'id_secuen';
				break;
			default:
				$nameColumn = 'id_secuen';
				break;
		}
		return $nameColumn;
	}
}