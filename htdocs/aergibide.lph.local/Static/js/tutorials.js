async function pushFile(formData) {
    return fetch(`/api/attachments/uploadAttachment`, {
        method: 'POST',
        body: formData
    });
}

async function deleteFile(fileId) {
    return fetch(`/api/attachments/manageTutorials`, {
        method: 'POST',
        body: new URLSearchParams({
            method: 'delete',
            tutorialId: fileId
        })
    });
}

async function getFileList() {
    return fetch(`/api/attachments/manageTutorials`, {
        method: 'GET'
    });
}

function uploadTutorial(){
    // Generate a hidden input element
    let input = document.createElement('input');
    input.type = 'file';
    input.onchange = e => {
        let file = e.target.files[0];
        let formData = new FormData();
        formData.append('file', file);
        formData.append('action', 'uploadTutorial');
        pushFile(formData).then(response => {
            if (response.ok) {
                response.json().then(data => {
                    if (data.status === 'success') {
                        hidePostView();
                        showToast(data.message, 'success', () => {
                            getTutorials();
                        });
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

function deleteTutorial(id){
    Swal.fire({
        title: '¿Estás seguro?',
        text: "Estas a punto de eliminar un tutorial, esta acción no se puede deshacer",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#3085d6',
        cancelButtonColor: '#d33',
        confirmButtonText: 'Sí, eliminar',
        cancelButtonText: 'Cancelar'
    }).then((result) => {
        if (result.isConfirmed) {
            deleteFile(id).then(response => {
                if (response.ok)
                    response.json().then(data => {
                        if (data.status === 'success') {
                            showToast(data.message, 'success', () => {
                                getTutorials();
                            });
                        } else
                            showToast(data.message, 'error');
                    }).catch(err => {
                        console.error(err);
                        showToast(err.message, 'error');
                    });
            });
        } else {
            Swal.fire(
                'Cancelado',
                'El tutorial no se ha eliminado',
                'error'
            )
        }

    });
}

function showPostView() {
    let skeletonContainer = document.getElementById("skeletonContainer");
    let postView = document.getElementById("postsContainer");
    skeletonContainer.classList.add("hidden");
    postView.classList.remove("hidden");
}

function hidePostView() {
    let skeletonContainer = document.getElementById("skeletonContainer");
    let postView = document.getElementById("postsContainer");
    skeletonContainer.classList.remove("hidden");
    postView.classList.add("hidden");

}


function getTutorials(){
    hidePostView();
    let container = document.getElementById('contenedorRespuestas');
    container.innerHTML = "";
    getFileList().then(response => {
        if (response.ok)
            response.json().then(data => {
                if (data.status === 'success') {
                    if (data.tutorials.length > 0) {
                        data.tutorials.forEach(tutorial => {
                            container.innerHTML += tutorialTemplate(tutorial);
                        });
                    } else {
                        container.innerHTML = '<h3 class="centrado">No hay tutoriales disponibles</h3>';
                    }
                }
                showPostView();
            }).catch(err => {
                console.error(err);
                showToast(err.message, 'error');
            });
    });
}

    function tutorialTemplate(tutorial) {
        const deleteElement = tutorial.isDeletable ? `<li><i class="fa-solid fa-trash-xmark red" onclick="deleteTutorial(${tutorial.id})"></i></li>`: '';
        return `<div class="respuesta">
                <div class="contenedorLista">
                    <div class="contenedorIden">
                        <img src="${tutorial.uploadedBy.avatar}" alt="img Avatar" class="identificadoAva">
                    </div>
                    <div class="contenidoIzq">
                        <p class="tituArchivo overflow-1">
                            <a class="unstyledLink" target="_blank" href="/api/attachments/id/${tutorial.id}">${tutorial.filename}</a>
                        </p>
                        <ul class="listContent">
                            <li>
                                <p class="autor">Publicado por: <a href="/user/${tutorial.uploadedBy.id}">${tutorial.uploadedBy.username}</a></p>
                            </li>
                            ${deleteElement}
                        </ul>
                    </div>
                </div>
                <hr>
            </div>`;
    }

    window.addEventListener('load', () => {
        getTutorials();
    });