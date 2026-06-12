console.log('APP JS LOADED');
import './bootstrap';

import Alpine from 'alpinejs';

window.Alpine = Alpine;

Alpine.start();

import { initializeApp } from "firebase/app";
import {
    getMessaging,
    getToken,
    onMessage
} from "firebase/messaging";

const firebaseConfig = {
    apiKey: import.meta.env.VITE_FIREBASE_API_KEY,
    authDomain: import.meta.env.VITE_FIREBASE_AUTH_DOMAIN,
    projectId: import.meta.env.VITE_FIREBASE_PROJECT_ID,
    storageBucket: import.meta.env.VITE_FIREBASE_STORAGE_BUCKET,
    messagingSenderId: import.meta.env.VITE_FIREBASE_MESSAGING_SENDER_ID,
    appId: import.meta.env.VITE_FIREBASE_APP_ID,
};

const app = initializeApp(firebaseConfig);

const messaging = getMessaging(app);

async function initFirebase() {

    const permission =
        await Notification.requestPermission();

    if (permission !== "granted") {
        console.log("Notifikasi ditolak");
        return;
    }

    const token = await getToken(messaging, {
        vapidKey:
            import.meta.env.VITE_FIREBASE_VAPID_KEY
    });

    console.log("FCM TOKEN:", token);

    if (token) {

        await fetch('/fcm/token', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN':
                    document.querySelector(
                        'meta[name="csrf-token"]'
                    ).content
            },
            body: JSON.stringify({
                token
            })
        });

    }
}

initFirebase();

onMessage(messaging, (payload) => {

    console.log(payload);

    new Notification(
        payload.notification.title,
        {
            body:
                payload.notification.body
        }
    );

});