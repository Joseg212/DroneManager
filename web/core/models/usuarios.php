<?php 


namespace DronesMaritime\Core\Models;

use DronesMaritime\Core\App\ConnectionDB;
use DronesMaritime\Core\App\FunctionsApp;

class Usuarios extends ConnectionDB
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

	public function validUser(string $userName, string $userPassword)
	{
		if ($this->openDB())
		{
			$sql = sprintf("SELECT count(id_usuario) as total FROM usuario WHERE user = '%s' AND password = '%s'",$userName, $userPassword);


			$total = $this->get_unique($sql);

			if ($this->error_cond)
			{
				echo "Error:".$this->msgServer;
			}

			$valor_ret = ($total>0) ? true : false;

			if ($valor_ret)
			{
				$sql = sprintf("SELECT id_usuario FROM usuario WHERE user = '%s' AND password = '%s'",$userName, $userPassword);
				
				$idUser = $this->get_unique_str($sql);

				$this->idUser = $idUser;
			}
			$this->closeDB();



		} else {
			echo sprintf("Error Conexión ".$this->msgServer);
		}

		return $valor_ret;
	}
	public function listUsuario(string $search,array $order, int $length, int $start):array
	{
		$resDev = array();

		$columnOrder = $this->getColumnName($order[0]['column']);
		$typeOrder = $order[0]['dir'];

		if ($this->openDB()){
			if (strlen($search)==0)
			{
				$sql = sprintf("SELECT id_usuario,nombres,user,role FROM usuario ORDER BY %s %s LIMIT %s,%s",$columnOrder,$typeOrder,$start,$length);

				$resDev = $this->get_array($sql);

			} else {
				$funcGen = new FunctionsApp();
				// Lista de campos de busqueda
				$funcGen->setFieldsSearch(array('id_usuario','nombres','user','email','telf_movil'));
				$funcGen->setTextSearch($search);
				$searchCondition = $funcGen->getSearchText();

				$sql = sprintf("SELECT id_usuario,nombres,user,role FROM usuario WHERE %s ORDER BY %s %s LIMIT %s,%s",$searchCondition,$columnOrder,$typeOrder,$start,$length);

				$resDev = $this->get_array($sql);
			}
		} 
		$this->closeDB();
		return $resDev;
	}
	public function listUsuarioSel(string $search,array $order, int $length, int $start,string $type):array
	{
		$resDev = array();

		$columnOrder = $this->getColumnName($order[0]['column']);
		$typeOrder = $order[0]['dir'];

		if ($this->openDB()){
			if (strlen($search)==0)
			{
				if ($type=='Gestor'){
					$sql = sprintf("SELECT id_usuario,nombres,user,role FROM usuario WHERE role='Gestor' ORDER BY %s %s LIMIT %s,%s",$columnOrder,$typeOrder,$start,$length);

				} else {
					$sql = sprintf("SELECT id_usuario,nombres,user,role FROM usuario WHERE role='Piloto' ORDER BY %s %s LIMIT %s,%s",$columnOrder,$typeOrder,$start,$length);
				}

				$resDev = $this->get_array($sql);

			} else {
				$funcGen = new FunctionsApp();
				// Lista de campos de busqueda
				$funcGen->setFieldsSearch(array('id_usuario','nombres','user','email','telf_movil'));
				$funcGen->setTextSearch($search);
				$searchCondition = $funcGen->getSearchText();

				if ($type=='Gestor'){
					$sql = sprintf("SELECT id_usuario,nombres,user,role FROM usuario WHERE role='Gestor' AND %s ORDER BY %s %s LIMIT %s,%s",$searchCondition,$columnOrder,$typeOrder,$start,$length);
				} else {
					$sql = sprintf("SELECT id_usuario,nombres,user,role FROM usuario WHERE role='Piloto' AND %s ORDER BY %s %s LIMIT %s,%s",$searchCondition,$columnOrder,$typeOrder,$start,$length);
				}

				$resDev = $this->get_array($sql);
			}
		} 
		$this->closeDB();
		return $resDev;
	}
	public function countUsuario(string $search):int
	{
		$resDev = 0;


		if ($this->openDB()){

			if (strlen($search)==0)
			{
				$sql = sprintf("SELECT count(id_usuario) as total  FROM usuario");

				$resDev = $this->get_unique($sql);

			} else {
				$funcGen = new FunctionsApp();
				// Lista de campos de busqueda
				$funcGen->setFieldsSearch(array('id_usuario','nombres','user','email','telf_movil'));
				$funcGen->setTextSearch($search);
				$searchCondition = $funcGen->getSearchText();

				$sql = sprintf("SELECT count(id_usuario) as total FROM usuario WHERE %s",$searchCondition);

				$resDev = $this->get_unique($sql);
			}

		}
		$this->closeDB();
		return $resDev;
	}
	public function countUsuarioSel(string $search, string $type):int
	{
		$resDev = 0;


		if ($this->openDB()){

			if (strlen($search)==0)
			{
				if ($type=='Gestor'){
					$sql = sprintf("SELECT count(id_usuario) as total  FROM usuario WHERE role='Gestor'");
				} else {
					$sql = sprintf("SELECT count(id_usuario) as total  FROM usuario WHERE role='Piloto'");
				}
				$resDev = $this->get_unique($sql);

			} else {
				$funcGen = new FunctionsApp();
				// Lista de campos de busqueda
				$funcGen->setFieldsSearch(array('id_usuario','nombres','user','email','telf_movil'));
				$funcGen->setTextSearch($search);
				$searchCondition = $funcGen->getSearchText();

				if ($type=='Gestor'){
					$sql = sprintf("SELECT count(id_usuario) as total FROM usuario WHERE role='Gestor' AND %s",$searchCondition);
				} else {
					$sql = sprintf("SELECT count(id_usuario) as total FROM usuario WHERE role='Piloto' AND %s",$searchCondition);
				}

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
				$nameColumn = 'id_usuario';
				break;
			case 1:
				$nameColumn = 'nombres';
				break;
			case 2:
				$nameColumn = 'user';
				break;
			default:
				$nameColumn = 'id_usuario';
				break;
		}
		return $nameColumn;

	}


	public function writeUser(array $data_w, string $type_w):bool
	{
		$ret_val = false;
		try {
			
			if ($type_w=='new'){
				$sql = FunctionsApp::createQuery($data_w,'insert','usuario');

			} elseif($type_w=='delete'){
				$sql = FunctionsApp::createQuery($data_w,'delete','usuario') . " WHERE id_usuario='".$data_w['id_usuario']."'";
			} elseif($type_w=='edit'){
				$sql = FunctionsApp::createQuery($data_w,'update','usuario') . " WHERE id_usuario='".$data_w['id_usuario']."'";

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
	public function get_dataUser(string $idUser):array
	{
		if ($idUser==null){
			return array();
		}

		$data_ret = array();

		$sql =sprintf("SELECT id_usuario,nombres,email,telf_movil,cargo,user,password, role 
						FROM usuario WHERE id_usuario = '%s'",$idUser);
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
			$data_ret[] = array('id_usuario'=>'','nombres'=>'','email'=>'','telf_movil'=>'','cargo'=>'','user'=>'','password'=>'','role'=>'nulo');
		}
		return $data_ret;
	}


}