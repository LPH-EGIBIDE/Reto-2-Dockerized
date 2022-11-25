async function setAvatar(formData) {
    return fetch(`/api/attachments/uploadAttachment`, {
        method: 'POST',
        body: formData
    });
}

async function setPassword(formData) {
    formData.append('method', 'changePassword');
    return fetch(`/api/users/manageUser`, {
        method: 'POST',
        body: formData
    });
}

async function setDescription(formData) {
    formData.append('method', 'changeDescription');
    return fetch(`/api/users/manageUser`, {
        method: 'POST',
        body: formData
    });
}

function changeAvatar(){
    // Generate a hidden input element
    let input = document.createElement('input');
    input.type = 'file';
    input.accept = 'image/*';
    input.onchange = e => {
        let file = e.target.files[0];
        let formData = new FormData();
        formData.append('file', file);
        formData.append('action', 'setAvatar');
        setAvatar(formData).then(response => {
            if (response.ok) {
                response.json().then(data => {
                    if (data.status === 'success') {
                        let avatar = document.getElementById('avatar');
                        let avatarHeader = document.getElementById('userLogo');
                        let avatarUrl = "/api/attachments/id/"+data.attachment;
                        avatar.src = avatarUrl;
                        avatarHeader.src = avatarUrl;
                        showToast(data.message, 'success');
                    } else
                        showToast(data.message, 'error');
                }).catch(err => {
                    console.error(err);
                    showToast(err.message, 'error');
                });
            }
        });
    };
    input.click();
}

function changePassword(formElement){
    let formData = new FormData(formElement);
    setPassword(formData).then(response => {
        if (response.ok) {
            response.json().then(data => {
                if (data.status === 'success') {
                    showToast(data.message, 'success');
                } else
                    showToast(data.message, 'error');
                formElement.reset();
            }).catch(err => {
                console.error(err);
                showToast(err.message, 'error');
            });
        }
    });
}

function changeDescription(formElement){
    let formData = new FormData(formElement);
    setDescription(formData).then(response => {
        if (response.ok) {
            response.json().then(data => {
                if (data.status === 'success') {
                    showToast(data.message, 'success');
                } else
                    showToast(data.message, 'error');
            }).catch(err => {
                console.error(err);
                showToast(err.message, 'error');
            });
        }
    });
}


function disable2Fa(){
    // Show a sweetalert modal to ask for the password
    Swal.fire({
        title: 'Desactivar verificación en dos pasos',
        text: 'Introduce tu contraseña para desactivar la verificación en dos pasos',
        input: 'password',
        inputAttributes: {
            autocapitalize: 'off'
        },
        showCancelButton: true,
        confirmButtonText: 'Desactivar',
        cancelButtonText: 'Cancelar',
        showLoaderOnConfirm: true,
        preConfirm: (password) => {
            fetch(`/api/users/manageUser`, {
                method: 'POST',
                body: new URLSearchParams({
                    method: 'deactivateMFA',
                    password: password
                })
            }).then(response => {
                if (response.ok) {
                    response.json().then(data => {
                        if (data.status === 'success') {
                            showToast(data.message, 'success');
                        } else
                            showToast(data.message, 'error');
                    }).catch(err => {
                        console.error(err);
                        showToast(err.message, 'error');
                    });
                }
            });
        },
        allowOutsideClick: () => !Swal.isLoading()
    });


}

function enable2Fa(){
    // Show a sweetalert modal to select the 2fa method for email or app
    Swal.fire({
        title: 'Activar verificación en dos pasos',
        text: 'Selecciona el método de verificación en dos pasos',
        input: 'select',
        inputOptions: {
            'activateEmailMFA': 'Email',
            'activateMFA': 'Aplicación'
        },
        inputPlaceholder: 'Selecciona un método',
        showCancelButton: true,
        confirmButtonText: 'Activar',
        cancelButtonText: 'Cancelar',
        showLoaderOnConfirm: true,
        preConfirm: (method) => {
            fetch(`/api/users/manageUser`, {
                method: 'POST',
                body: new URLSearchParams({
                    method: method
                })
            }).then(response => {
                if (response.ok) {
                    response.json().then(data => {
                        if (data.status === 'success') {
                            console.log(data);
                            console.log(method);
                            if (method === 'activateMFA'){
                                console.log('show qr');
                                show2FaQr(data.mfaUri);
                            } else{
                                showToast(data.message, 'success');
                            }
                        } else
                            showToast(data.message, 'error');
                    }).catch(err => {
                        console.error(err);
                        showToast(err.message, 'error');
                    });
                }
            });
        },
        allowOutsideClick: () => !Swal.isLoading()
    });
}

function show2FaQr(qrData){
    // Prevent closing the modal only if confirmed 2 times
    Swal.fire({
        title: 'Escanea el código QR en tu aplicación de autenticación',
        imageUrl: 'https://api.qrserver.com/v1/create-qr-code/?data=' + qrData + '&amp;size=400x400',
        imageWidth: 400,
        imageHeight: 400,
        imageAlt: 'Código QR',
        confirmButtonText: 'Cerrar',
        allowOutsideClick: false,
        allowEscapeKey: false,
        allowEnterKey: false,
        showCancelButton: false,
        showConfirmButton: true,
        showLoaderOnConfirm: true
    }).then((result) => {
        Swal.fire({
            title: '¿Has escaneado el código QR?',
            text: 'Si no has escaneado el código QR no podrás acceder a tu cuenta',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonText: 'Sí',
            cancelButtonText: 'No',
            showLoaderOnConfirm: true
        }).then((result) => {
            if (result.isConfirmed) {
                Swal.fire({
                    title: 'Verificación en dos pasos activada',
                    text: 'La verificación en dos pasos ha sido activada',
                    icon: 'success',
                    showCancelButton: false,
                    confirmButtonText: 'Cerrar',
                    showLoaderOnConfirm: true
                });
            } else {
                // Call the function again
                show2FaQr(qrData);
            }
        });
    });

}

// Get the form elements defined in your form HTML above and add the event listener
document.getElementById('changePassword').addEventListener('submit', (e) => {
    e.preventDefault();
    changePassword(e.target);
});

document.getElementById('changeDescription').addEventListener('submit', (e) => {
    e.preventDefault();
    changeDescription(e.target);
});