var Equipo = 0;
var Orden = '';
var Actividad = '';
var FechaInicial = '';
var FechaFinal = '';
var PaginasTotal = 0;
var PaginaActual = 0;

const vgLoader = document.querySelector('.container-loader-full');

window.onload = function() {
    document.getElementById('MenuOrdenes').classList.add('menu-activo','fw-bold');
    const datos = sessionStorage.getItem('gpem_ordenes');
    if (datos){FnMostrarRegistros(JSON.parse(datos));}
    vgLoader.classList.add('loader-full-hidden');
};

$(document).ready(function() {
    $('#cbEquipo').select2({
        width: 'resolve', //Personalizar el alto del select, aplicar estilo.
        ajax: {
            delay: 450, //Tiempo de demora para buscar
            url: '/gesman/search/ListarActivos.php',
            type: 'POST',
            dataType: 'json',
            data: function (params) {
                return {
                    nombre: params.term // parametros a enviar al server. params.term captura lo que se escribe en el input
                };
            },
            processResults: function (data) {
                return {
                    results: data.data //Retornar el json obtenido
                }
            },
            cache: true
        },
        placeholder: 'Seleccionar',
        allowClear: true, // Permite borrar la selección
        minimumInputLength:1 //Caracteres minimos para buscar
    });
});


$(document).ready(function() {
    $('#cbEquipo2').select2({
        dropdownParent: $('#modalAgregarOrden'),//Agregar el select a un modal
        width: 'resolve', //Personalizar el alto del select, aplicar estilo.
        ajax: {
            delay: 450, //Tiempo de demora para buscar
            url: '/gesman/search/BuscarEquipos.php',
            type: 'POST',
            dataType: 'json',
            data: function (params) {
                return {
                    nombre: params.term // parametros a enviar al server. params.term captura lo que se escribe en el input
                };
            },
            processResults:function(datos) {
                return {
                    results:datos.data.map(function(elem){
                        return {
                            id: elem.id,
                            text: elem.codigo,
                            km: elem.km,
                            hm: elem.hm
                        };
                    })
                }
            },
            cache: true
        },
        placeholder: 'Seleccionar',
        minimumInputLength:1 //Caracteres minimos para buscar
    }).on('select2:select',function(e){
        document.getElementById('txtEquKm2').value=e.params.data.km;
        document.getElementById('txtEquHm2').value=e.params.data.hm;
    });
});

function FnModalAgregarOrden(){
    const modalAgregarOrden=new bootstrap.Modal(document.getElementById('modalAgregarOrden'),{
        keyboard: false
    }).show();
    return false;
}

async function FnAgregarOrden(){
    vgLoader.classList.remove('loader-full-hidden');
    try {        
        const formData = new FormData();
        formData.append('fecha', document.getElementById('txtFecha2').value);
        formData.append('nombre', document.getElementById('txtNombre2').value);
        formData.append('equid', document.getElementById('cbEquipo2').value);
        formData.append('equcodigo', document.getElementById("cbEquipo2").options[document.getElementById("cbEquipo2").selectedIndex].text);
        formData.append('equkm', document.getElementById('txtEquKm2').value);
        formData.append('equhm', document.getElementById('txtEquHm2').value);
        formData.append('tipid', document.getElementById('cbTipo2').value);
        formData.append('tipnombre', document.getElementById("cbTipo2").options[document.getElementById("cbTipo2").selectedIndex].text);        
        formData.append('sisid', document.getElementById('cbSistema2').value);
        formData.append('sisnombre', document.getElementById("cbSistema2").options[document.getElementById("cbSistema2").selectedIndex].text);
        formData.append('actnombre', document.getElementById('txtActNombre2').value);
        const response = await fetch("/gesman/insert/AgregarOrden.php", {
            method: "POST",
            body: formData
        });//.then(response=>response.text()).then((response)=>{console.log(response)}).catch(err=>console.log(err));

        if(!response.ok){throw new Error(`${response.status} ${response.statusText}`)}
        const datos = await response.json();
        if(!datos.res){throw new Error(datos.msg);}
        setTimeout(()=>{window.location.href='/gesman/EditarOrden.php?id='+datos.id;},1000);
    } catch (ex) {
        showToast(ex.message, 'bg-danger');
        setTimeout(()=>{vgLoader.classList.add('loader-full-hidden');},500);
    }
}

