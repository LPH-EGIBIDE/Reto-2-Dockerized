let currentPage = 1;
let currentFormData = new FormData();
async function filterPosts(formData) {
    formData.set("page", currentPage);
    return fetch(`/api/posts/filterPosts`, {
        method: 'POST',
        body: formData
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
function getPosts(reload = false) {
    if (reload) {
        hidePostView();
    }
    return filterPosts(currentFormData).then(response => {
        return response.json();
    }).then(data => {
        if (data.status === "success") {
            let postsContainer = document.getElementById("contenedorRespuestas");
            if (reload){
                currentPage = 1
                postsContainer.innerHTML = "";
            }
            let posts = data.posts;
            posts.forEach(post => {
                postsContainer.innerHTML += postTemplate(post);
            });
            showPostView();
            return true
        } else {
            console.log(data);
            showToast(data.message, "error");
            showPostView();
            return false
        }
    }).catch(error => {
        console.log(error);
        showToast("Error al cargar las preguntas", "error");
    });
}

function searchPosts(formElement) {
    currentPage = 1;
    currentFormData = new FormData(formElement);
    saveSearchParameters();
    getPosts(true);
}


function restoreSearchParameters() {
    // Get the search parameters from local storage
    let searchParameters = localStorage.getItem("searchParameters");
    let formElement = document.getElementById("filterForm");
    if (searchParameters !== null) {
        searchParameters = JSON.parse(searchParameters);
        // Restore the search parameters using form element
        for (let key in searchParameters) {
                if (formElement.elements[key] !== undefined) {
                formElement.elements[key].value = searchParameters[key];
            }
        }
    }
    // Search posts
    searchPosts(formElement);
}

function saveSearchParameters() {
    let formElement = document.getElementById("filterForm");
    let searchParameters = {};
    for (let key in formElement.elements) {
        if (formElement.elements[key].value !== "") {
            searchParameters[key] = formElement.elements[key].value;
        }
    }
    localStorage.setItem("searchParameters", JSON.stringify(searchParameters));
}

function morePosts() {
    currentPage++;
    getPosts(false).then(r => {
        if (!r) {
            currentPage--;
        }
    });
}

window.addEventListener("load", () => {
    restoreSearchParameters();
});