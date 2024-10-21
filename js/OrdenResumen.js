const vgLoader=document.querySelector('.container-loader-full');

window.onload = async function(){
    vgLoader.classList.add('loader-full-hidden');
};

function FnDeshabilitarBoton(elem){
    elem.style.pointerEvents = 'none';
    elem.style.color = 'gray';
}

async function FnBuscarSaleOrders(elem){
    vgLoader.classList.remove('loader-full-hidden');
    try {
        FnDeshabilitarBoton(elem);
        const formData = new FormData();
        formData.append('ordid', document.getElementById('txtId').value);
        //formData.append('ordid', 412134);
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
        setTimeout(function(){vgLoader.classList.add('loader-full-hidden');},500);
    }
}

async function FnBuscarSaleOrder(elem){
    vgLoader.classList.remove('loader-full-hidden');
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
        setTimeout(function(){vgLoader.classList.add('loader-full-hidden');},500);
    }
}

async function FnBuscarSaleOrderLine(elem){
    vgLoader.classList.remove('loader-full-hidden');
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
        setTimeout(function(){vgLoader.classList.add('loader-full-hidden');},500);
    }
}






async function fnBuscarTecnicos() {
    vgLoader.classList.remove('loader-full-hidden');
    const datos = await fnBuscarTecnicos2();
    if (datos.res === '200') {
        let html = `
        <table style="border-collapse: collapse; width:100%;">
            <thead>
                <tr style="background-color: #F7F7F7;">
                    <th style="border-right: 1pt solid #CFCFCF; text-align: center; padding: 3px;">N°</th>
                    <th style="border-right: 1pt solid #CFCFCF; text-align: center; padding: 3px;">Nombre</th>
                    <th style="border-right: 1pt solid #CFCFCF; text-align: center; padding: 3px;">Ingreso</th>
                    <th style="border-right: 1pt solid #CFCFCF; text-align: center; padding: 3px;">Salida</th>
                    <th style="border-right: 1pt solid #CFCFCF; text-align: center; padding: 3px;">Minutos</th>
                </tr>
            </thead>
            <tbody>`;
            let i=1;
            datos.data.forEach(tec => {
                html += `
                <tr>
                    <td style="vertical-align:middle; border-right: 1pt solid #CFCFCF; padding: 3px; text-align:center;">${i}</td>
                    <td style="vertical-align:middle; border-right: 1pt solid #CFCFCF; padding: 3px;">${tec.personal}</td>
                    <td style="vertical-align:middle; border-right: 1pt solid #CFCFCF; padding: 3px; text-align:center;">${tec.ingreso}</td>
                    <td style="vertical-align:middle; border-right: 1pt solid #CFCFCF; padding: 3px; text-align:center;">${tec.salida}</td>
                    <td style="vertical-align:middle; border-right: 1pt solid #CFCFCF; padding: 3px; text-align:right;">${tec.tmin}</td>
                </tr>`;
                i += 1;
            });
            html += `
            </tbody>
        </table>`;
        document.getElementById('divTecnicos').innerHTML = html;
        document.getElementById('aBuscarTecnicos').classList.add('d-none');
    } else {
        alert(datos.msg);
        document.getElementById('divTecnicos').innerHTML = datos.msg;
    }
    await new Promise((resolve, reject) => {
        setTimeout(function () {
            vgLoader.classList.add('loader-full-hidden');
        }, 500)
    })
    return false;
}

async function fnBuscarTecnicos2(){
    const data = new FormData();
    data.append('otid', document.getElementById("txtOtId").value);
    const response = await this.fetch('/gpemsac/gesman/man/admin/search/BuscarTecnicosOt.php', {
        method:'POST', 
        body:data
        })
    .then(res=>res.json())
    .catch(err => console.log(err));
    /*.then(response => response.text())
    .then((response) => {
        console.log(response)
    })
    .catch(err => console.log(err))*/
    return response;
}

async function fnBuscarVales() {
    vgLoader.classList.remove('loader-full-hidden');
    const datos = await fnBuscarVales2();
    console.log(datos);
    if (datos.res === '200') {
        let html = '';
        datos.data.forEach(vale => {
            html += `
            <div class="row mb-3">
                <div class="col-12"></div>`;
                html += `
                <div class="col-12">
                    <table style="border-collapse: collapse; border:1pt solid #CFCFCF; width:100%;">
                        <thead>
                            <tr style="background-color: #F7F7F7; border:1pt solid #CFCFCF;"><th colspan="3" style="text-align:center;">Orden Salida: ${vale.sonombre} | Tipo: ${vale.sotsalida} | Vale: ${vale.sovcliente} | Referencia: ${vale.sonota}</th></tr>
                            <tr style="border:1pt solid #CFCFCF;">
                                <th style="border-right: 1pt solid #CFCFCF; padding: 3px;">Producto</th>
                                <th style="border-right: 1pt solid #CFCFCF; padding: 3px;">Cantidad</th>
                                <th style="border-right: 1pt solid #CFCFCF; padding: 3px;">Medida</th>
                            </tr>
                        </thead>
                        <tbody>`;
                        vale.productos.forEach(prod => {
                            html += `
                            <tr>
                                <td style="vertical-align:middle; border-right: 1pt solid #CFCFCF; padding: 3px;">${prod.proname}</td>
                                <td style="vertical-align:middle; border-right: 1pt solid #CFCFCF; padding: 3px;">${prod.procantidad}</td>
                                <td style="vertical-align:middle; border-right: 1pt solid #CFCFCF; padding: 3px;">${prod.promedida}</td>
                            </tr>`;
                        });
                        html += `
                        </tbody>
                    </table>
                </div>
            </div>`;
            document.getElementById('divVales').innerHTML = html;
        });
        document.getElementById('aBuscarValesOdoo').classList.add('d-none');
    } else {
        alert(datos.msg);
        document.getElementById('aBuscarValesOdoo').innerHTML = datos.msg;
    }
    await new Promise((resolve, reject) => {
        setTimeout(function () {
            vgLoader.classList.add('loader-full-hidden');
        }, 500)
    })
    return false;
}

async function fnBuscarVales2(){
    const data = new FormData();
    data.append('otid', document.getElementById("txtOtId").value);
    const response = await this.fetch('/gpemsac/gesman/man/admin/search/BuscarOdooOrdenSalidaOt.php', {
        method:'POST', 
        body:data
        })
    .then(res=>res.json())
    .catch(err => console.log(err));
    /*.then(response => response.text())
    .then((response) => {
        console.log(response)
    })
    .catch(err => console.log(err))*/
    return response;
}