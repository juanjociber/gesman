var Nombre='';
var Estado=0;
var PaginasTotal = 0;
var PaginaActual = 0;

const vgLoader=document.querySelector('.container-loader-full');

window.onload = function() {
    document.getElementById('MenuSistemas').classList.add('menu-activo','fw-bold');
    document.getElementById('MenuSistemasFlotas').classList.add('menu-activo','fw-bold');

    const datos = sessionStorage.getItem('gpem_flotas');
    if (datos){FnMostrarRegistros(JSON.parse(datos));}
    vgLoader.classList.add('loader-full-hidden');
};

async function FnBuscarFlotas(){
    vgLoader.classList.remove('loader-full-hidden');
    try {
        Nombre=document.getElementById('txtNombre').value;
        Estado=document.getElementById('cbEstado').value;
        PaginasTotal=0
        PaginaActual=0
        await FnBuscarFlotas2();
    } catch (ex) {
        showToast(ex.message, 'bg-danger');
    } finally {
        setTimeout(function(){vgLoader.classList.add('loader-full-hidden');},500);
    }
}

async function FnBuscarFlotas2(){
    try {
        const formData = new FormData();
        formData.append('nombre', Nombre);
        formData.append('estado', Estado);
        formData.append('pagina', PaginasTotal);
        const response = await fetch('/gesman/search/BuscarFlotas.php', {
            method:'POST',
            body: formData
        });//.then(response=>response.text()).then((response)=>{console.log(response)}).catch(err=>console.log(err));

        if (!response.ok) { throw new Error(`${response.status} ${response.statusText}`);}
        const datos = await response.json();
        if (!datos.res) { throw new Error(`${datos.msg}`); }

        sessionStorage.setItem('gpem_flotas', JSON.stringify(datos));
        FnMostrarRegistros(datos);
    } catch (ex) {
        throw ex;
    }
}

function FnMostrarRegistros(datos){
    document.getElementById('tblFlotas').innerHTML = '';
    let estado = '';
    datos.data.forEach(flota => {
        switch (flota.estado){
            case 1:
                estado='<span class="badge bg-danger">Inactivo</span>';
            break;
            case 2:
                estado='<span class="badge bg-success">Activo</span>';
            break;
            default:
                estado='<span class="badge bg-light text-dark">Unknown</span>';
        }
        document.getElementById('tblFlotas').innerHTML +=`
        <div class="col-12 mb-1">
            <div class="border-bottom divselect px-1" style="min-height:3rem;" dataid='${flota.id}' datanombre='${flota.nombre}' dataestado='${flota.estado}' onclick="FnModalModificarFlota(this); return false;">
                <div class="d-flex justify-content-between">
                    <p class='m-0'>${flota.nombre}</p>
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
        await FnBuscarFlotas2();
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
        await FnBuscarFlotas2()
    } catch (ex) {
        document.getElementById("btnPrimero").classList.add('d-none');
        showToast(ex.message, 'bg-danger');
    } finally {
        setTimeout(function(){vgLoader.classList.add('loader-full-hidden');},500);
    }
}

const modalAgregarFlota=new bootstrap.Modal(document.getElementById('modalAgregarFlota'), {
    keyboard: false
});

function FnModalAgregarFlota(){
    document.getElementById('txtNombre2').value='';
    modalAgregarFlota.show();
}

async function FnAgregarFlota(){
    vgLoader.classList.remove('loader-full-hidden');
    try {        
        const formData = new FormData();
        formData.append('nombre', document.getElementById('txtNombre2').value);
        const response = await fetch("/gesman/insert/AgregarFlota.php",{
            method: "POST",
            body: formData
        });//.then(response=>response.text()).then((response)=>{console.log(response)}).catch(err=>console.log(err));

        if(!response.ok){throw new Error(`${response.status} ${response.statusText}`)}
        const datos = await response.json();
        if(!datos.res){throw new Error(datos.msg);}

        showToast(datos.msg, 'bg-success');
        setTimeout(()=>{modalAgregarFlota.hide();},500);
    } catch (ex) {
        showToast(ex.message, 'bg-danger');
    }finally{
        setTimeout(()=>{vgLoader.classList.add('loader-full-hidden');},500);
    }
}

const modalModificarFlota=new bootstrap.Modal(document.getElementById('modalModificarFlota'), {
    keyboard: false
});

function FnModalModificarFlota(flota){
    document.getElementById('txtId3').value=flota.getAttribute('dataid');
    document.getElementById('txtNombre3').value=flota.getAttribute('datanombre');
    document.getElementById('cbEstado3').value=flota.getAttribute('dataestado');
    modalModificarFlota.show();
}

async function FnModificarFlota(){
    vgLoader.classList.remove('loader-full-hidden');
    try {        
        const formData = new FormData();
        formData.append('id', document.getElementById('txtId3').value);
        formData.append('nombre', document.getElementById('txtNombre3').value);
        formData.append('estado', document.getElementById('cbEstado3').value);
        const response = await fetch("/gesman/update/ModificarFlota.php",{
            method: "POST",
            body: formData
        });//.then(response=>response.text()).then((response)=>{console.log(response)}).catch(err=>console.log(err));

        if(!response.ok){throw new Error(`${response.status} ${response.statusText}`)}
        const datos = await response.json();
        if(!datos.res){throw new Error(datos.msg);}

        showToast(datos.msg, 'bg-success');
        setTimeout(()=>{modalModificarFlota.hide();},500);
    } catch (ex) {
        showToast(ex.message, 'bg-danger');
    }finally{
        setTimeout(()=>{vgLoader.classList.add('loader-full-hidden');},500);
    }
}