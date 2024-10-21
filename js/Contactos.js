var Nombre='';
var Estado=0;
var PaginasTotal = 0;
var PaginaActual = 0;

const vgLoader=document.querySelector('.container-loader-full');

window.onload = function() {
    document.getElementById('MenuSistemas').classList.add('menu-activo','fw-bold');
    document.getElementById('MenuSistemasContactos').classList.add('menu-activo','fw-bold');

    const datos = sessionStorage.getItem('gpem_contactos');
    if (datos){FnMostrarRegistros(JSON.parse(datos));}
    vgLoader.classList.add('loader-full-hidden');
};

async function FnBuscarContactos(){
    vgLoader.classList.remove('loader-full-hidden');
    try {
        Nombre=document.getElementById('txtNombre').value;
        Estado=document.getElementById('cbEstado').value;
        PaginasTotal=0
        PaginaActual=0
        await FnBuscarContactos2();
    } catch (ex) {
        showToast(ex.message, 'bg-danger');
    } finally {
        setTimeout(function(){vgLoader.classList.add('loader-full-hidden');},500);
    }
}

async function FnBuscarContactos2(){
    try {
        const formData = new FormData();
        formData.append('nombre', Nombre);
        formData.append('estado', Estado);
        formData.append('pagina', PaginasTotal);
        const response = await fetch('/gesman/search/BuscarContactos.php', {
            method:'POST',
            body: formData
        });//.then(response=>response.text()).then((response)=>{console.log(response)}).catch(err=>console.log(err));

        if (!response.ok) { throw new Error(`${response.status} ${response.statusText}`);}
        const datos = await response.json();
        if (!datos.res) { throw new Error(`${datos.msg}`); }

        sessionStorage.setItem('gpem_contactos', JSON.stringify(datos));
        FnMostrarRegistros(datos);
    } catch (ex) {
        throw ex;
    }
}

function FnMostrarRegistros(datos){
    document.getElementById('tblContactos').innerHTML = '';
    let estado = '';
    datos.data.forEach(contacto=>{
        switch (contacto.estado){
            case 1:
                estado='<span class="badge bg-danger">Inactivo</span>';
            break;
            case 2:
                estado='<span class="badge bg-success">Activo</span>';
            break;
            default:
                estado='<span class="badge bg-light text-dark">Unknown</span>';
        }
        document.getElementById('tblContactos').innerHTML +=`
        <div class="col-12 mb-1">
            <div class="border-bottom divselect px-1" style="min-height:3rem;" dataid='${contacto.id}' datanombre='${contacto.nombre}' dataestado='${contacto.estado}' onclick="FnModalModificarContacto(this); return false;">
                <div class="d-flex justify-content-between">
                    <p class='m-0'>${contacto.nombre}</p>
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
        await FnBuscarContactos2();
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
        await FnBuscarContactos2()
    } catch (ex) {
        document.getElementById("btnPrimero").classList.add('d-none');
        showToast(ex.message, 'bg-danger');
    } finally {
        setTimeout(function(){vgLoader.classList.add('loader-full-hidden');},500);
    }
}

const modalAgregarContacto=new bootstrap.Modal(document.getElementById('modalAgregarContacto'), {
    keyboard: false
});

function FnModalAgregarContacto(){
    document.getElementById('txtNombre2').value='';
    modalAgregarContacto.show();
}

async function FnAgregarContacto(){
    vgLoader.classList.remove('loader-full-hidden');
    try {        
        const formData = new FormData();
        formData.append('nombre', document.getElementById('txtNombre2').value);
        const response = await fetch("/gesman/insert/AgregarContacto.php",{
            method: "POST",
            body: formData
        });//.then(response=>response.text()).then((response)=>{console.log(response)}).catch(err=>console.log(err));

        if(!response.ok){throw new Error(`${response.status} ${response.statusText}`)}
        const datos = await response.json();
        if(!datos.res){throw new Error(datos.msg);}

        showToast(datos.msg, 'bg-success');
        setTimeout(()=>{modalAgregarContacto.hide();},500);
    } catch (ex) {
        showToast(ex.message,'bg-danger');
    }finally{
        setTimeout(()=>{vgLoader.classList.add('loader-full-hidden');},500);
    }
}

const modalModificarContacto=new bootstrap.Modal(document.getElementById('modalModificarContacto'), {
    keyboard: false
});

function FnModalModificarContacto(contacto){
    document.getElementById('txtId3').value=contacto.getAttribute('dataid');
    document.getElementById('txtNombre3').value=contacto.getAttribute('datanombre');
    document.getElementById('cbEstado3').value=contacto.getAttribute('dataestado');
    modalModificarContacto.show();
}

async function FnModificarContacto(){
    vgLoader.classList.remove('loader-full-hidden');
    try {        
        const formData = new FormData();
        formData.append('id', document.getElementById('txtId3').value);
        formData.append('nombre', document.getElementById('txtNombre3').value);
        formData.append('estado', document.getElementById('cbEstado3').value);
        const response = await fetch("/gesman/update/ModificarContacto.php",{
            method: "POST",
            body: formData
        });//.then(response=>response.text()).then((response)=>{console.log(response)}).catch(err=>console.log(err));

        if(!response.ok){throw new Error(`${response.status} ${response.statusText}`)}
        const datos = await response.json();
        if(!datos.res){throw new Error(datos.msg);}

        showToast(datos.msg, 'bg-success');
        setTimeout(()=>{modalModificarContacto.hide();},500);
    } catch (ex) {
        showToast(ex.message, 'bg-danger');
    }finally{
        setTimeout(()=>{vgLoader.classList.add('loader-full-hidden');},500);
    }
}