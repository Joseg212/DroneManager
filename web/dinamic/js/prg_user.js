var dataTable = null;
$(document).ready(function(){
	console.log("Pasa por aquí....");
	if (!stop_list){
		listUsuarios();
	}

	$('form[name="frm_user"]').submit(function(event){
		event.preventDefault();
		var typeForm = $(this).data('type');
		var nombres = String($("#nombres").val());
		var telfm = String($("#telfm").val());
		var email = String($("#email").val());
		var cargo = String($("#cargo").val());
		var userName = String($("#userName").val());
		var userPassw = String($("#userPassw").val());
		var userPassw2 = String($("#userPassw2").val());

		var roll = String($("#roll option:selected" ).text());

		console.log(roll);

		if (nombres.length<=0){
			$("#nombres").focus()
			swal("Ingrese el nombre del usuario!!");
			return false;
		}
		if (email.length<=0){
			$("#email").focus();
			swal("El correo electrónico es obligatorio!!");
			return false;
		}
		if (telfm.length<=0){
			$("#telfm").focus();
			swal("Ingrese el telefono del usuario");
			return false;
		}
		if (roll=="Escoger"){
			swal("No puede ser nulo el roll de usuario!!");
			return false;
		}

		if (userName.length<=0)
		{
			$("#userName").focus();
			swal("Indique el nombre de usuario o alias!!");
			return false;
		}
		if (typeForm=='n01'){
			if (userPassw.length<=0)
			{
				$("#userPassw").focus();
				swal("Escriba la contraseña a validar!!");
				return false;
			}
			if (userPassw2.length<=0)
			{
				$("#userPassw2").focus();
				swal("Es necesario comprobar contraseña!!");
				return false;
			}
			if (userPassw!=userPassw2)
			{
				$("#userPassw").focus();
				swal("Contraseña de Verficación no son iguales");
				return false;
			}
		}
		if (typeForm=='n11'){
			if (userPassw.length>0)
			{
				if (userPassw2.length<=0)
				{
					$("#userPassw2").focus();
					swal("Es necesario comprobar contraseña!!");
					return false;
				}
				if (userPassw!=userPassw2)
				{
					$("#userPassw").focus();
					swal("Contraseña de Verficación no son iguales");
					return false;
				}
			}
	
		}


		$("form[name='frm_user']").unbind('submit').submit();
	});

	$(document).on("click",".delete_user",function(event){
		event.preventDefault()
		var idUser = $(this).parent().data('id');
		//pathdelete = returnPath('config_cia_delete');
	    swal({
	        title: '¿Esta seguro de eliminar el usuario?',
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
	            data:{'idUser':idUser},
	            type:'post',
	            dataType:'json',
	            url: '/ajax_process/usuario/borrar',
	            error: function(err,txt,thr){
	              $('body').html(err.responseText);
	            },
	            success: function(result){
	            	console.log('Pasa por aquí');
	             	 if (result.ok=='01'){
	              		listUsuarios();
						$.toast({
							heading: 'Usuario Eliminado!!!',
							text: 'Proceso Correcto!!',
							position: 'top-right',
							loaderBg:'#e6b034',
							icon: 'success',
							hideAfter: 3500, 
							stack: 6
						})

	              	} else {
	                	swal("Eliminar Usuario",result.msg,"error");
	              	}	
	            }

	          });
	        }
      	});

	});// fin function document

	$(document).on("click",".modify_user",function(event){
		event.preventDefault();
		var idUser = $(this).parent().data('id');

		window.location = '/usuarios/modificar/' + idUser;
		//console.log('/usuarios/modificar/' + idUser);
	}); // fin de update_user

	$(document).on("click",".btn-search-dm",function(event){
		event.preventDefault();
		listUsuarios();
	});

}) // fin de document ready
function listUsuarios() {
	var txtSearch = String($("#txtSearch").val());


	tabla2 = $('#tblUsuarios').dataTable({
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
				url:'/ajax_process/usuario/listar',
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
				{data:"nombres"},
				{data:"usuario"},
				{data:"roll"},
				{data:"opcion"}
			],
			deferRender: true
	 });	


}		
