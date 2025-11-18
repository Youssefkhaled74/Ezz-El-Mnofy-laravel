importScripts('https://www.gstatic.com/firebasejs/8.10.1/firebase-app.js');
importScripts('https://www.gstatic.com/firebasejs/8.10.1/firebase-messaging.js');
let config = {
        apiKey: "AIzaSyA6phFMDk3GUYUTSOLOMmzRf_dn_rW40QU",
        authDomain: "ezz-el-mnofy.firebaseapp.com",
        projectId: "ezz-el-mnofy",
        storageBucket: "ezz-el-mnofy.firebasestorage.app",
        messagingSenderId: "220371239685",
        appId: "1:220371239685:web:54b3f70965e5c3d730ad6c",
        measurementId: "G-546TRTB2PF",
 };
firebase.initializeApp(config);
const messaging = firebase.messaging();
messaging.onBackgroundMessage((payload) => {
    const notificationTitle = payload.notification.title;
    const notificationOptions = {
        body: payload.notification.body,
        icon: '/images/default/firebase-logo.png'
    };
    self.registration.showNotification(notificationTitle, notificationOptions);
});
