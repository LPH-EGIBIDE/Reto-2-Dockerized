let userId;
let currentTab = "posts";
let currentPage = 1;

async function getUsersPosts(userId) {
    return fetch(`/api/posts/getPosts`, {
        method: 'POST',
        headers: {
            "Content-Type": "application/x-www-form-urlencoded"
        },
        body: new URLSearchParams({
            method: 'userPosts',
            userId: userId,
            page: String(currentPage)
        })
    });
}

async function toggleActive(formData) {
    return fetch(`/api/users/manageUser`, {
        method: 'POST',
        headers: {
            "Content-Type": "application/x-www-form-urlencoded"
        },
        body: formData
    });
}


function deactivateUser(){
    let userId = document.getElementById("userId").value;
    let formData = new URLSearchParams({
        method: 'deactivateAccount',
        userId: userId
    });
    toggleActive(formData).then((response) => {
        if (response.ok) {
            response.json().then((data) => {
                if (data.status === "success") {
                    showToast("Usuario desactivado correctamente", "success");
                    location.reload();
                }
            }).catch((err) => {
                console.error(err);
                showToast(err.message, "error");
            });
        }
    });
}

function reactivateUser(){
    let userId = document.getElementById("userId").value;
    let formData = new URLSearchParams({
        method: 'reactivateAccount',
        userId: userId
    });
    toggleActive(formData).then((response) => {
        if (response.ok) {
            response.json().then((data) => {
                if (data.status === "success") {
                    showToast("Usuario reactivado correctamente", "success");
                    location.reload();
                }
            }).catch((err) => {
                console.error(err);
                showToast(err.message, "error");
            });
        }
    });
}






async function getUsersFavuriteAnswers(userId) {
    return fetch(`/api/posts/getPosts`, {
        method: 'POST',
        headers: {
            "Content-Type": "application/x-www-form-urlencoded"
        },
        body: new URLSearchParams({
            userId: userId,
            method: 'userFavourites',
            page: String(currentPage)
        })
    });
}

async function getUsersAnswers(userId) {
    return fetch(`/api/posts/getPosts`, {
        method: 'POST',
        headers: {
            "Content-Type": "application/x-www-form-urlencoded"
        },
        body: new URLSearchParams({
            userId: userId,
            method: 'userAnswers',
            page: String(currentPage)
        })
    });
}


function postTemplate(post){
    //check if post has a post subobject
    let postObj = post.post !== undefined ? post.post : post;
    let author = postObj.author;
    let description = post.message ?? postObj.description;

    return `
    <div class="respuesta">
                <div class="contenedorLista">
                    <div class="contenedorIden">
                        <img src="${htmlEncode(author.avatar)}" alt="img Avatar" class="identificadoAva">
                    </div>
                    <div class="contenidoIzq">
                        <p class="tituPregunta overflow-1"><a class="unstyledLink" href="/post/${htmlEncode(postObj.id)}">${htmlEncode(postObj.title)}</a></p>
                        <p class="decripPre overflow-1">${htmlEncode(description)}</p>
                        <ul class="listContent">
                            <li><p class="autor">Publicado por: <a href="/user/${htmlEncode(author.id)}">${htmlEncode(author.username)}</p></a></li>
                            <li><p class="topics">${htmlEncode(postObj.topic.name)}</p></li>
                            <li><i class="fa-regular fa-eye" id="visitas"><span class="numisitas"> ${htmlEncode(postObj.views)}</span></i></li>
                        </ul>
                    </div> 
                    
                </div>
               <hr>
            </div>`

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

async function loadUserPosts(userId) {
        getUsersPosts(userId).then((response) => {
            if (response.ok) {
                response.json().then((data) => {
                    let postList = document.getElementById("contenedorRespuestas");

                    if (data.status === "success") {
                        let posts = data.data.posts;
                        if (posts.length === 0 && currentPage === 1) {
                            postList.innerHTML = `<h2 class="centrado rellenoPosts">No hay posts para mostrar</h2>`;
                        }
                        posts.forEach((post) => {
                            postList.innerHTML += postTemplate(post);
                        });
                    }
                }).catch((err) => {
                    console.error(err);
                    showToast(err.message, "error");
                });
            }
        });
}

async function loadUserFavourites(userId) {
    getUsersFavuriteAnswers(userId).then((response) => {
        if (response.ok) {
            response.json().then((data) => {
                let postList = document.getElementById("contenedorRespuestas");

                if (data.status === "success") {
                    let posts = data.data.posts;
                    if (posts.length === 0 && currentPage === 1) {
                        postList.innerHTML = `<h2 class="centrado rellenoPosts">No hay posts para mostrar</h2>`;
                    }
                    posts.forEach((post) => {
                        postList.innerHTML += postTemplate(post);
                    });
                }
            }).catch((err) => {
                console.error(err);
                showToast(err.message, "error");
            });
        }
    });
}

async function loadUserAnswers(userId) {
    getUsersAnswers(userId).then((response) => {
        if (response.ok) {
            response.json().then((data) => {
                let postList = document.getElementById("contenedorRespuestas");

                if (data.status === "success") {
                    let posts = data.data.posts;
                    if (posts.length === 0 && currentPage === 1) {
                        postList.innerHTML = `<h2 class="centrado rellenoPosts">No hay posts para mostrar</h2>`;
                    }
                    posts.forEach((post) => {
                        postList.innerHTML += postTemplate(post);
                    });
                }
            }).catch((err) => {
                console.error(err);
                showToast(err.message, "error");
            });
        }
    });
}



function setupListeners() {
    hidePostView();
    //get navOpciones children and add event listener to each one
    let navOpciones = document.querySelector(".navOpciones");
    let navOpcionesChildren = navOpciones.children;
    document.getElementById("mas").addEventListener("", () => {
        morePosts();
    });
    for (let i = 0; i < navOpcionesChildren.length; i++) {
        navOpcionesChildren[i].addEventListener("click", (e) => {
            currentPage = 1;
            let clickedElement = e.target;
            if (clickedElement.classList.contains("active")) {
                return;
            }

            // Remove active class from all navOpciones children
            for (let i = 0; i < navOpcionesChildren.length; i++) {
                navOpcionesChildren[i].classList.remove("active");
            }

            // Get tab name
            let tabName = clickedElement.getAttribute("data-tab");
            // Get current tab using navOpcionesChildren
            navOpciones.querySelector(".active").classList.remove("active");
            // Add active class to clicked element
            clickedElement.classList.add("active");
            // Show clicked tab
           clickedElement.classList.add("active");
            // Set current tab
            currentTab = tabName;
            // Load posts and clear posts container
            document.getElementById("contenedorRespuestas").innerHTML = "";
            if (tabName === "posts") {
                loadUserPosts(userId).then(() => {
                    showPostView();
                });
            } else if (tabName === "favourites") {
                loadUserFavourites(userId).then(() => {
                    showPostView();
                });
            } else if (tabName === "answers") {
                loadUserAnswers(userId).then(() => {
                    showPostView();
                });
            }
        });
    }
}

function morePosts() {
    currentPage++;
    switch (currentTab) {
        case "posts":
            loadUserPosts(userId);
            break;
        case "favourites":
            loadUserFavourites(userId);
            break;
        case "answers":
            loadUserAnswers(userId);
            break;
    }
}

window.addEventListener("load", () => {
    userId = document.getElementById("userId").value;
    setTimeout(() => {
        setupListeners();
        loadUserPosts(userId).then(() => {
            showPostView();
        });
    }, 1000);
});
