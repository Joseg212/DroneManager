var dataTable = null;
$(document).ready(function(){
	if (!stop_list){
		listCliente();
	}

	$('form[name="frm_client"]').submit(function(event){
		event.preventDefault();
		var typeForm = $(this).data('type');
		var compania = String($("#compania").val());
		var email = String($("#email").val());
		var direc = String($("#direccion").val());
		var rif_nit = String($("#rif_nit").val());
		var telf_cia = String($("#telf_cia").val());
		var contacto = String($("#contacto").val());
		var telf_contact = String($("#telf_contact").val());


		if (compania.length<=0){
			$("#compania").focus()
			swal("Ingrese el nombre de la compañia!!");
			return false;
		}
		if (email.length<=0){
			$("#email").focus();
			swal("El correo electrónico es obligatorio!!");
			return false;
		}
		if (direc.length<=0){
			$("#telfm").focus();
			swal("Ingrese la direccion de la compañia");
			return false;
		}
		if (rif_nit.length<=0){
			swal("Escriba el rif o nit de la compañia!!");
			return false;
		}

		if (telf_cia.length<=0)
		{
			$("#telf_cia").focus();
			swal("Ingrese el teléfono de la compañia!!");
			return false;
		}
		if (contacto.length<=0)
		{
			$("#contacto").focus();
			swal("Falta el nombre del contacto!!");
			return false;
		}
		if (telf_contact.length<=0)
		{
			$("#telf_contact").focus();
			swal("Teléfono del contacto no puede ser blanco!!");
			return false;
		}
		if (typeForm=='n01'){
		}
		if (typeForm=='n11'){
		}


		$("form[name='frm_client']").unbind('submit').submit();
	});

	$(document).on("click",".delete_client",function(event){
		event.preventDefault()
		var idClient = $(this).parent().data('id');
		//pathdelete = returnPath('config_cia_delete');
	    swal({
	        title: '¿Esta seguro de eliminar el cliente?',
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
	            data:{'idClient':idClient},
	            type:'post',
	            dataType:'json',
	            url: '/ajax_process/cliente/borrar',
	            error: function(err,txt,thr){
	              $('body').html(err.responseText);
	            },
	            success: function(result){
	            	console.log('Pasa por aquí');
	             	 if (result.ok=='01'){
	              		listCliente();
						$.toast({
							heading: 'Cliente Eliminado!!!',
							text: 'Proceso correcto!!',
							position: 'top-right',
							loaderBg:'#e6b034',
							icon: 'success',
							hideAfter: 3500, 
							stack: 6
						})

	              	} else {
	                	swal("Eliminar Cliente",result.msg,"error");
	              	}	
	            }

	          });
	        }
      	});

	});// fin function document

	$(document).on("click",".modify_client",function(event){
		event.preventDefault();
		var idUser = $(this).parent().data('id');

		window.location = '/clientes/modificar/' + idUser;
		//console.log('/usuarios/modificar/' + idUser);
	}) // fin de update_user
	$(document).on("click",".btn-search-dm",function(event){
		event.preventDefault();
		listCliente();
	});
})
function listCliente() {
	var txtSearch = String($("#txtSearch").val());

	tabla2 = $('#tblCliente').dataTable({
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
				url:'/ajax_process/cliente/listar',
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
				{data:"compania"},
				{data:"contacto"},
				{data:"email"},
				{data:"opcion"}
			],
			deferRender: true
	 });	


}		
