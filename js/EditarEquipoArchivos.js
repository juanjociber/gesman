const vgLoader=document.querySelector('.container-loader-full');

window.onload = function() {
    document.getElementById('MenuSistemas').classList.add('menu-activo','fw-bold');
    document.getElementById('MenuSistemasEquipos').classList.add('menu-activo','fw-bold');
    vgLoader.classList.add('loader-full-hidden');
};

function FnModalAgregarArchivo(){
    const modalAgregarArchivo=new bootstrap.Modal(document.getElementById('modalAgregarArchivo'), {
        keyboard: false
    }).show();
}

const MAX_WIDTH = 1080;
const MAX_HEIGHT = 720;
const MIME_TYPE = "image/jpeg";
const QUALITY = 0.7;
const $divImagen = document.getElementById("divImagen");

document.getElementById('fileArchivo').addEventListener('change', function(event) {

    vgLoader.classList.remove('loader-full-hidden');
    try {
        const file = event.target.files[0];

        if (!isValidFileType(file)) {
            document.getElementById('fileArchivo').value='';
            throw new Error('El archivo ' + file.name + 'es un tipo no permitido.');
        }

        if (!isValidFileSize(file)) { 
            document.getElementById('fileArchivo').value='';
            throw new Error('El tamaño del archivo ' + file.name + 'excede los 3MB.');
        }

        while ($divImagen.firstChild) {
            $divImagen.removeChild($divImagen.firstChild);
        }

        if (file.type.startsWith('image/')) {
            displayImage(file);
        }
    } catch (ex) {
        showToast(ex.message, 'bg-danger');
    }finally{
        setTimeout(function(){vgLoader.classList.add('loader-full-hidden');},500);
    }
});

function isValidFileType(file) {
    const acceptedTypes = ['image/jpeg', 'image/png', 'image/gif', 'application/pdf'];
    return acceptedTypes.includes(file.type);
}

function isValidFileSize(file) {
    const maxSize = 3 * 1024 * 1024; // 4MB en bytes
    return file.size <= maxSize;
}

function displayImage(file) {
    const reader = new FileReader();
    reader.onload = function(event) {
        const imageUrl = event.target.result;
        const canvas = document.createElement('canvas');
        canvas.style.border = '1px solid black';

        $divImagen.appendChild(canvas);
        const context = canvas.getContext('2d');

        const image = new Image();
        image.onload = function() {
            const [newWidth, newHeight] = calculateSize(image, MAX_WIDTH, MAX_HEIGHT);
            canvas.width = newWidth;
            canvas.height = newHeight;
            canvas.id="canvas";
            context.drawImage(image, 0, 0, newWidth, newHeight);

            // Agregar texto como marca de agua
            context.strokeStyle = 'rgba(216, 216, 216, 0.7)';// color del texto (blanco con opacidad)
            context.font = '15px Verdana'; // fuente y tamaño del texto
            context.strokeText("GPEM SAC", 10, newHeight-10);// texto y posición

            canvas.toBlob(
                (blob) => {
                    // Handle the compressed image. es. upload or save in local state
                    displayInfo('Original: ', file);
                    displayInfo('Comprimido: ', blob);
                },
                MIME_TYPE,
                QUALITY
            );

        };
        image.src = imageUrl;
    };
    reader.readAsDataURL(file);
}

function calculateSize(img, maxWidth, maxHeight) {
    let width = img.width;
    let height = img.height;
    if (width > height) {
        if (width > maxWidth) {
            height = Math.round((height * maxWidth) / width);
            width = maxWidth;
        }
    } else {
        if (height > maxHeight) {
            width = Math.round((width * maxHeight) / height);
            height = maxHeight;
        }
    }
    return [width, height];
}

function displayInfo(label, file) {
    const p = document.createElement('p');
    p.classList.add('text-secondary', 'm-0', 'fs-6');
    p.innerText = `${label} ${readableBytes(file.size)}`;
    $divImagen.append(p);
}

function readableBytes(bytes) {
    const i = Math.floor(Math.log(bytes) / Math.log(1024)),
    sizes = ['B', 'KB', 'MB', 'GB', 'TB', 'PB', 'EB', 'ZB', 'YB'];
    return (bytes / Math.pow(1024, i)).toFixed(2) + ' ' + sizes[i];
}

async function FnAgregarArchivo(){
    vgLoader.classList.remove('loader-full-hidden');
    try {
        var archivo;

        if(document.getElementById('canvas')){
            archivo = document.querySelector("#canvas").toDataURL("image/jpeg");
        }else if(document.getElementById('fileArchivo').files.length == 1){
            archivo = document.getElementById('fileArchivo').files[0];
        }else{
            throw new Error('No se reconoce el archivo');
        }

        const formData = new FormData();
        formData.append('refid', document.getElementById('txtId').value);
        formData.append('tabla', 'EQU');
        formData.append('titulo', document.getElementById('txtTitulo').value);
        formData.append('archivo', archivo);

        const response = await fetch('/gesman/insert/AgregarArchivo.php',{
            method:'POST',
            body: formData
        });

        if(!response.ok){throw new Error(`${response.status} ${response.statusText}`);}
        const datos = await response.json();
        if(!datos.res){throw new Error(datos.msg);}

        setTimeout(function(){location.reload();},1000);
    } catch (ex) {
        showToast(ex.message, 'bg-danger');
        setTimeout(function(){vgLoader.classList.add('loader-full-hidden');},500);
    }
}

async function FnEliminarArchivo(id){
    vgLoader.classList.remove('loader-full-hidden');
    try {
        const formData = new FormData();
        formData.append('id',id)
        formData.append('refid', document.getElementById('txtId').value)

        const response = await fetch('/gesman/delete/EliminarArchivo.php', {
            method:'POST',
            body: formData
        });

        if(!response.ok){throw new Error(`Error del servidor: ${response.status} ${response.statusText}`);}
        const datos = await response.json();
        if(!datos.res){throw new Error(datos.msg);}

        setTimeout(function(){location.reload();},500);
    } catch (ex) {
        showToast(ex.message, 'bg-danger');
        setTimeout(function(){vgLoader.classList.add('loader-full-hidden');},500);
    }
}

function FnEquipo(){
    let id=document.getElementById('txtId').value;
    if(id>0){
        window.location.href='/gesman/Equipo.php?id='+id;
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
            document.getElementById('pNombre').innerHTML=archivo.getAttribute('datatitulo');
        }else{
            document.getElementById('pNombre').innerHTML=tipo +' '+nombre;
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