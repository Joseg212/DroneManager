<?php 


namespace DronesMaritime\Core\Models;

use DronesMaritime\Core\App\ConnectionDB;
use DronesMaritime\Core\App\FunctionsApp;

class Mision extends ConnectionDB
{

	public function __construct()
	{

	}

	private $msgErr='';
	public function get_msgErr()
	{
		return $this->msgErr;
	}	

	private $filterData=array();
	private $condiFilter = false;

	public function set_filterData(array $filter_data)
	{
		$this->filterData = $filter_data;
		return $filter_data;
	}
	public function set_activeFilter(bool $condi)
	{
		$this->condiFilter = $condi;
		return $condi;
	}
	private function addCondiAnd(string $lastFilter):string
	{
		if (!empty($lastFilter))
		{
			$lastFilter .= " AND ";
		}
		return $lastFilter;
	}
	private function get_filterData(string $lastFilter)
	{

		if ($this->condiFilter==true)
		{
			$lastFilter = "";

			if (!empty($this->filterData['fecha_inicio']))
			{
				// crear la fecha de inicio
				$fechaInicio = new  \DateTime($this->filterData['fecha_inicio']);
			}
			if (!empty($this->filterData['fecha_final']))
			{
				// crear la fecha de inicio
				$fechaFinal = new  \DateTime($this->filterData['fecha_final']);
			}
			if (!$fechaInicio==null)
			{
				// se determina el rango de fecha
				$lastFilter .= "(m.fecha_inicio>='".$fechaInicio->format("Y/m/d")."' AND fecha_inicio<='".$fechaFinal->format("Y/m/d")."')";
			}
			if ($this->filterData['estatus']!='All')
			{
				$lastFilter = $this->addCondiAnd($lastFilter);
				$lastFilter .= "m.estatus='".$this->filterData['estatus']."'";
			}

			if (!empty($this->filterData['id_cliente']))
			{
				$lastFilter = $this->addCondiAnd($lastFilter);
				$lastFilter .= "m.id_cliente ='".$this->filterData['id_cliente']."'";
			}
			if (!empty($this->filterData['id_usuario']))
			{
				//$lastFilter = $this->addCondiAnd($lastFilter);
				$this->innerJoinGestor = " INNER JOIN plan AS p ON p.id_mision = m.id_mision AND p.id_usuario='".$this->filterData['id_usuario']."' ";
			} else {
				$this->innerJoinGestor="";
			}
			if (empty($lastFilter))
			{
				$lastFilter = " m.estatus<>'' ";
			}
		}
		return $lastFilter;
	}

	private $innerJoinGestor = "";

