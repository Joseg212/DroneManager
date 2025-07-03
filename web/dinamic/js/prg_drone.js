var dataTable = null;
$(document).ready(function(){
	if (!stop_list){
		listDrone();
	}

	$('form[name="frm_drone"]').submit(function(event){

		event.preventDefault();
		var typeForm = $(this).data('type');

		var modelo = String($("#modelo").val());
		var num_serie = String($("#num_serie").val());


		if (modelo.length<=0){
			$("#modelo").focus()
			swal("Indique el modelo del drone!!");
			return false;
		}
		if (num_serie.length<=0){
			$("#num_serie").focus()
			swal("Indique número de serie del drone!!");
			return false;
		}
		if (typeForm=='n01'){
		}
		if (typeForm=='n11'){
		}

		$("form[name='frm_drone']").unbind('submit').submit();
	});

	$(document).on("click",".delete_drone",function(event){
		event.preventDefault()
		var idDrone = $(this).parent().data('id');
		//pathdelete = returnPath('config_cia_delete');
	    swal({
	        title: '¿Esta seguro de eliminar este Drone?',
	        text: "No podrás deshacer está acción.",
	        type: 'warning',
	        showCancelButton: true,
	        confirmButtonColor: '#3085d6',
	        cancelButtonColor: '#d33',
	        confirmButtonText: 'Si, Eliminar!'
	      }).then((result)=>{
	        if (result){
	          $.ajax({
	            data:{'idDrone':idDrone},
	            type:'post',
	            dataType:'json',
	            url: '/ajax_process/drone/borrar',
	            error: function(err,txt,thr){
	              $('body').html(err.responseText);
	            },
	            success: function(result){
	            	//console.log('Pasa por aquí');
	             	 if (result.ok=='01'){
	              		listDrone();
						$.toast({
							heading: 'Se eliminado el Drone ingresado!!!',
							text: 'Proceso correcto!!',
							position: 'top-right',
							loaderBg:'#e6b034',
							icon: 'success',
							hideAfter: 3500, 
							stack: 6
						})

	              	} else {
	                	swal("Eliminar Drone",result.msg,"error");
	              	}	
	            }

	          });
	        }
      	});

	});// fin function document

	$(document).on("click",".modify_drone",function(event){
		event.preventDefault();
		var idDrone = $(this).parent().data('id');

		window.location = '/drones/modificar/' + idDrone;
	}) // fin  
	$(document).on("click",".btn-search-dm",function(event){
		event.preventDefault();
		listDrone();
	});
})
function listDrone() {
	var txtSearch = String($("#txtSearch").val());

	tabla2 = $('#tblDrone').dataTable({
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
				url:'/ajax_process/drone/listar',
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
					 $("#newpPagins").html("");
					 $("#myPagins").prependTo("#newpPagins");
				}


			},
			columns:[
				{data:"id"},
				{data:"modelo"},
				{data:"numserie"},
				{data:"opcion"}
			],
			deferRender: true
	 });	


}		
