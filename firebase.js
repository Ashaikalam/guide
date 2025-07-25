// Import the functions you need from the SDKs you need
import { initializeApp } from "firebase/app";
// TODO: Add SDKs for Firebase products that you want to use
// https://firebase.google.com/docs/web/setup#available-libraries

// Your web app's Firebase configuration
const firebaseConfig = {
  apiKey: "AIzaSyB6p9gtCGpBksjMuh2RzK6uU041k_pGtsM",
  authDomain: "guide-75cd0.firebaseapp.com",
  projectId: "guide-75cd0",
  storageBucket: "guide-75cd0.firebasestorage.app",
  messagingSenderId: "812430810168",
  appId: "1:812430810168:web:43cba3744b36e6a8f79acd"
};

// Initialize Firebase
const app = initializeApp(firebaseConfig);
firebase.initializeApp(firebaseConfig);
const auth = firebase.auth();
const db = firebase.firestore();
const storage = firebase.storage();
