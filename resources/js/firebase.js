import { initializeApp } from "firebase/app";
import { getMessaging, getToken } from "firebase/messaging";

const firebaseConfig = {
  apiKey: "AIzaSyAuEny5YjvHn_XH1wfsOUEzo-k9UZHiGZE",
  authDomain: "aquasync-6710b.firebaseapp.com",
  projectId: "aquasync-6710b",
  storageBucket: "aquasync-6710b.firebasestorage.app",
  messagingSenderId: "50686905162",
  appId: "1:50686905162:web:3126e1819b6e8631920fb3",
};

const app = initializeApp(firebaseConfig);

const messaging = getMessaging(app);

window.requestFirebaseToken = async function () {
    try {
        const token = await getToken(messaging, {
            vapidKey: "BA65Y-t8G_VlYe3xPmFe2edsbJIIj9RIT_kruBqvm80FvjcoCATaMvvGUdMMBFdDZrnoW1x-NQqDO6nybU_OfM0"
        });

        console.log("FCM TOKEN:", token);

        return token;
    } catch (error) {
        console.error(error);
    }
};