<?php 

namespace DronesMaritime\Core\App;

use JasperPHP\JasperPHP;
use Defuse\Crypto\Crypto;
use Defuse\Crypto\Key;
use PHPExcel;
use PHPExcel_IOFactory;
use DronesMaritime\Core\Models\Mision;
use DronesMaritime\Core\Models\Pilotos;
use DronesMaritime\Core\Models\Archivos;
use DronesMaritime\Core\App\FunctionsApp;

final class ReportesApp extends Controller
{

	public function report_resultado()
	{
		$id_section = uniqid();


		echo $this->render('reporte/reporte_resultado.html.twig', ['id_section' => $id_section, 'msgSystem'=>'', 'action_system'=>'dont']);

	}
	public function report_rpt14()
	{
        // Crear el objeto JasperPHP

		/*$baseDir = dirname(__DIR__);
 		$baseDir = dirname($baseDir);

 		$tmp_id = 'c'.substr(uniqid(),1,10);


        $jasper = new JasperPHP($baseDir."\\dinamic\\fonts_type2");
        
        // Generar el Reporte
        $jasper->process($baseDir."\\reports\\prueba2.jasper",$baseDir."\\reports\\cache\\resultado_".$tmp_id,array('pdf'),['IdCodigo'=>'jose'],array('driver' => FS_DB_TYPE,'username' => FS_DB_USER,'password'=>FS_DB_PASS,'host' => FS_DB_HOST,'database' => FS_DB_NAME,'port' => FS_DB_PORT))->execute();

		ob_clean();

		header("Content-type:application/pdf");

		// It will be called downloaded.pdf
		header("Content-Disposition:attachment;filename=\"resultado_".$tmp_id.".pdf\"");

		// The PDF source is in original.pdf
		readfile($baseDir."\\reports\\cache\\resultado_".$tmp_id.".pdf");
		//echo $this->render('reporte/reporte_resultado.html.twig', ['id_section' => $id_section, 'msgSystem'=>'', 'action_system'=>'dont']);*/

	}
	public function report_rpt01()
	{
		$baseDir = dirname(__DIR__);
 		$baseDir = dirname($baseDir);

 		$tmp_id = 'c'.substr(uniqid(),1,10);

 		$rootDocExcel = $baseDir . '/excels/' . 'resultado_' .$tmp_id.'.xlsx' ;
 		try {

			extract($_POST);

			$fechaInicio = new \DateTime($fecha_inicio);
			$fechaFinal = new \DateTime($fecha_final);

			$fecha_inicio = $fechaInicio->format('Y/m/d');
			$fecha_final = $fechaFinal->format('Y/m/d');

			$condi = " m.fecha_inicio >= '$fecha_inicio' AND m.fecha_finalizado <= '$fecha_final' ";

			if (!empty($id_usuario))
			{
				// Se filtra por gestor 
				$condi .= " AND plan.id_usuario='$id_usuario' ";
			}
			if (!empty($id_cliente))
			{
				// Se filtra por gestor 
				$condi .= " AND m.id_cliente='$id_cliente' ";
			}

			$data = array();

			$mision = new Mision();

			$data['part_one'] = $mision->get_dataReport($condi);

			if ($data['part_one'][0]['id_mision']=='')
			{

				// se redirecciona a la pantalla anterior
				header("Location: /reportes/resultado");
				die();

			} else {
				//$inputFileType = PHPExcel_IOFactory::identify($rootDocExcel);
				//$objReader = PHPExcel_IOFactory::createReader($inputFileType);
				//$objPHPExcel = $objReader->load($rootDocExcel);

				$objPHPExcel = new PHPExcel();

				$objPHPExcel->createSheet();
				$objPHPExcel->createSheet();

				$objPHPExcel->setActiveSheetIndex(0);
				$objPHPExcel->getActiveSheet()->setTitle('Misiones Culminadas');

				$objPHPExcel->setActiveSheetIndex(1);
				$objPHPExcel->getActiveSheet()->setTitle('Pilotos Asignados');

				$objPHPExcel->setActiveSheetIndex(2);
				$objPHPExcel->getActiveSheet()->setTitle('Documentos subidos');

				$objPHPExcel->setActiveSheetIndex(0);
    			$objPHPExcel->getActiveSheet()->setCellValue(FunctionsApp::poscCelda(2,1), 'Identificación');
    			$objPHPExcel->getActiveSheet()->setCellValue(FunctionsApp::poscCelda(2,2), 'Ciudad');
    			$objPHPExcel->getActiveSheet()->setCellValue(FunctionsApp::poscCelda(2,3), 'Descripción');
				$objPHPExcel->getActiveSheet()->setCellValue(FunctionsApp::poscCelda(2,4), 'Objetivo');
				$objPHPExcel->getActiveSheet()->setCellValue(FunctionsApp::poscCelda(2,5), 'Coordenadas');
				$objPHPExcel->getActiveSheet()->setCellValue(FunctionsApp::poscCelda(2,6), 'Inicia');
				$objPHPExcel->getActiveSheet()->setCellValue(FunctionsApp::poscCelda(2,7), 'Termina');
				$objPHPExcel->getActiveSheet()->setCellValue(FunctionsApp::poscCelda(2,8), 'Finalizada');
				$objPHPExcel->getActiveSheet()->setCellValue(FunctionsApp::poscCelda(2,9), 'ID Cliente');
				$objPHPExcel->getActiveSheet()->setCellValue(FunctionsApp::poscCelda(2,10), 'Empresa');
				$objPHPExcel->getActiveSheet()->setCellValue(FunctionsApp::poscCelda(2,11), 'Rif/Nit');
				$objPHPExcel->getActiveSheet()->setCellValue(FunctionsApp::poscCelda(2,12), 'Contacto');
				$objPHPExcel->getActiveSheet()->setCellValue(FunctionsApp::poscCelda(2,13), 'Tipo Misión');
				$objPHPExcel->getActiveSheet()->setCellValue(FunctionsApp::poscCelda(2,14), 'Tipo Descrip.');
				$objPHPExcel->getActiveSheet()->setCellValue(FunctionsApp::poscCelda(2,15), 'Gestor');
				$objPHPExcel->getActiveSheet()->setCellValue(FunctionsApp::poscCelda(2,16), 'Vehículo');
				$objPHPExcel->getActiveSheet()->setCellValue(FunctionsApp::poscCelda(2,17), 'Matrícula');
				$objPHPExcel->getActiveSheet()->setCellValue(FunctionsApp::poscCelda(2,18), 'Planificación');
				$objPHPExcel->getActiveSheet()->setCellValue(FunctionsApp::poscCelda(2,19), 'Tipo Recopilación');
				$objPHPExcel->getActiveSheet()->setCellValue(FunctionsApp::poscCelda(2,20), 'Plan Tiempo Estimado');
				$objPHPExcel->getActiveSheet()->setCellValue(FunctionsApp::poscCelda(2,21), 'Observación final Gestor');
				$objPHPExcel->getActiveSheet()->setCellValue(FunctionsApp::poscCelda(2,22), 'Fecha Finalización Plan');
				$objPHPExcel->getActiveSheet()->setCellValue(FunctionsApp::poscCelda(2,23), 'Total horas pilotos');
				$objPHPExcel->getActiveSheet()->setCellValue(FunctionsApp::poscCelda(2,24), 'Promedio de horas');
				$objPHPExcel->getActiveSheet()->setCellValue(FunctionsApp::poscCelda(2,25), 'Hora máxima');
				$objPHPExcel->getActiveSheet()->setCellValue(FunctionsApp::poscCelda(2,26), 'Promedio Vuelos');

				$rango = FunctionsApp::poscCelda(2,1).':'.FunctionsApp::poscCelda(2,25);

				$styleArray = array(
    				'font'  => array(
        			'bold'  => true,
        			'color' => array('rgb' => '3D3D3D'),
        			'size'  => 10,
        			'name'  => 'Verdana'
    			));

    			$objPHPExcel->getActiveSheet()->getStyle($rango)->applyFromArray($styleArray);

    			$objPHPExcel->setActiveSheetIndex(1);

    			$objPHPExcel->getActiveSheet()->setCellValue(FunctionsApp::poscCelda(2,1), 'ID Misión');
    			$objPHPExcel->getActiveSheet()->setCellValue(FunctionsApp::poscCelda(2,2), 'ID Piloto');
    			$objPHPExcel->getActiveSheet()->setCellValue(FunctionsApp::poscCelda(2,3), 'Nombre');
    			$objPHPExcel->getActiveSheet()->setCellValue(FunctionsApp::poscCelda(2,4), 'Labor Asignada');
    			$objPHPExcel->getActiveSheet()->setCellValue(FunctionsApp::poscCelda(2,5), 'Tipo');
    			$objPHPExcel->getActiveSheet()->setCellValue(FunctionsApp::poscCelda(2,6), 'Drone');
    			$objPHPExcel->getActiveSheet()->setCellValue(FunctionsApp::poscCelda(2,7), 'Nro. Vuelos');
    			$objPHPExcel->getActiveSheet()->setCellValue(FunctionsApp::poscCelda(2,8), 'Total Horas');
    			$objPHPExcel->getActiveSheet()->setCellValue(FunctionsApp::poscCelda(2,9), 'Finalizado');
    			$objPHPExcel->getActiveSheet()->setCellValue(FunctionsApp::poscCelda(2,10), 'Observación');

    			$objPHPExcel->getActiveSheet()->getStyle($rango)->applyFromArray($styleArray);

    			$objPHPExcel->setActiveSheetIndex(2);

    			$objPHPExcel->getActiveSheet()->setCellValue(FunctionsApp::poscCelda(2,1), 'ID Misión');
    			$objPHPExcel->getActiveSheet()->setCellValue(FunctionsApp::poscCelda(2,2), 'Nombre Archivo');
    			$objPHPExcel->getActiveSheet()->setCellValue(FunctionsApp::poscCelda(2,3), 'Tipo');
    			$objPHPExcel->getActiveSheet()->setCellValue(FunctionsApp::poscCelda(2,4), 'Peso');
    			$objPHPExcel->getActiveSheet()->setCellValue(FunctionsApp::poscCelda(2,5), 'Archivo Original');
    			$objPHPExcel->getActiveSheet()->setCellValue(FunctionsApp::poscCelda(2,6), 'Extensión');
    			$objPHPExcel->getActiveSheet()->setCellValue(FunctionsApp::poscCelda(2,7), 'Descripción');
    			$objPHPExcel->getActiveSheet()->setCellValue(FunctionsApp::poscCelda(2,8), 'Subido por');
    			$objPHPExcel->getActiveSheet()->setCellValue(FunctionsApp::poscCelda(2,9), 'Fecha de Subida');

    			$objPHPExcel->getActiveSheet()->getStyle($rango)->applyFromArray($styleArray);

				$objPHPExcel->setActiveSheetIndex(0);


    			$poscF = 3;
    			$poscF_P = 3;
    			$poscF_A = 3;
				$pilotos = new Pilotos();
				$archivos = new Archivos();

				$total_hrs = 0;
				$cantidad_p=0;
				$hora_max = '';
				$total_vuelos=0;
    			foreach ($data['part_one'] as $fila) {
    				
	    			$objPHPExcel->getActiveSheet()->setCellValue(FunctionsApp::poscCelda($poscF,1), $fila['id_mision']);
	    			$objPHPExcel->getActiveSheet()->setCellValue(FunctionsApp::poscCelda($poscF,2), $fila['ciudad']);
	    			$objPHPExcel->getActiveSheet()->setCellValue(FunctionsApp::poscCelda($poscF,3), $fila['descrip']);
					$objPHPExcel->getActiveSheet()->setCellValue(FunctionsApp::poscCelda($poscF,4), $fila['objetivo']);
					$objPHPExcel->getActiveSheet()->setCellValue(FunctionsApp::poscCelda($poscF,5), $fila['coord']);
					$objPHPExcel->getActiveSheet()->setCellValue(FunctionsApp::poscCelda($poscF,6), $fila['fecha_inicio']);
					$objPHPExcel->getActiveSheet()->setCellValue(FunctionsApp::poscCelda($poscF,7), $fila['fecha_final']);
					$objPHPExcel->getActiveSheet()->setCellValue(FunctionsApp::poscCelda($poscF,8), $fila['fecha_finalizado']);
					$objPHPExcel->getActiveSheet()->setCellValue(FunctionsApp::poscCelda($poscF,9), $fila['id_cliente']);
					$objPHPExcel->getActiveSheet()->setCellValue(FunctionsApp::poscCelda($poscF,10), $fila['compania']);
					$objPHPExcel->getActiveSheet()->setCellValue(FunctionsApp::poscCelda($poscF,11), $fila['rif_nit']);
					$objPHPExcel->getActiveSheet()->setCellValue(FunctionsApp::poscCelda($poscF,12), $fila['contacto']);
					$objPHPExcel->getActiveSheet()->setCellValue(FunctionsApp::poscCelda($poscF,13), $fila['tipo_mision']);
					$objPHPExcel->getActiveSheet()->setCellValue(FunctionsApp::poscCelda($poscF,14), $fila['tipo_descrip']);
					$objPHPExcel->getActiveSheet()->setCellValue(FunctionsApp::poscCelda($poscF,15), $fila['nombr_gestor']);
					$objPHPExcel->getActiveSheet()->setCellValue(FunctionsApp::poscCelda($poscF,16), $fila['mod_vehiculo']);
					$objPHPExcel->getActiveSheet()->setCellValue(FunctionsApp::poscCelda($poscF,17), $fila['matr_vehiculo']);
					$objPHPExcel->getActiveSheet()->setCellValue(FunctionsApp::poscCelda($poscF,18), $fila['plan_descrip']);
					$objPHPExcel->getActiveSheet()->setCellValue(FunctionsApp::poscCelda($poscF,19), $fila['plan_trecop']);
					$objPHPExcel->getActiveSheet()->setCellValue(FunctionsApp::poscCelda($poscF,20), $fila['plan_timeh']);
					$objPHPExcel->getActiveSheet()->setCellValue(FunctionsApp::poscCelda($poscF,21), $fila['plan_observf']);
					$objPHPExcel->getActiveSheet()->setCellValue(FunctionsApp::poscCelda($poscF,22), $fila['plan_finalizado']);

					$data['pilotos'] = $pilotos->listItemsPilotAll($fila['id_plan']);

					$objPHPExcel->setActiveSheetIndex(1);
					foreach ($data['pilotos'] as $piloto) {
		    			$objPHPExcel->getActiveSheet()->setCellValue(FunctionsApp::poscCelda($poscF_P,1), $fila['id_mision']);
		    			$objPHPExcel->getActiveSheet()->setCellValue(FunctionsApp::poscCelda($poscF_P,2), $piloto['id_usuario']);
		    			$objPHPExcel->getActiveSheet()->setCellValue(FunctionsApp::poscCelda($poscF_P,3), $piloto['nombres']);
		    			$objPHPExcel->getActiveSheet()->setCellValue(FunctionsApp::poscCelda($poscF_P,4), $piloto['tipo']);
		    			$objPHPExcel->getActiveSheet()->setCellValue(FunctionsApp::poscCelda($poscF_P,5), $piloto['labor']);
		    			$objPHPExcel->getActiveSheet()->setCellValue(FunctionsApp::poscCelda($poscF_P,6), $piloto['modelo']);
		    			$objPHPExcel->getActiveSheet()->setCellValue(FunctionsApp::poscCelda($poscF_P,7), $piloto['num_vuelo']);
		    			$objPHPExcel->getActiveSheet()->setCellValue(FunctionsApp::poscCelda($poscF_P,8), $piloto['total_hrs']);
		    			$objPHPExcel->getActiveSheet()->setCellValue(FunctionsApp::poscCelda($poscF_P,9), $piloto['hora_final']);
		    			$objPHPExcel->getActiveSheet()->setCellValue(FunctionsApp::poscCelda($poscF_P,10), $piloto['observ_final']);

		    			$total_hrs +=$piloto['total_hrs'];
		    			$cantidad_p++;
		    			$hora_max = max($hora_max,$piloto['hora_final']);
		    			$total_vuelos +=$piloto['num_vuelo'];
		    			$poscF_P++;
					}
					$objPHPExcel->setActiveSheetIndex(2);
					$data['archivos'] = $archivos->get_dataFiles($fila['id_mision']);

					foreach ($data['archivos'] as $file) {
		    			$objPHPExcel->getActiveSheet()->setCellValue(FunctionsApp::poscCelda($poscF_A,1), $fila['id_mision']);
		    			$objPHPExcel->getActiveSheet()->setCellValue(FunctionsApp::poscCelda($poscF_A,2), $file['name_file']);
		    			$objPHPExcel->getActiveSheet()->setCellValue(FunctionsApp::poscCelda($poscF_A,3), $file['tipo_archivo']);
		    			$objPHPExcel->getActiveSheet()->setCellValue(FunctionsApp::poscCelda($poscF_A,4), $file['tamanio'].' Mb');
		    			$objPHPExcel->getActiveSheet()->setCellValue(FunctionsApp::poscCelda($poscF_A,5), $file['arch_orig']);
		    			$objPHPExcel->getActiveSheet()->setCellValue(FunctionsApp::poscCelda($poscF_A,6), $file['extension']);
		    			$objPHPExcel->getActiveSheet()->setCellValue(FunctionsApp::poscCelda($poscF_A,7), $file['descrip_arch']);
		    			$objPHPExcel->getActiveSheet()->setCellValue(FunctionsApp::poscCelda($poscF_A,8), $file['nombres']);
		    			$objPHPExcel->getActiveSheet()->setCellValue(FunctionsApp::poscCelda($poscF_A,9), $file['fecha_subida']);

						$poscF_A++;						
					}
					

					$objPHPExcel->setActiveSheetIndex(0);


					// resumen de data
					$objPHPExcel->getActiveSheet()->setCellValue(FunctionsApp::poscCelda($poscF,23), strval($total_hrs));
					$objPHPExcel->getActiveSheet()->setCellValue(FunctionsApp::poscCelda($poscF,24), strval(round(($total_hrs/$cantidad_p),0)));
					$objPHPExcel->getActiveSheet()->setCellValue(FunctionsApp::poscCelda($poscF,25), $hora_max);    
					$objPHPExcel->getActiveSheet()->setCellValue(FunctionsApp::poscCelda($poscF,26), strval(round(($total_vuelos/$cantidad_p),0)));
		

					$objPHPExcel->getActiveSheet()->getRowDimension($poscF)->setRowHeight(20);
					$poscF++;
    			}

    			//$objPHPExcel->getActiveSheet()->mergeCells(FunctionsApp::poscCelda(2,1).':'.FunctionsApp::poscCelda($poscF,25));

				$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');

				$objWriter->save($rootDocExcel);

				ob_clean();

		        header('Content-Description: File Transfer');
		        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
				header("Content-Disposition:attachment;filename=\"resultado_".$tmp_id.".xlsx\"");
		        header('Content-Transfer-Encoding: binary');
		        header('Expires: 0');
		        header('Cache-Control: must-revalidate');
		        header('Pragma: public');
		        header('Content-Length: ' . filesize($rootDocExcel));
				flush();
				readfile($rootDocExcel);
			}

		} catch (Exception $err) {
			throw new \Exception("Erro en procesamiento del excel:".$err->getMessage(), 1);
		}
	}

}