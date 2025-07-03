<?php 


namespace DronesMaritime\Core\Models;

use DronesMaritime\Core\App\ConnectionDB;
use DronesMaritime\Core\App\FunctionsApp;

class Archivos extends ConnectionDB
{

	public function __construct()
	{

	}
	private $msgErr='';
	public function get_msgErr()
	{
		return $this->msgErr;
	}	

	public function writeArchivo(array $data_w, string $type_w):bool
	{
		$ret_val = false;
		try {
			
			if ($type_w=='new'){
				$sql = FunctionsApp::createQuery($data_w,'insert','archivos');

			} elseif($type_w=='delete'){
				$sql = FunctionsApp::createQuery($data_w,'delete','archivos') . " WHERE id_archivo='".$data_w['id_archivo']."'";
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
	public function listFiles(string $search,array $order, int $length, int $start,string $id_mision):array
	{
		$resDev = array();

		$columnOrder = $this->getColumnName($order[0]['column']);
		$typeOrder = $order[0]['dir'];

		if ($this->openDB()){
			$sql = sprintf("
				SELECT a.id_archivo,a.name_file,a.tipo_archivo,
					a.arch_orig,a.extension,a.descrip_arch, a.fecha_subida,
					a.file_id_drive,a.tamanio
				FROM archivos AS a 
				WHERE a.id_mision = '%s'
				ORDER BY %s %s limit %s,%s	
				",$id_mision,$columnOrder,$typeOrder,$start,$length);

			$resDev = $this->get_array($sql);
		} 
		$this->closeDB();
		return $resDev;
	}
	public function countFiles(string $search,string $id_mision):int
	{
		$resDev = 0;


		if ($this->openDB()){

			$sql = sprintf("
				SELECT count(a.id_mision) AS total
				FROM archivos AS a 
				WHERE a.id_mision = '%s'
				",$id_mision);

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
				$nameColumn = 'a.name_file';
				break;
			case 1:
				$nameColumn = 'a.descrip_arch';
				break;
			case 2:
				$nameColumn = 'a.tipo_archivo';
				break;
			case 3:
				$nameColumn = 'a.tamanio';
				break;
			default:
				$nameColumn = 'a.name_file';
				break;
		}
		return $nameColumn;
	}

	public function get_dataFiles(string $idMission):array
	{
		$data_ret = array();

		$sql =sprintf("
				SELECT a.id_archivo,a.name_file, a.tipo_archivo, a.arch_orig,
				    a.extension, a.descrip_arch, a.id_usuario,u.nombres,a.tamanio,
				    DATE_FORMAT(a.fecha_subida,'%s') as fecha_subida
				FROM archivos AS a
				INNER JOIN usuario AS u ON u.id_usuario = a.id_usuario
				WHERE a.id_mision = '%s'
			","%d\/%m\/%Y",$idMission);
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
			$data_ret[] = array('id_archivo'=>'','name_file'=>'','tipo_archivo'=>'','arch_orig'=>'','extension'=>'','descrip_arch'=>'','id_usuario'=>'','nombres'=>'','fecha_subida'=>'','tamanio'=>'');
		}
		return $data_ret;
	}
}