async function FnBuscarOrdenes(){
    vgLoader.classList.remove('loader-full-hidden');
    try {
        Orden = document.getElementById('txtOrden').value;
        Equipo = document.getElementById('cbEquipo').value;
        Actividad = document.getElementById('txtActividad').value,
        FechaInicial = document.getElementById('dtpFechaInicial').value;
        FechaFinal = document.getElementById('dtpFechaFinal').value;
        PaginasTotal = 0
        PaginaActual = 0
        await FnBuscarOrdenes2();
    } catch (ex) {
        showToast(ex.message, 'bg-danger');
    } finally {
        setTimeout(function(){vgLoader.classList.add('loader-full-hidden');},500);
    }
}

async function FnBuscarOrdenes2(){
    try {
        const formData = new FormData();
        formData.append('nombre', Orden);
        formData.append('equid', Equipo)
        formData.append('actividad', Actividad);
        formData.append('fechainicial', FechaInicial);
        formData.append('fechafinal', FechaFinal);
        formData.append('pagina', PaginasTotal);
        const response = await fetch('/gesman/search/BuscarOrdenes.php', {
            method:'POST',
            body: formData
        });//.then(response=>response.text()).then((response)=>{console.log(response)}).catch(err=>console.log(err));

        if (!response.ok) { throw new Error(`${response.status} ${response.statusText}`);}
        const datos = await response.json();
        if (!datos.res) { throw new Error(`${datos.msg}`); }
        sessionStorage.setItem('gpem_ordenes', JSON.stringify(datos));
        FnMostrarRegistros(datos);
    } catch (ex) {
        throw ex;
    }
}

function FnMostrarRegistros(datos){
    document.getElementById('tblOrdenes').innerHTML = '';
    let estado = '';
    datos.data.forEach(orden => {
        switch (orden.estado){
            case 0:
                estado='<span class="badge bg-danger">Anulado</span>';
            break;
            case 1:
                estado='<span class="badge bg-secondary">Abierto</span>';
            break;
            case 2:
                estado='<span class="badge bg-primary">Proceso</span>';
            break;
            case 3:
                estado='<span class="badge bg-success">Cerrado</span>';
            break;
            case 4:
                estado='<span class="badge bg-warning">Obervado</span>';
            break;
            default:
                estado='<span class="badge bg-light text-dark">Unknown</span>';
        }
        document.getElementById('tblOrdenes').innerHTML +=`
        <div class="col-12 mb-1">
            <div class="border-bottom divselect px-1" onclick="FnOrden(${orden.id}); return false;">
                <div class="d-flex justify-content-between">
                    <p class='m-0'><span class="fw-bold">${orden.nombre}</span> <span style="font-size: 12px; font-style: italic;">${orden.fecha}</span></p>
                    <p class='m-0'>${estado}</p>
                </div>
                <div>${orden.equcodigo} ${orden.tipnombre} ${orden.actnombre}</div>
            </div>
        </div>`;
    });
    FnPaginacion(datos.pag);
}

function FnPaginacion(cantidad){
    try {
        PaginaActual += 1;
        if (cantidad == 15) {
            PaginasTotal += 15;
            document.getElementById("btnSiguiente").classList.remove('d-none');
        } else {
            document.getElementById("btnSiguiente").classList.add('d-none');
        }

        if (PaginaActual > 1) {
            document.getElementById("btnPrimero").classList.remove('d-none');
        } else {
            document.getElementById("btnPrimero").classList.add('d-none');
        }
    } catch (ex) {
        throw ex;
    }
}

async function FnBuscarSiguiente() {
    vgLoader.classList.remove('loader-full-hidden');
    try {
        await FnBuscarOrdenes2();
    } catch (ex) {
        document.getElementById("btnSiguiente").classList.add('d-none');
        showToast(ex.message, 'bg-danger');
    } finally {
        setTimeout(function(){vgLoader.classList.add('loader-full-hidden');},500);
    }
}

async function FnBuscarPrimero() {
    vgLoader.classList.remove('loader-full-hidden');
    try {
        PaginasTotal = 0
        PaginaActual = 0
        await FnBuscarOrdenes2()
    } catch (ex) {
        document.getElementById("btnPrimero").classList.add('d-none');
        showToast(ex.message, 'bg-danger');
    } finally {
        setTimeout(function(){vgLoader.classList.add('loader-full-hidden');},500);
    }
}

function FnOrden(id){
    if(id > 0){
        window.location.href='/gesman/Orden.php?id='+id;
    }
    return false;
}
