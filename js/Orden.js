const vgLoader=document.querySelector('.container-loader-full');

window.onload = function() {
    document.getElementById('MenuOrdenes').classList.add('menu-activo','fw-bold');
    vgLoader.classList.add('loader-full-hidden');
};

function FnModalAgregarInforme(){
    const modalAgregarInforme=new bootstrap.Modal(document.getElementById('modalAgregarInforme'), {
        keyboard: false
    }).show();
}

async function FnAgregarInforme(){
    vgLoader.classList.remove('loader-full-hidden');
    try {        
        const formData = new FormData();
        formData.append('ordid', document.getElementById('txtId').value);
        formData.append('fecha', document.getElementById('txtFecha').value);
        formData.append('actividad', document.getElementById('txtActividad').value);
        const response = await fetch("/gesman/insert/AgregarOrdenInforme.php", {
            method: "POST",
            body: formData
        });//.then(response=>response.text()).then((response)=>{console.log(response)}).catch(err=>console.log(err));

        if(!response.ok){throw new Error(`${response.status} ${response.statusText}`)}
        const datos = await response.json();
        if(!datos.res){throw new Error(datos.msg);}
        setTimeout(()=>{window.location.href='/informes/Informe.php?id='+datos.id;},1000);
    } catch (ex) {
        setTimeout(()=>{vgLoader.classList.add('loader-full-hidden');},500);
        showToast(ex.message, 'bg-danger');
    }
}

function FnModalFinalizarOrden(){
    const modalFinalizarOrden=new bootstrap.Modal(document.getElementById('modalFinalizarOrden'), {
        keyboard: false
    }).show();
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

        if(!response.ok){throw new Error(`${response.status} ${response.statusText}`);}
        const datos = await response.json();
        if(!datos.res){throw new Error(datos.msg);} 

        setTimeout(function(){location.reload();},500);

    } catch (ex) {
        showToast(ex.message, 'bg-danger');
        setTimeout(function(){vgLoader.classList.add('loader-full-hidden');},1000);
    }
}

function FnEditarOrden(){
    let id = document.getElementById('txtId').value;
    if(id > 0){
        window.location.href='/gesman/EditarOrden.php?id='+id;
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
    } catch (ex) {
        alert(ex.message)
    }
    setTimeout(function(){vgLoader.classList.add('loader-full-hidden');},500);
    return false;
};

function FnOrdenResumen(id){
    try {
        const ancho=1000;
        const alto=600;
        let x=(screen.width/2)-(ancho/2);
        let y=(screen.height/2)-(alto/2);
        window.open('/gesman/OrdenResumen.php?id='+id, 'ORDID'+id, 'width=' + ancho + ', height=' + alto + ', left=' + x + ', top=' + y +', Scrollbars=YES'+'');
    } catch (ex) {
        alert(ex.message)
    }
}
