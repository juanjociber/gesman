const loader = document.querySelector('.container-loader-full');
var search={
    equipo:0,
    tipo:0,
    orden:'',
    actividad:'',
    fechaInicial:'',
    fechaFinal:'',
    paginasTotal:0,
    paginaActual:0
};

window.addEventListener('load', function() {
    document.getElementById('MenuOrdenes').classList.add('menu-activo','fw-bold');
    const datos = sessionStorage.getItem('gpem_ordenes');
    if (datos){FnMostrarRegistros(JSON.parse(datos));}
    loader.classList.add('loader-full-hidden');
});

$(document).ready(function() {
    $('#cbEquipo').select2({
        width: 'resolve', //Personalizar el alto del select, aplicar estilo.
        ajax: {
            delay: 450, //Tiempo de demora para buscar
            url: '/gesman/search/ListarEquipos.php',
            type: 'POST',
            dataType: 'json',
            data: function (params) {
                return {
                    codigo:params.term
                };
            },
            processResults:function(datos){
                return {
                    results:datos.data.map(function(elem) {
                        return {
                            id:elem.id,
                            text:elem.codigo,
                        };
                    })
                }
            },
            cache: true
        },
        placeholder: 'Seleccionar',
        allowClear: true,
        minimumInputLength:1
    });
});

$(document).ready(function() {
    $('#cbEquipo2').select2({
        dropdownParent: $('#modalAgregarOrden'),
        width: 'resolve', //Personalizar el alto del select, aplicar estilo.
        ajax: {
            delay: 450,
            url: '/gesman/search/ListarEquipos.php',
            type: 'POST',
            dataType: 'json',
            data: function(params){
                return {
                    codigo:params.term
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

$(document).ready(function() {
    $('#cbSistema2').select2({
        dropdownParent: $('#modalAgregarOrden'),
        width: 'resolve', //Personalizar el alto del select, aplicar estilo.
        ajax: {
            delay: 450,
            url: '/gesman/search/ListarSistemas.php',
            type: 'POST',
            dataType: 'json',
            data: function(params){
                return {
                    nombre: params.term
                };
            },
            processResults:function(datos){
                return {
                    results:datos.data.map(function(elem){
                        return {
                            id: elem.id,
                            text: elem.nombre
                        };
                    })
                }
            },
            cache: true
        },
        placeholder: 'Seleccionar'
    });
});

function FnModalAgregarOrden(){
    const modalAgregarOrden=new bootstrap.Modal(document.getElementById('modalAgregarOrden'),{
        keyboard: false
    }).show();
}

async function FnAgregarOrden(){
    loader.classList.remove('loader-full-hidden');
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
        setTimeout(()=>{loader.classList.add('loader-full-hidden');},500);
    }
}

async function FnBuscarOrdenes(){
    loader.classList.remove('loader-full-hidden');
    try {
        search.orden = document.getElementById('txtOrden').value;
        search.equipo = document.getElementById('cbEquipo').value;
        search.actividad = document.getElementById('txtActividad').value,
        search.fechaInicial = document.getElementById('dtpFechaInicial').value;
        search.fechaFinal = document.getElementById('dtpFechaFinal').value;
        search.paginasTotal = 0
        search.paginaActual = 0
        await FnBuscarOrdenes2();
    } catch (ex) {
        showToast(ex.message, 'bg-danger');
        document.getElementById('tblOrdenes').innerHTML=`<div class="col-12"><div class="fst-italic text-danger p-2">${ex.message}</div></div>`;
        document.getElementById("btnSiguiente").classList.add('d-none');
        document.getElementById("btnPrimero").classList.add('d-none');
        sessionStorage.removeItem('gpem_ordenes');
    } finally {
        setTimeout(function(){loader.classList.add('loader-full-hidden');},500);
    }
}

async function FnBuscarOrdenes2(){
    try {
        let json = {
            cliid : search.cliente,
            equid : search.equipo,
            tipid : search.tipo,
            sisid : search.sistema,
            oriid : search.origen,
            nombre : search.orden,
            actnombre : search.actividad,
            fechainicial : search.fechaInicial,
            fechafinal : search.fechaFinal,
            pagina : search.paginaActual
        }
        const response = await fetch('/gesman/search/BuscarOrdenes.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json'
            },
            body: JSON.stringify(json)
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
        search.paginaActual += 1;
        if (cantidad == 15) {
            search.paginasTotal += 15;
            document.getElementById("btnSiguiente").classList.remove('d-none');
        } else {
            document.getElementById("btnSiguiente").classList.add('d-none');
        }

        if (search.paginaActual > 1) {
            document.getElementById("btnPrimero").classList.remove('d-none');
        } else {
            document.getElementById("btnPrimero").classList.add('d-none');
        }
    } catch (ex) {
        throw ex;
    }
}

async function FnBuscarSiguiente() {
    loader.classList.remove('loader-full-hidden');
    try {
        await FnBuscarOrdenes2();
    } catch (ex) {
        document.getElementById("btnSiguiente").classList.add('d-none');
        showToast(ex.message, 'bg-danger');
    } finally {
        setTimeout(function(){loader.classList.add('loader-full-hidden');},500);
    }
}

async function FnBuscarPrimero() {
    loader.classList.remove('loader-full-hidden');
    try {
        search.paginasTotal = 0
        search.paginaActual = 0
        await FnBuscarOrdenes2()
    } catch (ex) {
        document.getElementById("btnPrimero").classList.add('d-none');
        showToast(ex.message, 'bg-danger');
    } finally {
        setTimeout(function(){loader.classList.add('loader-full-hidden');},500);
    }
}

function FnOrden(id){
    if(id > 0){
        window.location.href='/gesman/Orden.php?id='+id;
    }
}
