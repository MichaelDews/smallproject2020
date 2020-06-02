var urlBase = 'http://cop4331contacts3.com/LAMPAPI';
var extension = 'php';

var userId = 0;
var firstName = "";
var lastName = "";
var pass;
var currentContactId = 0;

function doLogin() {
	userId = 0;
	firstName = "";
	lastName = "";

	var login = document.getElementById("loginName").value;
	var password = document.getElementById("loginPassword").value;
	console.log(pass)
	var hash = md5(password);
	document.getElementById("loginResult").innerHTML = "";
	
	var jsonPayload = '{"login" : "' + login + '", "password" : "' + hash + '"}';
	var url = urlBase + '/Login.' + extension;
	
	var xhr = new XMLHttpRequest();
	xhr.open("POST", url, false);
	xhr.setRequestHeader("Content-type", "application/json; charset=UTF-8");
	try {
		xhr.send(jsonPayload);
		
		var jsonObject = JSON.parse(xhr.responseText);
		
		userId = jsonObject.id;
		pass = hash;
		
		if (userId < 1) {
			document.getElementById("loginResult").innerHTML = "User/Password combination incorrect";
			return;
		}
		
		firstName = jsonObject.firstName;
		lastName = jsonObject.lastName;
		
		saveCookie();
		
		window.location.href = "contacts.html";
		document.getElementById("userName").innerHTML = login;

		return jsonObject;
	}
	catch (err) {
		document.getElementById("loginResult").innerHTML = err.message;
		return null;
	}
}

function createAccount() {
	userId = 0;

	var firstName = document.getElementById("firstName").value;
	var lastName = document.getElementById("lastName").value;
	var userName = document.getElementById("newLoginName").value;
	var password1 = document.getElementById("loginPassword1").value;
	var password2 = document.getElementById("loginPassword2").value;

	if (firstName == null || firstName == "", lastName == null || lastName == "",
		userName == null || userName == "", password1 == null || password1 == "", password2 == null || password2 == "") {
		document.getElementById("createAccountResult").innerHTML = "Please fill all required fields";
		return;
	}

	if (password1 != password2) {
		document.getElementById("createAccountResult").innerHTML = "Passwords do not match. Please try again";
		return;
	}

	document.getElementById("createAccountResult").innerHTML = "";

	var hash = md5(password1);
	var jsonPayload = `{"firstName": "${firstName}", "lastName": "${lastName}", "login": "${userName}", "password": "${hash}"}`;
	var url = urlBase + '/AddLogin.' + extension;
	
	console.log(jsonPayload);
	var xhr = new XMLHttpRequest();
	xhr.open("POST", url, false);
	xhr.setRequestHeader("Content-type", "application/json; charset=UTF-8");
	try {
		xhr.send(jsonPayload);
		
		var jsonObject = JSON.parse(xhr.responseText);
		
		userId = jsonObject.id;
		pass = hash;
		
		saveCookie();
		
		window.location.href = "index.html";
		//document.getElementById("userName").innerHTML = login;

	}
	catch (err) {
		document.getElementById("createAccountResult").innerHTML = err.message;
	}
}

function saveCookie() {
	var minutes = 20;
	var date = new Date();
	date.setTime(date.getTime() + (minutes * 60 * 1000));
	document.cookie = "firstName=" + firstName + ",lastName=" + lastName + ",userId=" + userId + ",pass=" + pass + ";expires=" + date.toGMTString();
}

function readCookie() {
	userId = -1;
	var data = document.cookie;
	var splits = data.split(",");
	for (var i = 0; i < splits.length; i++) {
		var thisOne = splits[i].trim();
		var tokens = thisOne.split("=");
		if (tokens[0] == "firstName") {
			firstName = tokens[1];
		}
		else if (tokens[0] == "lastName") {
			lastName = tokens[1];
		}
		else if (tokens[0] == "userId") {
			userId = parseInt(tokens[1].trim());
		}
		else if (tokens[0] == "pass") {
			pass = tokens[1];
		}
	}

	if (userId < 0) {
		window.location.href = "index.html";
	}
	else {
		document.getElementById("userName").innerHTML = firstName + " " + lastName;
	}
}

function doLogout() {
	userId = 0;
	firstName = "";
	lastName = "";
	document.cookie = "firstName= ; expires = Thu, 01 Jan 1970 00:00:00 GMT";
	window.location.href = "index.html";
}

