const vgLoader=document.querySelector('.container-loader-full');

window.onload = function() {
    document.getElementById('MenuOrdenes').classList.add('menu-activo','fw-bold');
    vgLoader.classList.add('loader-full-hidden');
};

$(document).ready(function() {
    $('#cbPersonal').select2({
        dropdownParent: $('#modalAgregarTareo'),//Agregar el select a un modal
        width: 'resolve', //Personalizar el alto del select, aplicar estilo.
        ajax: {
            delay: 450, //Tiempo de demora para buscar
            url: '/gesman/search/ListarPersonal.php',
            type: 'POST',
            dataType: 'json',
            data: function (params){
                return {
                    nombre: params.term // parametros a enviar al server. params.term captura lo que se escribe en el input
                };
            },
            processResults: function (data) {
                console.log(data)
                return {
                    results: data.data //Retornar el json obtenido
                }
            },
            cache: true
        },
        placeholder: 'Seleccionar',
        minimumInputLength:1 //Caracteres minimos para buscar
    });
    /*.on('select2:select', function (e) {
        nuevaOrden.idactivo=e.params.data.id;
        nuevaOrden.activo=e.params.data.text;
        console.log(nuevaOrden);
    });*/
});

var modalAgregarTareo=new bootstrap.Modal(document.getElementById('modalAgregarTareo'), {
    keyboard: false
});

function FnModalAgregarTareo(){
    document.getElementById('msjAgregarTareo').innerHTML="";
    modalAgregarTareo.show();
};

async function FnAgregarTareo(){
    vgLoader.classList.remove('loader-full-hidden');
    try {        
        const formData = new FormData();
        formData.append('idot', document.getElementById('txtIdOt').value);
        formData.append('idpersonal', document.getElementById('cbPersonal').value);
        formData.append('personal', document.getElementById("cbPersonal").options[document.getElementById("cbPersonal").selectedIndex].text);
        formData.append('ingreso', document.getElementById('dtpIngreso').value);
        formData.append('salida', document.getElementById('dtpSalida').value);
        const response = await fetch("/gesman/update/ModificarOrdenTareo.php", {
            method: "POST",
            body: formData
        });
        /*.then(response => response.text())
        .then((response) => {
            console.log(response)
        })
        .catch(err => console.log(err))*/
        if(!response.ok){
            throw new Error(`Error del servidor: ${response.status} ${response.statusText}`)
        }

        const datos = await response.json();
        
        if(datos.res){
            location.reload();
        }else{
            throw new Error(datos.msg);
        }
    } catch (error) {
        document.getElementById('msjAgregarTareo').innerHTML = `<div class="alert alert-danger m-0 p-1 text-center" role="alert">${error}</div>`;
        setTimeout(()=>{
            vgLoader.classList.add('loader-full-hidden');
        },500);
    }
}

async function FnEliminarTareo(id){
    vgLoader.classList.remove('loader-full-hidden');
    try {        
        const formData = new FormData();
        formData.append('id', id);
        formData.append('idot', document.getElementById('txtIdOt').value);
        const response = await fetch("/gesman/update/ModificarOrdenTareo.php", {
            method: "POST",
            body: formData
        });
        /*.then(response => response.text())
        .then((response) => {
            console.log(response)
        })
        .catch(err => console.log(err))*/
        if(!response.ok){
            throw new Error(`Error del servidor: ${response.status} ${response.statusText}`)
        }

        const datos = await response.json();
        
        if(datos.res){
            location.reload();
        }else{
            throw new Error(datos.msg);
        }
    } catch (error) {
        alert(error);
        setTimeout(()=>{
            vgLoader.classList.add('loader-full-hidden');
        },500);
    }
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