var dt_ItemsPilots = null;
$(document).ready(function(){

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

	$('form[name="frm_report01"]').submit(function(event){

		event.preventDefault();
		var typeForm = $(this).data('type');

		var fecha_inicio = String($("#fecha_inicio").val());
		var fecha_final = String($("#fecha_final").val());


		if (fecha_inicio.length<=0){
			$("#fecha_inicio").focus()
			swal("Indique la fecha de inicio!!");
			return false;
		}
		if (fecha_final.length<=0){
			$("#fecha_final").focus()
			swal("Indique la fecha final!!");
			return false;
		}
		arrfec = fecha_inicio.split('/');
		var fromDate = new Date(arrfec[2]+'/'+arrfec[1]+'/'+arrfec[0]);
		arrfec = fecha_final.split('/');
		var toDate = new Date(arrfec[2]+'/'+arrfec[1]+'/'+arrfec[0]);

		if (fromDate>toDate){
			$("#fecha_inicio").focus()
			swal("Fecha final debe ser mayor que la  inicial!!");
			return false;
		}

		if (typeForm=='n01'){
		}
		if (typeForm=='n11'){
		}

		$("form[name='frm_report01']").unbind('submit').submit();
	});	

}) // fin de document ready


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
