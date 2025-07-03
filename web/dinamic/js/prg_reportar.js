var dt_ItemsPilots = null;
$(document).ready(function(){
	lst_ItemsPilots();

	lst_ItemsFiles();

	$(document).on("click",".reportar_piloto",function(event){
		event.preventDefault();
		var idPiloto = $(this).parent().data('id');
		var idMision = $(this).parent().data('mision');
		window.location = "/reportar/piloto/"+idPiloto+"_"+idMision;
	});

	$('form[name="frm_pilotoReporte"]').submit(function(event){

		event.preventDefault();
		var typeForm = $(this).data('type');

		var num_vuelo = parseInt($("#num_vuelo").val());
		var total_hrs = parseInt($("#total_hrs").val());
		var hora_final = String($("#hora_final").val());
		var observ_final = String($("#observ_final").val());


		if (num_vuelo<=0){
			$("#num_vuelo").focus()
			swal("Indique la cantidad de vuelos!!");
			return false;
		}
		if (total_hrs<=0){
			$("#total_hrs").focus()
			swal("Escriba el total de horas voladas!!");
			return false;
		}
		if (hora_final.length<=0){
			$("#hora_final").focus()
			swal("Indique la hora de culminación!!");
			return false;
		}
		if (observ_final.length<=0){
			$("#observ_final").focus()
			swal("Escriba la Observación sobre el trabajo!!");
			return false;
		}
		if (typeForm=='n01'){
		}
		if (typeForm=='n11'){
		}

		$("form[name='frm_pilotoReporte']").unbind('submit').submit();
	});

	$('form[name="frm_docDriver"]').submit(function(event){

		event.preventDefault();
		var typeForm = $(this).data('type');

		var name_file = String($("#name_file").val());
		var fileName = String($("#fileName").val());
		var descrip_arch = String($("#descrip_arch").val());
		var tipo_archivo = String($("#tipo_archivo").val());


		if (name_file.length<=0){
			$("#name_file").focus()
			swal("Escriba el nombre del documento o archivo!!");
			return false;
		}
		if (fileName.length<=0){
			swal("Por favor seleccione un archivo a subir!!");
			return false;
		}
		if (descrip_arch.length<=0){
			$("#descrip_arch").focus()
			swal("Escriba una información referente al archivo!!");
			return false;
		}
		if (tipo_archivo.length<=0){
			$("#tipo_archivo").focus()
			swal("Indique que tipo de documento esta subiendo!!");
			return false;
		}


		$("form[name='frm_docDriver']").unbind('submit').submit();
	});	

	$("#searchFileGDriver").on("click",function(event){
		event.preventDefault();
		$("#file_document").click();
	});
	// Registra la observación final GESTOR
	$("#saveInfo").click(function(event){
		event.preventDefault();

		var token_id  = String($("#token_id").val());
		var fecha_final = String($("#fecha_final").val());
		var observg_final = String($("#observg_final").val());
		var idMision = $("#idMision").val();

		if (fecha_final.length<=0)
		{
			$("#fecha_final").focus();
			swal("Indique la fecha en que termino el trabajo!!");
			return false;		
		}
		if (observg_final.length<=0)
		{
			$("#observg_final").focus();
			swal("Escriba la observación general del trabajo!!");
			return false;		
		}

		$.ajax({
			data:{'token_id':token_id,'fecha_final':fecha_final,'observg_final':observg_final,'idMision':idMision},
			url: '/ajax_process/reportar/gestor_reporte',
			type:'post',
			dataType:'json',
			error: function(err,txt,thr){
				console.log(err.responseText);
			},
			success: function(result){
				if (result.ok=='01'){
					$.toast({
						heading: 'Se ha registrado su reporte correctamente!!!',
						text: 'Reportar Misión!!',
						position: 'top-right',
						loaderBg:'#e6b034',
						icon: 'success',
						hideAfter: 3500, 
						stack: 6
					})
				} else{
                	swal("Reportar Misión Error",result.msg,"error");
				}
			},
		})

	});


	$(document).on("click",".delete_archivo",function(event){
		event.preventDefault()
		var idArchivo = $(this).parent().data('id');
		var FileId = $(this).parent().data('fileid');
		//pathdelete = returnPath('config_cia_delete');
	    swal({
	        title: '¿Esta seguro de eliminar el archivo en google drive?',
	        text: "No podrás deshacer está acción.",
	        type: 'warning',
	        showCancelButton: true,
	        confirmButtonColor: '#3085d6',
	        cancelButtonColor: '#d33',
	        confirmButtonText: 'Si, Eliminar!'
	      }).then((result)=>{
	        if (result){
	          $.ajax({
	            data:{'idArchivo':idArchivo,'FileId':FileId},
	            type:'post',
	            dataType:'json',
	            url: '/ajax_process/reportar/archivoBorrar',
	            error: function(err,txt,thr){
	              $('body').html(err.responseText);
	            },
	            success: function(result){
	            	//console.log('Pasa por aquí');
	             	 if (result.ok=='01'){
	              		lst_ItemsFiles();
						$.toast({
							heading: 'Se retiro el archivo desde Google Drive!!!',
							text: 'Proceso correcto!!',
							position: 'top-right',
							loaderBg:'#e6b034',
							icon: 'success',
							hideAfter: 3500, 
							stack: 6
						})

	              	} else {
	                	swal("Eliminar Archivo",result.msg,"error");
	              	}	
	              	console.log("Result "+result.resultDrive);
	            }

	          });
	        }
      	});

	});// fin eliminar archivo


}); // fin de document ready


