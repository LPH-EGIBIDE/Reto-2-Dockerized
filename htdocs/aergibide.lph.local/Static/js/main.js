async function fetchNotifications() {
    fetch("/api/user/manageNotifications").then((response) => {
        return response.json();
    }).then((data) => {
        if (data.status === "success") {
            let notifications = data.notifications;
            saveNotifications(notifications);
            let notificationContainer = document.querySelector("#notificationContainer");
            notificationContainer.innerHTML = "";
            document.getElementById("notifiQuant").innerHTML = `${notifications.length} `;
            notifications.forEach((notification) => {
                notificationContainer.innerHTML += notificationTemplate(notification);
            });
        } else {
            throw new Error("Error fetching notifications");
        }
    }).catch((error) => {
        showToast("Cargando notificaciones guardadas localmente", "warning", {});
        getOfflineNotifications();
    });
}


function dismissNotification(notificationElement) {
    let notificationId = notificationElement.parentElement.getAttribute("notification-id");
    fetch(`/api/user/manageNotifications`, {
        method: "POST",
        headers: {
            "Content-Type": "application/x-www-form-urlencoded"
        },
        body: `method=dismiss&id=${notificationId}`

    }).then((response) => {
        return response.json();
    }).then((data) => {
        if (data.status === "success") {
            showToast(data.message, "success", {});
            notificationElement.parentElement.remove();
            fetchNotifications();
        } else {
            showToast("Error eliminando notificacion", "error", {});
        }
    }).catch((error) => {
        console.log(error);
        showToast("Error eliminando notificacion", "error", {});
    });
}


function saveNotifications(notifications) {
    // Save notifications to indexedDB
    let db;
    let request = window.indexedDB.open("notifications", 1);
    request.onerror = function(event) {
        console.log("Error opening database");
    }
    request.onsuccess = function(event) {
        db = event.target.result;
        let objectStore = db.transaction("notifications", "readwrite").objectStore("notifications");
        objectStore.clear();
        notifications.forEach((notification) => {
            objectStore.add(notification);
        });
        db.close();
    }
    request.onupgradeneeded = function(event) {
        db = event.target.result;
        let objectStore = db.createObjectStore("notifications", { keyPath: "id" });
    }
}

function getOfflineNotifications() {
    let db;
    let request = window.indexedDB.open("notifications", 1);
    request.onerror = function(event) {
        console.log("Error opening database");
    }
    request.onsuccess = function(event) {
        db = event.target.result;
        let objectStore = db.transaction("notifications").objectStore("notifications");
        objectStore.getAll().onsuccess = function(event) {
            let notifications = event.target.result;
            let notificationsHTML = "";
            document.getElementById("notifiQuant").innerHTML = `${notifications.length} `;
            notifications.forEach((notification) => {
                notificationsHTML += notificationTemplate(notification);
            });
            document.querySelector("#notificationContainer").innerHTML = notificationsHTML;
            db.close();
        }
    }
}
function notificationTemplate(notificationData) {
    let notificationTypeIcons = {
        0: "fa-regular fa-bell",
        1: "fa-regular fa-up",
        2: "fa-solid fa-trophy-star",
    }
    return `
    <div class="notificacion hoverElement" notification-id="${htmlEncode(notificationData.id)}">
        <i class="${htmlEncode([notificationData.type])} notifiIcon" ></i>
        <p class="mensaje"><a href="${htmlEncode(notificationData.href)}">${htmlEncode(notificationData.text)}</a></p>
        <div class="fecha">
            <p>Hace: 1 dia</p>
        </div>
        <i class="fa-solid fa-trash basura" ></i>
    </div>
    `;
}

function showToast(message, type, callback){
    const Toast = Swal.mixin({
        toast: true,
        position: 'top-end',
        showConfirmButton: false,
        timer: 2000,
        timerProgressBar: true
    })
    Toast.fire({
        icon: type,
        title: message
    }).then(callback)
}


function htmlEncode(str){
    return String(str).replace(/[^\w. ]/gi, function(c){
        return '&#'+c.charCodeAt(0)+';';
    });
}

window.addEventListener("load", () => {
    fetchNotifications();
    document.querySelector("#notificationContainer").addEventListener("click", (event) => {
        if (event.target.classList.contains("basura")) {
            dismissNotification(event.target);
        }
    });
});


