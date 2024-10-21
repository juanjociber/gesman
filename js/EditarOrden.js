const vgLoader=document.querySelector('.container-loader-full');

window.onload = function() {
    document.getElementById('MenuOrdenes').classList.add('menu-activo','fw-bold');
    vgLoader.classList.add('loader-full-hidden');    
};

const modalBuscarRecursos=new bootstrap.Modal(document.getElementById('modalBuscarRecursos'), {
    keyboard: false
});

function FnModalBuscarRecursos(tabla){
    document.getElementById('txtTabla').value = tabla;
    document.getElementById('txtBuscar').vale = '';
    document.getElementById("tblRecursos").innerHTML = '<div class="col-12 fst-italic">Haga clic en buscar para obtener resultados.</div>';
    modalBuscarRecursos.show();
};

async function FnBuscarRecursos(){
    vgLoader.classList.remove('loader-full-hidden');
    try {
        let tabla=document.getElementById('txtTabla').value;
        let url='';
        switch (tabla) {
            case 'sistema':
                url='/gesman/search/BuscarSistemas.php';  
                break;
            case 'origen':
                url='/gesman/search/BuscarOrigenes.php';
                break;
            case 'supervisor':
                url='/gesman/search/BuscarSupervisores.php';
                break;
            case 'contacto':
                url='/gesman/search/BuscarContactos.php';
                break;
            default:
                throw new Error("No se reconoce la Tabla.");
        }

        const formData = new FormData();
        formData.append('nombre', document.getElementById('txtBuscar').value);
        formData.append('estado', 2);
        formData.append('pagina', 0);

        const response = await fetch(url, {
            method:'POST',
            body: formData
        });//.then(response=>response.text()).then((response)=>{console.log(response)}).catch(err=>console.log(err));

        if(!response.ok){throw new Error(`${response.status} ${response.statusText}`);}
        const datos = await response.json();
        if(!datos.res){throw new Error(datos.msg);}

        document.getElementById('tblRecursos').innerHTML = '';

        datos.data.forEach(elem => {
            document.getElementById("tblRecursos").innerHTML += `
            <div class="col-12 border-bottom mb-1 p-2 divselect" dataid='${elem.id}' datanombre='${elem.nombre}' onclick=FnCargarRecurso(this); return false>
                ${elem.id} - ${elem.nombre}
            </div>`;
        });

    } catch (ex) {
        showToast(ex.message, 'bg-danger');
    }finally{
        setTimeout(function(){vgLoader.classList.add('loader-full-hidden');},500);
    }
}

function FnCargarRecurso(recurso) {
    switch (document.getElementById('txtTabla').value) {
        case 'sistema':            
            document.getElementById('txtSisId').value = recurso.getAttribute('dataid');
            document.getElementById('txtSisNombre').value = recurso.getAttribute('datanombre');
            break;
        case 'origen':
            document.getElementById('txtOriId').value = recurso.getAttribute('dataid');
            document.getElementById('txtOriNombre').value = recurso.getAttribute('datanombre');
            break;
        case 'supervisor':
            document.getElementById('txtSupervisor').value = recurso.getAttribute('datanombre');
            break;
        case 'contacto':
            document.getElementById('txtCliContacto').value = recurso.getAttribute('datanombre');
            break;
        default:
            break;
    }
    modalBuscarRecursos.hide();
}

async function FnModificarOrden(){
    vgLoader.classList.remove('loader-full-hidden');
    try {
        const formData = new FormData();
        formData.append('id', document.getElementById('txtId').value);
        formData.append('fecha', document.getElementById('txtFecha').value);
        formData.append('sisid', document.getElementById('txtSisId').value);
        formData.append('sisnombre', document.getElementById('txtSisNombre').value);
        formData.append('oriid', document.getElementById('txtOriId').value);
        formData.append('orinombre', document.getElementById('txtOriNombre').value);
        formData.append('supervisor', document.getElementById('txtSupervisor').value);
        formData.append('clicontacto', document.getElementById('txtCliContacto').value);
        formData.append('equkm', document.getElementById('txtEquKm').value);
        formData.append('equhm', document.getElementById('txtEquHm').value);
        formData.append('actividades', document.getElementById('txtActividades').value);
        formData.append('trabajos', document.getElementById('txtTrabajos').value);
        formData.append('observaciones', document.getElementById('txtObservaciones').value);

        const response = await fetch('/gesman/update/ModificarOrden.php', {
            method:'POST',
            body: formData
        });//.then(response=>response.text()).then((response)=>{console.log(response)}).catch(err=>console.log(err));

        if (!response.ok) { throw new Error(`${response.status} ${response.statusText}`);}
        const datos = await response.json();
        if (!datos.res) { throw new Error(`${datos.msg}`); }

        setTimeout(function(){location.reload();},500);
    } catch (ex) {
        showToast(ex.message, 'bg-danger');
        setTimeout(function(){vgLoader.classList.add('loader-full-hidden');},1000);
    }
}

function FnOrden(){
    let id=document.getElementById('txtId').value;
    if(id>0){
        window.location.href='/gesman/Orden.php?id='+id;
    }
}

function FnOrdenes(){
    window.location.href='/gesman/Ordenes.php';
    return false;
}