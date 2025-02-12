const loader = document.querySelector('.container-loader-full');

var search={
    cliente:0,
    equipo:0,
    familia:0,
    origen:0,
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
    loader.classList.add('loader-full-hidden');
});

$(document).ready(function() {
    $('#cbCliente').select2({
        width: 'resolve', //Personalizar el alto del select, aplicar estilo.
        ajax: {
            delay: 450, //Tiempo de demora para buscar
            url: '/gesman/search/ListarClientes.php',
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
                            text: elem.alias
                        };
                    })
                }
            },
            cache: true
        },
        placeholder: 'Seleccionar'
    }).on('select2:select',function(e){
        $('#cbFamilia').val(null).trigger('change');
        $('#cbEquipo').val(null).trigger('change');
        $('#cbOrigen').val(null).trigger('change');
    });
});

$(document).ready(function() {
    $('#cbFamilia').select2({
        width: 'resolve', //Personalizar el alto del select, aplicar estilo.
        ajax: {
            delay: 450,
            url: '/gesman/search/AdmListarFamilias.php',
            type: 'POST',
            dataType: 'json',
            data: function(params){
                return {
                    nombre: params.term,
                    cliid: document.getElementById('cbCliente').value
                };
            },
            processResults: function(datos){
                return {
                    results:datos.data.map(function(elem){
                        return {
                            id: elem.id,
                            text: elem.ruta
                        };
                    })
                }
            },
            cache: true
        },
        placeholder: 'Seleccionar',
        allowClear: true
    }).on('select2:select',function(e){
        $('#cbEquipo').val(null).trigger('change');
    });
});

$(document).ready(function() {
    $('#cbEquipo').select2({
        width: 'resolve', //Personalizar el alto del select, aplicar estilo.
        ajax: {
            delay: 450,
            url: '/gesman/search/AdmListarEquipos.php',
            type: 'POST',
            dataType: 'json',
            data: function(params){
                return {
                    nombre: params.term,
                    cliid: document.getElementById('cbCliente').value,
                    famid: document.getElementById('cbFamilia').value
                };
            },
            processResults: function(datos){
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
        placeholder: 'Seleccionar',
        allowClear: true
    });
});

$(document).ready(function() {
    $('#cbOrigen').select2({
        width: 'resolve', //Personalizar el alto del select, aplicar estilo.
        ajax: {
            delay: 450, //Tiempo de demora para buscar
            url: '/gesman/search/AdmListarOrigenes.php',
            type: 'POST',
            dataType: 'json',
            data: function (params) {
                return {
                    nombre: params.term,
                    cliid: document.getElementById('cbCliente').value
                };
            },
            processResults: function(datos){
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
        placeholder: 'Seleccionar',
        allowClear: true
    });
});

async function FnBuscarOrdenes(){
    loader.classList.remove('loader-full-hidden');
    try {
        search.cliente=document.getElementById('cbCliente').value;
        search.equipo=document.getElementById('cbEquipo').value;
        search.familia=document.getElementById('cbFamilia').value;
        search.origen=document.getElementById('cbOrigen').value;
        search.tipo=document.getElementById('cbTipo').value;
        search.orden=document.getElementById('txtOrden').value;        
        search.actividad=document.getElementById('txtActividad').value,
        search.fechaInicial=document.getElementById('dtpFechaInicial').value;
        search.fechaFinal=document.getElementById('dtpFechaFinal').value;
        search.paginasTotal=0
        search.paginaActual=0
        await FnBuscarOrdenes2();
    } catch (ex) {
        showToast(ex.message, 'bg-danger');
        document.getElementById('tblOrdenes').innerHTML=`<div class="col-12"><div class="fst-italic text-danger p-2">${ex.message}</div></div>`;
        document.getElementById("btnSiguiente").classList.add('d-none');
        document.getElementById("btnPrimero").classList.add('d-none');
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
            famid : search.familia,
            oriid : search.origen,
            nombre : search.orden,
            actnombre : search.actividad,
            fechainicial : search.fechaInicial,
            fechafinal : search.fechaFinal,
            pagina : search.paginasTotal
        }
        
        const response = await fetch('/gesman/search/AdmBuscarOrdenes.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json'
            },
            body: JSON.stringify(json)
        });//.then(response=>response.text()).then((response)=>{console.log(response)}).catch(err=>console.log(err));

        if (!response.ok) { throw new Error(`${response.status} ${response.statusText}`);}
        const datos = await response.json();
        if (!datos.res) { throw new Error(`${datos.msg}`); }

        FnMostrarRegistros(datos);
    } catch (ex) {
        throw ex;
    }
}

function FnMostrarRegistros(datos){
    document.getElementById('tblOrdenes').innerHTML = '';
    let estado = '';
    datos.data.forEach(elem => {
        switch (elem.estado){
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
                estado='<span class="badge bg-warning">Observado</span>';
            break;
            default:
                estado='<span class="badge bg-light text-dark">Unknown</span>';
        }
        document.getElementById('tblOrdenes').innerHTML +=`
        <div class="col-12 mb-1">
            <div class="border-bottom divselect px-1" onclick="FnOrden(${elem.cliid}, ${elem.id}); return false;">
                <div class="d-flex justify-content-between">
                    <p class='m-0'><span class="fw-bold">${elem.nombre}</span> <span style="font-size: 12px; font-style: italic;">${elem.fecha}</span></p>
                    <p class='m-0'>${estado}</p>
                </div>
                <div>${elem.equnombre} ${elem.tipnombre} ${elem.actnombre}</div>
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

function FnOrden(cliid, id) {
    try {
        if(cliid>0 && id > 0){
            let ancho = 1000;
            let alto = 600;
            let x = (screen.width / 2) - (ancho / 2);
            let y = (screen.height / 2) - (alto / 2);
            const nuevaVentana = window.open('AdmOrden.php?cliid='+cliid + '&id=' + id, 'ORDEN_' + id, `width=${ancho}, height=${alto}, left=${x}, top=${y}, scrollbars=yes`);
            if (!nuevaVentana){
                throw new Error("El sitio no permite ventanas emergentes.");
            }
        }else{
            throw new Error("No se reconoce la Orden.");            
        }
    } catch (ex) {
        showToast(ex.message, 'bg-danger');
    }
}

function FnReporteOrdenes(){
    try {
        let cliid=document.getElementById('cbCliente').value;
        let fechaInicial=document.getElementById('dtpFechaInicial').value;
        let fechaFinal=document.getElementById('dtpFechaFinal').value;
        if(cliid=='' || fechaInicial=='' || fechaFinal=='') throw new Error("La información esta incompleta.");
        window.location.href='/gesman/download/ReporteOrdenes.php?cliid='+cliid+'&fechainicial='+fechaInicial+'&fechafinal='+fechaFinal;   
    } catch (ex) {
        showToast(ex.message, 'bg-danger');
    }
}

function FnReporteOrdenesTareos(){
    try {
        let cliid=document.getElementById('cbCliente').value;
        let fechaInicial=document.getElementById('dtpFechaInicial').value;
        let fechaFinal=document.getElementById('dtpFechaFinal').value;
        if(cliid=='' || fechaInicial=='' || fechaFinal=='') throw new Error("La información esta incompleta.");
        window.location.href='/gesman/download/ReporteOrdenesTareos.php?cliid='+cliid+'&fechainicial='+fechaInicial+'&fechafinal='+fechaFinal;   
    } catch (ex) {
        showToast(ex.message, 'bg-danger');
    }
}