function lst_ItemsPilots() {
	var txtSearch = "";
	var idMision = $("#idMision").val();

	dt_ItemsPilots = $('#tblItemsPilots').dataTable({
			destroy:true,
			serverSide: true,
			searching: false,
			lengthChange: false,
			pageLength:8,
			dom:'<"#myPaginsPilots"rp>',
			language:{
				info:"_PAGE_/_PAGES_ _MAX_",
				infoEmpty:"1/1 1", 
				infoFiltered:"",
				paginate:{
					first:"|<",
					previous:"<<",
					next:">>",
					last:">|"
				}
			},
			ajax:{
				data:{'txtSearch':txtSearch,'id_plan':idPlanG, 'idMision':idMision},
				method:"POST",
				dataType:"JSON",
				url:'/ajax_process/reportar/itemsPilotos',
				error: function(err, txt,thr){
					//console.log(err.responseText);
					$.toast({
						heading: 'DataTable',
						text: 'Error al intentar recuperar la lista de datos',
						position: 'top-right',
						loaderBg:'#e6b034',
						icon: 'error',
						hideAfter: 3500, 
						stack: 6
					});

					///$('body').html(err.responseText);
				}, 
				complete:function(){
					 $("#newpPaginsPilots").html("");
					 $("#myPaginsPilots").prependTo("#newpPaginsPilots");
				}


			},
			columns:[
				{data:"id"},
				{data:"nombres"},
				{data:"tipo"},
				{data:"labor"},
				{data:"opcion"},
				{data:"estado"}
			],
			deferRender: true
	 });	


}		

dt_itemsArchivos = null;
function lst_ItemsFiles() {
	var txtSearch = "";
	var idMision = $("#idMision").val();

	dt_ItemsArchivos = $('#tblItemsFiles').dataTable({
			destroy:true,
			serverSide: true,
			searching: false,
			lengthChange: false,
			pageLength:8,
			dom:'<"#myPaginsPilots"rp>',
			language:{
				info:"_PAGE_/_PAGES_ _MAX_",
				infoEmpty:"1/1 1", 
				infoFiltered:"",
				paginate:{
					first:"|<",
					previous:"<<",
					next:">>",
					last:">|"
				}
			},
			ajax:{
				data:{'txtSearch':txtSearch,'idMision':idMision},
				method:"POST",
				dataType:"JSON",
				url:'/ajax_process/reportar/itemsArchivos',
				error: function(err, txt,thr){
					//console.log(err.responseText);
					$.toast({
						heading: 'DataTable',
						text: 'Error al intentar recuperar la lista de datos',
						position: 'top-right',
						loaderBg:'#e6b034',
						icon: 'error',
						hideAfter: 3500, 
						stack: 6
					});

					///$('body').html(err.responseText);
				}, 
				complete:function(){
					 $("#newpPaginsPilots").html("");
					 $("#myPaginsPilots").prependTo("#newpPaginsPilots");
				}


			},
			columns:[
				{data:"archivo"},
				{data:"descrip"},
				{data:"tipo"},
				{data:"tamanio"},
				{data:"opcion"},
			],
			deferRender: true
	 });	
}		
