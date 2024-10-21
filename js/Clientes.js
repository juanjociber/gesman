var Nombre='';
var Estado=0;
var PaginasTotal = 0;
var PaginaActual = 0;

const vgLoader=document.querySelector('.container-loader-full');

window.onload = function() {
    document.getElementById('MenuSistemas').classList.add('menu-activo','fw-bold');
    document.getElementById('MenuSistemasClientes').classList.add('menu-activo','fw-bold');

    const datos = localStorage.getItem('gpem_clientes');
    if (datos){FnMostrarRegistros(JSON.parse(datos));}
    vgLoader.classList.add('loader-full-hidden');
};

async function FnBuscarClientes(){
    vgLoader.classList.remove('loader-full-hidden');
    try {
        Nombre=document.getElementById('txtNombre').value;
        Estado=document.getElementById('cbEstado').value;
        PaginasTotal=0
        PaginaActual=0
        await FnBuscarClientes2();
    } catch (ex) {
        showToast(ex.message, 'bg-danger');
    } finally {
        setTimeout(function(){vgLoader.classList.add('loader-full-hidden');},500);
    }
}

async function FnBuscarClientes2(){
    try {
        const formData = new FormData();
        formData.append('nombre', Nombre);
        formData.append('estado', Estado);
        formData.append('pagina', PaginasTotal);
        const response = await fetch('/gesman/search/BuscarClientes.php', {
            method:'POST',
            body: formData
        });//.then(response=>response.text()).then((response)=>{console.log(response)}).catch(err=>console.log(err));

        if (!response.ok) { throw new Error(`${response.status} ${response.statusText}`);}
        const datos = await response.json();
        if (!datos.res) { throw new Error(`${datos.msg}`); }

        localStorage.setItem('gpem_clientes', JSON.stringify(datos));
        FnMostrarRegistros(datos);
    } catch (ex) {
        throw ex;
    }
}

function FnMostrarRegistros(datos){
    document.getElementById('tblClientes').innerHTML = '';
    let estado = '';
    datos.data.forEach(cliente => {
        switch (cliente.estado){
            case 1:
                estado='<span class="badge bg-danger">Inactivo</span>';
            break;
            case 2:
                estado='<span class="badge bg-success">Activo</span>';
            break;
            default:
                estado='<span class="badge bg-light text-dark">Unknown</span>';
        }
        document.getElementById('tblClientes').innerHTML +=`
        <div class="col-12 mb-1">
            <div class="border-bottom divselect px-1" style="min-height:2.2rem;" onclick="FnModalModificarCliente(${cliente.id}); return false;">
                <div class="d-flex justify-content-between">
                    <p class='m-0'><span class="fw-bold">${cliente.ruc}</span> <span class="text-secondary">${cliente.nombre}</span></p>
                    <p class='m-0'>${estado}</p>
                </div>
                <div>${cliente.direccion}</div>
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
        await FnBuscarClientes2();
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
        await FnBuscarClientes2()
    } catch (ex) {
        document.getElementById("btnPrimero").classList.add('d-none');
        showToast(ex.message, 'bg-danger');
    } finally {
        setTimeout(function(){vgLoader.classList.add('loader-full-hidden');},500);
    }
}

const modalAgregarCliente=new bootstrap.Modal(document.getElementById('modalAgregarCliente'),{keyboard:false});

function FnModalAgregarCliente(){
    modalAgregarCliente.show();
}

async function FnAgregarCliente(){
    vgLoader.classList.remove('loader-full-hidden');
    try {        
        const formData = new FormData();
        formData.append('ruc', document.getElementById('txtRuc2').value);
        formData.append('nombre', document.getElementById('txtNombre2').value);        
        formData.append('alias', document.getElementById('txtAlias2').value);
        formData.append('direccion', document.getElementById('txtDireccion2').value);
        formData.append('odoid', document.getElementById('txtOdoId2').value);
        formData.append('almid', document.getElementById('txtAlmId2').value);
        const response = await fetch("/gesman/insert/AgregarCliente.php",{
            method: "POST",
            body: formData
        });//.then(response=>response.text()).then((response)=>{console.log(response)}).catch(err=>console.log(err));

        if(!response.ok){throw new Error(`${response.status} ${response.statusText}`)}
        const datos = await response.json();
        if(!datos.res){throw new Error(datos.msg);}

        showToast(datos.msg, 'bg-success');
        setTimeout(()=>{modalAgregarCliente.hide();},500);
    } catch (ex) {
        showToast(ex.message, 'bg-danger');
    }finally{
        setTimeout(()=>{vgLoader.classList.add('loader-full-hidden');},500);
    }
}

const modalModificarCliente=new bootstrap.Modal(document.getElementById('modalModificarCliente'),{keyboard:false});

async function FnModalModificarCliente(id){
    vgLoader.classList.remove('loader-full-hidden');
    try {
        const formData = new FormData();
        formData.append('id', id);
        const response = await fetch("/gesman/search/BuscarCliente.php",{
            method: "POST",
            body: formData
        });//.then(response=>response.text()).then((response)=>{console.log(response)}).catch(err=>console.log(err));

        if(!response.ok){throw new Error(`${response.status} ${response.statusText}`)}
        const datos = await response.json();
        if(!datos.res){throw new Error(datos.msg);}

        document.getElementById('txtId3').value=datos.data.id;
        document.getElementById('txtRuc3').value=datos.data.ruc;
        document.getElementById('txtNombre3').value=datos.data.nombre;
        document.getElementById('txtAlias3').value=datos.data.alias;
        document.getElementById('txtDireccion3').value=datos.data.direccion;
        document.getElementById('txtOdoId3').value=datos.data.odoid;
        document.getElementById('txtAlmId3').value=datos.data.almid;
        document.getElementById('cbEstado3').value=datos.data.estado;
        setTimeout(()=>{modalModificarCliente.show()},500);
    } catch (ex) {
        showToast(ex.message, 'bg-danger');
    }finally{
        setTimeout(()=>{vgLoader.classList.add('loader-full-hidden');},500);
    }    
}

async function FnModificarCliente(){
    vgLoader.classList.remove('loader-full-hidden');
    try {        
        const formData = new FormData();
        formData.append('id', document.getElementById('txtId3').value);
        formData.append('nombre', document.getElementById('txtNombre3').value);        
        formData.append('alias', document.getElementById('txtAlias3').value);
        formData.append('direccion', document.getElementById('txtDireccion3').value);
        formData.append('odoid', document.getElementById('txtOdoId3').value);
        formData.append('almid', document.getElementById('txtAlmId3').value);
        formData.append('estado', document.getElementById('cbEstado3').value);
        const response = await fetch("/gesman/update/ModificarCliente.php",{
            method: "POST",
            body: formData
        });//.then(response=>response.text()).then((response)=>{console.log(response)}).catch(err=>console.log(err));

        if(!response.ok){throw new Error(`${response.status} ${response.statusText}`)}
        const datos = await response.json();
        if(!datos.res){throw new Error(datos.msg);}

        showToast(datos.msg, 'bg-success');
        setTimeout(()=>{modalModificarCliente.hide();},500);
    } catch (ex) {
        showToast(ex.message, 'bg-danger');
    }finally{
        setTimeout(()=>{vgLoader.classList.add('loader-full-hidden');},500);
    }
}