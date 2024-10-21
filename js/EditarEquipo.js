const vgLoader=document.querySelector('.container-loader-full');

window.onload = function() {
    document.getElementById('MenuOrdenes').classList.add('menu-activo','fw-bold');
    vgLoader.classList.add('loader-full-hidden');    
};

const modalAgregarFlota=new bootstrap.Modal(document.getElementById('modalAgregarFlota'), {
    keyboard: false
});

function FnModalAgregarFlota(){
    document.getElementById('txtBuscar').vale = '';
    document.getElementById("tblFlotas").innerHTML = '<div class="col-12 fst-italic">Haga clic en buscar para obtener resultados.</div>';
    modalAgregarFlota.show();
};

async function FnBuscarFlotas(){
    vgLoader.classList.remove('loader-full-hidden');
    try {
        const formData = new FormData();
        formData.append('nombre', document.getElementById('txtBuscar').value);

        const response = await fetch('/gesman/search/ListarFlotas.php', {
            method:'POST',
            body: formData
        });

        if(!response.ok){throw new Error(`${response.status} ${response.statusText}`);}
        const datos = await response.json();
        if(!datos.res){throw new Error(datos.msg);}

        document.getElementById('tblFlotas').innerHTML = '';

        datos.data.forEach(clase => {
            document.getElementById("tblFlotas").innerHTML += `
            <div class="col-12 border-bottom mb-1 p-2 divselect" dataId='${clase.id}' dataNombre='${clase.nombre}' onclick=FnCargarFlota(this); return false>
                ${clase.id} => ${clase.nombre}
            </div>`;
        });

    } catch (ex) {
        showToast(ex.message, 'bg-danger');
    }finally{
        setTimeout(function(){vgLoader.classList.add('loader-full-hidden');},500);
    }
}

function FnCargarFlota(recurso) {
    document.getElementById('txtFloId').value = recurso.getAttribute('dataId');
    document.getElementById('txtFloNombre').value = recurso.getAttribute('dataNombre');
    modalAgregarFlota.hide();
}

async function FnModificarEquipo(){
    vgLoader.classList.remove('loader-full-hidden');
    try {
        const formData = new FormData();
        formData.append('id', document.getElementById('txtId').value);
        formData.append('floid', document.getElementById('txtFloId').value);
        formData.append('nombre', document.getElementById('txtNombre').value);
        formData.append('flonombre', document.getElementById('txtFloNombre').value);
        formData.append('marca', document.getElementById('txtMarca').value);
        formData.append('modelo', document.getElementById('txtModelo').value);
        formData.append('placa', document.getElementById('txtPlaca').value);
        formData.append('serie', document.getElementById('txtSerie').value);
        formData.append('motor', document.getElementById('txtMotor').value);
        formData.append('transmision', document.getElementById('txtTransmision').value);
        formData.append('diferencial', document.getElementById('txtDiferencial').value);
        formData.append('anio', document.getElementById('txtAnio').value);
        formData.append('fabricante', document.getElementById('txtFabricante').value);
        formData.append('procedencia', document.getElementById('txtProcedencia').value);
        formData.append('ubicacion', document.getElementById('txtUbicacion').value);
        formData.append('datos', document.getElementById('txtDatos').value);

        const response = await fetch('/gesman/update/ModificarEquipo.php', {
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

function FnEquipo(){
    let id=document.getElementById('txtId').value;
    if(id>0){
        window.location.href='/gesman/Equipo.php?id='+id;
    }
}

function FnEquipos(){
    window.location.href='/gesman/Equipos.php';
    return false;
}