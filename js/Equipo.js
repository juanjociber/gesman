const vgLoader=document.querySelector('.container-loader-full');

window.onload = function() {
    document.getElementById('MenuSistemas').classList.add('menu-activo','fw-bold');
    document.getElementById('MenuSistemasEquipos').classList.add('menu-activo','fw-bold');
    vgLoader.classList.add('loader-full-hidden');
};

function FnModalAnularEquipo(){
    const modalAnularEquipo=new bootstrap.Modal(document.getElementById('modalAnularEquipo'), {
        keyboard: false
    }).show();
};

async function FnAnularEquipo(){
    vgLoader.classList.remove('loader-full-hidden');
    try {
        const formData = new FormData();
        formData.append('id', document.getElementById('txtId').value);
        const response = await fetch('/gesman/update/AnularEquipo.php', {
            method:'POST',
            body: formData
        });//.then(response=>response.text()).then((response)=>{console.log(response)}).catch(err=>console.log(err));

        if(!response.ok){throw new Error(`${response.status} ${response.statusText}`);}
        const datos = await response.json();
        if(!datos.res){throw new Error(datos.msg);} 

        setTimeout(function(){location.reload();},500);

    } catch (ex) {
        showToast(ex.message, 'bg-danger');
        setTimeout(function(){vgLoader.classList.add('loader-full-hidden');},1000);
    }
}

function FnEditarEquipo(){
    let id = document.getElementById('txtId').value;
    if(id > 0){
        window.location.href='/gesman/EditarEquipo.php?id='+id;
    }
    return false;
}

function FnEquipos(){
    window.location.href='/gesman/Equipos.php';
    return false;
}

function FnModalVerArchivo(archivo){
    vgLoader.classList.remove('loader-full-hidden');
    try {
        const modalVerArchivo=new bootstrap.Modal(document.getElementById('modalVerArchivo'), {keyboard: false});
        let tipo=archivo.getAttribute('datatipo');
        let nombre=archivo.getAttribute('datanombre');

        if(archivo.getAttribute('datatitulo')){
            document.getElementById('pTitulo').innerHTML=archivo.getAttribute('datatitulo');
        }else{
            document.getElementById('pTitulo').innerHTML=tipo +' '+nombre;
        }

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
        showToast(ex.message, 'bg-danger');
    }finally{
        setTimeout(function(){vgLoader.classList.add('loader-full-hidden');},500);
    }
};

/*async function FnVerArchivo(archivo){
    vgLoader.classList.remove('loader-full-hidden');
    try{
        var fileContainer=document.getElementById('fileContainer');
        if (fileContainer.childElementCount === 1) {
            var hijoUnico = fileContainer.firstElementChild;
            fileContainer.removeChild(hijoUnico);
        }
        
        const url=new URL('http://localhost/gpemsac/intranet/modulos/descargas/DescargarArchivo.php');
        url.searchParams.append('nombre', archivo.getAttribute('datanombre'));
        url.searchParams.append('tipo', archivo.getAttribute('datatipo'));

        const response = await fetch(url, {
            method: 'GET',
        });//.then(response=>response.text()).then((response)=>{console.log(response)}).catch(err=>console.log(err));
        
        if (!response.ok){throw new Error(response.status + ' ' + response.statusText);}
        const datos = await response.json();
        if (!datos.res) {throw new Error(datos.msg);}

        document.getElementById('pTitulo').innerText=archivo.getAttribute('datatitulo');

        switch (archivo.getAttribute('datatipo')) {
            case 'IMG':
                var nuevaImagen = document.createElement('img');
                nuevaImagen.src = `data:${datos.tipo};base64,${datos.archivo}`;
                nuevaImagen.alt = "Imagen";
                nuevaImagen.classList.add('img-fluid');
                fileContainer.appendChild(nuevaImagen);
                break;
            case 'PDF':
                var nuevoPdf = document.createElement("embed");
                nuevoPdf.src = `data:${datos.tipo};base64,${datos.archivo}`;
                nuevoPdf.type = "application/pdf";
                nuevoPdf.width = "100%";
                nuevoPdf.height = "600px";
                fileContainer.appendChild(nuevoPdf);
                break;        
            default:
                throw new Error('El tipo no esta disponible.');
        }
    } catch (ex) {
        showToast(ex.message, 'bg-danger');
    } finally{
        setTimeout(function(){vgLoader.classList.add('loader-full-hidden'); }, 1000);
    }
}*/
