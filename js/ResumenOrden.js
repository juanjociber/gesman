const vgLoader=document.querySelector('.container-loader-full');

window.onload = function() {
    document.getElementById('MenuOrdenes').classList.add('menu-activo','fw-bold');
    vgLoader.classList.add('loader-full-hidden');
};

const modalFinalizarOrden=new bootstrap.Modal(document.getElementById('modalFinalizarOrden'), {
    keyboard: false
});

function FnModalFinalizarOrden(){
    document.getElementById('msjFinalizarOrden').innerHTML = '';
    modalFinalizarOrden.show();
};

async function FnFinalizarOrden(){
    vgLoader.classList.remove('loader-full-hidden');
    try {
        const formData = new FormData();
        formData.append('id', document.getElementById('txtId').value);

        const response = await fetch('/gesman/update/FinalizarOrden.php', {
            method:'POST',
            body: formData
        });

        if(!response.ok){
            throw new Error(`${response.status} ${response.statusText}`)
        }

        const datos = await response.json();
        
        if(datos.res){
            location.reload();
        }else{
            throw new Error(datos.msg)
        }
    } catch (error) {
        document.getElementById('msjFinalizarOrden').innerHTML = `<div class="alert alert-danger m-0 p-1 text-center" role="alert">${error}</div>`;
    }
    
    setTimeout(function () {
        vgLoader.classList.add('loader-full-hidden');
    }, 500);

    return false;
}

function FnEditarOrden(){
    let orden = document.getElementById('txtId').value;
    if(orden > 0){
        window.location.href='/gesman/EditarOrden.php?orden='+orden;
    }
    return false;
}

function FnListarOrdenes(){
    window.location.href='/gesman/Ordenes.php';
    return false;
}

function FnModalVerArchivo(nombre, tipo){
    vgLoader.classList.remove('loader-full-hidden');
    try {
        const modalVerArchivo=new bootstrap.Modal(document.getElementById('modalVerArchivo'), {keyboard: false});
        document.getElementById('pNombre').innerHTML = tipo+' '+nombre;

        var fileContainer=document.getElementById('fileContainer');
        if (fileContainer.childElementCount === 1) {
            var hijoUnico = fileContainer.firstElementChild;
            fileContainer.removeChild(hijoUnico);
        }
        
        switch (tipo) {
            case 'IMG':
                var nuevaImagen = document.createElement('img');
                nuevaImagen.src = '/mycloud/gesman/files/'+nombre;
                nuevaImagen.alt = "Imagen";
                nuevaImagen.classList.add('img-fluid');
                fileContainer.appendChild(nuevaImagen);
                break;
            case 'PDF':
                var nuevoPdf = document.createElement("embed");
                nuevoPdf.src = '/mycloud/gesman/files/'+nombre;
                nuevoPdf.type = "application/pdf";
                nuevoPdf.width = "100%";
                nuevoPdf.height = "600px";
                fileContainer.appendChild(nuevoPdf);
                break;        
            default:
                throw new Error('El tipo no esta disponible.');
        }
        modalVerArchivo.show();   
    } catch (error) {
        alert(error.message)
    }
    setTimeout(function(){vgLoader.classList.add('loader-full-hidden');},500);
    return false;
};
