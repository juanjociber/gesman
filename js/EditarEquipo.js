const loader=document.querySelector('.container-loader-full');

window.addEventListener('load', function(){
    document.getElementById('MenuOrdenes').classList.add('menu-activo','fw-bold');
    loader.classList.add('loader-full-hidden');    
});

async function FnModificarEquipo(){
    loader.classList.remove('loader-full-hidden');
    try {
        let json = {
            id: document.getElementById('txtId').value,
            marca: document.getElementById('txtMarca').value,
            modelo: document.getElementById('txtModelo').value,
            serie: document.getElementById('txtSerie').value,
            placa: document.getElementById('txtPlaca').value,
            ubicacion: document.getElementById('txtUbicacion').value,
            datos: document.getElementById('txtDatos').value
        }

        const response = await fetch('/gesman/update/ModificarEquipo.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json'
            },
            body: JSON.stringify(json)
        });//.then(response=>response.text()).then((response)=>{console.log(response)}).catch(err=>console.log(err));

        if (!response.ok) { throw new Error(`${response.status} ${response.statusText}`);}
        const datos = await response.json();
        if (!datos.res) { throw new Error(`${datos.msg}`); }

        setTimeout(function(){location.reload();},500);
    } catch (ex) {
        showToast(ex.message, 'bg-danger');
        setTimeout(function(){loader.classList.add('loader-full-hidden');},1000);
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