var dt_ConsultMissions = null;

$(document).ready(function(){
	if (!stop_list){
		listaMisiones();
	}

	$("#search_client").click(function(event){
		event.preventDefault();
		$("#win-modal-01").fadeIn("slow",function(e){
			$("#win-modal-01").css("display","block");
		});

		listCliente();

	});	

	$("#search_user").click(function(event){
		event.preventDefault();
		$("#win-modal-02").fadeIn("slow",function(e){
			$("#win-modal-02").css("display","block");
		});
		type_list = 'Gestor';
		listUsuario();

	});


	$('#tblCliente').on('click','tr',function() {
	    var data = tablaClient.fnGetData(this);
	   	$("#win-modal-01").fadeOut("slow",function(e){
			$("#win-modal-01").css("display","none");
		});

		$("#id_cliente").val(data.midata.id_cliente);
		$("#id_cliente").parent().attr("class","form-group label-floating");
		
		$("#compania").val(data.midata.compania);
		$("#compania").parent().attr("class","form-group label-floating");


		$.material.init();
	});

	$('#tblUsuario').on('click','tr',function() {
	    var data = dt_User.fnGetData(this);
	   	$("#win-modal-02").fadeOut("slow",function(e){
			$("#win-modal-02").css("display","none");
		});

		$("#id_usuario").val(data.midata.id_usuario);
		$("#id_usuario").parent().attr("class","form-group label-floating");
		
		$("#nombres").val(data.midata.nombres);
		$("#nombres").parent().attr("class","form-group label-floating");

		$.material.init();
	});	

	$(document).on("click",".dm-close-all-01",function(event){
		event.preventDefault();

	   	$("#win-modal-01").fadeOut("slow",function(e){
			$("#win-modal-01").css("display","none");
		});

		$("#id_cliente").val("");
		$("#id_cliente").parent().attr("class","form-group label-floating is-empty");
		
		$("#compania").val("");
		$("#compania").parent().attr("class","form-group label-floating is-empty");


		$.material.init();		
	});

	$(document).on("click",".dm-close-all-02",function(event){
		event.preventDefault();

	   	$("#win-modal-02").fadeOut("slow",function(e){
			$("#win-modal-02").css("display","none");
		});

		$("#id_usuario").val("");
		$("#id_usuario").parent().attr("class","form-group label-floating is-empty");
		
		$("#nombres").val("");
		$("#nombres").parent().attr("class","form-group label-floating is-empty");


		$.material.init();		
	});

	$("#processConsult").click(function(event){
		event.preventDefault();
		listaMisiones();
	});
	$(document).on("click",".view_detail",function(event){
		event.preventDefault();
		var idDrone = $(this).parent().data('id');

		window.location = '/consultas/ver/' + idDrone;
	}) // fin  	
});// Fin de document ready

function listaMisiones() {

	//var txtSearch = String($("#txtSearch").val());
	var txtSearch = "";

	var fecha_inicio = String($("#fecha_inicio").val());
	var fecha_final = String($("#fecha_final").val());
	var estatus = String($("#estatus").val());
	var id_cliente = String($("#id_cliente").val());
	var id_usuario = String($("#id_usuario").val());

	//console.log("estatus" + estatus);


	dt_ConsultMissions = $('#tblListaMisiones').dataTable({
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
				data:{'txtSearch':txtSearch,'fecha_inicio':fecha_inicio,'fecha_final':fecha_final,'estatus':estatus,'id_cliente':id_cliente,'id_usuario':id_usuario},
				method:"POST",
				dataType:"JSON",
				url:'/ajax_process/consulta/listar',
				error: function(err, txt,thr){
					console.log(err.responseText);
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
				{data:"mision"},
				{data:"compania"},
				{data:"opcion"}
			],
			deferRender: true
	 });	


}		

var tablaClient=null;

function listCliente() {
	var txtSearch = String($("#txtSearchClient").val());

	if ($(window).width()>700){
		pageLength_long = 5;
	} else {
		pageLength_long = 3;
	}


	tablaClient = $('#tblCliente').dataTable({
			destroy:true,
			serverSide: true,
			searching: false,
			lengthChange: false,
			pageLength:pageLength_long,
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
				url:'/ajax_process/cliente/searchList',
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
					 $("#newpPaginsClient").html("");
					 $("#myPagins").prependTo("#newpPaginsClient");

				}

			},
			columns:[
				{data:"id_cliente"},
				{data:"compania"}
			],
			deferRender: true
	});	
}

var dt_User = null;
var type_list = 'Gestor';

function listUsuario() {
	var txtSearch = String($("#txtSearchUser").val());

	dt_User = $('#tblUsuario').dataTable({
			destroy:true,
			serverSide: true,
			searching: false,
			lengthChange: false,
			pageLength:15,
			dom:'<"#myPaginsUser"rp>',
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
				data:{'txtSearch':txtSearch, 'type_list':type_list},
				method:"POST",
				dataType:"JSON",
				url:'/ajax_process/usuario/searchList',
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
					 $("#newpPaginsUser").html("");
					 $("#myPaginsUser").prependTo("#newpPaginsUser");
				}


			},
			columns:[
				{data:"id"},
				{data:"nombres"},
				{data:"usuario"}
			],
			deferRender: true
	 });	


}	
