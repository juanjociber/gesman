var Nombre='';
var Estado=0;
var PaginasTotal = 0;
var PaginaActual = 0;

const loader=document.querySelector('.container-loader-full');

window.addEventListener('load', function(){
    document.getElementById('MenuSistemas').classList.add('menu-activo','fw-bold');
    document.getElementById('MenuSistemasFamilias').classList.add('menu-activo','fw-bold');
    loader.classList.add('loader-full-hidden');

    $(document).ready(function() {
        $('#cbOwn2').select2({
            dropdownParent: $('#modalAgregarFamilia'),
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
        }).on('select2:select',function(e){
            document.getElementById('txtOwnId2').value=e.params.data.id;
            document.getElementById('txtOwnRuta2').value=e.params.data.text;
        }).on('change', function(e){
            var valorSeleccionado = $(this).val();
            if (valorSeleccionado === null || valorSeleccionado.length === 0) {
                document.getElementById('txtOwnId2').value=0;
                document.getElementById('txtOwnRuta2').value='';
            }
        });

        $('#cbOwn3').select2({
            dropdownParent: $('#modalModificarFamilia'),
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
        }).on('select2:select',function(e){
            document.getElementById('txtOwnId3').value=e.params.data.id;
            document.getElementById('txtOwnRuta3').value=e.params.data.text;
        }).on('change', function(e){
            var valorSeleccionado = $(this).val();
            if (valorSeleccionado === null || valorSeleccionado.length === 0) {
                document.getElementById('txtOwnId3').value=0;
                document.getElementById('txtOwnRuta3').value='';
            }
        });
    });
});

async function FnBuscarFamilias(){
    loader.classList.remove('loader-full-hidden');
    try {
        Nombre=document.getElementById('txtNombre').value;
        Estado=document.getElementById('cbEstado').value;
        PaginasTotal=0
        PaginaActual=0
        await FnBuscarFamilias2();
    } catch (ex) {
        showToast(ex.message, 'bg-danger');
    } finally {
        setTimeout(function(){loader.classList.add('loader-full-hidden');},500);
    }
}

