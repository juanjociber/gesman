const vgLoader=document.querySelector('.container-loader-full');
var productos=[];

window.onload = function() {
    document.getElementById('MenuOrdenes').classList.add('menu-activo','fw-bold');
    vgLoader.classList.add('loader-full-hidden');
};

$(document).ready(function() {
    $('#cbProducto').select2({
        dropdownParent: $('#modalAgregarVale'),//Agregar el select a un modal
        width: 'resolve', //Personalizar el alto del select, aplicar estilo.
        ajax: {
            delay: 450, //Tiempo de demora para buscar
            url: '/gesman/search/ListarProductos.php',
            type: 'POST',
            dataType: 'json',
            data: function (params){
                return {
                    nombre: params.term // parametros a enviar al server. params.term captura lo que se escribe en el input
                };
            },
            processResults: function (data) {
                console.log(data)
                return {
                    results: data.data //Retornar el json obtenido
                }
            },
            cache: true
        },
        placeholder: 'Seleccionar',
        minimumInputLength:1 //Caracteres minimos para buscar
    }).on('select2:select', function (e) {
        document.getElementById('txtProId').value=e.params.data.id;
        document.getElementById('txtProOdooId').value=e.params.data.idodoo;
        document.getElementById('txtProListaId').value=e.params.data.idlista;
        document.getElementById('txtProCodigo').value=e.params.data.codigo;
        document.getElementById('txtProNombre').value=e.params.data.text;
        document.getElementById('txtProMedida').value=e.params.data.medida;
        document.getElementById('txtProCantidad').value=1;
    });
});

var modalAgregarVale=new bootstrap.Modal(document.getElementById('modalAgregarVale'), { keyboard: false });

function FnModalAgregarVale(){
    document.getElementById('msjAgregarVale').innerHTML="";
    modalAgregarVale.show();
};

function FnAgregarProducto(id, idodoo, idlista, codigo, nombre, medida, cantidad ){
    try {
        if(id>0 && idodoo>0 && idlista>0 && codigo!="" && nombre!="" && medida!="" && cantidad>0){
            productos.push({id:id, idodoo:idodoo, idlista:idlista, codigo:codigo, nombre:nombre, medida:medida, cantidad:cantidad});
            FnMostrarProductos();
            document.getElementById('txtProId').value=0;
            document.getElementById('txtProOdooId').value=0;
            document.getElementById('txtProListaId').value=0;
            document.getElementById('txtProCodigo').value='';
            document.getElementById('txtProNombre').value='';
            document.getElementById('txtProMedida').value='';
            document.getElementById('txtProCantidad').value=1;
            $("#cbProducto").empty();
        }else{
            throw new Error("La información esta incompleta.")
        }
    } catch (ex) {
        document.getElementById('msjAgregarVale').innerHTML = `<div class="alert alert-danger m-0 p-1 text-center w-100 alert-dismissible fade show" role="alert">${ex.message}<button type="button" class="btn-close p-2" data-bs-dismiss="alert" aria-label="Close"></button></div>`;
    }
}

function FnEliminarProducto(id){
    try {
        productos.splice(id, 1);
        FnMostrarProductos();
    } catch (ex) {
        document.getElementById('msjAgregarVale').innerHTML = `<div class="alert alert-danger m-0 p-1 text-center w-100 alert-dismissible fade show" role="alert">${ex.message}<button type="button" class="btn-close p-2" data-bs-dismiss="alert" aria-label="Close"></button></div>`;
    }
}

function FnMostrarProductos(){
    try {
        document.getElementById('divProductos').innerHTML='';
        productos.forEach(function(valor, indice, array){
            document.getElementById('divProductos').innerHTML+=`
                <div class="row mb-2 mx-0 border-bottom border-secondary">
                    <div class="col-12 p-0 d-flex justify-content-between">
                        <p class="m-0">${valor.nombre}</p>
                        <a class="text-secondary text-decoration-none p-0" href="#" onclick="FnEliminarProducto(${indice}); return false;"><i class="fas fa-times link-wa" style="font-size:22px;"></i></a>
                    </div>
                    <div class="col-12 p-0 d-flex justify-content-between">
                        <p class="m-0">${valor.codigo}</p>
                        <p class="m-0">${valor.cantidad} ${valor.medida}</p>
                    </div>
                </div>`;
        });
    } catch (ex) {
        throw ex
    }
}

