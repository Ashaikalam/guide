function userLogin(e) {
  e.preventDefault();
  const email = document.getElementById('userEmail').value;
  const password = document.getElementById('userPassword').value;

  firebase.auth().signInWithEmailAndPassword(email, password)
    .then(() => window.location.href = 'guides.html')
    .catch(err => alert(err.message));
}

function adminLogin(e) {
  e.preventDefault();
  const email = document.getElementById('adminEmail').value;
  const password = document.getElementById('adminPassword').value;

  firebase.auth().signInWithEmailAndPassword(email, password)
    .then(() => window.location.href = 'dashboard.html')
    .catch(err => alert(err.message));
}

function addManual(e) {
  e.preventDefault();
  const title = document.getElementById('title').value;
  const steps = document.getElementById('steps').value;
  const imageFile = document.getElementById('imageUpload').files[0];

  if (imageFile) {
    const storageRef = firebase.storage().ref(`images/${imageFile.name}`);
    storageRef.put(imageFile).then(snapshot => {
      snapshot.ref.getDownloadURL().then(imageURL => {
        saveManual(title, steps, imageURL);
      });
    });
  } else {
    saveManual(title, steps, '');
  }
}

function saveManual(title, steps, imageURL) {
  db.collection('manuals').add({ title, steps, imageURL })
    .then(() => {
      alert('Manual added.');
      location.reload();
    })
    .catch(err => alert(err.message));
}

function displayManuals(containerId) {
  db.collection('manuals').get().then(snapshot => {
    const container = document.getElementById(containerId);
    container.innerHTML = '';
    snapshot.forEach(doc => {
      const data = doc.data();
      const div = document.createElement('div');
      div.innerHTML = `<h3>${data.title}</h3><p>${data.steps}</p>${data.imageURL ? `<img src=\"${data.imageURL}\" width=\"300\" />` : ''}<hr>`;
      container.appendChild(div);
    });
  });
}

window.onload = () => {
  if (document.getElementById('manualList')) displayManuals('manualList');
  if (document.getElementById('manualAdminList')) displayManuals('manualAdminList');
};
