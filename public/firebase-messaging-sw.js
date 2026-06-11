importScripts(
    'https://www.gstatic.com/firebasejs/10.13.2/firebase-app-compat.js'
    );
    
    importScripts(
    'https://www.gstatic.com/firebasejs/10.13.2/firebase-messaging-compat.js'
    );
    
    firebase.initializeApp({
        apiKey: "AIzaSyAuEny5YjvHn_XH1wfsOUEzo-k9UZHiGZE",
        authDomain: "aquasync-6710b.firebaseapp.com",
        projectId: "aquasync-6710b",
        storageBucket: "aquasync-6710b.firebasestorage.app",
        messagingSenderId: "50686905162",
        appId: "1:50686905162:web:3126e1819b6e8631920fb3",
    });
    
    const messaging =
        firebase.messaging();
    
    messaging.onBackgroundMessage(
        function(payload) {
    
            self.registration.showNotification(
                payload.notification.title,
                {
                    body:
                        payload.notification.body,
                    icon:
                        '/icons/icon-192.png'
                }
            );
    
        }
    );