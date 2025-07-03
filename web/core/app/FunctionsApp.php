<?php

namespace DronesMaritime\Core\App;

use DronesMaritime\Core\Models\LogSistema;
use Google_Client;
use Google_Service_Drive;
use Google_Service_Drive_DriveFile;
use Google_Service_Exception;

final class FunctionsApp extends Controller
{
    /* Procedimiento para la Busqueda Normal o Selectiva */
    private $textSearch='';
    private $arrSearch=array();
    private $arrFieldSearch = array();

    public function setTextSearch(string $txtSearch)
    {
        $this->textSearch=$txtSearch;
        $this->arrSearch=explode(" ",$txtSearch);
    }
    public function setFieldsSearch(array $arrFieldS)
    {
        $this->arrFieldSearch = $arrFieldS;
    }

    public function getSearchText()
    {
        $cadena = " (";
        $longArray = sizeof($this->arrFieldSearch);
        /* Devuelve el texto para la busqueda de información */
        // Si una busqueda Generalizada.
        foreach ($this->arrFieldSearch as $posc => $field)
        {
            if ($longArray==($posc+1))
            {
                $cadena.=$this->getCadena($field).") ";
            } else {
                $cadena.=$this->getCadena($field)." OR ";
            }
        }

        return $cadena;
    }
    
    private function getCadena($fieldName)
    {
        $cadenaSub = "";

        $longArray = sizeof($this->arrSearch);

        foreach ($this->arrSearch as $key => $value) {
            if ($longArray==($key+1))
            {
                $cadenaSub.=$fieldName." like '%".$value."%'";
            } else {
                $cadenaSub.=$fieldName." like '%".$value."%' OR ";
            }
        }
        return $cadenaSub;
    }
    public static function createQuery(array $data_q, string $type_q, string $tableName):string
    {
        $cadena = "";
        $complemento = ") values (";

        if ($type_q=='insert'){
            $cadena = "INSERT INTO ".$tableName." (";
        } elseif ($type_q=='update'){
            $cadena = "UPDATE ".$tableName." SET ";
        } elseif ($type_q=='delete') {
            $cadena = "DELETE FROM ".$tableName." ";
        }

        if ($type_q!='delete'){
            $sec=0;
            foreach ($data_q as $field => $value) {
                if (strpos($field,"id_")!== false && $sec==0)
                {
                    $sec++;
                    continue;
                }

                if ($complemento==") values ("){
                    if ($type_q=='insert'){
                        $cadena .= $field;
                    } elseif ($type_q=='update'){
                        $cadena .= $field ."=";
                    }
                } else {
                    if ($type_q=='insert'){
                        $cadena .= ",".$field;
                    } elseif ($type_q=='update'){
                        $cadena .= ",".$field ."=";
                    }
                    $complemento .=",";
                }
                switch (gettype($value)) {
                    case 'string':
                        $complemento .="'".$value."'";
                        if ($type_q=='update'){
                            $cadena .= "'".$value."'";
                        }
                        break;
                    case 'integer':
                        $complemento .=strval($value);
                        if ($type_q=='update'){
                            $cadena .= strval($value);
                        }
                        break;
                    case 'double':
                        $complemento .=strval($value);
                        if ($type_q=='update'){
                            $cadena .= strval($value);
                        }
                        break;
                    case 'object':
                        $complemento .="'".$value->format("Y/m/d H:i:s")."'";
                        if ($type_q=='update'){
                            $cadena .= "'".$value->format("Y/m/d H:i:s")."'";
                        }
                        break;
                }
            }
        }
        if ($type_q=='insert'){
            $cadena = $cadena . $complemento . ")";
        }
        return $cadena;
    }
    public function UserAccess(string $role,string $program):bool
    {
        $result = false;
        if ($role=='Administrador')
        {
            $result = true;
        }
        if (strtoupper($program)=='LOGOUT' || strtoupper($program)=='DASHBOARD' || strtoupper($program)=='VALIDFORM')
        {
            $result = true;
        }
        switch (true) {
            case $role=='Piloto' && strtoupper($program) =='CONSULTAS':
                $result = true;
                break;
            case $role=='Piloto' && strtoupper($program) =='REPORTAR':
                $result = true;
                break;
            case $role=='Gestor' && strtoupper($program) =='PLANES':
                $result = true;
                break;
            case $role=='Gestor' && strtoupper($program) =='CONSULTAS':
                $result = true;
                break;
            case $role=='Gestor' && strtoupper($program) =='REPORTAR':
                $result = true;
                break;
            case $role=='Gestor' && strtoupper($program) =='RESULTADOS':
                $result = true;
                break;
            case $role=='Gestor' && strtoupper($program) =='MISIONINFORME':
                $result = true;
                break;
        }
        return $result;
    }
    public function render_access_denied()
    {
        $id_section = uniqid();


        echo $this->render('home/access_denied.html.twig', ['id_section' => $id_section, 'msgSystem'=>'', 'action_system'=>'dont']);        
    }

