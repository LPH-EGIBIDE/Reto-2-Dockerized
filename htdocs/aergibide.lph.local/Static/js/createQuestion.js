async function insertPost(title, content, topicId) {
    return fetch(`/api/posts/managePost`, {
        method: 'POST',
        headers: {
            "Content-Type": "application/x-www-form-urlencoded"
        },
        body: new URLSearchParams({
            action: 'insert',
            title: title,
            content: content,
            topic: topicId
        })
    });
}

function handleInsertPost(formElement) {
    let title = formElement.title.value;
    let content = formElement.content.value;
    let topicId = formElement.tag.value;
    insertPost(title, content, topicId).then(response => {
        return response.json();
    }).then(data => {
        if(data.status === 'success') {
            showToast(data.message, 'success', () => {
                //Set a cookie that contains last used the topic id
                document.cookie = `lastUsedTopicId=${topicId}`;
                window.location.href = `/post/${data.postId}`;
            });
        } else {
            showToast(data.message,'error');
        }
    }).catch(error => {
        console.log(error);
        showToast(error.message, 'error');
        formElement.reset();
    });
}

document.getElementById("createPost").addEventListener("submit", function(event) {
    event.preventDefault();
    handleInsertPost(this);
});