// Import the functions you need from the SDKs
import { initializeApp } from "firebase/app";
import { getAnalytics } from "firebase/analytics";

// Your web app's Firebase configuration
const firebaseConfig = {
    apiKey: "AIzaSyA4fupQFb5Lpgzxn3s18udZ70i_JaIK4jE",
    authDomain: "hospital-48835.firebaseapp.com",
    databaseURL: "https://hospital-48835-default-rtdb.asia-southeast1.firebasedatabase.app",
    projectId: "hospital-48835",
    storageBucket: "hospital-48835.firebasestorage.app",
    messagingSenderId: "1037938274017",
    appId: "1:1037938274017:web:4572f142a2b46e330411a0",
    measurementId: "G-F904JS2D7T"
};

// Initialize Firebase
const app = initializeApp(firebaseConfig);
const analytics = getAnalytics(app);

export { app, analytics }; // Optional: Export for use in other files
