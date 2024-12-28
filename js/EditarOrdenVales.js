const loader=document.querySelector('.container-loader-full');
const modalAgregarVale=new bootstrap.Modal(document.getElementById('modalAgregarVale'),{keyboard:false});
var productos=[];

window.addEventListener('load', async()=>{
    await FnBuscarSaleOrders();
    document.getElementById('MenuOrdenes').classList.add('menu-activo','fw-bold');
    loader.classList.add('loader-full-hidden');    
});

async function FnBuscarSaleOrders(){
    try {
        const formData = new FormData();
        //formData.append('ordid', 415380);
        formData.append('ordid', document.getElementById('txtId').value);
        const response = await fetch('/gesman/search/BuscarSaleOrders.php', {
            method:'POST',
            body: formData
        });//.then(response=>response.text()).then((response)=>{console.log(response)}).catch(err=>console.log(err));

        if (!response.ok) { throw new Error(`${response.status} ${response.statusText}`);}
        const datos = await response.json();
        if (!datos.res) { throw new Error(`${datos.msg}`); }
        
        document.getElementById('tblSaleOrders').innerHTML = '';
        datos.data.forEach(elem=>{
            document.getElementById('tblSaleOrders').innerHTML +=`
            <div class="col-12 mb-1">
                <div class="border-bottom divselect px-1" style="min-height:2.2rem;" onclick="FnBuscarSaleOrderLine(${elem.id}); return false;">
                    <div class="d-flex justify-content-between">
                        <p class='m-0'><span class="fw-bold">${elem.nombre}</span> <span style="font-size: 13px; color:gray;">${elem.fecha}</span></p>
                        <p class='m-0'>${elem.estado}</p>
                    </div>
                    <div>Vale ${elem.vale}</div>
                </div>
            </div>`;
        });
    } catch (ex) {
        showToast(ex.message, 'bg-danger');
    }
}

async function FnBuscarSaleOrderLine(soid){
    loader.classList.remove('loader-full-hidden');
    try {
        const formData = new FormData();
        formData.append('soid', soid);
        const response = await fetch('/gesman/search/BuscarSaleOrderLines.php', {
            method:'POST',
            body: formData
        });//.then(response=>response.text()).then((response)=>{console.log(response)}).catch(err=>console.log(err));

        if (!response.ok) { throw new Error(`${response.status} ${response.statusText}`);}
        const datos = await response.json();
        if (!datos.res) { throw new Error(`${datos.msg}`); }
        
        document.getElementById('tblSaleOrderLines').innerHTML = '';
        datos.data.forEach(elem=>{
            document.getElementById('tblSaleOrderLines').innerHTML +=`
            <div class="col-12 mb-1">
                <div class="border-bottom px-1" style="min-height:2.2rem;">
                    <div>${elem.nombre}</div>
                    <div class="d-flex justify-content-between">
                        <p class='m-0 fw-bold'>${elem.cantidad}</p>
                        <p class='m-0'>${elem.estado}</p>
                    </div>
                </div>
            </div>`;
        });

        const modalSaleOrderLines=new bootstrap.Modal(document.getElementById('modalSaleOrderLines'),{keyboard:false}).show();
    } catch (ex) {
        showToast(ex.message, 'bg-danger');
    }finally{
        setTimeout(()=>{loader.classList.add('loader-full-hidden');},500);
    }
}

$(document).ready(function() {
    $('#cbProducto').select2({
        dropdownParent: $('#modalAgregarVale'),//Agregar el select a un modal
        width: 'resolve', //Personalizar el alto del select, aplicar estilo.
        ajax: {
            delay: 450, //Tiempo de demora para buscar
            url: '/gesman/search/ListarProductos.php',
            type: 'POST',
            dataType: 'json',
            data: function (params){
                return {
                    nombre: params.term // parametros a enviar al server. params.term captura lo que se escribe en el input
                };
            },
            processResults:function(datos) {
                return {
                    results:datos.data.map(function(elem){
                        return {
                            id: elem.id,
                            odoid: elem.odoid,
                            lisid: elem.lisid,
                            codigo: elem.codigo,
                            text: elem.nombre,
                            medida: elem.medida
                        };
                    })
                }
            },
            cache: true
        },
        placeholder: 'Seleccionar',
        minimumInputLength:1 //Caracteres minimos para buscar
    }).on('select2:select', function (e) {
        document.getElementById('txtProId').value=e.params.data.id;
        document.getElementById('txtOdoId').value=e.params.data.odoid;
        document.getElementById('txtLisId').value=e.params.data.lisid;
        document.getElementById('txtProCodigo').value=e.params.data.codigo;
        document.getElementById('txtProNombre').value=e.params.data.text;
        document.getElementById('txtProMedida').value=e.params.data.medida;
        document.getElementById('txtProCantidad').value=1;
    });
});

