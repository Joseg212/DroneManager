<?php 
namespace DronesMaritime\Core\App;

abstract class ConnectionDB
{
	private $conDB = null;

	public $msgServer = '';

	public $error_cond = false;

	public $lastIdInsert = '';

	public function __construct()
	{

	}
	public function openDB():bool
	{
		$valor_ret = false;
		$this->conDB = mysqli_connect(FS_DB_HOST,FS_DB_USER,FS_DB_PASS,FS_DB_NAME,FS_DB_PORT);
		if (mysqli_connect_errno()) 
		{
			$msgServer = 'Error en Conexión: '.mysqli_connect_error();
		} else {
			$valor_ret=true;
		}
		return $valor_ret;
	}
	public function closeDB()
	{
		mysqli_close($this->conDB);
	}

	private $affect_rows = 0;

	public function rowsAffect()
	{
		return $this->affect_rows;
	}

	public function get_results(string $query):object
	{

		$result = mysqli_query($this->conDB,$query);
		$this->affect_rows = mysqli_affected_rows($conDB);
		return $result;
	}

	public function actionQuery(string $query)
	{

		$result = mysqli_query($this->conDB,$query);
		if (strpos($query, 'INSERT')!==false)
		{
			$this->lastIdInsert = mysqli_insert_id($this->conDB);
		}
		
		$this->affect_rows = mysqli_affected_rows($this->conDB);
	}

	public function get_array(string $query):array 
	{
		$result = mysqli_query($this->conDB,$query);
		$datos = array();

		while ($arrData = mysqli_fetch_array($result, MYSQLI_ASSOC))
		{
			$datos[] = $arrData; 	
		};

		mysqli_free_result($result);
		return $datos;
	}
	public function get_idLastInsert()
	{
		return  $this->lastIdInsert;
	}

	public function get_unique(string $query):int
	{
		$valor_ret = 0;

		if ($result=mysqli_query($this->conDB,$query))
		{
		  	mysqli_data_seek($result,0);

		  	$row=mysqli_fetch_row($result);
		  	$valor_ret = $row[0]; // devuelve unico valor 
		  	mysqli_free_result($result);
		  	$this->error_cond = false;
		} else {
			$this->error_cond = true;
			$this->msgServer = "No se ejecuto la consulta";
		}
		return $valor_ret;		
	}

	public function get_unique_str(string $query):string
	{
		$valor_ret = '';

		if ($result=mysqli_query($this->conDB,$query))
		{
		  	mysqli_data_seek($result,0);

		  	$row=mysqli_fetch_row($result);
		  	$valor_ret = $row[0]; // devuelve unico valor 
		  	mysqli_free_result($result);
		  	$this->error_cond = false;
		} else {
			$this->error_cond = true;
			$this->msgServer = "No se ejecuto la consulta";
		}
		return $valor_ret;		
	}
}