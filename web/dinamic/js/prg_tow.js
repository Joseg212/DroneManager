var dataTable = null;
$(document).ready(function(){
	if (!stop_list){
		listVehiculo();
	}

	$('form[name="frm_tow"]').submit(function(event){

		event.preventDefault();
		var typeForm = $(this).data('type');

		var modelo = String($("#modelo").val());
		var matricula = String($("#matricula").val());
		var responsable = String($("#responsable").val());


		if (modelo.length<=0){
			$("#modelo").focus()
			swal("Indique el modelo del vehículo!!");
			return false;
		}
		if (matricula.length<=0){
			$("#matricula").focus()
			swal("Indique la matricula del vehículo!!");
			return false;
		}
		if (responsable.length<=0){
			$("#responsable").focus();
			swal("Indique el responsable del vehiculo!!");
			return false;
		}
		if (typeForm=='n01'){
		}
		if (typeForm=='n11'){
		}

		$("form[name='frm_tow']").unbind('submit').submit();
	});

	$(document).on("click",".delete_tow",function(event){
		event.preventDefault()
		var idTow = $(this).parent().data('id');
		//pathdelete = returnPath('config_cia_delete');
	    swal({
	        title: '¿Esta seguro de eliminar el vehículo?',
	        text: "No podrás deshacer está acción.",
	        type: 'warning',
	        showCancelButton: true,
	        confirmButtonColor: '#3085d6',
	        cancelButtonColor: '#d33',
	        confirmButtonText: 'Si, Eliminar!'
	      }).then((result)=>{
	      	console.log(result);
	        if (result){
	          $.ajax({
	            data:{'idVehiculo':idTow},
	            type:'post',
	            dataType:'json',
	            url: '/ajax_process/vehiculo/borrar',
	            error: function(err,txt,thr){
	              $('body').html(err.responseText);
	            },
	            success: function(result){
	            	//console.log('Pasa por aquí');
	             	 if (result.ok=='01'){
	              		listVehiculo();
						$.toast({
							heading: 'Se eliminado el Vehículo ingresado!!!',
							text: 'Proceso correcto!!',
							position: 'top-right',
							loaderBg:'#e6b034',
							icon: 'success',
							hideAfter: 3500, 
							stack: 6
						})

	              	} else {
	                	swal("Eliminar Vehículo",result.msg,"error");
	              	}	
	            }

	          });
	        }
      	});

	});// fin function document

	$(document).on("click",".modify_tow",function(event){
		event.preventDefault();
		var idTow = $(this).parent().data('id');

		window.location = '/vehiculos/modificar/' + idTow;
		//console.log('/usuarios/modificar/' + idUser);
	}) // fin de update_user

	$(document).on("click",".btn-search-dm",function(event){
		event.preventDefault();
		listVehiculo();
	});
})
function listVehiculo() {
	var txtSearch = String($("#txtSearch").val());

	tabla2 = $('#tblVehiculo').dataTable({
			destroy:true,
			serverSide: true,
			searching: false,
			lengthChange: false,
			pageLength:15,
			dom:'<"#myPagins"rp>',
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
				data:{'txtSearch':txtSearch},
				method:"POST",
				dataType:"JSON",
				url:'/ajax_process/vehiculo/listar',
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

					console.log(err.responseText);

					///$('body').html(err.responseText);
				}, 
				complete:function(){
					 $("#newpPagins").html("");
					 $("#myPagins").prependTo("#newpPagins");
				}


			},
			columns:[
				{data:"id"},
				{data:"modelo"},
				{data:"matricula"},
				{data:"responsable"},
				{data:"opcion"}
			],
			deferRender: true
	 });	


}		
