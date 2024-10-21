var Nombre='';
var Estado=0;
var PaginasTotal = 0;
var PaginaActual = 0;

const vgLoader=document.querySelector('.container-loader-full');

window.onload = function() {
    document.getElementById('MenuSistemas').classList.add('menu-activo','fw-bold');
    document.getElementById('MenuSistemasOrigenes').classList.add('menu-activo','fw-bold');

    const datos = sessionStorage.getItem('gpem_origenes');
    if (datos){FnMostrarRegistros(JSON.parse(datos));}
    vgLoader.classList.add('loader-full-hidden');
};

async function FnBuscarOrigenes(){
    vgLoader.classList.remove('loader-full-hidden');
    try {
        Nombre=document.getElementById('txtNombre').value;
        Estado=document.getElementById('cbEstado').value;
        PaginasTotal=0
        PaginaActual=0
        await FnBuscarOrigenes2();
    } catch (ex) {
        showToast(ex.message, 'bg-danger');
    } finally {
        setTimeout(function(){vgLoader.classList.add('loader-full-hidden');},500);
    }
}

async function FnBuscarOrigenes2(){
    try {
        const formData = new FormData();
        formData.append('nombre', Nombre);
        formData.append('estado', Estado);
        formData.append('pagina', PaginasTotal);
        const response = await fetch('/gesman/search/BuscarOrigenes.php', {
            method:'POST',
            body: formData
        });//.then(response=>response.text()).then((response)=>{console.log(response)}).catch(err=>console.log(err));

        if (!response.ok) { throw new Error(`${response.status} ${response.statusText}`);}
        const datos = await response.json();
        if (!datos.res) { throw new Error(`${datos.msg}`); }

        sessionStorage.setItem('gpem_origenes', JSON.stringify(datos));
        FnMostrarRegistros(datos);
    } catch (ex) {
        throw ex;
    }
}

function FnMostrarRegistros(datos){
    document.getElementById('tblOrigenes').innerHTML = '';
    let estado = '';
    datos.data.forEach(origen => {
        switch (origen.estado){
            case 1:
                estado='<span class="badge bg-danger">Inactivo</span>';
            break;
            case 2:
                estado='<span class="badge bg-success">Activo</span>';
            break;
            default:
                estado='<span class="badge bg-light text-dark">Unknown</span>';
        }
        document.getElementById('tblOrigenes').innerHTML +=`
        <div class="col-12 mb-1">
            <div class="border-bottom divselect px-1" style="min-height:3rem;" dataid='${origen.id}' datanombre='${origen.nombre}' dataestado='${origen.estado}' onclick="FnModalModificarOrigen(this); return false;">
                <div class="d-flex justify-content-between">
                    <p class='m-0'>${origen.nombre}</p>
                    <p class='m-0'>${estado}</p>
                </div>
            </div>
        </div>`;
    });
    FnPaginacion(datos.pag);
}

function FnPaginacion(cantidad){
    try {
        PaginaActual += 1;
        if (cantidad == 2) {
            PaginasTotal += 2;
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
        await FnBuscarOrigenes2();
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
        await FnBuscarOrigenes2()
    } catch (ex) {
        document.getElementById("btnPrimero").classList.add('d-none');
        showToast(ex.message, 'bg-danger');
    } finally {
        setTimeout(function(){vgLoader.classList.add('loader-full-hidden');},500);
    }
}

const modalAgregarOrigen=new bootstrap.Modal(document.getElementById('modalAgregarOrigen'), {
    keyboard: false
});

function FnModalAgregarOrigen(){
    document.getElementById('txtNombre2').value='';
    modalAgregarOrigen.show();
}

async function FnAgregarOrigen(){
    vgLoader.classList.remove('loader-full-hidden');
    try {        
        const formData = new FormData();
        formData.append('nombre', document.getElementById('txtNombre2').value);
        const response = await fetch("/gesman/insert/AgregarOrigen.php",{
            method: "POST",
            body: formData
        });//.then(response=>response.text()).then((response)=>{console.log(response)}).catch(err=>console.log(err));

        if(!response.ok){throw new Error(`${response.status} ${response.statusText}`)}
        const datos = await response.json();
        if(!datos.res){throw new Error(datos.msg);}

        showToast(datos.msg, 'bg-success');
        setTimeout(()=>{modalAgregarOrigen.hide();},500);
    } catch (ex) {
        showToast(ex.message,'bg-danger');
    }finally{
        setTimeout(()=>{vgLoader.classList.add('loader-full-hidden');},500);
    }
}

const modalModificarOrigen=new bootstrap.Modal(document.getElementById('modalModificarOrigen'), {
    keyboard: false
});

function FnModalModificarOrigen(origen){
    document.getElementById('txtId3').value=origen.getAttribute('dataid');
    document.getElementById('txtNombre3').value=origen.getAttribute('datanombre');
    document.getElementById('cbEstado3').value=origen.getAttribute('dataestado');
    modalModificarOrigen.show();
}

async function FnModificarOrigen(){
    vgLoader.classList.remove('loader-full-hidden');
    try {        
        const formData = new FormData();
        formData.append('id', document.getElementById('txtId3').value);
        formData.append('nombre', document.getElementById('txtNombre3').value);
        formData.append('estado', document.getElementById('cbEstado3').value);
        const response = await fetch("/gesman/update/ModificarOrigen.php",{
            method: "POST",
            body: formData
        });//.then(response=>response.text()).then((response)=>{console.log(response)}).catch(err=>console.log(err));

        if(!response.ok){throw new Error(`${response.status} ${response.statusText}`)}
        const datos = await response.json();
        if(!datos.res){throw new Error(datos.msg);}

        showToast(datos.msg, 'bg-success');
        setTimeout(()=>{modalModificarOrigen.hide();},500);
    } catch (ex) {
        showToast(ex.message, 'bg-danger');
    }finally{
        setTimeout(()=>{vgLoader.classList.add('loader-full-hidden');},500);
    }
}