	public function listMission(string $search,array $order, int $length, int $start):array
	{
		$resDev = array();

		$columnOrder = $this->getColumnName($order[0]['column']);
		$typeOrder = $order[0]['dir'];

		


		if ($this->openDB()){
			if (strlen($search)==0)
			{
				$filterData = $this->get_filterData("m.estatus = 'Activo'");
				$innerJGestor = $this->innerJoinGestor;

				$sql = sprintf("SELECT m.id_mision,c.compania,
						DATE_FORMAT(m.fecha_inicio,'%s') AS fecha_inicio,
						DATE_FORMAT(m.fecha_final,'%s') AS fecha_final,
						m.descrip,m.objetivo, m.coord,m.ciudad,t.denominacion, t.descripcion
						FROM mision AS m 
						INNER JOIN cliente AS c ON m.id_cliente=c.id_cliente
						INNER JOIN tipo_mision AS t ON m.id_tipom = t.id_tipom
						$innerJGestor
						WHERE $filterData
						ORDER BY %s %s LIMIT %s,%s",
						"%d\/%m\/%Y","%d\/%m\/%Y",
						$columnOrder,$typeOrder,$start,$length);

				$resDev = $this->get_array($sql);

				//$this->msgErr = $sql;

			} else {
				$funcGen = new FunctionsApp();
				// Lista de campos de busqueda
				$funcGen->setFieldsSearch(array('m.id_mision','t.id_tipom','c.id_cliente','m.decrip','m.objetivo','m.coord','c.compania','m.ciudad','t.denominacion','t.descripcion'));

				$filterData = $this->get_filterData("m.estatus = 'Activo'");
				$innerJGestor = $this->innerJoinGestor;

				if (!empty($filterData) && $this->condiFilter==true)
				{
					$filterData .= " AND ";
				}

				$funcGen->setTextSearch($search);
				$searchCondition = $funcGen->getSearchText();


				$sql = sprintf("SELECT m.id_mision,c.compania, 
						DATE_FORMAT(m.fecha_inicio,'%s') AS fecha_inicio,
						DATE_FORMAT(m.fecha_final,'%s') AS fecha_final,
						m.descrip,m.objetivo, m.coord,m.ciudad,t.denominacion, t.descripcion
						FROM mision AS m 
						INNER JOIN cliente AS c ON m.id_cliente=c.id_cliente
						INNER JOIN tipo_mision AS t ON m.id_tipom = t.id_tipom
						$innerJGestor
						WHERE $filterData %s 
						ORDER BY %s %s LIMIT %s,%s",
						"%d\/%m\/%Y","%d\/%m\/%Y",
						$searchCondition,$columnOrder,$typeOrder,$start,$length);

				$resDev = $this->get_array($sql);
			}
		} 
		$this->closeDB();
		return $resDev;
	}
	public function countMission(string $search):int
	{
		$resDev = 0;


		if ($this->openDB()){

			if (strlen($search)==0)
			{
				$sql = sprintf("SELECT count(m.id_mision) as total  
					FROM mision AS m 
					INNER JOIN cliente AS c ON m.id_cliente=c.id_cliente
					INNER JOIN tipo_mision AS t ON m.id_tipom = t.id_tipom
					WHERE m.estatus='Activo'
					");

				$resDev = $this->get_unique($sql);

			} else {
				$funcGen = new FunctionsApp();
				// Lista de campos de busqueda
				$funcGen->setFieldsSearch(array('m.id_mision','t.id_tipom','c.id_cliente','m.decrip','m.objetivo','m.coord','c.compania','m.ciudad','t.denominacion','t.descripcion'));

				$funcGen->setTextSearch($search);
				$searchCondition = $funcGen->getSearchText();

				$sql = sprintf("SELECT count(m.id_mision) as total 
					FROM mision AS m 
					INNER JOIN cliente AS c ON m.id_cliente=c.id_cliente
					INNER JOIN tipo_mision AS t ON m.id_tipom = t.id_tipom
					WHERE m.estatus='Activo' AND %s 
					",$searchCondition);

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
				$nameColumn = 'm.id_mision';
				break;
			default:
				$nameColumn = 'm.id_mision';
				break;
		}
		return $nameColumn;

	}

	public function writeMision(array $data_w, string $type_w):bool
	{
		$ret_val = false;
		try {
			
			if ($type_w=='new'){
				$sql = FunctionsApp::createQuery($data_w,'insert','mision');

			} elseif($type_w=='delete'){
				$sql = FunctionsApp::createQuery($data_w,'delete','mision') . " WHERE id_mision='".$data_w['id_mision']."' AND estatus='Activo' ";
			} elseif($type_w=='edit'){
				$sql = FunctionsApp::createQuery($data_w,'update','mision') . " WHERE id_mision='".$data_w['id_mision']."' AND estatus='Activo' ";
			} elseif($type_w=='editPlain'){
				$sql = FunctionsApp::createQuery($data_w,'update','mision') . " WHERE id_mision='".$data_w['id_mision']."' AND estatus='Planificado' ";
			} elseif($type_w=='editFinish'){
				$sql = FunctionsApp::createQuery($data_w,'update','mision') . " WHERE id_mision='".$data_w['id_mision']."' AND estatus='Reportado' ";
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

	public function get_dataMision(string $idMission):array
	{
		$data_ret = array();

		$sql =sprintf("
			SELECT m.id_mision,c.id_cliente, c.compania,c.direccion,c.telf_cia,c.rif_nit,
        		c.contacto,c.telf_contact,c.email, mt.id_tipom,mt.denominacion,mt.descripcion, m.ciudad,
        		m.descrip,m.objetivo,m.coord,m.estatus,
				DATE_FORMAT(m.fecha_inicio,'%s') AS fecha_inicio,
				DATE_FORMAT(m.fecha_final,'%s') AS fecha_final		    
			FROM mision AS m
		    INNER JOIN cliente AS c ON c.id_cliente = m.id_cliente
		    INNER JOIN tipo_mision AS mt ON mt.id_tipom=m.id_tipom			
		 	WHERE id_mision = '%s'","%d\/%m\/%Y","%d\/%m\/%Y",$idMission);
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
			$data_ret[] = array('id_mision'=>'','id_cliente'=>'','compania'=>'','rif_nit'=>'','direccion'=>'','telf_cia'=>'','contacto'=>'','telf_contact'=>'','id_tipom'=>'','denominacion'=>'');
		}
		return $data_ret;
	}

	public function get_dataReport(string $condi):array
	{
		$data_ret = array();

		$sql =sprintf("
				SELECT m.id_mision, m.ciudad,m.descrip,m.objetivo, m.coord, 
				        DATE_FORMAT(m.fecha_inicio,'%s') AS fecha_inicio, 
				        DATE_FORMAT(m.fecha_final,'%s') AS fecha_final,
				        DATE_FORMAT(m.fecha_finalizado,'%s') AS fecha_finalizado,
				        m.id_cliente,clte.compania, clte.rif_nit, clte.contacto, 
				        tipm.denominacion AS tipo_mision, tipm.descripcion As tipo_descrip,
				        us1.nombres AS nombr_gestor, vh.modelo AS mod_vehiculo,vh.matricula AS matr_vehiculo,
				        plan.descrip AS plan_descrip,plan.tipo_recop AS plan_trecop,
				        plan.tiempo_hrs AS plan_timeh,plan.observg_final AS plan_observf, 
				        DATE_FORMAT(plan.fecha_final,'%s') AS plan_finalizado, plan.id_plan
				    FROM mision AS m 
				    INNER JOIN cliente AS clte ON clte.id_cliente = m.id_cliente
				    INNER JOIN tipo_mision AS tipm ON tipm.id_tipom = m.id_tipom
				    INNER JOIN plan AS plan ON plan.id_mision = m.id_mision
				    INNER JOIN usuario AS us1 ON us1.id_usuario = plan.id_usuario
				    INNER JOIN vehiculo as vh  ON vh.id_vehiculo = plan.id_vehiculo
				    WHERE %s AND m.estatus = 'Finalizado'
    		 	","%d\/%m\/%Y","%d\/%m\/%Y","%d\/%m\/%Y","%d\/%m\/%Y",$condi);
		//var_dump($sql);

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
			$data_ret[] = array('id_mision'=>'');
		}
		return $data_ret;
	}

}