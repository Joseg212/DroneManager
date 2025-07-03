var dataTable = null;
$(document).ready(function(){
	if (!stop_list){
		listTipoMision();
	}

	$('form[name="frm_mtype"]').submit(function(event){

		event.preventDefault();
		var typeForm = $(this).data('type');

		var denominacion = String($("#denominacion").val());
		var descripcion = String($("#descripcion").val());


		if (denominacion.length<=0){
			$("#denominacion").focus()
			swal("Escriba el tipo de denominación!!");
			return false;
		}
		if (descripcion.length<=0){
			$("#descripcion").focus();
			swal("Describa el tipo de misión a emplear!!");
			return false;
		}
		if (typeForm=='n01'){
		}
		if (typeForm=='n11'){
		}

		$("form[name='frm_mtype']").unbind('submit').submit();
	});

	$(document).on("click",".delete_mtype",function(event){
		event.preventDefault()
		var idMType = $(this).parent().data('id');
		//pathdelete = returnPath('config_cia_delete');
	    swal({
	        title: '¿Desea eliminar el tipo de misión seleccionado?',
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
	            data:{'idTipom':idMType},
	            type:'post',
	            dataType:'json',
	            url: '/ajax_process/tipomision/borrar',
	            error: function(err,txt,thr){
	              $('body').html(err.responseText);
	            },
	            success: function(result){
	            	//console.log('Pasa por aquí');
	             	 if (result.ok=='01'){
	              		listTipoMision();
						$.toast({
							heading: 'Tipo de Misión Eliminado!!!',
							text: 'Proceso correcto!!',
							position: 'top-right',
							loaderBg:'#e6b034',
							icon: 'success',
							hideAfter: 3500, 
							stack: 6
						})

	              	} else {
	                	swal("Eliminar Tipo de Misión",result.msg,"error");
	              	}	
	            }

	          });
	        }
      	});

	});// fin function document

	$(document).on("click",".modify_mtype",function(event){
		event.preventDefault();
		var idMType = $(this).parent().data('id');

		window.location = '/tipomisiones/modificar/' + idMType;
		//console.log('/usuarios/modificar/' + idUser);
	}) // fin de update_user

	$(document).on("click",".btn-search-dm",function(event){
		event.preventDefault();
		listTipoMision();
	});
})
function listTipoMision() {
	var txtSearch = String($("#txtSearch").val());

	tabla2 = $('#tblTipoMision').dataTable({
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
				url:'/ajax_process/tipomision/listar',
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
				{data:"denominacion"},
				{data:"descripcion"},
				{data:"opcion"}
			],
			deferRender: true
	 });	


}		
