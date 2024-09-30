const vgLoader=document.querySelector('.container-loader-full');

window.onload = async function(){
    document.getElementById('MenuEmpresas').classList.add('menu-activo','fw-bold');
    vgLoader.classList.add('loader-full-hidden')
};

var modalCambiarEmpresa=new bootstrap.Modal(document.getElementById('modalCambiarEmpresa'), {
    keyboard: false
})

function FnModalCambiarEmpresa(id, nombre){
    document.getElementById('txtId').value = id;
    document.getElementById('txtEmpresa').value = nombre;
    document.getElementById('msjCambiarEmpresa').innerHTML = "";
    modalCambiarEmpresa.show();
}


async function FnCambiarEmpresa(){
    vgLoader.classList.remove('loader-full-hidden');
    try {        
        const formData = new FormData();
        formData.append('id', document.getElementById('txtId').value);
        const response = await fetch("/gesman/update/EstablecerEmpresa.php", {
            method: "POST",
            body: formData
        });
        /*.then(response => response.text())
        .then((response) => {
            console.log(response)
        })
        .catch(err => console.log(err))*/        
        if(response.ok){
            const datos = await response.json();
            if(datos.res){
                location.reload();
            }else{            
                throw new Error(datos.msg);
            }
        }else{
            throw new Error(`${response.status} ${response.statusText}`)
        }        
    } catch (error) {
        document.getElementById('msjCambiarEmpresa').innerHTML=`<div class="alert alert-danger mb-2 p-1 text-center" role="alert">${error}</div>`;
    }

    setTimeout(()=>{
        vgLoader.classList.add('loader-full-hidden');
    },500);
}



async function fnEstablecerEmpresa(empresa){
    vgLoader.classList.remove('loader-full-hidden');
    const data=await fnEstablecerEmpresa2(empresa)
    if(data[0].res==='200'){
        document.getElementById('msjEstablecerEmpresa').innerHTML=`<div class="alert alert-success m-0 p-1 text-center" role="alert">${data[1].msg}</div>`;
        await new Promise((resolve, reject)=>{
            setTimeout(function(){
                vgLoader.classList.add('loader-full-hidden');
                location.reload();
            },500)
        })
    }else{
        document.getElementById('msjEstablecerEmpresa').innerHTML=`<div class="alert alert-danger m-0 p-1 text-center" role="alert">${data[1].msg}</div>`;
        await new Promise((resolve, reject)=>{
            setTimeout(function(){
                vgLoader.classList.add('loader-full-hidden');
            },500)
        })
    }
    return false;
}

async function fnEstablecerEmpresa2(empresa){
    var formData = new FormData();
    formData.append("empresa", empresa);    
    const response=await this.fetch("session/MakeDefaultCompany.php", {
        method: "POST",
        body: formData
    })
    .then(res => res.json())
    .then(data => data)
    .catch(err=>console.log(err));
    /*.then(response => response.text())
    .then((response) => {
        console.log(response)
    })
    .catch(err => console.log(err))*/
    return response;
}