const loader=document.querySelector('.container-loader-full');

window.addEventListener('load', function(){
    document.getElementById('MenuEmpresas').classList.add('menu-activo','fw-bold');
    loader.classList.add('loader-full-hidden')
});

function FnModalCambiarEmpresa(id, nombre){
    document.getElementById('txtId').value = id;
    document.getElementById('txtEmpresa').value = nombre;
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

        loader.classList.add('loader-full-hidden');
        showToast(datos.msg, 'bg-success');

        setTimeout(function(){location.reload();},500);
    } catch (ex) {
        showToast(ex.message, 'bg-danger');
        setTimeout(function(){loader.classList.add('loader-full-hidden');},500);
    }
}