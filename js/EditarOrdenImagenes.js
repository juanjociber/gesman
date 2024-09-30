const vgLoader=document.querySelector('.container-loader-full');

window.onload = function() {
    document.getElementById('MenuOrdenes').classList.add('menu-activo','fw-bold');
    vgLoader.classList.add('loader-full-hidden');
};

const modalAgregarImagen=new bootstrap.Modal(document.getElementById('modalAgregarImagen'), {
    keyboard: false
})

function FnModalAgregarArchivo(){
    document.getElementById('msjAgregarImagen').innerHTML = '';
    modalAgregarImagen.show();
}

const MAX_WIDTH = 1080;
const MAX_HEIGHT = 720;
const MIME_TYPE = "image/jpeg";
const QUALITY = 0.7;
//const $fileImagen = document.getElementById("fileImagen");
const $divImagen = document.getElementById("divImagen");

document.getElementById('fileImagen').addEventListener('change', function(event) {
    vgLoader.classList.remove('loader-full-hidden');
    
    const file = event.target.files[0];

    if (!isValidFileType(file)) {
        console.log('El archivo', file.name, 'Tipo de archivo no permitido.');
    }

    if (!isValidFileSize(file)) {
        console.log('El archivo', file.name, 'El tamaño del archivo excede los 3MB.');
    }

    while ($divImagen.firstChild) {
        $divImagen.removeChild($divImagen.firstChild);
    }

    if (file.type.startsWith('image/')) {
        displayImage(file);
    }

    console.log('Nombre del archivo:', file.name);
    console.log('Tipo del archivo:', file.type);
    console.log('Tamaño del archivo:', file.size, 'bytes');

    setTimeout(function() {vgLoader.classList.add('loader-full-hidden');}, 1000)
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

/*
$fileImagen.onchange = function (ev) {
    const file = ev.target.files[0]; // get the file
    const blobURL = URL.createObjectURL(file);
    const img = new Image();
    img.src = blobURL;
    img.onerror = function () {
        URL.revokeObjectURL(this.src);
        // Handle the failure properly
        console.log("No se pudo cargar la imágen.");
    };
    img.onload = function () {
        URL.revokeObjectURL(this.src);        
        if ( $divImagen.hasChildNodes()){
            while ($divImagen.childNodes.length >= 1){
                $divImagen.removeChild($divImagen.firstChild);
            }
        }

        const [newWidth, newHeight] = calculateSize(img, MAX_WIDTH, MAX_HEIGHT);
        const canvas = document.createElement("canvas");
        canvas.width = newWidth;
        canvas.height = newHeight;
        canvas.id="canvas";
        const ctx = canvas.getContext("2d");
        ctx.drawImage(img, 0, 0, newWidth, newHeight);
        ctx.font = "15px Verdana";
        ctx.strokeStyle = 'rgba(216, 216, 216, 0.7)';
        ctx.strokeText("GPEM SAC.", 10, newHeight-10);
        canvas.toBlob(
            (blob) => {
                // Handle the compressed image. es. upload or save in local state
                displayInfo('Original: ', file);
                displayInfo('Comprimido: ', blob);
            },
            MIME_TYPE,
            QUALITY
        );
        console.log(canvas);
        $divImagen.append(canvas);
    };
};*/


function calculateSize(img, maxWidth, maxHeight) {
    let width = img.width;
    let height = img.height;
    // calculate the width and height, constraining the proportions
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

// Utility functions for demo purpose
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

async function FnAgregarImagen(){
    vgLoader.classList.remove('loader-full-hidden');
    try {
        var archivo;

        if(document.getElementById('canvas')){
            archivo = document.querySelector("#canvas").toDataURL("image/jpeg");
        }else if(document.getElementById('fileImagen').files.length == 1){
            archivo = fileOrCanvasData = document.getElementById('fileImagen').files[0];
        }else{
            throw new Error('No se reconoce el archivo');
        }

        const formData = new FormData();
        formData.append('refid', document.getElementById('txtIdOt').value);
        formData.append('tabla', 'ORD');
        formData.append('descripcion', document.getElementById('txtDescripcion').value);
        formData.append('archivo', archivo);

        const response = await fetch('/gesman/update/ModificarOrdenImagen.php', {
            method:'POST',
            body: formData
        });

        if(!response.ok){throw new Error(`${response.status} ${response.statusText}`);}
        const datos = await response.json();
        if(!datos.res){throw new Error(datos.msg);}

        setTimeout(function() {location.reload();}, 1000)

    } catch (error) {
        document.getElementById('msjAgregarImagen').innerHTML = `<div class="alert alert-danger m-0 p-1 text-center" role="alert">${error.message}</div>`;
        setTimeout(function() {vgLoader.classList.add('loader-full-hidden');}, 1000)
    }
}

async function FnEliminarImagen(id){
    vgLoader.classList.remove('loader-full-hidden');
    try {
        const formData = new FormData();
        formData.append('id',id)
        formData.append('refid', document.getElementById('txtIdOt').value);

        const response = await fetch('/gesman/update/ModificarOrdenImagen.php', {
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

function FnModalVerArchivo(archivo){
    //vgLoader.classList.remove('loader-full-hidden');
    try {
        const modalVerArchivo=new bootstrap.Modal(document.getElementById('modalVerArchivo'), {keyboard: false});
        let tipo=archivo.getAttribute('datatipo');
        let nombre=archivo.getAttribute('datanombre');
        document.getElementById('pNombre').innerHTML = tipo +' '+nombre;

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
    }finally{
        setTimeout(function(){vgLoader.classList.add('loader-full-hidden');},500);
    }
};
