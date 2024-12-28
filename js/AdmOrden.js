const loader=document.querySelector('.container-loader-full');

window.addEventListener('load', function() {
    loader.classList.add('loader-full-hidden');
});

function FnDeshabilitarBoton(elem){
    elem.style.pointerEvents = 'none';
    elem.style.color = 'gray';
}

async function FnBuscarSaleOrders(elem){
    loader.classList.remove('loader-full-hidden');
    try {
        FnDeshabilitarBoton(elem);
        const formData = new FormData();
        formData.append('ordid', document.getElementById('txtId').value);//formData.append('ordid', 412134);
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
            <div class="col-12" style="margin-left:7px;">
                <div><p class="m-0"><i class="fas fa-plus-square" dataid=${elem.id} onclick="FnBuscarSaleOrder(this); return false;" style="cursor: pointer;"></i> ${elem.nombre} | ${elem.fecha}</p></div>
                <div id="tblSaleOrder${elem.id}"></div>
            </div>`;
        });
    } catch (ex) {
        showToast(ex.message,'bg-danger');
    } finally {
        setTimeout(function(){loader.classList.add('loader-full-hidden');},500);
    }
}

async function FnBuscarSaleOrder(elem){
    loader.classList.remove('loader-full-hidden');
    try {
        FnDeshabilitarBoton(elem);
        const formData = new FormData();
        formData.append('soid', elem.getAttribute('dataid'));
        const response = await fetch('/gesman/search/BuscarSaleOrderLines.php', {
            method:'POST',
            body: formData
        });//.then(response=>response.text()).then((response)=>{console.log(response)}).catch(err=>console.log(err));

        if (!response.ok) { throw new Error(`${response.status} ${response.statusText}`);}
        const datos = await response.json();
        if (!datos.res) { throw new Error(`${datos.msg}`); }

        document.getElementById('tblSaleOrder'+elem.getAttribute('dataid')).innerHTML = '';
        datos.data.forEach(data=>{
            document.getElementById('tblSaleOrder'+elem.getAttribute('dataid')).innerHTML +=`
            <div style="margin-left:7px;">
                <div class="d-flex justify-content-between">
                    <p class="m-0"><i class="fas fa-plus-square" dataid=${data.id} onclick="FnBuscarSaleOrderLine(this); return false;" style="cursor: pointer;"></i> ${data.nombre}</p>
                    <p class="m-0">${data.cantidad}</p>
                </div>            
                <div id="tblSaleOrderLine${data.id}"></div>
            </div>`;
        });
    } catch (ex) {
        showToast(ex.message,'bg-danger');
    } finally {
        setTimeout(function(){loader.classList.add('loader-full-hidden');},500);
    }
}

async function FnBuscarSaleOrderLine(elem){
    loader.classList.remove('loader-full-hidden');
    try {
        FnDeshabilitarBoton(elem);
        const formData = new FormData();
        formData.append('linid', elem.getAttribute('dataid'));
        const response = await fetch('/gesman/search/BuscarSaleOrderLinePicking.php', {
            method:'POST',
            body: formData
        });//.then(response=>response.text()).then((response)=>{console.log(response)}).catch(err=>console.log(err));

        if (!response.ok) { throw new Error(`${response.status} ${response.statusText}`);}
        const datos = await response.json();
        if (!datos.res) { throw new Error(`${datos.msg}`); }

        document.getElementById('tblSaleOrderLine'+elem.getAttribute('dataid')).innerHTML = '';
        datos.data.forEach(data=>{
            document.getElementById('tblSaleOrderLine'+elem.getAttribute('dataid')).innerHTML +=`
            <div style="margin-left:7px;">
                <div class="d-flex justify-content-between">
                    <p class="m-0"><i class="fas fa-plus-square" style="color:gray;"></i> ${data.nombre}</p>
                    <p class="m-0">${data.cantidad}</p>
                </div>
            </div>`;
        });
    } catch (ex) {
        showToast(ex.message,'bg-danger');
    } finally {
        setTimeout(function(){loader.classList.add('loader-full-hidden');},500);
    }
}

function FnModalVerArchivo(archivo){
    loader.classList.remove('loader-full-hidden');
    try {
        const modalVerArchivo=new bootstrap.Modal(document.getElementById('modalVerArchivo'), {keyboard: false});
        let tipo=archivo.getAttribute('datatipo');
        let nombre=archivo.getAttribute('datanombre');

        if(archivo.getAttribute('datatitulo')){
            document.getElementById('pNombre').innerHTML=archivo.getAttribute('datatitulo');
        }else{
            document.getElementById('pNombre').innerHTML=tipo +' '+nombre;
        }

        let fileContainer=document.getElementById('fileContainer');
        if (fileContainer.childElementCount === 1) {
            let hijoUnico = fileContainer.firstElementChild;
            fileContainer.removeChild(hijoUnico);
        }
        
        switch (tipo) {
            case 'IMG':
                let nuevaImagen = document.createElement('img');
                nuevaImagen.src = '/mycloud/gesman/files/'+nombre;
                nuevaImagen.alt = "Imagen";
                nuevaImagen.classList.add('img-fluid');
                fileContainer.appendChild(nuevaImagen);
                break;
            case 'PDF':
                let nuevoPdf = document.createElement("embed");
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
        setTimeout(function(){loader.classList.add('loader-full-hidden');},500);
    }
};