function changePassword() {
	var oldPassword = document.getElementById("oldPass").value;
	var newPass1 = document.getElementById("newPass1").value;
	var newPass2 = document.getElementById("newPass2").value;

	if (oldPassword == null || oldPassword == "",
		newPass1 == null || newPass1 == "", newPass2 == null || newPass2 == "") {
		document.getElementById("changePasswordResult").innerHTML = "Please fill all required fields";
		return;
	}

	if (newPass1 != newPass2) {
		document.getElementById("changePasswordResult").innerHTML = "New passwords do not match. Please try again";
		return;
	}

	if(md5(oldPassword) != pass) {
		document.getElementById("changePasswordResult").innerHTML = "Current password is incorrect. Please try again";
		return;
	}
	
	var hash = md5(newPass1);
	var jsonPayload = `{"userId": "${userId}", "newpass": "${hash}"}`;
	var url = urlBase + '/ChangePassword.' + extension;

	console.log(jsonPayload);
	var xhr = new XMLHttpRequest();
	xhr.open("POST", url, false);
	xhr.setRequestHeader("Content-type", "application/json; charset=UTF-8");
	try {
		xhr.send(jsonPayload);
		
		saveCookie();

		window.location.href = "contacts.html";
		document.getElementById("userName").innerHTML = login;
	}
	catch (err) {
		document.getElementById("changePasswordResult").innerHTML = err.message;
	}

}

function createContact() {
	var firstName = document.getElementById("contactFirstName").value;
	var lastName = document.getElementById("contactLastName").value;
	var phoneNumber = document.getElementById("contactNumber").value;
	var email = document.getElementById("contactEmail").value;

	if (firstName == null || firstName == "") {
		document.getElementById("contactAddError").innerHTML = "Please provide at least a first name";
		return;
	}

	var jsonPayload = `{
		"firstname": "${firstName}",
		"lastname": "${lastName}",
		"email": "${email}",
		"phone": "${phoneNumber}",
		"userId": "${userId}"
	}`;

	var url = urlBase + '/AddContact.' + extension;

	var xhr = new XMLHttpRequest();
	xhr.open("POST", url, true);
	xhr.setRequestHeader("Content-type", "application/json; charset=UTF-8");
	try {
		xhr.onreadystatechange = function () {
			if (this.readyState == 4 && this.status == 200) {
				closeAddModal();
				document.getElementById("contactAddResult").innerHTML = "Contact has been added successfully";
				searchContact(true);
				setTimeout(function () {
					document.getElementById("contactAddResult").innerHTML = "";
				}, 5000);
			}
		};
		xhr.send(jsonPayload);
	}
	catch (err) {
		document.getElementById("contactAddError").innerHTML = err.message;

	}


}

function editContact() {
	var newFirstName = document.getElementById("newFirstName").value;
	var newLastName = document.getElementById("newLastName").value;
	var newNumber = document.getElementById("newNumber").value;
	var newEmail = document.getElementById("newEmail").value;

	if (newFirstName == null || newFirstName == "") {
		document.getElementById("contactEditError").innerHTML = "Please provide at least a first name";
		return;
	}

	var jsonPayload = `{
		"ID": "${currentContactId}",
		"newfirstName": "${newFirstName}",
		"newlastName": "${newLastName}",
		"newEmail": "${newEmail}",
		"newPhone": "${newNumber}"
	}`;

	console.log(jsonPayload)

	var url = urlBase + '/UpdateContact.' + extension;

	var xhr = new XMLHttpRequest();
	xhr.open("POST", url, true);
	xhr.setRequestHeader("Content-type", "application/json; charset=UTF-8");
	try {
		xhr.onreadystatechange = function () {
			if (this.readyState == 4 && this.status == 200) {
				closeEditModal();
				document.getElementById("contactAddResult").innerHTML = "Contact has been updated successfully";
				searchContact(true);
				setTimeout(function () {
					document.getElementById("contactAddResult").innerHTML = "";
				}, 5000);
			}
		};
		xhr.send(jsonPayload);
	}
	catch (err) {
		document.getElementById("contactEditError").innerHTML = err.message;
	}


}