    public function registerAction(array $user, string $accion)
    {
        
        $logSistema = new LogSistema();

        $fecha_reg = new \DateTime(date("Y/m/d H:i:s"));


        $dataWrite = array(
            'id_secuen'     => '',
            'accion'        => $accion,
            'error'         => 'ninguno',
            'id_usuario'    => $user['id_usuario'],
            'nombres'       => $user['nombres'],
            'alias'         => $user['user'],
            'role'          => $user['role'],
            'fecha_hora'    => $fecha_reg,
            );

        $logSistema->writeLog($dataWrite,'new');
    }
    public function uploadDocumentGoogleDriver($documento,$descripcion,$baseDir,$id_mision,$nameFile):string 
    {
        // Variables de credenciales.
        //$claveJSON = FS_KEY_JSON;
        //$pathJSON = FS_PATH_JSON;
        $file_id_drive="";

        //configurar variable de entorno
        putenv('GOOGLE_APPLICATION_CREDENTIALS='.$baseDir.FS_PATH_JSON);

        $client = new Google_Client();
        $client->useApplicationDefaultCredentials();
        $client->setScopes([FS_VINC_JSON]);
        try{        
            //instanciamos el servicio
            $service = new Google_Service_Drive($client);

            //instacia de archivo
            $file = new Google_Service_Drive_DriveFile();
            $file->setName($nameFile);
            //$file->setMimeType('application/vnd.google-apps.Mision_'.$id_mision);

            //obtenemos el mime type
            $finfo = finfo_open(FILEINFO_MIME_TYPE); 
            $mime_type=finfo_file($finfo, $documento);

            //id de la carpeta donde hemos dado el permiso a la cuenta de servicio 
            $file->setParents(array(FS_KEY_JSON));
            $file->setDescription($descripcion);
            $file->setMimeType($mime_type);
            $result = $service->files->create(
              $file,
              array(
                'data' => file_get_contents($documento),
                'mimeType' => $mime_type,
                'uploadType' => 'media',
              )
            );
            $file_id_drive = $result->id;
            //echo "2.- Fichero subido a Google Drive.";
        }catch(Google_Service_Exception $gs){
            $m=json_decode($gs->getMessage());
            var_dump($m);
            echo $m->error->message;
        }catch(Exception $e){
            echo $e->getMessage();  
        }
        return $file_id_drive;
    }
    public  $objResult=null;

    public function deleteFileInGoogleDrive(string $nameFile=null, string $baseDir):bool
    {
        $valRet = false;
        if ($nameFile==null)
        {
            return $valRet;
        } else {
            //configurar variable de entorno
            putenv('GOOGLE_APPLICATION_CREDENTIALS='.$baseDir.FS_PATH_JSON);

            var_dump("basedir ".$baseDir.FS_PATH_JSON);

            $client = new Google_Client();
            $client->useApplicationDefaultCredentials();
            $client->setScopes([FS_VINC_JSON]);
            try {
                //instanciamos el servicio
                $service = new Google_Service_Drive($client);


                /*$file->setParents(array(FS_KEY_JSON));
                $file->setId($idFileDrive);

                $this->objResult = $service->files->delete($file->getId());*/

                $optParams = array(
                        'pageSize' => 10,
                        'fields' => "nextPageToken, files(contentHints/thumbnail,fileExtension,iconLink,id,name,size,thumbnailLink,webContentLink,webViewLink,mimeType,parents)",
                        'q' => "'".FS_KEY_JSON."' in parents"
                        );

                $results = $service->files->listFiles($optParams);

                if (count($results->getFiles()) != 0) {
                    foreach ($results->getFiles() as $file) {
                        if (strpos($file['name'], $nameFile)>-1)
                        {
                            $service->files->delete($file['id']);
                        }
                    }
                }

                
            }catch(Google_Service_Exception $gs){
                $m=json_decode($gs->getMessage());
                var_dump($m);
                echo $m->error->message;
            }catch(Exception $e){
                echo $e->getMessage();  
            }
        }
        return $valRet;
    }

    public static function poscCelda(int $fila, int $columna):string
    {
        $valRetorno = "";
        $arrayLabel = array("A","B","C","D","E","F","G","H","I","J","K","L","M","N","O","P","Q","R","S","T","U","V","W","X","Y","Z","AA","AB","AC","AD","AE","AF","AG","AH","AI","AJ","AK","AL","AM","AN","AO","AP","AQ","AR","AS","AT","AU","AV","AW","AX","AY","AZ");

        $valRetorno = $arrayLabel[$columna-1].strval($fila);
        return $valRetorno;
    }    
} 
