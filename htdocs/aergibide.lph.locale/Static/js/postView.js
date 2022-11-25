let currentPage = 1;
let answerCount = 0;

async function getPost(postId) {
    return fetch(`/api/posts/managePost`, {
        method: 'POST',
        headers: {
            "Content-Type": "application/x-www-form-urlencoded"
        },
        body: new URLSearchParams({
            postId: postId
        })
    });
}

async function disablePost(postId) {
    return fetch(`/api/posts/managePost`, {
        method: 'POST',
        headers: {
            "Content-Type": "application/x-www-form-urlencoded"
        },
        body: new URLSearchParams({
            postId: postId,
            action: 'close'
        })
    });
}

async function getPostAnswers(postId, page) {
    return fetch(`/api/posts/manageAnswers`, {
        method: 'POST',
        headers: {
            "Content-Type": "application/x-www-form-urlencoded"
        },
        body: new URLSearchParams({
            id: postId,
            page: page
        })
    });
}

async function insertAnswer(postId, message) {
    return fetch(`/api/posts/manageAnswers`, {
        method: 'POST',
        headers: {
            "Content-Type": "application/x-www-form-urlencoded"
        },
        body: new URLSearchParams({
            id: postId,
            content: message,
            action: 'insert'
        })
    });
}

async function addAttachmentAnswer(answerId, fileId) {
    return fetch(`/api/posts/manageAnswers`, {
        method: 'POST',
        headers: {
            "Content-Type": "application/x-www-form-urlencoded"
        },
        body: new URLSearchParams({
            id: answerId,
            attachment: fileId,
            action: 'addAttachment'
        })
    });
}

async function insertAttachment(file) {
    let formData = new FormData();
    formData.append('file', file);
    formData.append('public', "1");
    return fetch(`/api/attachments/uploadAttachment`, {
        method: 'POST',
        body: formData
    });
}

async function deleteAttachment(fileId) {
    return fetch(`/api/attachments/deleteAttachment`, {
        method: 'POST',
        headers: {
            "Content-Type": "application/x-www-form-urlencoded"
        },
        body: new URLSearchParams({
            id: fileId
        })
    });
}


function hiddenPostAnswer() {
    return `
        <div class="cajaRespuesta hidden" id="agregarRespuesta">
            <div class="answerForm">
                <form>
                    <p class="respuestaTitu">Añadir respuesta</p>
                    <textarea name="text" placeholder="Escriba aquí" oninput='this.style.height = ""; this.style.height = this.scrollHeight + "px"'></textarea>
                    <p class="respuestaFile">Añadir archivos</p>
                <div class="contenedorAbajo">
                    <div>
                        <input type="file" name="fileUpload" id="fileUploader">
                        <ul id="fileList">
                         
                        </ul>
    
                    </div>
                <div class="answerButtons">
                    <input class="anadir" type="button" onclick="handleAnswerInsert(this)" value="Enviar">
                    <input class="anadir" type="reset" placeholder="Borrar">
                </div>
            </div>
        </form>
        </div>
    `;
}


function createPost(data) {
    return `
            <div class="conTags overflow-1">
                <p class="tags">${data.topic.name}</p>
            </div>
            <div class="conPreguntaTitu centrado">
                <h3 class="preguntaTitu">${data.title}</h3>
            </div>
            <div class="conTextoPre">
                    <p class="textoPre">${data.description}</p>
            </div>
            <div class="conAdjunto">
                <p class="publishedBy">
                <span style="font-weight: bold">Publicado por: </span> <img src="${data.author.avatar}" id="userLogo" alt="User logo"><a href="/user/${data.author.id}">${data.author.username}</a>
                </p>
            </div>
    `;
}

function createPostAnswer(data) {
    //TODO: Attachments && Clickable upvote and favorite
    let attachmentList = "";
    data.attachments.forEach((attachment) => {
        attachmentList += `<li><i class="fa-solid fa-file"></i><a target="_blank" class="overflow-1" href="/api/attachments/id/${attachment.id}"> ${attachment.filename}</a></li>`;
    });
    let userInfo = [
        data.isFavorite ? 'yellow' : '',
        data.Upvoted ? 'green' : '',
    ]

    return `
        <div class="cajaRespuesta" answer-id="${data.id}">
        
            <div class="conTextoRes">
                <p class="textoRes">${data.message}</p>
            </div>
            <div class="conAdjunto2">
                <p class="adjunto">Archivos adjuntos:</p>
            </div>
            <div class="conArchivosRe">
                <div class="listaAdjuntos">
                  <ul class="listaFicheros">
                    ${attachmentList}
                  </ul>
                </div>
            </div>
            <div class="contenedorAbajo">
                <div class="conUsuario">
                    <div class="publishedBy">
                    <span style="font-weight: bold">Publicado por: </span><img src="${data.author.avatar}" id="userLogo" alt="User logo"> <a href="/user/${data.author.id}">${data.author.username}</a>
                    </div>
                </div>
                <div class="parteDrc">
                   <span class="star" onclick="favouriteAnswer(this)"><i class="fa-solid fa-star ${userInfo[0]}"></i> <span>${data.favourites}</span></span>
                   <span class="up" onclick="upvotePost(this)"><i class="fa-solid fa-up ${userInfo[1]}"></i> <span>${data.upvotes}</span></span>
                </div>
            </div>
        </div>
    `;
}

