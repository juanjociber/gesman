var Equipo='';
var Estado=0;
var Sistema=0;
var PaginasTotal = 0;
var PaginaActual = 0;

const vgLoader=document.querySelector('.container-loader-full');

window.addEventListener('load', function(){
    document.getElementById('MenuSistemas').classList.add('menu-activo','fw-bold');
    document.getElementById('MenuSistemasEquipos').classList.add('menu-activo','fw-bold');

    const datos = sessionStorage.getItem('gpem_equipos');
    if (datos){FnMostrarRegistros(JSON.parse(datos));}
    vgLoader.classList.add('loader-full-hidden');

    $(document).ready(function() {
        $('#cbFamilia').select2({
            width: 'resolve', //Personalizar el alto del select, aplicar estilo.
            ajax: {
                delay: 450,
                url: '/gesman/search/ListarFamilias.php',
                type: 'POST',
                dataType: 'json',
                data: function (params) {
                    return {
                        nombre:params.term
                    };
                },
                processResults:function(datos){
                    return {
                        results:datos.data.map(function(elem) {
                            return {
                                id:elem.id,
                                text:elem.ruta
                            };
                        })
                    }
                },
                cache: true
            },
            placeholder: 'Seleccionar',
            allowClear: true
        });

        $('#cbFamilia2').select2({
            dropdownParent: $('#modalAgregarEquipo'),
            width: 'resolve', //Personalizar el alto del select, aplicar estilo.
            ajax: {
                delay: 450,
                url: '/gesman/search/ListarFamilias.php',
                type: 'POST',
                dataType: 'json',
                data: function (params) {
                    return {
                        nombre:params.term
                    };
                },
                processResults:function(datos){
                    return {
                        results:datos.data.map(function(elem) {
                            return {
                                id:elem.id,//familia del equipo
                                text:elem.ruta,
                                ownid:elem.ownid,//familia para los equipos
                                nombre:elem.nombre//familia del equipo
                            };
                        })
                    }
                },
                cache: true
            },
            placeholder: 'Seleccionar',
            allowClear: true
        }).on('select2:select',function(e){
            $('#cbEquipo2').val(null).trigger('change');
            document.getElementById('txtFamId2').value=e.params.data.id;
            document.getElementById('txtFamOwnId2').value=e.params.data.ownid;
            document.getElementById('txtFamNombre2').value=e.params.data.nombre;
        });

        $('#cbEquipo2').select2({
            dropdownParent: $('#modalAgregarEquipo'),
            width: 'resolve',
            ajax: {
                delay: 450,
                url: '/gesman/search/ListarFamiliaEquipos.php',
                type: 'POST',
                dataType: 'json',
                data: function (params) {
                    return {
                        id:0,
                        nombre:params.term,
                        famid:document.getElementById('txtFamOwnId2').value
                    };
                },
                processResults:function(datos){
                    return {
                        results:datos.data.map(function(elem) {
                            return {
                                id:elem.id,
                                text:elem.nombre
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
});

async function FnBuscarEquipos(){
    vgLoader.classList.remove('loader-full-hidden');
    try {
        Equipo=document.getElementById('txtEquipo').value;
        Sistema=document.getElementById('cbFamilia').value;
        Estado=document.getElementById('cbEstado').value;
        PaginasTotal=0
        PaginaActual=0
        await FnBuscarEquipos2();
    } catch (ex) {
        showToast(ex.message, 'bg-danger');
        document.getElementById('tblEquipos').innerHTML=`<div class="col-12"><div class="fst-italic text-danger p-2">${ex.message}</div></div>`;
        document.getElementById("btnSiguiente").classList.add('d-none');
        document.getElementById("btnPrimero").classList.add('d-none');
        sessionStorage.removeItem('gpem_equipos');
    } finally {
        setTimeout(function(){vgLoader.classList.add('loader-full-hidden');},500);
    }
}

async function FnBuscarEquipos2(){
    try {
        const formData = new FormData();
        formData.append('famid', Sistema);
        formData.append('nombre', Equipo);
        formData.append('estado', Estado);
        formData.append('pagina', PaginasTotal);
        const response = await fetch('/gesman/search/BuscarEquipos.php', {
            method:'POST',
            body: formData
        });//.then(response=>response.text()).then((response)=>{console.log(response)}).catch(err=>console.log(err));

        if (!response.ok) { throw new Error(`${response.status} ${response.statusText}`);}
        const datos = await response.json();
        if (!datos.res) { throw new Error(`${datos.msg}`); }

        sessionStorage.setItem('gpem_equipos', JSON.stringify(datos));
        FnMostrarRegistros(datos);
    } catch (ex) {
        throw ex;
    }
}

function FnMostrarRegistros(datos){
    document.getElementById('tblEquipos').innerHTML = '';
    let estado = '';
    datos.data.forEach(equipo => {
        switch (equipo.estado){
            case 1:
                estado='<span class="badge bg-danger">Inactivo</span>';
            break;
            case 2:
                estado='<span class="badge bg-success">Activo</span>';
            break;
            default:
                estado='<span class="badge bg-light text-dark">Unknown</span>';
        }
        document.getElementById('tblEquipos').innerHTML +=`
        <div class="col-12 mb-1">
            <div class="border-bottom divselect px-1" style="min-height:3rem;" onclick="FnEquipo(${equipo.id}); return false;">
                <div class="d-flex justify-content-between">
                    <p class='m-0'><span class="fw-bold">${equipo.nombre}</span> <span style="font-size: 13px; color:gray;">${equipo.famnombre}</span></p>
                    <p class='m-0'>${estado}</p>
                </div>
                <div style="font-size:13px;">${equipo.marca} ${equipo.modelo} <span class="d-none d-sm-block" style="color:gray;">${equipo.fecha}, ${equipo.km} KM, ${equipo.hm} HM</span></div>
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
        await FnBuscarEquipos2();
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
        await FnBuscarEquipos2()
    } catch (ex) {
        document.getElementById("btnPrimero").classList.add('d-none');
        showToast(ex.message, 'bg-danger');
    } finally {
        setTimeout(function(){vgLoader.classList.add('loader-full-hidden');},500);
    }
}

function FnModalAgregarEquipo(){
    $('#cbFamilia2').val(null).trigger('change');
    $('#cbEquipo2').val(null).trigger('change');
    const modalAgregarEquipo=new bootstrap.Modal(document.getElementById('modalAgregarEquipo'), {
        keyboard: false
    }).show();
}

async function FnAgregarEquipo(){
    vgLoader.classList.remove('loader-full-hidden');
    try {

        let json = {
            ownid : document.getElementById('cbEquipo2').value,
            nombre : document.getElementById('txtNombre2').value,
            marca : document.getElementById('txtMarca2').value,
            modelo : document.getElementById('txtModelo2').value,
            serie : document.getElementById('txtSerie2').value,
            placa : document.getElementById('txtPlaca2').value,
            km : document.getElementById('txtKm2').value,
            hm : document.getElementById('txtHm2').value,
            datos : document.getElementById('txtDatos2').value,
            famid : document.getElementById('txtFamId2').value,
            famownid : document.getElementById('txtFamOwnId2').value,
            famnombre : document.getElementById("txtFamNombre2").value
        }

        const response = await fetch('/gesman/insert/AgregarEquipo.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json'
            },
            body: JSON.stringify(json)
        });//.then(response=>response.text()).then((response)=>{console.log(response)}).catch(err=>console.log(err));

        if(!response.ok){throw new Error(`${response.status} ${response.statusText}`)}
        const datos = await response.json();
        if(!datos.res){throw new Error(datos.msg);}
        
        setTimeout(()=>{window.location.href='/gesman/Equipo.php?id='+datos.id;},1000);
    } catch (ex) {
        setTimeout(()=>{vgLoader.classList.add('loader-full-hidden');},500);
        showToast(ex.message, 'bg-danger');
    }
}

function FnEquipo(id){
    if(id > 0){
        window.location.href='/gesman/Equipo.php?id='+id;
    }
    return false;
}

function FnModoArbol(){
    window.location.href='/gesman/Equipos2.php';
}

const modalSubirArchivo=new bootstrap.Modal(document.getElementById('modalSubirArchivo'), {keyboard: false});

function FnModalSubirArchivo(){
    modalSubirArchivo.show();
}

const fileInput = document.getElementById('fileArchivo');
fileInput.addEventListener('change', function(event) {
    vgLoader.classList.remove('loader-full-hidden');
    try {
        const file = event.target.files[0];
        if (!isValidFileType(file)) {throw new Error('El archivo ' + file.name + 'es un tipo no permitido.');}
        if (!isValidFileSize(file)) { throw new Error('El tamaño del archivo ' + file.name + 'excede los 4MB.');}
    } catch (ex) {
        fileInput.value = '';
        showToast(ex.message, 'bg-danger');
    }finally{
        setTimeout(function(){vgLoader.classList.add('loader-full-hidden');},500);
    }
});

function isValidFileType(file) {
    const acceptedTypes = [
        'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet', // Excel(.xlsx)
        'application/vnd.ms-excel'// Excel(.xls)
    ];
    return acceptedTypes.includes(file.type);
}

function isValidFileSize(file) {
    const maxSize = 2 * 1024 * 1024; // 2MB en bytes
    return file.size <= maxSize;
}

async function FnProcesarArchivo(){
    vgLoader.classList.remove('loader-full-hidden');
    try {
        var archivo;
        if(document.getElementById('fileArchivo').files.length == 1){
            archivo = document.getElementById('fileArchivo').files[0];
        }else{
            throw new Error('No se reconoce el archivo');
        }

        const formData = new FormData();
        formData.append('archivo', archivo);
        formData.append('fecha', document.getElementById('dtpFecha').value);

        const response = await fetch('/gesman/update/ModificarEquiposOdometros.php', {
            method:'POST',
            body: formData
        });//.then(response=>response.text()).then((response)=>{console.log(response)}).catch(err=>console.log(err));

        if(!response.ok){throw new Error(`${response.status} ${response.statusText}`);}
        const datos = await response.json();
        if(!datos.res){throw new Error(datos.msg);}

        showToast(datos.msg, 'bg-success');
        setTimeout(function(){vgLoader.classList.add('loader-full-hidden');},500);
        setTimeout(function(){location.reload();},1000);
    } catch (ex) {
        showToast(ex.message, 'bg-danger');
        setTimeout(function(){vgLoader.classList.add('loader-full-hidden');},500);
    }
}

function FnDescargarPlantillaOdometros(){
    window.location.href='/gesman/descargas/DescargarPlantillaOdometro.php';
    return false;
}