async function FnBuscarFamilias2(){
    try {
        const formData = new FormData();
        formData.append('nombre', Nombre);
        formData.append('estado', Estado);
        formData.append('pagina', PaginasTotal);
        const response = await fetch('/gesman/search/BuscarFamilias.php', {
            method:'POST',
            body: formData
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
    document.getElementById('tblFamilias').innerHTML = '';
    let estado = '';
    datos.data.forEach(elem => {
        switch (elem.estado){
            case 1:
                estado='<span class="badge bg-danger">Inactivo</span>';
            break;
            case 2:
                estado='<span class="badge bg-success">Activo</span>';
            break;
            default:
                estado='<span class="badge bg-light text-dark">Unknown</span>';
        }
        document.getElementById('tblFamilias').innerHTML +=`
        <div class="col-12 mb-1">
            <div class="border-bottom divselect px-1" style="min-height:3rem;" dataid='${elem.id}' dataownid='${elem.ownid}' datanombre='${elem.nombre}' dataruta='${elem.ruta}' dataestado='${elem.estado}' onclick="FnModalModificarFamilia(this); return false;">
                <div class="d-flex justify-content-between">
                    <p class='m-0 fw-bold'>${elem.nombre}</p>
                    <p class='m-0 text-secondary'>${estado}</p>
                </div>
                <div class="text-secondary" style="font-size:12px;">=> ${elem.ruta}</div>
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
    loader.classList.remove('loader-full-hidden');
    try {
        await FnBuscarFamilias2();
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
        PaginasTotal = 0
        PaginaActual = 0
        await FnBuscarFamilias2()
    } catch (ex) {
        document.getElementById("btnPrimero").classList.add('d-none');
        showToast(ex.message, 'bg-danger');
    } finally {
        setTimeout(function(){loader.classList.add('loader-full-hidden');},500);
    }
}

const modalAgregarFamilia=new bootstrap.Modal(document.getElementById('modalAgregarFamilia'), {
    keyboard: false
});

function FnModalAgregarFamilia(){
    $('#cbOwn2').val(null).trigger('change');
    document.getElementById('txtNombre2').value='';
    document.getElementById('txtOwnId2').value=0;
    document.getElementById('txtOwnRuta2').value='';
    modalAgregarFamilia.show();
}

async function FnAgregarFamilia(){
    loader.classList.remove('loader-full-hidden');
    try {        
        let json = {
            ownid : document.getElementById('txtOwnId2').value,
            nombre : document.getElementById('txtNombre2').value,
            ownruta:document.getElementById('txtOwnRuta2').value
        }

        const response = await fetch("/gesman/insert/AgregarFamilia.php",{
            method: 'POST',
            headers: {
                'Content-Type': 'application/json'
            },
            body: JSON.stringify(json)
        });//.then(response=>response.text()).then((response)=>{console.log(response)}).catch(err=>console.log(err));

        if(!response.ok){throw new Error(`${response.status} ${response.statusText}`)}
        const datos = await response.json();
        if(!datos.res){throw new Error(datos.msg);}

        showToast(datos.msg, 'bg-success');
        setTimeout(()=>{modalAgregarFamilia.hide();},500);
    } catch (ex) {
        showToast(ex.message,'bg-danger');
    }finally{
        setTimeout(()=>{loader.classList.add('loader-full-hidden');},500);
    }
}

const modalModificarFamilia=new bootstrap.Modal(document.getElementById('modalModificarFamilia'), {
    keyboard: false
});

function FnModalModificarFamilia(sistema){
    try {
        let lastElem = sistema.getAttribute('dataruta').lastIndexOf("/");
        if(lastElem!==-1){
            let ownRuta = sistema.getAttribute('dataruta').slice(0, lastElem);

            var nuevaOpcion = document.createElement("option");
            nuevaOpcion.value = sistema.getAttribute('dataownid');
            nuevaOpcion.text = ownRuta;
            document.getElementById('cbOwn3').appendChild(nuevaOpcion);
            nuevaOpcion.selected=true;

            document.getElementById('txtOwnId3').value=sistema.getAttribute('dataownid');
            document.getElementById('txtOwnRuta3').value=ownRuta;
        }else{
            $('#cbOwn3').val(null).trigger('change');
            document.getElementById('txtOwnId3').value=0;
            document.getElementById('txtOwnRuta3').value='';
        }

        document.getElementById('txtId3').value=sistema.getAttribute('dataid');
        document.getElementById('txtNombre3').value=sistema.getAttribute('datanombre');
        document.getElementById('cbEstado3').value=sistema.getAttribute('dataestado');
        modalModificarFamilia.show();   
    } catch (ex) {
        showToast(ex.message,'bg-danger');
    }
}

async function FnModificarSistema(){
    loader.classList.remove('loader-full-hidden');
    try {
        let json = {
            id: document.getElementById('txtId3').value,
            ownid: document.getElementById('txtOwnId3').value,
            nombre: document.getElementById('txtNombre3').value,
            ownruta: document.getElementById('txtOwnRuta3').value,
            estado: document.getElementById('cbEstado3').value
        }

        const response = await fetch("/gesman/update/ModificarFamilia.php",{
            method: 'POST',
            headers: {
                'Content-Type': 'application/json'
            },
            body: JSON.stringify(json)
        });//.then(response=>response.text()).then((response)=>{console.log(response)}).catch(err=>console.log(err));

        if(!response.ok){throw new Error(`${response.status} ${response.statusText}`)}
        const datos = await response.json();
        if(!datos.res){throw new Error(datos.msg);}

        showToast(datos.msg, 'bg-success');
        setTimeout(()=>{modalModificarFamilia.hide();},500);
    } catch (ex) {
        showToast(ex.message, 'bg-danger');
    }finally{
        setTimeout(()=>{loader.classList.add('loader-full-hidden');},500);
    }
}