async function FnAgregarVale(){
    //console.log(document.getElementById('dtpValeFecha').value);
    vgLoader.classList.remove('loader-full-hidden');
    try {
        let productosOdoo = [];
        productos.forEach(producto=>productosOdoo.push({IdOdoo:producto.idodoo, IdLista:producto.idlista, Cantidad:producto.cantidad}));
        const response = await fetch('/gpemsac/intranet/modulos/xmlrpc/insert/ApiAddSaleOrderAll.php', {
            method: 'POST',
            headers:{'Content-Type':'application/json; charset=UTF-8'},
            body:JSON.stringify({
                CliId:document.getElementById('txtCliId').value,
                WhId: document.getElementById('txtWhId').value,
                OtId:document.getElementById('txtId').value,
                OtNombre:document.getElementById('txtOtNombre').value,
                OtTipo:document.getElementById('txtOtTipo').value,
                Equipo:document.getElementById('txtOtEquipo').value,
                Vale:document.getElementById('txtOtVale').value,
                Fecha:document.getElementById('dtpValeFecha').value,
                Tecnico:document.getElementById('txtOtTecnico').value,
                Usuario:document.getElementById('txtOtUsuario').value,
                Productos:productosOdoo
            })
        });//.then(response=>response.text()).then((response)=>{console.log(response)}).catch(err=>console.log(err));
        if(!response.ok){throw new Error(`${response.status} ${response.statusText}`)}
        const datos = await response.json();        
        if(!datos.res){throw new Error(datos.msg);}
        location.reload();
    } catch (ex) {
        document.getElementById('msjAgregarVale').innerHTML = `<div class="alert alert-danger m-0 p-1 text-center" role="alert">${ex.message}</div>`;
        setTimeout(()=>{vgLoader.classList.add('loader-full-hidden');},500);
    }
}

async function FnModalVerVale(id){
    vgLoader.classList.remove('loader-full-hidden');
    let modalVerVale=new bootstrap.Modal(document.getElementById('modalVerVale'), { keyboard: false });
    modalVerVale.show();
    document.getElementById('divProductos2').innerHTML='';
    try {    
        const formData = new FormData();
        formData.append('id', id);
        const response = await fetch("/gesman/search/BuscarValeProductos.php", {
            method: "POST",
            body: formData
        })//.then(response=>response.text()).then((response)=>{console.log(response)}).catch(err=>console.log(err));
        if(!response.ok){throw new Error(`Error del servidor: ${response.status} ${response.statusText}`)}
        const datos = await response.json();        
        if(datos.res){
            datos.data.forEach(function(valor, indice, array){
                document.getElementById('divProductos2').innerHTML+=`
                <div class="row mb-2 mx-0 border-bottom border-secondary">
                    <div class="col-12 p-0">${valor.procodigo} ${valor.pronombre}</div>
                    <div class="col-12 p-0 d-flex justify-content-between">
                        <p class="m-0">${valor.procantidad} ${valor.promedida}</p>
                        <p class="m-0">${valor.proestado}</p>
                    </div>
                </div>`;
            });
        }else{
            throw new Error(datos.msg);
        }
    } catch (ex) {
        document.getElementById('msjVerVale').innerHTML = `<div class="alert alert-danger m-0 p-1 text-center" role="alert">${ex.message}</div>`;
    }finally{
        setTimeout(()=>{vgLoader.classList.add('loader-full-hidden');},500);
    }
}

function FnResumenOrden(){
    let id = document.getElementById('txtId').value;
    if(id > 0){
        window.location.href='/gesman/Orden.php?id='+id;
    }
    return false;
}

function FnListarOrdenes(){
    window.location.href='/gesman/Ordenes.php';
    return false;
}