function FnModalAgregarVale(){
    try {
        document.getElementById('tblProductos').innerHTML='<p class="fst-italic">No hay productos en el Vale.</p>';
        document.getElementById('txtProId').value=0;
        document.getElementById('txtOdoId').value=0;
        document.getElementById('txtLisId').value=0;
        document.getElementById('txtProCodigo').value='';
        document.getElementById('txtProNombre').value='';
        document.getElementById('txtProMedida').value='';
        document.getElementById('txtProCantidad').value=1;
        modalAgregarVale.show();   
    } catch (ex) {
        showToast(ex.message, 'bg-danger');
    }
};

function FnAgregarProducto(proid, odoid, lisid, procodigo, pronombre, promedida, procantidad ){
    try {
        if(proid>0 && odoid>0 && lisid>0 && procodigo!="" && pronombre!="" && promedida!="" && procantidad>0){
            productos.push({proid, odoid, lisid, procodigo, pronombre, promedida, procantidad});
            FnMostrarProductos();
            document.getElementById('txtProId').value=0;
            document.getElementById('txtOdoId').value=0;
            document.getElementById('txtLisId').value=0;
            document.getElementById('txtProCodigo').value='';
            document.getElementById('txtProNombre').value='';
            document.getElementById('txtProMedida').value='';
            document.getElementById('txtProCantidad').value=1;
            $("#cbProducto").empty();
        }else{
            throw new Error("La información esta incompleta.")
        }
    } catch (ex) {
        showToast(ex.message, 'bg-danger');
    }
}

function FnEliminarProducto(id){
    try {
        productos.splice(id, 1);
        FnMostrarProductos();
    } catch (ex) {
        showToast(ex.message, 'bg-danger');
    }
}

function FnMostrarProductos(){
    try {
        document.getElementById('tblProductos').innerHTML='';
        productos.forEach(function(elem, indice, array){
            document.getElementById('tblProductos').innerHTML+=`
                <div class="row mb-2 mx-0 border-bottom border-secondary">
                    <div class="col-12 p-0 d-flex justify-content-between">
                        <p class="m-0">${elem.pronombre}</p>
                        <a class="text-secondary text-decoration-none p-0" href="#" onclick="FnEliminarProducto(${indice}); return false;"><i class="fas fa-times link-wa" style="font-size:22px;"></i></a>
                    </div>
                    <div class="col-12 p-0 d-flex justify-content-between">
                        <p class="m-0">${elem.procodigo}</p>
                        <p class="m-0">${elem.procantidad} ${elem.promedida}</p>
                    </div>
                </div>`;
        });
    } catch (ex) {
        throw ex
    }
}

async function FnAgregarVale(){
    loader.classList.remove('loader-full-hidden');
    try {
        let productosOdoo = [];
        productos.forEach(producto=>productosOdoo.push({odoid:producto.odoid, lisid:producto.lisid, cantidad:producto.procantidad}));

        const jsonData={
            cliid:document.getElementById('txtCliId').value,
            almid: document.getElementById('txtAlmId').value,
            ordid:document.getElementById('txtId').value,
            ordnombre:document.getElementById('txtOrdNombre').value,
            ordtipo:document.getElementById('txtOrdTipo').value,
            equcodigo:document.getElementById('txtEquCodigo').value,
            clivale:document.getElementById('txtCliVale').value,
            clifecha:document.getElementById('dtpCliFecha').value,
            tecnico:document.getElementById('txtSupervisor').value,
            usuario:document.getElementById('txtUsuNombre').value,
            productos:productosOdoo
        }

        const response = await fetch('https://gpemsac.com/gesman/odoo/ApiAddSaleOrderAll2.php',{
            method:'POST',
            headers:{'Content-Type':'application/json; charset=UTF-8'},
            body: JSON.stringify(jsonData)
        });//.then(response=>response.text()).then((response)=>{console.log(response)}).catch(err=>console.log(err));

        if(!response.ok){throw new Error(`${response.status} ${response.statusText}`)}
        const datos = await response.json();        
        if(!datos.res){throw new Error(datos.msg);}

        await FnBuscarSaleOrders();
        modalAgregarVale.hide();

    } catch (ex) {
        showToast(ex.message, 'bg-danger');
    }finally{
        setTimeout(()=>{loader.classList.add('loader-full-hidden');},500);
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