function closePost() {
    const postId = window.location.pathname.split("/")[2];
    Swal.fire({
        title: '¿Estás seguro?',
        text: "¡No podrás revertir esto!",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#3085d6',
        cancelButtonColor: '#d33',
        confirmButtonText: 'Sí, cerrar pregunta!'
    }).then((result) => {
        if (result.isConfirmed) {
            disablePost(postId).then((response) => {
                return response.json();
            }).then((data) => {
                    if (data.status === "success") {
                        Swal.fire(
                            'Cerrada!',
                            'La pregunta ha sido cerrada.',
                            'success'
                        ).then(() => {
                            disableButtons();
                        });

                    }

                }
            )
        } else {
            Swal.fire(
                'Cancelado',
                'La pregunta no ha sido cerrada',
                'error'
            )
        }
    });
}

function disableButtons() {
    const buttons = document.querySelectorAll("#medio input[type=button]");
    buttons.forEach((button) => {
        button.classList.add("hidden");
    });
}

function loadPost(page = 1, reload = false) {
    const postId = window.location.pathname.split("/")[2];
    if (!postId || postId.length < 1) {
        window.location.href = '/';
    }
    getPost(postId).then((response) => {
        return response.json();
    }).then((data) => {
        if (data.status === "success") {
            //Generate a div with the class cajaPregunta
            let post = document.createElement("div");
            post.classList.add("cajaPregunta");
            post.innerHTML = createPost(data.data);
            if (!data.data.active){
                disableButtons();
            }
            //Replace the skeleton with the post
            if (!reload) {
                let postSkeleton = document.querySelector('#cajaPreguntaSkeleton');
                if (postSkeleton) {
                    let postParent = postSkeleton.parentElement;
                    postParent.replaceChild(post, postSkeleton);
                }
            }

        } else {
            showToast(data.message, "error", () => {
                window.location.href = "/";
            });
        }
    }).catch((error) => {
        console.error(error);
        showToast("Error desconocido", "error", () => {
            window.location.href = "/";
        });
    });
    loadAnswers(postId, page, reload);
}

async function loadAnswers(postId, page, reload = false) {
    getPostAnswers(postId, page).then((response) => {
        return response.json();
    }).then((data) => {
        let answersContainer = document.getElementById("contenedorRespuestas");
        if (data.status === "success") {
            // Delete all but hiddenAnswer
            if (!reload) {
                answersContainer.innerHTML = hiddenPostAnswer();
            }
            // Add the answers
            if (data.data.answers.length > 0) {
                data.data.answers.forEach((answer) => {
                    answersContainer.innerHTML += createPostAnswer(answer);
                });
            } else {
                if (!reload) {
                    answersContainer.innerHTML = hiddenPostAnswer();
                }
                if (page !== 1) {
                    if (document.querySelectorAll("#contenedorRespuestas .centrado").length === 0)
                        answersContainer.innerHTML += `<h4 class="centrado">No hay respuestas aun en este post</h4>`;
                } else {
                    answersContainer.innerHTML += `<h4 class="centrado">No hay respuestas aun en este post</h4>`;
                }
            }
            if (!reload) {
                document.getElementById("fileUploader").addEventListener("change", (event) => {
                    if (event.target.files.length > 0) {
                        handleFileUpload(event);
                    }
                });
            }
        }
    }).catch((error) => {
        showToast("Error desconocido obteniendo las respuestas", "error", () => {
        });
    });

}

function favouriteAnswer(element) {
    //check if the element `element` has a color set
    let childStar = element.children[0];
    let counter = element.children[1];
    let action;
    if (childStar.classList.contains("yellow")) {
        //if the color is set, remove it and set the action to remove
        childStar.classList.remove("yellow");
        action = "remove";
    } else {
        //if the color is not set, set it and set the action to add
        childStar.classList.add("yellow");
        action = "add";
    }
    const answerId = element.parentElement.parentElement.parentElement.getAttribute("answer-id");
    fetch(`/api/user/manageFavAnswers`, {
        method: 'POST',
        headers: {
            "Content-Type": "application/x-www-form-urlencoded"
        },
        body: new URLSearchParams({
            id: answerId,
            method: action
        })
    }).then((response) => {
        return response.json();
    }).then((data) => {
        if (data.status === "success") {
            showToast(data.message, "success", () => {
            });
            switch (action) {
                case "add":
                    counter.innerHTML = counter.innerHTML === "" ? 1 : " " + (parseInt(counter.innerHTML) + 1);
                    break;
                case "remove":

                    counter.innerHTML = counter.innerHTML === "" ? 0 : " " + (parseInt(counter.innerHTML) - 1);
                    break;
            }
        } else {
            showToast(data.message, "error", () => {
            });
        }
    }).catch((error) => {
        console.log(error);
        showToast("Error desconocido", "error", () => {
        });
    });

}