function searchContact(flag) {

	setTimeout(function () {
		var srch = document.getElementById("searchText").value;
		var jsonPayload = `{
			"query": "${srch}",
			"ID": "${userId}"
		}`;

		var url = urlBase + '/Search2.' + extension;

		var table = document.getElementById("contacts").getElementsByTagName('tbody')[0];
		table.innerHTML = "";

		var xhr = new XMLHttpRequest();
		xhr.open("POST", url, true);
		xhr.setRequestHeader("Content-type", "application/json; charset=UTF-8");
		try {
			xhr.onreadystatechange = function () {
				if (this.readyState == 4 && this.status == 200) {
					var jsonObject = JSON.parse(xhr.responseText);
					for (var i = 0; i < jsonObject.result.length; i++) {
						var newRow = table.insertRow();
						var fName = newRow.insertCell(0);
						var fNameText = document.createTextNode(jsonObject.result[i].firstname);
						fName.appendChild(fNameText);

						var lName = newRow.insertCell(1);
						var lNameText = document.createTextNode(jsonObject.result[i].lastName);
						lName.appendChild(lNameText);

						var phone = newRow.insertCell(2);
						var phoneText = document.createTextNode(jsonObject.result[i].phone);
						phone.appendChild(phoneText);

						var email = newRow.insertCell(3);
						var emailText = document.createTextNode(jsonObject.result[i].email);
						email.appendChild(emailText);

						var date = newRow.insertCell(4);
						var dateText = document.createTextNode(jsonObject.result[i].DateRecorded);
						date.appendChild(dateText);

						var buttons = newRow.insertCell(5);
						var buttonsContent = document.createElement('div');
						buttonsContent.innerHTML = `
							<button type="button" id="editContactButton${i}" data-toggle="modal"
								data-target="#editContactModal"
								onclick="editContactModal('${jsonObject.result[i].firstname}', '${jsonObject.result[i].lastName}', '${jsonObject.result[i].phone}', '${jsonObject.result[i].email}', ${jsonObject.result[i].id})"	
							>
								<span class="glyphicon glyphicon-pencil" />
							</button>
							<button type="button" onclick="deleteContact(${jsonObject.result[i].id});">
								<span class="glyphicon glyphicon-trash" />
							</button>
						`
						buttons.appendChild(buttonsContent);

					}

					console.log(jsonObject);

					if (jsonObject.result.length < 1) {
						document.getElementById("contactAddResult").innerHTML = "No contacts were found";
						table.innerHTML = "";
					}

					else if (!flag) {
						document.getElementById("contactAddResult").innerHTML = "";
					}

					else {
						return;
					}


				}
			};
			xhr.send(jsonPayload);
		}
		catch (err) {
			console.log("no results");
			document.getElementById("contactAddResult").innerHTML = err.message;
		}
	}, 500);

}

function deleteContact(contactID) {
	var jsonPayload = `{
		"contactID": "${contactID}"
	}`;

	var url = urlBase + '/DeleteContact.' + extension;

	if (window.confirm("Are you sure you would like to delete this contact?")) {
		var xhr = new XMLHttpRequest();
		xhr.open("POST", url, true);
		xhr.setRequestHeader("Content-type", "application/json; charset=UTF-8");
		try {
			xhr.onreadystatechange = function () {
				if (this.readyState == 4 && this.status == 200) {
					document.getElementById("contactAddResult").innerHTML = "Contact has been deleted successfully";
					setTimeout(function () {
						document.getElementById("contactAddResult").innerHTML = "";
						searchContact(true);
					}, 5000);
				}
			};
			xhr.send(jsonPayload);
		}
		catch (err) {
			document.getElementById("contactAddError").innerHTML = err.message;
		}
	}


}

function editContactModal(firstName, lastName, phone, email, contactId) {
	document.getElementById("newFirstName").value = firstName;
	document.getElementById("newLastName").value = lastName;
	document.getElementById("newEmail").value = email;
	document.getElementById("newNumber").value = phone;
	currentContactId = contactId;
}

function closeAddModal() {
	document.getElementById("contactAddError").innerHTML = "";
	document.getElementById("addModalClose").click();
	document.getElementById("addContactForm").reset();
}

function closeEditModal() {
	document.getElementById("contactEditError").innerHTML = "";
	document.getElementById("editModalClose").click();
}
