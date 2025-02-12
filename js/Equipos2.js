const loader=document.querySelector('.container-loader-full');

window.addEventListener('load', function(){
    document.getElementById('MenuSistemas').classList.add('menu-activo','fw-bold');
    document.getElementById('MenuSistemasEquipos').classList.add('menu-activo','fw-bold');
    loader.classList.add('loader-full-hidden');

    $('#cbFamilia').select2({
        dropdownParent: $('#modalModificarRuta'),
        width: 'resolve', //Personalizar el alto del select, aplicar estilo.
        ajax: {
            delay: 450,
            url: '/gesman/search/ListarFamilias.php',
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
                            id:elem.id,//familia del equipo
                            text:elem.ruta,
                            ownid:elem.ownid,//familia para los equipos
                            nombre:elem.nombre//familia del equipo
                        };
                    })
                }
            },
            cache: true
        },
        placeholder: 'Seleccionar',
        allowClear: true
    }).on('select2:select',function(e){
        $('#cbEquipo').val(null).trigger('change');
        document.getElementById('txtFamId').value=e.params.data.id;
        document.getElementById('txtFamOwnId').value=e.params.data.ownid;
        document.getElementById('txtFamNombre').value=e.params.data.nombre;
    });

    $('#cbEquipo').select2({
        dropdownParent: $('#modalModificarRuta'),
        width: 'resolve',
        ajax: {
            delay: 450,
            url: '/gesman/search/ListarFamiliaEquipos.php',
            type: 'POST',
            dataType: 'json',
            data: function (params) {
                return {
                    id: document.getElementById('txtId').value,
                    nombre:params.term,
                    famid:document.getElementById('txtFamOwnId').value
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
        placeholder: 'Seleccionar',
        allowClear: true
    });
});

function FnModalModificarRuta(elem){
    $('#cbFamilia').val(null).trigger('change');
    $('#cbEquipo').val(null).trigger('change');
    document.getElementById('txtId').value=elem.getAttribute('dataid');
    document.getElementById('txtNombre').value=elem.getAttribute('datanombre');
    const modalModificarRuta=new bootstrap.Modal(document.getElementById('modalModificarRuta'), {
        keyboard: false
    }).show();
}

async function FnModificarEquipoRuta(){
    loader.classList.remove('loader-full-hidden');
    try {
        let json = {
            id: document.getElementById('txtId').value,
            ownid : document.getElementById('cbEquipo').value,
            famid : document.getElementById('txtFamId').value,
            famownid: document.getElementById('txtFamOwnId').value,
            famnombre : document.getElementById("txtFamNombre").value
        }

        const response = await fetch('/gesman/update/ModificarEquipoRuta.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json'
            },
            body: JSON.stringify(json)
        });//.then(response=>response.text()).then((response)=>{console.log(response)}).catch(err=>console.log(err));

        if(!response.ok){throw new Error(`${response.status} ${response.statusText}`)}
        const datos = await response.json();
        if(!datos.res){throw new Error(datos.msg);}

        loader.classList.add('loader-full-hidden');
        showToast(datos.msg, 'bg-success');
        setTimeout(function(){location.reload();},500);
    } catch (ex) {
        showToast(ex.message, 'bg-danger');
        setTimeout(function(){loader.classList.add('loader-full-hidden');},500);
    }
}

function FnModoLista(){
    window.location.href='/gesman/Equipos.php';
}

function FnEquipos(){
    window.location.href='/gesman/Equipos.php';
    return false;
}