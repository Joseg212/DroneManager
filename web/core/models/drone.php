<?php 


namespace DronesMaritime\Core\Models;

use DronesMaritime\Core\App\ConnectionDB;
use DronesMaritime\Core\App\FunctionsApp;

class Drone extends ConnectionDB
{

	public function __construct()
	{

	}

	private $msgErr='';
	public function get_msgErr()
	{
		return $this->msgErr;
	}	


	public function listDrone(string $search,array $order, int $length, int $start):array
	{
		$resDev = array();

		$columnOrder = $this->getColumnName($order[0]['column']);
		$typeOrder = $order[0]['dir'];

		if ($this->openDB()){
			if (strlen($search)==0)
			{
				$sql = sprintf("SELECT id_drone,modelo,num_serie FROM drone ORDER BY %s %s LIMIT %s,%s",$columnOrder,$typeOrder,$start,$length);

				$resDev = $this->get_array($sql);

			} else {
				$funcGen = new FunctionsApp();
				// Lista de campos de busqueda
				$funcGen->setFieldsSearch(array('id_drone','modelo','num_serie'));
				$funcGen->setTextSearch($search);
				$searchCondition = $funcGen->getSearchText();

				$sql = sprintf("SELECT id_drone,modelo,num_serie FROM drone WHERE %s ORDER BY %s %s LIMIT %s,%s",$searchCondition,$columnOrder,$typeOrder,$start,$length);

				$resDev = $this->get_array($sql);
			}
		} 
		$this->closeDB();
		return $resDev;
	}
	public function countDrone(string $search):int
	{
		$resDev = 0;


		if ($this->openDB()){

			if (strlen($search)==0)
			{
				$sql = sprintf("SELECT count(id_drone) as total  FROM drone");

				$resDev = $this->get_unique($sql);

			} else {
				$funcGen = new FunctionsApp();
				// Lista de campos de busqueda
				$funcGen->setFieldsSearch(array('id_drone','modelo','num_serie'));
				$funcGen->setTextSearch($search);
				$searchCondition = $funcGen->getSearchText();

				$sql = sprintf("SELECT count(id_drone) as total FROM drone WHERE %s",$searchCondition);

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
				$nameColumn = 'id_drone';
				break;
			case 1:
				$nameColumn = 'modelo';
				break;
			default:
				$nameColumn = 'num_serie';
				break;
		}
		return $nameColumn;

	}


	public function writeDrone(array $data_w, string $type_w):bool
	{
		$ret_val = false;
		try {
			
			if ($type_w=='new'){
				$sql = FunctionsApp::createQuery($data_w,'insert','drone');

			} elseif($type_w=='delete'){
				$sql = FunctionsApp::createQuery($data_w,'delete','drone') . " WHERE id_drone='".$data_w['id_drone']."'";
			} elseif($type_w=='edit'){
				$sql = FunctionsApp::createQuery($data_w,'update','drone') . " WHERE id_drone='".$data_w['id_drone']."'";

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
	public function get_dataDrone(string $idDrone):array
	{
		$data_ret = array();

		$sql =sprintf("SELECT id_drone,modelo,num_serie
						FROM drone WHERE id_drone = '%s'",$idDrone);
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
			$data_ret[] = array('id_drone'=>'','modelo'=>'','num_serie'=>'');
		}
		return $data_ret;
	}


}