var dataTable = null;

$(document).ready(function(){
	if (!stop_list){
		listMission();
	}

	if ($("#link_new").length){

		$("#link_new").prependTo("#arco-superior");
	}
	//var date = new Date(2019,1,1); 
	//$("#fecha_inicio").datepicker("setDate",date);

	$("#search_client").click(function(event){
		event.preventDefault();
		$("#win-modal-01").fadeIn("slow",function(e){
			$("#win-modal-01").css("display","block");
		});

		listCliente();

	});
	$("#search_tipom").click(function(event){
		event.preventDefault();
		$("#win-modal-02").fadeIn("slow",function(e){
			$("#win-modal-02").css("display","block");
		});

		listTipoMision();
	});

	$("#search_tow").click(function(event){
		event.preventDefault();
		$("#win-modal-01").fadeIn("slow",function(e){
			$("#win-modal-01").css("display","block");
		});

		listVehiculo();

	});
	$("#search_gestor").click(function(event){
		event.preventDefault();
		$("#win-modal-02").fadeIn("slow",function(e){
			$("#win-modal-02").css("display","block");
		});
		type_list = 'Gestor';
		listUsuario();

	});

	$("#search_piloto").click(function(event){
		event.preventDefault();
		$("#win-modal-02").fadeIn("slow",function(e){
			$("#win-modal-02").css("display","block");
		});
		type_list = 'Piloto';
		listUsuario();

	});

	$("#search_drone").click(function(event){
		event.preventDefault();
		$("#win-modal-03").fadeIn("slow",function(e){
			$("#win-modal-03").css("display","block");
		});
		listDrone();

	});


	$(document).on("click",".btn-search-dm-client",function(event){
		event.preventDefault();
		listCliente();
	});	

	$('form[name="frm_mission"]').submit(function(event){
		event.preventDefault();
		var typeForm = $(this).data('type');

		var id_cliente = String($("#id_cliente").val());
		var id_tipom = String($("#id_tipom").val());
		var ciudad = String($("#ciudad").val());
		var descrip = String($("#descrip").val());
		var objetivo = String($("#objetivo").val());
		var rango_fecha = String($("#rango_fecha").val());
		var coord = String($("#coord").val());


		if (id_cliente.length<=0){
			$("#id_cliente").focus()
			swal("Debe seleccionar el cliente!!");
			return false;
		}
		if (id_tipom<=0){
			$("#id_tipom").focus();
			swal("Debe seleccionar el tipo de misión!!");
			return false;
		}
		if (descrip.length<=0){
			$("#descrip").focus();
			swal("Ingrese una breve descripción de la misión!!");
			return false;
		}
		if (objetivo.length<=0){
			swal("Escriba el objetivo general de la misión!!");
			return false;
		}

		if (rango_fecha.length<=0)
		{
			$("#rango_fecha").focus();
			swal("El rango de fechas no puede ser blanco!!");
			return false;
		}
		if (ciudad.length<=0)
		{
			$("#ciudad").focus();
			swal("Escriba en que ciudad será misión!!");
			return false;
		}
		if (coord.length<=0)
		{
			$("#coord").focus();
			swal("Indique las coordenadas de la misión!!");
			return false;
		}
		if (typeForm=='n01'){
		}
		if (typeForm=='n11'){
		}


		$("form[name='frm_mission']").unbind('submit').submit();
	});


	$('form[name="frm_plainMission"]').submit(function(event){
		event.preventDefault();
		var typeForm = $(this).data('type');
		var tmp_id = String($("#tmp_id").val());

		var id_vehiculo = String($("#id_vehiculo").val());
		var id_usuario = String($("#id_usuario").val());
		var fecha_comienzo = String($("#fecha_comienzo").val());
		var hora_comienzo = String($("#hora_comienzo").val());
		var tiempo_hrs = String($("#tiempo_hrs").val());
		var tipo_recop = String($("#tipo_recop").val());
		var descrip = String($("#descrip").val());


		if (id_vehiculo.length<=0){
			$("#id_vehiculo").focus()
			swal("Debe seleccionar el vehiculo!!");
			return false;
		}
		if (id_usuario<=0){
			$("#id_usuario").focus();
			swal("Debe seleccionar el usuario gestor se encargara de misión!!");
			return false;
		}
		if (fecha_comienzo<=0){
			$("#fecha_comienzo").focus();
			swal("Indique una fecha de comienzo!!");
			return false;
		}
		if (hora_comienzo.length<=0){
			swal("Hora en que estima comenzara la misión!!");
			return false;
		}

		if (tiempo_hrs.length<=0)
		{
			$("#tiempo_hrs").focus();
			swal("Un Tiempo estimado de la misión!!");
			return false;
		}
		if (tipo_recop.length<=0)
		{
			$("#tipo_recop").focus();
			swal("Indique el tipo de recolección de datos!!");
			return false;
		}
		if (descrip.length<=0)
		{
			$("#descrip").focus();
			swal("Una breve descripción del plan!!");
			return false;
		}
		if (typeForm=='n01'){
		}
		if (typeForm=='n11'){
		}


		$("form[name='frm_plainMission']").unbind('submit').submit();
	});



	$(document).on("click",".delete_mission",function(event){
		event.preventDefault()
		var idMision = $(this).parent().parent().data('id');
		//pathdelete = returnPath('config_cia_delete');
	    swal({
	        title: '¿Desea eliminar esta misión?',
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
	            data:{'idMission':idMision},
	            type:'post',
	            dataType:'json',
	            url: '/ajax_process/mision/borrar',
	            error: function(err,txt,thr){
	              $('body').html(err.responseText);
	            },
	            success: function(result){
	            	console.log('Pasa por aquí');
	             	 if (result.ok=='01'){
	              		listMission();
						$.toast({
							heading: 'Misión Eliminada!!!',
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


	$(document).on("click",".edit_mission",function(event){
		event.preventDefault();
		var idMission = $(this).parent().parent().data('id');

		window.location = '/misiones/modificar/' + idMission;
		//console.log('/usuarios/modificar/' + idUser);
	}) // fin de update_use

	/* Session de Busqueda ......*/


	$('#tblCliente').on('click','tr',function() {
	    var data = tablaClient.fnGetData(this);
	   	$("#win-modal-01").fadeOut("slow",function(e){
			$("#win-modal-01").css("display","none");
		});

		$("#id_cliente").val(data.midata.id_cliente);
		$("#id_cliente").parent().attr("class","form-group label-floating");
		
		$("#compania").val(data.midata.compania);
		$("#compania").parent().attr("class","form-group label-floating");

		info = data.midata;

		/* La información del cliente es:  Teléfono 0555 5202020; Dirección asd asd asdasd ads ; Contacto Juan  0500 5200 5200  */
		$("#infoClientText").html('La información del cliente es: Teléfono ' + info.telf_cia + '; Dirección ' +
			info.direccion + '; Contacto ' + info.contacto + ' ' + info.telf_contact);

		$.material.init();
	});

	$('#tblTipom').on('click','tr',function() {
	    var data = tablaTypem.fnGetData(this);
	   	$("#win-modal-02").fadeOut("slow",function(e){
			$("#win-modal-02").css("display","none");
		});

		$("#id_tipom").val(data.midata.id_tipom);
		$("#id_tipom").parent().attr("class","form-group label-floating");
		
		$("#denominacion").val(data.midata.denominacion);
		$("#denominacion").parent().attr("class","form-group label-floating");

		$.material.init();
	});

	$('#tblVehiculo').on('click','tr',function() {
	    var data = dt_Tow.fnGetData(this);
	   	$("#win-modal-01").fadeOut("slow",function(e){
			$("#win-modal-01").css("display","none");
		});

		$("#id_vehiculo").val(data.midata.id_vehiculo);
		$("#id_vehiculo").parent().attr("class","form-group label-floating");
		
		$("#modelo").val(data.midata.modelo);
		$("#modelo").parent().attr("class","form-group label-floating");

		$.material.init();
	});

	$('#tblUsuario').on('click','tr',function() {
	    var data = dt_User.fnGetData(this);
	   	$("#win-modal-02").fadeOut("slow",function(e){
			$("#win-modal-02").css("display","none");
		});

	   	if (type_list=='Gestor'){
			$("#id_usuario").val(data.midata.id_usuario);
			$("#id_usuario").parent().attr("class","form-group label-floating");
			
			$("#nombres").val(data.midata.nombres);
			$("#nombres").parent().attr("class","form-group label-floating");
	   	}
	   	if (type_list=='Piloto'){
			$("#idPiloto").val(data.midata.id_usuario);
			$("#idPiloto").parent().attr("class","form-group label-floating");
			
			$("#nombrePiloto").val(data.midata.nombres);
			$("#nombrePiloto").parent().attr("class","form-group label-floating");
	   	}

		$.material.init();
	});

	$('#tblDrone').on('click','tr',function() {
	    var data = dt_Drone.fnGetData(this);
	   	$("#win-modal-03").fadeOut("slow",function(e){
			$("#win-modal-03").css("display","none");
		});

		$("#id_drone").val(data.midata.id_drone);
		$("#id_drone").parent().attr("class","form-group label-floating");
		
		$("#modeloDrone").val(data.midata.modelo);
		$("#modeloDrone").parent().attr("class","form-group label-floating");

		$.material.init();
	});


	$("#addPilot").click(function(event){
		event.preventDefault();

		var tmp_id 		= $("#tmp_id").val();
		var id_usuario 	= String($("#idPiloto").val());
		var id_drone 	= String($("#id_drone").val());
		var labor    	= String($("#labor").val());
		var token_id 	= String($("#token_id").val());

		if (id_usuario.length<=0){
			$("#idPiloto").focus()
			swal("Debe indicar el Piloto!!");
			return false;
		}
		if (id_drone.length<=0){
			$("#idDrone").focus()
			swal("Seleccione el Drone!!");
			return false;
		}
		if (labor.length<=0){
			$("#labor").focus()
			swal("Debe indicar el Piloto!!");
			return false;
		}
		$.ajax({
			data:{'tmp_id':tmp_id,'id_usuario':id_usuario,'id_drone':id_drone,'labor':labor, 'token_id':token_id},
			type:'post',
			dataType:'json',
			url: '/ajax_process/mision/addPilot',
			error: function(err,txt,thr){
			  	//$('body').html(err.responseText);
			  	console.log(err.responseText);
			},
			success: function(result){
			  	if (result.ok=='01'){
			  		//console.log("Resul:"+result.msg);
			  		$("#idPiloto").val('');
			  		$("#idPiloto").parent().addClass('is-empty');

			  		$("#nombrePiloto").val('');
			  		$("#nombrePiloto").parent().addClass('is-empty');

			  		$("#id_drone").val('');
			  		$("#id_drone").parent().addClass('is-empty');

			  		$("#modeloDrone").val('');
			  		$("#modeloDrone").parent().addClass('is-empty');
			  		
			  		$("#labor").val('');
			  		$("#labor").parent().addClass('is-empty');

			  		$.material.init();

			  		listItemsPilot();
			    	//swal("Accesos Al Sistema",result.msg,"success");
			  	} else {
			    	swal("Agregando Piloto",result.msg,"error");
			  	}
			}
		});
	}); // click function 

	$(document).on("click",".delete_itemPilot",function(event){
		event.preventDefault()
		var idPiloto = $(this).parent().data('id');
		var tmp_id 	 = $("#tmp_id").val();
		//pathdelete = returnPath('config_cia_delete');
	    swal({
	        title: '¿Esta seguro de Piloto Agregado?',
	        text: "No podrás deshacer está acción.",
	        type: 'warning',
	        showCancelButton: true,
	        confirmButtonColor: '#3085d6',
	        cancelButtonColor: '#d33',
	        confirmButtonText: 'Si, Eliminar!'
	      }).then((result)=>{
	        if (result){
	          $.ajax({
	            data:{'idPiloto':idPiloto,'tmp_id':tmp_id},
	            type:'post',
	            dataType:'json',
	            url: '/ajax_process/mision/delete_itemPilot',
	            error: function(err,txt,thr){
	              $('body').html(err.responseText);
	            },
	            success: function(result){
	            	//console.log('Pasa por aquí');
	             	 if (result.ok=='01'){
	              		listItemsPilot();
						$.toast({
							heading: 'Se eliminado el Piloto ingresado!!!',
							text: 'Proceso correcto!!',
							position: 'top-right',
							loaderBg:'#e6b034',
							icon: 'success',
							hideAfter: 3500, 
							stack: 6
						})

	              	} else {
	                	swal("Eliminar Piloto",result.msg,"error");
	              	}	
	            }

	          });
	        }
      	});

	});// fin function document	

}); // document ready 

function listMission() {
	//var txtSearch = String($("#txtSearch").val());
	var txtSearch = '';

	tabla2 = $('#tblMision').dataTable({
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
				url:'/ajax_process/mision/listar',
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

					$('#map-dm-mission-0').empty();
					$('#map-dm-mission-0').css('background-color','none');

					//singlemap = null;

					$("table .map-definition").each(function(index){
						var gps = String($(this).data('coord'));
						var city = String($(this).data('city'));

						var arrGps = gps.split(',');

						//console.log(arrGps);

						$(this).vectorMap({
							map: 'es_merc',
				  			backgroundColor: null,
							color: '#eaeaea',
							hoverOpacity: 0.7,
							enableZoom: false,
							zoomButtons : false,
							showTooltip: true,
							values: {gdpData},
							scaleColors: ['#6FC6EA', '#0A4D70'],
							normalizeFunction: 'polynomial',
							markerStyle: {
							      initial: {
							        fill: '#F8E23B',
							        stroke: '#383f47'
							      },
							      scale: {'1':'#10a28b'}
							    },
							regionStyle:{
								initial:{fill:"#06b599"}
							},
	    					markers: [{latLng: [arrGps[0],arrGps[1]],name:city}],

						});

						var mapObj = $(this).vectorMap('get', 'mapObject');

						var zoomSettings = {scale:3,lat:arrGps[0],lng:arrGps[1]};
						mapObj.setFocus(zoomSettings);
					});

				}


			},
			columns:[
				{data:"contenido"}
			],
			deferRender: true,
			fnDrawCallback: function ( oSettings ) {
	    		$(oSettings.nTHead).hide();
			},
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

var tablaTypem=null;
function listTipoMision() {
	var txtSearch = String($("#txtSearchTypem").val());

	tablaTypem = $('#tblTipom').dataTable({
			destroy:true,
			serverSide: true,
			searching: false,
			lengthChange: false,
			pageLength:15,
			dom:'<"#myPaginsTypem"rp>',
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
				url:'/ajax_process/tipomision/searchList',
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
					 $("#newpPaginsTypem").html("");
					 $("#myPaginsTypem").prependTo("#newpPaginsTypem");
				}


			},
			columns:[
				{data:"id_tipom"},
				{data:"denominacion"},
			],
			deferRender: true
	 });	



}

/* Bloque de Planes  */

function listSelMission() {
	//var txtSearch = String($("#txtSearch").val());
	var txtSearch = '';

	tablaPlainSelect = $('#tblSelMision').dataTable({
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
				url:'/ajax_process/mision/planSeleccion',
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

					$('#map-dm-mission-0').empty();
					$('#map-dm-mission-0').css('background-color','none');

					//singlemap = null;

					$("table .map-definition").each(function(index){
						var gps = String($(this).data('coord'));
						var city = String($(this).data('city'));

						var arrGps = gps.split(',');

						//console.log(arrGps);

						$(this).vectorMap({
							map: 'es_merc',
				  			backgroundColor: null,
							color: '#eaeaea',
							hoverOpacity: 0.7,
							enableZoom: false,
							zoomButtons : false,
							showTooltip: true,
							values: {gdpData},
							scaleColors: ['#6FC6EA', '#0A4D70'],
							normalizeFunction: 'polynomial',
							markerStyle: {
							      initial: {
							        fill: '#F8E23B',
							        stroke: '#383f47'
							      },
							      scale: {'1':'#10a28b'}
							    },
							regionStyle:{
								initial:{fill:"#06b599"}
							},
	    					markers: [{latLng: [arrGps[0],arrGps[1]],name:city}],

						});

						var mapObj = $(this).vectorMap('get', 'mapObject');

						var zoomSettings = {scale:3,lat:arrGps[0],lng:arrGps[1]};
						mapObj.setFocus(zoomSettings);
					});

				}


			},
			columns:[
				{data:"contenido"}
			],
			deferRender: true,
			fnDrawCallback: function ( oSettings ) {
	    		$(oSettings.nTHead).hide();
			},
	});	



}		

var dt_Tow = null;
function listVehiculo() {
	var txtSearch = String($("#txtSearchVehiculo").val());

	dt_Tow = $('#tblVehiculo').dataTable({
			destroy:true,
			serverSide: true,
			searching: false,
			lengthChange: false,
			pageLength:15,
			dom:'<"#myPaginsTow"rp>',
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
				url:'/ajax_process/vehiculo/searchList',
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
					 $("#newpPaginsTow").html("");
					 $("#myPaginsTow").prependTo("#newpPaginsTow");
				}


			},
			columns:[
				{data:"id"},
				{data:"modelo"},
				{data:"matricula"}
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

					console.log(err.responseText);

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

dt_Drone = 	null;

function listDrone() {
	var txtSearch = String($("#txtSearchDrone").val());

	dt_Drone = $('#tblDrone').dataTable({
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
				url:'/ajax_process/drone/searchList',
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
					 $("#newpPaginsDrone").html("");
					 $("#myPaginsDrone").prependTo("#newpPaginsDrone");
				}


			},
			columns:[
				{data:"id"},
				{data:"modelo"},
				{data:"numserie"},
			],
			deferRender: true
	 });	


}		

dt_ItemsPilot = null;

function listItemsPilot() {
	//var txtSearch = String($("#txtSearchDrone").val());
	var tmp_id = String($("#tmp_id").val());

	dt_ItemsPilot = $('#tblPilotos').dataTable({
			destroy:true,
			serverSide: true,
			searching: false,
			lengthChange: false,
			pageLength:15,
			dom:'<"#myPaginsItemPilot"rp>',
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
				data:{'txtSearch':'','tmp_id':tmp_id},
				method:"POST",
				dataType:"JSON",
				url:'/ajax_process/mision/itemsPilot',
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
					 $("#newpPaginsItemPilot").html("");
					 $("#myPaginsItemPilot").prependTo("#newpPaginsItemPilot");
				}


			},
			columns:[
				{data:"contenido"},
			],
			deferRender: true,
			fnDrawCallback: function ( oSettings ) {
	    		$(oSettings.nTHead).hide();
			},
	 });	


}		

dt_Plains = null;
function listPains() {
	//var txtSearch = String($("#txtSearch").val());
	var txtSearch = '';

	dt_Plains = $('#tblPlanes').dataTable({
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
				url:'/ajax_process/mision/planesListar',
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
				{data:"contenido"}
			],
			deferRender: true,
			fnDrawCallback: function ( oSettings ) {
	    		$(oSettings.nTHead).hide();
			},
	});	
}		
