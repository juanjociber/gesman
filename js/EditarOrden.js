const vgLoader=document.querySelector('.container-loader-full');

window.onload = function() {
    document.getElementById('MenuOrdenes').classList.add('menu-activo','fw-bold');
    vgLoader.classList.add('loader-full-hidden');    
};

const modalBuscarRecursos=new bootstrap.Modal(document.getElementById('modalBuscarRecursos'), {
    keyboard: false
});

function FnModalBuscarRecursos(recurso){
    document.getElementById('txtRecurso').value = recurso;
    document.getElementById('txtBuscar').vale = '';
    document.getElementById('msjBuscarRecursos').innerHTML = '';
    document.getElementById("divRecursos").innerHTML = '<div class="col-12 fst-italic">Haga clic en buscar para obtener resultados.</div>';
    modalBuscarRecursos.show();
};

async function FnBuscarRecursos(){
    vgLoader.classList.remove('loader-full-hidden');
    document.getElementById('divRecursos').innerHTML = '';
    try {
        const formData = new FormData();
        formData.append('recurso', document.getElementById('txtRecurso').value);
        formData.append('nombre', document.getElementById('txtBuscar').value);

        const response = await fetch('/gesman/search/ListarRecursos.php', {
            method:'POST',
            body: formData
        });

        if(!response.ok){
            throw new Error(`Error del servidor: ${response.status} ${response.statusText}`)
        }

        const datos = await response.json();
        
        if(datos.res){
            datos.data.forEach(clase => {
                document.getElementById("divRecursos").innerHTML += `
                <div class="col-12 border-bottom mb-1 p-2 divselect" dataId='${clase.id}' dataNombre='${clase.nombre}' onclick=FnCargarRecurso(this); return false>
                    ${clase.id} => ${clase.nombre}
                </div>`;
            });
        }else{
            document.getElementById('msjBuscarRecursos').innerHTML = `<div class="alert alert-danger m-0 p-1 text-center" role="alert">${datos.msg}</div>`;
        }
    } catch (error) {
        document.getElementById('msjBuscarRecursos').innerHTML = `<div class="alert alert-danger m-0 p-1 text-center" role="alert">${error}</div>`;
    }

    await new Promise((resolve, reject) => {
        setTimeout(function () {
            vgLoader.classList.add('loader-full-hidden');
        }, 500)
    });
}

function FnCargarRecurso(recurso) {
    switch (document.getElementById('txtRecurso').value) {
        case 'sistema':            
            document.getElementById('txtIdSistema').value = recurso.getAttribute('dataId');
            document.getElementById('txtSistema').value = recurso.getAttribute('dataNombre');
            break;
        case 'origen':
            document.getElementById('txtIdOrigen').value = recurso.getAttribute('dataId');
            document.getElementById('txtOrigen').value = recurso.getAttribute('dataNombre');
            break;
        case 'supervisor':
            document.getElementById('txtSupervisor').value = recurso.getAttribute('dataNombre');
            break;
        case 'contacto':
            document.getElementById('txtContacto').value = recurso.getAttribute('dataNombre');
            break;
        default:
            document.getElementById('msjBuscarRecursos').innerHTML = `<div class="alert alert-danger m-0 p-1 text-center" role="alert">No se reconoce el recurso.</div>`;
            break;
    }
    modalBuscarRecursos.hide();
    return false;
}

async function FnModificarOrden(){
    vgLoader.classList.remove('loader-full-hidden');
    try {
        const formData = new FormData();
        formData.append('idot', document.getElementById('txtIdOt').value);
        formData.append('fecha', document.getElementById('txtFecha').value);
        formData.append('idsistema', document.getElementById('txtIdSistema').value);
        formData.append('sistema', document.getElementById('txtSistema').value);
        formData.append('idorigen', document.getElementById('txtIdOrigen').value);
        formData.append('origen', document.getElementById('txtOrigen').value);
        formData.append('supervisor', document.getElementById('txtSupervisor').value);
        formData.append('contacto', document.getElementById('txtContacto').value);
        formData.append('km', document.getElementById('txtKm').value);
        formData.append('actividad', document.getElementById('txtActividad').value);
        formData.append('descripcion', document.getElementById('txtDescripcion').value);
        formData.append('observacion', document.getElementById('txtObservacion').value);

        const response = await fetch('/gesman/update/ModificarOrden.php', {
            method:'POST',
            body: formData
        });

        if(!response.ok){
            throw new Error(`Error del servidor: ${response.status} ${response.statusText}`)
        }

        const datos = await response.json();
        
        if(datos.res){
            location.reload();
        }else{
            throw new Error(datos.msg)
        }
    } catch (error) {
        alert(error);
    }

    await new Promise((resolve, reject) => {
        setTimeout(function () {
            vgLoader.classList.add('loader-full-hidden');
        }, 500)
    });

    return false;
}

function FnResumenOrden(){
    orden = document.getElementById('txtIdOt').value;
    if(orden > 0){
        window.location.href='/gesman/ResumenOrden.php?orden='+orden;
    }
    return false;
}

function FnListarOrdenes(){
    window.location.href='/gesman/Ordenes.php';
    return false;
}