const vgLoader=document.querySelector('.container-loader-full');

window.addEventListener('load', function(){
    document.getElementById('MenuOrdenes').classList.add('menu-activo','fw-bold');
    vgLoader.classList.add('loader-full-hidden');
    
    $(document).ready(function() {
        $('#cbEquipo').select2({
            width: 'resolve', //Personalizar el alto del select, aplicar estilo.
            ajax: {
                delay: 450,
                url: '/gesman/search/ListarEquipos.php',
                type: 'POST',
                dataType: 'json',
                data: function (params) {
                    return {
                        nombre:params.term
                    };
                },
                processResults:function(datos){
                    return {
                        results:datos.data.map(function(elem) {
                            return {
                                id:elem.id,
                                text:elem.nombre,
                                famid:elem.famid,
                                famnombre:elem.famnombre
                            };
                        })
                    }
                },
                cache: true
            },
            placeholder: 'Seleccionar'
        }).on('select2:select',function(e){
            document.getElementById('txtEquId').value=e.params.data.id;
            document.getElementById('txtFamId').value=e.params.data.famid;
            document.getElementById('txtEquNombre').value=e.params.data.text;
            document.getElementById('txtFamNombre').value=e.params.data.famnombre;
        });

        $('#cbOrigen').select2({
            width: 'resolve', //Personalizar el alto del select, aplicar estilo.
            ajax: {
                delay: 450,
                url: '/gesman/search/ListarOrigenes.php',
                type: 'POST',
                dataType: 'json',
                data: function (params) {
                    return {
                        nombre:params.term
                    };
                },
                processResults:function(datos){
                    return {
                        results:datos.data.map(function(elem) {
                            return {
                                id:elem.id,
                                text:elem.nombre
                            };
                        })
                    }
                },
                cache: true
            },
            placeholder: 'Seleccionar'
        }).on('select2:select',function(e){
            document.getElementById('txtOriId').value=e.params.data.id;
            document.getElementById('txtOriNombre').value=e.params.data.text;
        });

        $('#cbSistema').select2({
            width: 'resolve', //Personalizar el alto del select, aplicar estilo.
            ajax: {
                delay: 450,
                url: '/gesman/search/ListarSistemas.php',
                type: 'POST',
                dataType: 'json',
                data: function (params) {
                    return {
                        nombre:params.term
                    };
                },
                processResults:function(datos){
                    return {
                        results:datos.data.map(function(elem) {
                            return {
                                id:elem.id,
                                text:elem.nombre
                            };
                        })
                    }
                },
                cache: true
            },
            placeholder: 'Seleccionar'
        }).on('select2:select',function(e){
            document.getElementById('txtSisId').value=e.params.data.id;
            document.getElementById('txtSisNombre').value=e.params.data.text;
        });

        $('#cbSupervisor').select2({
            width: 'resolve', //Personalizar el alto del select, aplicar estilo.
            ajax: {
                delay: 450,
                url: '/gesman/search/ListarSupervisores.php',
                type: 'POST',
                dataType: 'json',
                data: function (params) {
                    return {
                        nombre:params.term
                    };
                },
                processResults:function(datos){
                    return {
                        results:datos.data.map(function(elem) {
                            return {
                                id:elem.nombre,
                                text:elem.nombre
                            };
                        })
                    }
                },
                cache: true
            },
            placeholder: 'Seleccionar'
        });

        $('#cbContacto').select2({
            width: 'resolve', //Personalizar el alto del select, aplicar estilo.
            ajax: {
                delay: 450,
                url: '/gesman/search/ListarContactos.php',
                type: 'POST',
                dataType: 'json',
                data: function (params) {
                    return {
                        nombre:params.term
                    };
                },
                processResults:function(datos){
                    return {
                        results:datos.data.map(function(elem) {
                            return {
                                id:elem.nombre,
                                text:elem.nombre
                            };
                        })
                    }
                },
                cache: true
            },
            placeholder: 'Seleccionar'
        });
    });

});

async function FnModificarOrden(){
    vgLoader.classList.remove('loader-full-hidden');
    try {
        let json = {
            id : document.getElementById('txtId').value,
            equid: document.getElementById('txtEquId').value,
            equnombre: document.getElementById('txtEquNombre').value,
            fecha : document.getElementById('dtpFecha').value,
            famid : document.getElementById('txtFamId').value,
            famnombre : document.getElementById('txtFamNombre').value,
            sisid: document.getElementById('txtSisId').value,
            sisnombre : document.getElementById('txtSisNombre').value,
            oriid : document.getElementById('txtOriId').value,
            orinombre : document.getElementById('txtOriNombre').value,
            supervisor : document.getElementById('cbSupervisor').value,
            clicontacto : document.getElementById('cbContacto').value,
            equkm : document.getElementById('txtEquKm').value,
            equhm : document.getElementById('txtEquHm').value,
            actnombre : document.getElementById('txtActNombre').value,
            trabajos : document.getElementById('txtTrabajos').value,
            observaciones : document.getElementById('txtObservaciones').value
        }

        const response = await fetch('/gesman/update/ModificarOrden.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json'
            },
            body: JSON.stringify(json)
        });//.then(response=>response.text()).then((response)=>{console.log(response)}).catch(err=>console.log(err));

        if (!response.ok) { throw new Error(`${response.status} ${response.statusText}`);}
        const datos = await response.json();
        if (!datos.res) { throw new Error(`${datos.msg}`); }

        setTimeout(function(){location.reload();},500);
    } catch (ex) {
        showToast(ex.message, 'bg-danger');
        setTimeout(function(){vgLoader.classList.add('loader-full-hidden');},1000);
    }
}

function FnOrden(){
    let id=document.getElementById('txtId').value;
    if(id>0){
        window.location.href='/gesman/Orden.php?id='+id;
    }
}

function FnOrdenes(){
    window.location.href='/gesman/Ordenes.php';
    return false;
}