var Equipo='';
var Estado=0;
var Flota=0;
var PaginasTotal = 0;
var PaginaActual = 0;

const vgLoader=document.querySelector('.container-loader-full');

window.onload = function() {
    document.getElementById('MenuSistemas').classList.add('menu-activo','fw-bold');
    document.getElementById('MenuSistemasEquipos').classList.add('menu-activo','fw-bold');

    const datos = localStorage.getItem('gpem_equipos');
    if (datos){FnMostrarRegistros(JSON.parse(datos));}
    vgLoader.classList.add('loader-full-hidden');
};

async function FnBuscarEquipos(){
    vgLoader.classList.remove('loader-full-hidden');
    try {
        Equipo=document.getElementById('txtEquipo').value;
        Flota=document.getElementById('cbFlota').value;
        Estado=document.getElementById('cbEstado').value;
        PaginasTotal=0
        PaginaActual=0
        await FnBuscarEquipos2();
    } catch (ex) {
        showToast(ex.message, 'bg-danger');
    } finally {
        setTimeout(function(){vgLoader.classList.add('loader-full-hidden');},500);
    }
}

async function FnBuscarEquipos2(){
    try {
        const formData = new FormData();
        formData.append('floid', Flota);
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

        localStorage.setItem('gpem_equipos', JSON.stringify(datos));
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
            <div class="border-bottom divselect px-1" style="min-height:2.2rem;" onclick="FnEquipo(${equipo.id}); return false;">
                <div class="d-flex justify-content-between">
                    <p class='m-0'><span class="fw-bold">${equipo.codigo}</span> <span style="font-size: 13px; color:gray;">${equipo.flonombre}</span></p>
                    <p class='m-0'>${estado}</p>
                </div>
                <div>${equipo.marca} ${equipo.modelo}</div>
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
    const modalAgregarEquipo=new bootstrap.Modal(document.getElementById('modalAgregarEquipo'), {
        keyboard: false
    }).show();
}

async function FnAgregarEquipo(){
    vgLoader.classList.remove('loader-full-hidden');
    try {        
        const formData = new FormData();
        formData.append('floid', document.getElementById('cbFlota1').value);
        formData.append('flonombre', document.getElementById("cbFlota1").options[document.getElementById("cbFlota1").selectedIndex].text);
        formData.append('codigo', document.getElementById('txtCodigo1').value);
        formData.append('nombre', document.getElementById('txtNombre1').value);        
        formData.append('marca', document.getElementById('txtMarca1').value);
        formData.append('modelo', document.getElementById('txtModelo1').value);
        formData.append('placa', document.getElementById('txtPlaca1').value);
        formData.append('serie', document.getElementById('txtSerie1').value);
        formData.append('motor', document.getElementById('txtMotor1').value);
        formData.append('transmision', document.getElementById('txtTransmision1').value);
        formData.append('diferencial', document.getElementById('txtDiferencial1').value);
        formData.append('anio', document.getElementById('txtAnio1').value);
        formData.append('fabricante', document.getElementById('txtFabricante1').value);
        formData.append('procedencia', document.getElementById('txtProcedencia1').value);
        formData.append('ubicacion', document.getElementById('txtUbicacion1').value);
        formData.append('datos', document.getElementById('txtDatos1').value);
        const response = await fetch("/gesman/insert/AgregarEquipo.php",{
            method: "POST",
            body: formData
        });//.then(response=>response.text()).then((response)=>{console.log(response)}).catch(err=>console.log(err));

        if(!response.ok){throw new Error(`${response.status} ${response.statusText}`)}
        const datos = await response.json();
        if(!datos.res){throw new Error(datos.msg);}
        setTimeout(()=>{window.location.href='/gesman/Equipos.php?id='+datos.id;},1000);
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