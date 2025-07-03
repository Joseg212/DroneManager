<?php 


namespace DronesMaritime\Core\Models;

use DronesMaritime\Core\App\ConnectionDB;
use DronesMaritime\Core\App\FunctionsApp;

class Cliente extends ConnectionDB
{

	public function __construct()
	{

	}

	private $msgErr='';
	public function get_msgErr()
	{
		return $this->msgErr;
	}	


	public function listCliente(string $search,array $order, int $length, int $start):array
	{
		$resDev = array();

		$columnOrder = $this->getColumnName($order[0]['column']);
		$typeOrder = $order[0]['dir'];

		if ($this->openDB()){
			if (strlen($search)==0)
			{
				$sql = sprintf("SELECT id_cliente,compania,contacto,email FROM cliente ORDER BY %s %s LIMIT %s,%s",$columnOrder,$typeOrder,$start,$length);

				$resDev = $this->get_array($sql);

			} else {
				$funcGen = new FunctionsApp();
				// Lista de campos de busqueda
				$funcGen->setFieldsSearch(array('id_cliente','compania','contacto','email','direccion'));
				$funcGen->setTextSearch($search);
				$searchCondition = $funcGen->getSearchText();

				$sql = sprintf("SELECT id_cliente,compania,contacto,email FROM cliente WHERE %s ORDER BY %s %s LIMIT %s,%s",$searchCondition,$columnOrder,$typeOrder,$start,$length);

				$resDev = $this->get_array($sql);
			}
		} 
		$this->closeDB();
		return $resDev;
	}
	public function listCliente02(string $search,array $order, int $length, int $start):array
	{
		$resDev = array();

		$columnOrder = $this->getColumnName($order[0]['column']);
		$typeOrder = $order[0]['dir'];

		if ($this->openDB()){
			if (strlen($search)==0)
			{
				$sql = sprintf("SELECT id_cliente,compania,contacto,email,direccion,telf_cia,telf_contact FROM cliente ORDER BY %s %s LIMIT %s,%s",$columnOrder,$typeOrder,$start,$length);

				$resDev = $this->get_array($sql);

			} else {
				$funcGen = new FunctionsApp();
				// Lista de campos de busqueda
				$funcGen->setFieldsSearch(array('id_cliente','compania','contacto','email','direccion'));
				$funcGen->setTextSearch($search);
				$searchCondition = $funcGen->getSearchText();

				$sql = sprintf("SELECT id_cliente,compania,contacto,email,direccion,telf_cia,telf_contact FROM cliente WHERE %s ORDER BY %s %s LIMIT %s,%s",$searchCondition,$columnOrder,$typeOrder,$start,$length);

				$resDev = $this->get_array($sql);
			}
		} 
		$this->closeDB();
		return $resDev;
	}

	public function countCliente(string $search):int
	{
		$resDev = 0;


		if ($this->openDB()){

			if (strlen($search)==0)
			{
				$sql = sprintf("SELECT count(id_cliente) as total  FROM cliente");

				$resDev = $this->get_unique($sql);

			} else {
				$funcGen = new FunctionsApp();
				// Lista de campos de busqueda
				$funcGen->setFieldsSearch(array('id_cliente','compania','contacto','email','direccion'));
				$funcGen->setTextSearch($search);
				$searchCondition = $funcGen->getSearchText();

				$sql = sprintf("SELECT count(id_cliente) as total FROM cliente WHERE %s",$searchCondition);

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
				$nameColumn = 'id_cliente';
				break;
			case 1:
				$nameColumn = 'compania';
				break;
			case 2:
				$nameColumn = 'contacto';
				break;
			default:
				$nameColumn = 'email';
				break;
		}
		return $nameColumn;

	}


	public function writeCliente(array $data_w, string $type_w):bool
	{
		$ret_val = false;
		try {
			
			if ($type_w=='new'){
				$sql = FunctionsApp::createQuery($data_w,'insert','cliente');

				var_dump($sql);

			} elseif($type_w=='delete'){
				$sql = FunctionsApp::createQuery($data_w,'delete','cliente') . " WHERE id_cliente='".$data_w['id_cliente']."'";
			} elseif($type_w=='edit'){
				$sql = FunctionsApp::createQuery($data_w,'update','cliente') . " WHERE id_cliente='".$data_w['id_cliente']."'";

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
	public function get_dataCliente(string $idClte):array
	{
		$data_ret = array();

		$sql =sprintf("SELECT id_cliente,compania,direccion,rif_nit,email,telf_cia,contacto,telf_contact,estatus
						FROM cliente WHERE id_cliente = '%s'",$idClte);
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
			$data_ret[] = array('id_cliente'=>'','compania'=>'','direccion'=>'','rif_nit'=>'','email'=>'','telf_cia'=>'','contacto'=>'','telf_contact'=>'estatus');
		}
		return $data_ret;
	}


}