function upvotePost(element) {
    const answerId = element.parentElement.parentElement.parentElement.getAttribute("answer-id");
    let childArrow = element.children[0];

    let counter = element.children[1];
    fetch(`/api/posts/manageAnswers`, {
        method: 'POST',
        headers: {
            "Content-Type": "application/x-www-form-urlencoded"
        },
        body: new URLSearchParams({
            id: answerId,
            action: "upvote"
        })
    }).then((response) => {
        return response.json();
    }).then((data) => {
        if (data.status === "success") {
            childArrow.classList.contains("green") ? childArrow.classList.remove("green") : childArrow.classList.add("green");
            console.log(counter.innerHTML == "");
            if (childArrow.classList.contains("green")) {
                counter.innerHTML = counter.innerHTML === "" ? 1 : " " + (parseInt(counter.innerHTML) + 1);
            } else {
                counter.innerHTML = counter.innerHTML === "" ? 0 : " " + (parseInt(counter.innerHTML) - 1);
            }
            showToast(data.message, "success", () => {
            });
        } else {
            showToast(data.message, "error", () => {
            });
        }
    }).catch((error) => {
        console.log(error);
        showToast("Error desconocido", "error", () => {
        });
    });

}

function handleAnswerInsert(element) {
    //Get the post id from the url
    const postId = window.location.pathname.split("/")[2];
    // Get the form using the element
    let form = element.parentElement.parentElement.parentElement;
    // Get the text area from the form with name text
    let message = form.querySelector("textarea[name='text']").value;

    // Get the file list
    let files = document.getElementById("fileList").children;
    insertAnswer(postId, message).then((response) => {
        return response.json();
    }).then(async (data) => {
        if (data.status === "success") {
            let answerId = data.answerId;
            for (const file of Array.from(files)) {
                let fileId = file.getAttribute("file-id");
                await addAttachmentAnswer(answerId, fileId);
            }
            showToast(data.message, "success", () => {
                loadPost(1, false);
                for (let i = 2; i < currentPage; i++) {
                    loadPost(i, true);
                }


            });
        } else {
            showToast(data.message, "error", () => {
            });
        }
    }).catch((error) => {
        console.log(error);
        showToast("Error desconocido", "error", () => {
        });
    });
}

function deleteFile(element) {
    let file = element.parentElement;
    deleteAttachment(file.getAttribute("file-id")).then((response) => {
        return response.json();
    }).then((data) => {
        if (data.status === "success") {
            file.remove();
        } else {
            showToast(data.message, "error", () => {
            });
        }
    });
}

function handleFileUpload(event) {
    // Get the file
    let file = event.target.files[0];
    // Get the file name
    let fileName = file.name;
    let fileList = event.target.parentElement.querySelector("ul");
    //check if file list has 3 files
    if (fileList.children.length >= 3) {
        showToast("Solo se pueden subir 3 archivos", "error", () => {
        });
        event.target.value = "";
        return;
    }
    // insert the file to the server
    insertAttachment(file).then((response) => {
        return response.json();
    }).then((data) => {
        if (data.status === "success") {
            // get the list inside the event target
            let list = event.target.parentElement.querySelector("ul");
            // create a list item
            let listItem = document.createElement("li");
            listItem.setAttribute("file-id", data.id);
            listItem.innerHTML = `<i class="fa-solid fa-files"></i> ${fileName} <i class="fa-solid fa-circle-xmark red" onclick="deleteFile(this)"></i>`;
            list.appendChild(listItem);
            //Clear the file input
            event.target.value = "";

        } else {
            showToast(data.message, "error", () => {
            });
        }
    }).catch((error) => {
        showToast("Error desconocido", "error", () => {
        });
        //Clear the file input
        event.target.value = "";
    });
}

function loadMore() {
    // Get the answer list element and count
    let answerList = document.getElementById("contenedorRespuestas");
    if (answerList.children.length === answerCount) {
        return;
    }
    answerCount = answerList.children.length + 1;
    // Get the post id from the url
    const postId = window.location.pathname.split("/")[2];
    currentPage++;
    loadAnswers(postId, currentPage, true);


}

window.addEventListener("load", () => {
    loadPost();
});


function switchHiddenAnswer() {
    let answer = document.getElementById("agregarRespuesta");
    // check if the
    if (answer.classList.contains("hidden")) {
        answer.classList.remove("hidden");
        document.getElementById("anadir").value = "Ocultar";
    } else {
        answer.classList.add("hidden");
        document.getElementById("anadir").value = "Añadir";
    }
}