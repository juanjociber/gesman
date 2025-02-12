var Nombre='';
var PaginasTotal = 0;
var PaginaActual = 0;
let typingTimer;

const loader=document.querySelector('.container-loader-full');

window.addEventListener('load', async function(){
    document.getElementById('MenuEmpresas').classList.add('menu-activo','fw-bold');
    loader.classList.add('loader-full-hidden')
    FnBuscarEmpresas('');
});

function FnModalCambiarEmpresa(elem){
    document.getElementById('txtId').value = elem.getAttribute('dataid');
    document.getElementById('txtEmpresa').value = elem.getAttribute('datanombre');
    const modalCambiarEmpresa=new bootstrap.Modal(document.getElementById('modalCambiarEmpresa'), {
        keyboard: false
    }).show();
}

async function FnCambiarEmpresa(){
    loader.classList.remove('loader-full-hidden');
    try {        
        const formData = new FormData();
        formData.append('id', document.getElementById('txtId').value);
        const response = await fetch("/gesman/update/EstablecerEmpresa.php", {
            method: "POST",
            body: formData
        });//.then(response=>response.text()).then((response)=>{console.log(response)}).catch(err=>console.log(err));
        
        if (!response.ok) { throw new Error(`${response.status} ${response.statusText}`);}
        const datos = await response.json();
        if (!datos.res) { throw new Error(`${datos.msg}`); }

        sessionStorage.clear();
        loader.classList.add('loader-full-hidden');
        showToast(datos.msg, 'bg-success');

        setTimeout(function(){location.reload();},500);
    } catch (ex) {
        showToast(ex.message, 'bg-danger');
        setTimeout(function(){loader.classList.add('loader-full-hidden');},500);
    }
}

async function FnBuscarEmpresas(input){
    console.log(input);
    loader.classList.remove('loader-full-hidden');
    try {
        Nombre=input;
        PaginasTotal=0
        PaginaActual=0
        await FnBuscarEmpresas2();
    } catch (ex) {
        showToast(ex.message, 'bg-danger');
    } finally {
        setTimeout(function(){loader.classList.add('loader-full-hidden');},500);
    }
}

async function FnBuscarEmpresas2(){
    try {
        const formData = new FormData();
        formData.append('nombre', Nombre);
        formData.append('pagina', PaginasTotal);
        const response = await fetch('/gesman/search/BuscarEmpresas.php', {
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
    document.getElementById('tblEmpresas').innerHTML = '';
    datos.data.forEach(cliente=>{
        let set='far fa-circle';
        if(cliente.id==document.getElementById('txtCliId').value){
            set='fas fa-check-circle';
        }
        document.getElementById('tblEmpresas').innerHTML +=`
        <div class="col-12 mb-1">
            <div class="border-bottom border-dark divselect p-1" style="min-height:2.2rem;" dataid="${cliente.id}" datanombre="${cliente.alias}" onclick="FnModalCambiarEmpresa(this); return false;">
                <div class="d-flex justify-content-between">
                    <p class='m-0 text-secondary' style="font-size:14px;">${cliente.ruc} | ${cliente.alias}</p>
                    <i class="${set} fs-5 text-primary"></i>
                </div>
                <div>${cliente.nombre}</div>
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
        await FnBuscarEmpresas2();
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
        await FnBuscarEmpresas2()
    } catch (ex) {
        document.getElementById("btnPrimero").classList.add('d-none');
        showToast(ex.message, 'bg-danger');
    } finally {
        setTimeout(function(){loader.classList.add('loader-full-hidden');},500);
    }
}

document.getElementById('txtBuscar').addEventListener('input', function() {
    clearTimeout(typingTimer);
    let input = this.value;
    typingTimer = setTimeout(() => {
        (async()=>{
            await FnBuscarEmpresas(input);
        })();
    }, 800);
});