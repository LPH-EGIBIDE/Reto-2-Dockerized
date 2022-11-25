//Eventos del html
const signUpButton = document.getElementById('signUp');
const signInButton = document.getElementById('signIn');
const signUpButton2 = document.getElementById('signUp2');
const signInButton2 = document.getElementById('signIn2');
const container = document.getElementById('container');

//Funciones para html
signUpButton.addEventListener('click', () => {
	container.classList.add("right-panel-active");
});

signInButton.addEventListener('click', () => {
	container.classList.remove("right-panel-active");
});


signUpButton2.addEventListener('click', () => {
	container.classList.add("cambio");
	container.classList.remove("cambio2");
});

signInButton2.addEventListener('click', () => {
	container.classList.add("cambio2");
	container.classList.remove("cambio");
});


//Recogida de datos

document.getElementById("loginForm").addEventListener("submit", function(e){
	e.preventDefault();
	performLogin(e.target);
});

document.getElementById("signupForm").addEventListener("submit", function(e){
	e.preventDefault();
	performRegister(e.target);
});


function performLogin(formElement){
	//Recogemos los datos del formulario por el campo name
	let username = formElement.username.value;
	let password = formElement.password.value;
	//Creamos un objeto con los datos
	let data = {
		username: username,
		password: password
	}
	//Seteamos el elemento loginBtn a un spinner con fontawesome
	document.getElementById("loginBtn").innerHTML = '<i class="fas fa-spinner fa-spin"></i>';

	//Creamos una petición POST a la API con contenido urlencoded
	fetch("/api/users/authenticateUser", {
		method: "POST",
		headers: {
			"Content-Type": "application/x-www-form-urlencoded"
		},
		body: new URLSearchParams(data)
	})
	.then(response => response.json())
	.then(data => {
		switch (data.status) {
			case "success":
				showToast(data.message, "success", () => {
					window.location.href = "/";
				});
				break;
			case "continueLogin":
				handleMfa(data.message);
				break;
			case "error":
				showToast(data.message, "error", () => {});
				break;
			default:
				showToast("Error desconocido", "error", () => {});
				break;
		}
		//Seteamos el elemento loginBtn a Iniciar Sesión
		document.getElementById("loginBtn").innerHTML = 'Iniciar Sesión';
	});

}

function performRegister(formElement){
	//Recogemos los datos del formulario por el campo name
	let username = formElement.username.value;
	let password = formElement.password.value;
	let email = formElement.email.value;
	//Creamos un objeto con los datos
	let data = {
		username: username,
		password: password,
		email: email
	}
	//Seteamos el elemento loginBtn a un spinner con fontawesome
	document.getElementById("regBtn").innerHTML = '<i class="fas fa-spinner fa-spin"></i>';

	//Creamos una petición POST a la API con contenido urlencoded
	fetch("/api/users/registerUser", {
		method: "POST",
		headers: {
			"Content-Type": "application/x-www-form-urlencoded"
		},
		body: new URLSearchParams(data)
	})
		.then(response => response.json())
		.then(data => {
			switch (data.status) {
				case "success":
					showToast(data.message, "success", () => {
						window.location.href = "/login";
					});
					break;
				case "error":
					showToast(data.message, "error", () => {});
					break;
				default:
					showToast("Error desconocido", "error", () => {});
					break;
			}
			//Seteamos el elemento signUp2 a Registrarse
			document.getElementById("regBtn").innerHTML = 'Registrarse';
		});

}

function resetPasswordEmailModal(){
	//Creamos un modal de SweetAlert2 para pedir el email y hacemos una petición POST a la API
	Swal.fire({
		title: 'Recuperar contraseña',
		input: 'email',
		inputLabel: 'Introduce tu email',
		inputPlaceholder: 'Introduce tu email',
		showCancelButton: true,
		confirmButtonText: 'Enviar',
		showLoaderOnConfirm: true,
		preConfirm: (email) => {
			return fetch(`/api/users/resetPassword`, {
				method: "POST",
				headers: {
					"Content-Type": "application/x-www-form-urlencoded"
				},
				body: new URLSearchParams({
					email: email
				})
			})
				.then(response => response.json())
				.then(data => {
					if (data.status === "error") {
						throw new Error(data.message)
					}
					return data.message;
				})
		},
		allowOutsideClick: () => !Swal.isLoading()
	}).then((result) => {
		//Show a toast with the result with the returned message from the API
		showToast(result.value, "success", () => {});
	}).catch((error) => {
		//Show a toast with the error message
		showToast(error.message, "error", () => {});
	});
}

function resetPasswordModal(token){
	//Creamos un modal de SweetAlert2 para pedir una nueva contraseña y hacemos una petición POST a la API con el token
	//Check that the password is at least 8 characters long
	Swal.fire({
		title: 'Nueva contraseña',
		input: 'password',
		inputLabel: 'Introduce tu nueva contraseña',
		inputPlaceholder: 'Introduce tu nueva contraseña',

		showCancelButton: true,
		confirmButtonText: 'Cambiar',
		showLoaderOnConfirm: true,
		preConfirm: (password) => {
			return fetch(`/api/users/resetPassword`, {
				method: "POST",
				headers: {
					"Content-Type": "application/x-www-form-urlencoded"
				},
				body: new URLSearchParams({
					password: password,
					token: token
				})
			})
				.then(response => response.json())
				.then(data => {
					if (data.status === "error") {
						throw new Error(data.message)
					}
					return data.message;
				})
		},
		allowOutsideClick: () => !Swal.isLoading()
	}).then((result) => {
		//Show a toast with the result with the returned message from the API
		showToast(result.value, "success", () => {});
	}).catch((error) => {
		//Show a toast with the error message
		showToast(error.message, "error", () => {
			resetPasswordModal(token);
		});
	});
}

//

function handleMfa(message){
	//Show a sweetalert with a form to enter the MFA code
	Swal.fire({
		title: "Verificación en dos pasos",
		text: message,
		input: "text",
		inputAttributes: {
			autocapitalize: "off"
		},
		showCancelButton: true,
		confirmButtonText: "Verificar",
		showLoaderOnConfirm: true,
		preConfirm: (code) => {
			return fetch("/api/users/twoFactorAuthentication", {
				method: "POST",
				headers: {
					"Content-Type": "application/x-www-form-urlencoded"
				},
				body: new URLSearchParams({
					mfaCode: code
				})
			})
			.then(response => response.json())
			.then(data => {
				switch (data.status) {
					case "success":
						showToast(data.message, "success", () => {
							window.location.href = "/";
						});
						break;
					case "error":
						showToast(data.message, "error", () => {
							handleMfa();
						});

						break;
					default:
						showToast("Error desconocido", "error", () => {
							handleMfa();
						});
						handleMfa();
						break;
				}
			});
		},
		allowOutsideClick: () => !Swal.isLoading()
	});
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

//Comprobar si en la URL hay un token para resetear la contraseña al cargar la página

window.onload = function(){
	if (window.location.search.includes("token")) {
		resetPasswordModal(window.location.search.split("=")[1]);
	}
}