const vgLoader=document.querySelector('.container-loader-full');

window.onload = function() {
    document.getElementById('MenuOrdenes').classList.add('menu-activo','fw-bold');
    vgLoader.classList.add('loader-full-hidden');
};

const modalAgregarImagen=new bootstrap.Modal(document.getElementById('modalAgregarImagen'), {
    keyboard: false
})

function FnModalAgregarImagen(){
    modalAgregarImagen.show();
}

const MAX_WIDTH = 1080;
const MAX_HEIGHT = 720;
const MIME_TYPE = "image/jpeg";
const QUALITY = 0.7;
const $divImagen = document.getElementById("divImagen");
const fileInput = document.getElementById('fileImagen');

fileInput.addEventListener('change', function(event) {
    vgLoader.classList.remove('loader-full-hidden');
    try {
        const file = event.target.files[0];
        if (file) {
            if (!isValidFileType(file)) {
                fileInput.value='';
                throw new Error("La Imágen no es válida.")
            }

            while ($divImagen.firstChild) {
                $divImagen.removeChild($divImagen.firstChild);
            }
            displayImage(file);
        }
    } catch (ex) {
        showToast(ex.message, 'bg-danger');
    }finally{
        setTimeout(function(){vgLoader.classList.add('loader-full-hidden');}, 500);
    }
});

function isValidFileType(file) {
    const acceptedTypes = ['image/jpeg', 'image/png'];
    return acceptedTypes.includes(file.type);
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

async function FnAgregarImagen(){
    vgLoader.classList.remove('loader-full-hidden');
    try {
        if(!document.getElementById('canvas')){throw new Error('No hay Imágen para guardar.');}
        const formData = new FormData();
        formData.append('id', document.getElementById('txtId').value);
        formData.append('imagen', document.querySelector("#canvas").toDataURL("image/jpeg"));
        const response = await fetch('/gesman/insert/AgregarEquipoImagen.php',{
            method:'POST',
            body: formData
        });//then(response=>response.text()).then((response)=>{console.log(response)}).catch(err=>console.log(err));

        if(!response.ok){throw new Error(`${response.status} ${response.statusText}`);}
        const datos = await response.json();
        if(!datos.res){throw new Error(datos.msg);}
        setTimeout(function(){location.reload();},1000);
    } catch (ex) {
        showToast(ex.message,'bg-danger');
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