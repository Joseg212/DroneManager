<?php 


namespace DronesMaritime\Core\Models;

use DronesMaritime\Core\App\ConnectionDB;
use DronesMaritime\Core\App\FunctionsApp;

class Vehiculo extends ConnectionDB
{

	public function __construct()
	{

	}

	private $msgErr='';
	public function get_msgErr()
	{
		return $this->msgErr;
	}	


	public function listVehiculo(string $search,array $order, int $length, int $start):array
	{
		$resDev = array();

		$columnOrder = $this->getColumnName($order[0]['column']);
		$typeOrder = $order[0]['dir'];

		if ($this->openDB()){
			if (strlen($search)==0)
			{
				$sql = sprintf("SELECT id_vehiculo,modelo,matricula,responsable FROM vehiculo ORDER BY %s %s LIMIT %s,%s",$columnOrder,$typeOrder,$start,$length);

				$resDev = $this->get_array($sql);

			} else {
				$funcGen = new FunctionsApp();
				// Lista de campos de busqueda
				$funcGen->setFieldsSearch(array('id_vehiculo','modelo','matricula','responsable'));
				$funcGen->setTextSearch($search);
				$searchCondition = $funcGen->getSearchText();

				$sql = sprintf("SELECT id_vehiculo,modelo,matricula FROM vehiculo WHERE %s ORDER BY %s %s LIMIT %s,%s",$searchCondition,$columnOrder,$typeOrder,$start,$length);

				$resDev = $this->get_array($sql);
			}
		} 
		$this->closeDB();
		return $resDev;
	}
	public function countVehiculo(string $search):int
	{
		$resDev = 0;


		if ($this->openDB()){

			if (strlen($search)==0)
			{
				$sql = sprintf("SELECT count(id_vehiculo) as total  FROM vehiculo");

				$resDev = $this->get_unique($sql);

			} else {
				$funcGen = new FunctionsApp();
				// Lista de campos de busqueda
				$funcGen->setFieldsSearch(array('id_vehiculo','modelo','matricula','responsable'));
				$funcGen->setTextSearch($search);
				$searchCondition = $funcGen->getSearchText();

				$sql = sprintf("SELECT count(id_vehiculo) as total FROM vehiculo WHERE %s",$searchCondition);

				$resDev = $this->get_unique($sql);
			}

		}
		$this->closeDB();
		return $resDev;
	}

	private function getColumnName(int $columnOrder):string
	{
		$nameColumn = '';
		switch ($columnOrder) {
			case 0:
				$nameColumn = 'id_vehiculo';
				break;
			case 1:
				$nameColumn = 'denominacion';
				break;
			default:
				$nameColumn = 'descripcion';
				break;
		}
		return $nameColumn;

	}


	public function writeVehiculo(array $data_w, string $type_w):bool
	{
		$ret_val = false;
		try {
			
			if ($type_w=='new'){
				$sql = FunctionsApp::createQuery($data_w,'insert','vehiculo');

			} elseif($type_w=='delete'){
				$sql = FunctionsApp::createQuery($data_w,'delete','vehiculo') . " WHERE id_vehiculo='".$data_w['id_vehiculo']."'";
			} elseif($type_w=='edit'){
				$sql = FunctionsApp::createQuery($data_w,'update','vehiculo') . " WHERE id_vehiculo='".$data_w['id_vehiculo']."'";

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
	public function get_dataVehiculo(string $idVehiculo):array
	{
		$data_ret = array();

		$sql =sprintf("SELECT id_vehiculo,modelo,matricula,responsable
						FROM vehiculo WHERE id_vehiculo = '%s'",$idVehiculo);
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
			$data_ret[] = array('id_vehiculo'=>'','modelo'=>'','matricula'=>'','responsable');
		}
		return $data_ret;
	}


}