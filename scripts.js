function userLogin(e) {
  e.preventDefault();
  const email = document.getElementById('userEmail').value;
  const password = document.getElementById('userPassword').value;

  firebase.auth().signInWithEmailAndPassword(email, password)
    .then(cred => {
      return db.collection('users').doc(cred.user.uid).get();
    })
    .then(doc => {
      if (doc.exists && doc.data().role === "user") {
        window.location.href = "guides.html";
      } else {
        alert("Access denied. Not a general user.");
        firebase.auth().signOut();
      }
    })
    .catch(err => alert(err.message));
}

function adminLogin(e) {
  e.preventDefault();
  const email = document.getElementById('adminEmail').value;
  const password = document.getElementById('adminPassword').value;

  firebase.auth().signInWithEmailAndPassword(email, password)
    .then(cred => {
      return db.collection('users').doc(cred.user.uid).get();
    })
    .then(doc => {
      if (doc.exists && doc.data().role === "admin") {
        window.location.href = "dashboard.html";
      } else {
        alert("Access denied. Not an admin.");
        firebase.auth().signOut();
      }
    })
    .catch(err => alert(err.message));
}
