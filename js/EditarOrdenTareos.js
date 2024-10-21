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

function FnModalAgregarTareo(){
    var modalAgregarTareo=new bootstrap.Modal(document.getElementById('modalAgregarTareo'), {
        keyboard: false
    }).show();
};

async function FnAgregarTareo(){
    vgLoader.classList.remove('loader-full-hidden');
    try {        
        const formData = new FormData();
        formData.append('ordid', document.getElementById('txtId').value);
        formData.append('perid', document.getElementById('cbPersonal').value);
        formData.append('pernombre', document.getElementById("cbPersonal").options[document.getElementById("cbPersonal").selectedIndex].text);
        formData.append('ingreso', document.getElementById('dtpIngreso').value);
        formData.append('salida', document.getElementById('dtpSalida').value);
        const response = await fetch("/gesman/insert/AgregarOrdenTareo.php", {
            method: "POST",
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

async function FnEliminarTareo(id){
    vgLoader.classList.remove('loader-full-hidden');
    try {        
        const formData = new FormData();
        formData.append('id', id);
        formData.append('ordid', document.getElementById('txtId').value);
        const response = await fetch("/gesman/delete/EliminarOrdenTareo.php", {
            method: "POST",
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

function FnOrden(){
    let id = document.getElementById('txtId').value;
    if(id > 0){
        window.location.href='/gesman/Orden.php?id='+id;
    }
    return false;
}

function FnOrdenes(){
    window.location.href='/gesman/Ordenes.php';
    return false;
}