<?php 


namespace DronesMaritime\Core\Models;

use DronesMaritime\Core\App\ConnectionDB;
use DronesMaritime\Core\App\FunctionsApp;

class Plan extends ConnectionDB
{

	public function __construct()
	{

	}

	public function writePlain(array $data_w, string $type_w):bool
	{
		$ret_val = false;
		try {
			
			if ($type_w=='new'){
				$sql = FunctionsApp::createQuery($data_w,'insert','plan');

			} elseif($type_w=='delete'){
				$sql = FunctionsApp::createQuery($data_w,'delete','plan') . " WHERE id_plan='".$data_w['id_plan']."'";
			} elseif($type_w=='edit'){
				$sql = FunctionsApp::createQuery($data_w,'update','plan') . " WHERE id_plan='".$data_w['id_plan']."'";
			} elseif($type_w=='editMision'){
				$sql = FunctionsApp::createQuery($data_w,'update','plan') . " WHERE id_mision='".$data_w['id_mision']."'";

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
	public function lastIdPlan(int $secuen):string
	{

		$id_plan = '';
		$data_ret = array();
		$sql =sprintf("
			SELECT p.id_plan
			FROM plan AS p
		 	WHERE secuencia=%s
		 	",$secuen);
		try {
			if ($this->openDB())
			{
				$data_ret = $this->get_array($sql);
			} else {
				$this->msgErr = $this->msgServer;
			}
			$id_plan = $data_ret[0]['id_plan'];
			$this->closeDB();
		} catch (Exception $err) {
			$this->msgErr = $err->getMessage();
		}

		return $id_plan;
	}
	public function get_IdMission(string $id_plain):string
	{
		$id_mission = '';
		$data_ret = array();
		$sql =sprintf("
			SELECT p.id_mision
			FROM plan AS p
		 	WHERE p.id_plan='%s'
		 	",$id_plain);

		try {
			if ($this->openDB())
			{
				$data_ret = $this->get_array($sql);
			} else {
				$this->msgErr = $this->msgServer;
			}
			$id_mission = $data_ret[0]['id_mision'];
			$this->closeDB();
		} catch (Exception $err) {
			$this->msgErr = $err->getMessage();
		}
		return $id_mission;
	}	
	public function listPlains(string $search,array $order, int $length, int $start, string $idUser, string $role, string $typeResult="Planificado"):array
	{
		$resDev = array();

		$columnOrder = $this->getColumnName($order[0]['column']);
		$typeOrder = $order[0]['dir'];

		if ($typeResult=='Planificado')
		{
			$htmlEstatus = " m.estatus='Planificado' ";
		} elseif($typeResult=='Reportado') {
			$htmlEstatus = " m.estatus IN ('Planificado','Reportado') ";
		}

		if ($this->openDB()){
			if (strlen($search)==0)
			{
				if ($role=='Administrador'){
					$sql = sprintf("
							SELECT p.id_plan,c.compania,
							    DATE_FORMAT(p.fecha_comienzo,'%s') AS fecha_comienzo,
							    p.hora_comienzo,p.tiempo_hrs,p.descrip,
							    p.tipo_recop,p.id_usuario,u.nombres,p.id_vehiculo,
							    v.modelo, p.id_mision, 
								DATE_FORMAT(p.fecha_final,'%s') AS fecha_final,
								p.observg_final, m.estatus
							FROM plan AS p 
							INNER JOIN mision AS m ON m.id_mision=p.id_mision
							INNER JOIN cliente AS c ON c.id_cliente=m.id_cliente
							INNER JOIN usuario AS u ON u.id_usuario=p.id_usuario
							INNER JOIN vehiculo AS v ON v.id_vehiculo = p.id_vehiculo
							WHERE $htmlEstatus
							ORDER BY %s %s LIMIT %s,%s",
							"%d\/%m\/%Y","%d\/%m\/%Y",$columnOrder,$typeOrder,$start,$length);
				} elseif ($role=='Gestor') {
					$sql = sprintf("
							SELECT p.id_plan,c.compania,
							    DATE_FORMAT(p.fecha_comienzo,'%s') AS fecha_comienzo,
							    p.hora_comienzo,p.tiempo_hrs,p.descrip,
							    p.tipo_recop,p.id_usuario,u.nombres,p.id_vehiculo,
							    v.modelo, p.id_mision, 
								DATE_FORMAT(p.fecha_final,'%s') AS fecha_final,
								p.observg_final,m.estatus
							FROM plan AS p 
							INNER JOIN mision AS m ON m.id_mision=p.id_mision
							INNER JOIN cliente AS c ON c.id_cliente=m.id_cliente
							INNER JOIN usuario AS u ON u.id_usuario=p.id_usuario
							INNER JOIN vehiculo AS v ON v.id_vehiculo = p.id_vehiculo
							WHERE $htmlEstatus AND p.id_usuario = '%s'
							ORDER BY %s %s LIMIT %s,%s",
							"%d\/%m\/%Y","%d\/%m\/%Y",$idUser,$columnOrder,$typeOrder,$start,$length);
				} elseif ($role=='Piloto') {
					$sql = sprintf("
							SELECT p.id_plan,c.compania,
							    DATE_FORMAT(p.fecha_comienzo,'%s') AS fecha_comienzo,
							    p.hora_comienzo,p.tiempo_hrs,p.descrip,
							    p.tipo_recop,p.id_usuario,u.nombres,p.id_vehiculo,
							    v.modelo, p.id_mision, 
								DATE_FORMAT(p.fecha_final,'%s') AS fecha_final,
								p.observg_final, m.estatus
							FROM plan AS p 
							INNER JOIN mision AS m ON m.id_mision=p.id_mision
							INNER JOIN cliente AS c ON c.id_cliente=m.id_cliente
							INNER JOIN usuario AS u ON u.id_usuario=p.id_usuario
							INNER JOIN vehiculo AS v ON v.id_vehiculo = p.id_vehiculo
							WHERE $htmlEstatus AND 
							p.id_plan IN (SELECT pp.id_plan FROM pilotos AS pp 
								WHERE pp.id_usuario='%s' ORDER BY pp.id_piloto DESC)
							ORDER BY %s %s LIMIT %s,%s",
							"%d\/%m\/%Y","%d\/%m\/%Y",$idUser,$columnOrder,$typeOrder,$start,$length);
				}

				$resDev = $this->get_array($sql);

			} else {
				$funcGen = new FunctionsApp();
				// Lista de campos de busqueda
				$funcGen->setFieldsSearch(array('p.id_plan','p.id_usuario','p.id_vehiculo','p.decrip','p.tipo_recop','u.nombres','c.compania','m.ciudad','v.modelo','p.tiempo_hrs'));

				$funcGen->setTextSearch($search);
				$searchCondition = $funcGen->getSearchText();

				if ($role=='Administrador'){
					$sql = sprintf("
							SELECT p.id_plan,c.compania,
							    DATE_FORMAT(p.fecha_comienzo,'%s') AS fecha_comienzo,
							    p.hora_comienzo,p.tiempo_hrs,p.descrip,
							    p.tipo_recop,p.id_usuario,u.nombres,p.id_vehiculo,
							    v.modelo, p.id_mision
							FROM plan AS p 
							INNER JOIN mision AS m ON m.id_mision=p.id_mision
							INNER JOIN cliente AS c ON c.id_cliente=m.id_cliente
							INNER JOIN usuario AS u ON u.id_usuario=p.id_usuario
							INNER JOIN vehiculo AS v ON v.id_vehiculo = p.id_vehiculo
							WHERE $htmlEstatus AND %s
							ORDER BY %s %s LIMIT %s,%s",
							"%d\/%m\/%Y",$searchCondition,$columnOrder,$typeOrder,$start,$length);
				} elseif($role=='Gestor') {
					$sql = sprintf("
							SELECT p.id_plan,c.compania,
							    DATE_FORMAT(p.fecha_comienzo,'%s') AS fecha_comienzo,
							    p.hora_comienzo,p.tiempo_hrs,p.descrip,
							    p.tipo_recop,p.id_usuario,u.nombres,p.id_vehiculo,
							    v.modelo, p.id_mision
							FROM plan AS p 
							INNER JOIN mision AS m ON m.id_mision=p.id_mision
							INNER JOIN cliente AS c ON c.id_cliente=m.id_cliente
							INNER JOIN usuario AS u ON u.id_usuario=p.id_usuario
							INNER JOIN vehiculo AS v ON v.id_vehiculo = p.id_vehiculo
							WHERE $htmlEstatus AND p.id_usuario = '%s' AND %s
							ORDER BY %s %s LIMIT %s,%s",
							"%d\/%m\/%Y",$idUser,$searchCondition,$columnOrder,$typeOrder,$start,$length);
				} elseif($role=='Piloto') {
					$sql = sprintf("
							SELECT p.id_plan,c.compania,
							    DATE_FORMAT(p.fecha_comienzo,'%s') AS fecha_comienzo,
							    p.hora_comienzo,p.tiempo_hrs,p.descrip,
							    p.tipo_recop,p.id_usuario,u.nombres,p.id_vehiculo,
							    v.modelo, p.id_mision
							FROM plan AS p 
							INNER JOIN mision AS m ON m.id_mision=p.id_mision
							INNER JOIN cliente AS c ON c.id_cliente=m.id_cliente
							INNER JOIN usuario AS u ON u.id_usuario=p.id_usuario
							INNER JOIN vehiculo AS v ON v.id_vehiculo = p.id_vehiculo
							WHERE $htmlEstatus AND 
							p.id_plan IN (SELECT pp.id_plan FROM pilotos AS pp 
								WHERE pp.id_usuario='%s' ORDER BY pp.id_piloto DESC) AND %s
							ORDER BY %s %s LIMIT %s,%s",
							"%d\/%m\/%Y",$idUser,$searchCondition,$columnOrder,$typeOrder,$start,$length);
				}

				$resDev = $this->get_array($sql);
			}
		} 
		$this->closeDB();
		return $resDev;
	}
	public function countPlains(string $search,string $idUser, string $role, string $typeResult='Planificado'):int
	{
		$resDev = 0;

		if ($typeResult=='Planificado')
		{
			$htmlEstatus = " m.estatus='Planificado' ";
		} elseif($typeResult=='Reportado')
		{
			$htmlEstatus = " m.estatus IN ('Planificado','Reportado') ";
		}

		if ($this->openDB()){

			if (strlen($search)==0)
			{
				if ($role=='Administrador'){
					$sql = sprintf("
							SELECT count(p.id_mision) as total 
							FROM plan AS p 
							INNER JOIN mision AS m ON m.id_mision=p.id_mision
							WHERE $htmlEstatus");
				} elseif ($role=='Gestor') {
					$sql = sprintf("
							SELECT count(p.id_mision) as total 
							FROM plan AS p 
							INNER JOIN mision AS m ON m.id_mision=p.id_mision
							WHERE $htmlEstatus AND p.id_usuario = '%s'",$idUser);
				} elseif ($role=='Piloto') {
					$sql = sprintf("
							SELECT count(p.id_mision) as total 
							FROM plan AS p 
							INNER JOIN mision AS m ON m.id_mision=p.id_mision
							WHERE $htmlEstatus AND 
							p.id_plan IN (SELECT pp.id_plan FROM pilotos AS pp 
								WHERE pp.id_usuario='%s' ORDER BY pp.id_piloto DESC)
							",$idUser);
				}
				$resDev = $this->get_unique($sql);

			} else {
				$funcGen = new FunctionsApp();
				// Lista de campos de busqueda
				$funcGen->setFieldsSearch(array('p.id_plan','p.id_usuario','p.id_vehiculo','p.decrip','p.tipo_recop','u.nombres','c.compania','m.ciudad','v.modelo','p.tiempo_hrs'));

				$funcGen->setTextSearch($search);
				$searchCondition = $funcGen->getSearchText();

				if ($role=='Administrador'){
					$sql = sprintf("
							SELECT count(p.id_mision) as total 
							FROM plan AS p 
							INNER JOIN mision AS m ON m.id_mision=p.id_mision
							WHERE $htmlEstatus");
				} elseif ($role=='Gestor') {
					$sql = sprintf("
							SELECT count(p.id_mision) as total 
							FROM plan AS p 
							INNER JOIN mision AS m ON m.id_mision=p.id_mision
							WHERE $htmlEstatus AND p.id_usuario = '%s'",$idUser);
				} elseif ($role=='Piloto') {
					$sql = sprintf("
							SELECT count(p.id_mision) as total 
							FROM plan AS p 
							INNER JOIN mision AS m ON m.id_mision=p.id_mision
							WHERE $htmlEstatus AND 
							p.id_plan IN (SELECT pp.id_plan FROM pilotos AS pp 
								WHERE pp.id_usuario='%s' ORDER BY pp.id_piloto DESC)
							",$idUser);
				}

				$resDev = $this->get_unique($sql);
			}

		}
		$this->closeDB();
		return $resDev;
	}


	public function get_dataPlan(string $idMission):array
	{
		$data_ret = array();

		$sql =sprintf("
			SELECT p.id_plan,p.id_usuario,u.nombres,p.id_vehiculo,
			    v.modelo, p.descrip, p.tipo_recop,
			    DATE_FORMAT(p.fecha_comienzo,'%s') as fecha_comienzo,
			    p.hora_comienzo, p.tiempo_hrs, p.id_mision, 
			    DATE_FORMAT(p.fecha_final,'%s') as fecha_final, p.observg_final
			FROM plan As p 
			INNER JOIN usuario AS u ON u.id_usuario=p.id_usuario
			INNER JOIN vehiculo AS v ON v.id_vehiculo= p.id_vehiculo
			WHERE p.id_mision = '%s'
			","%d\/%m\/%Y","%Y\-%m\-%d",$idMission);
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
			$data_ret[] = array('id_plan'=>'','id_usuario'=>'','nombres'=>'','id_vehiculo'=>'','modelo'=>'','descrip'=>'','tipo_recop'=>'','fecha_comienzo'=>'','hora_comienzo'=>'','tiempo_hrs'=>'');
		}
		return $data_ret;
	}	
	private function getColumnName(int $columnOrder):string
	{
		$nameColumn = '';
		switch ($columnOrder) {
			case 0:
				$nameColumn = 'p.id_plan';
				break;
			default:
				$nameColumn = 'p.id_plan';
				break;
		}
		return $nameColumn;

	}
}