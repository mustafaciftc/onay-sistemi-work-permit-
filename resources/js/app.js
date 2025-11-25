import Echo from 'laravel-echo';
import Pusher from 'pusher-js';

window.Pusher = Pusher;

window.Echo = new Echo({
    broadcaster: 'pusher',
    key: import.meta.env.VITE_PUSHER_APP_KEY,
    cluster: import.meta.env.VITE_PUSHER_APP_CLUSTER,
    forceTLS: true
});

// Kullanıcıya özel bildirimleri dinle
if (window.userId) {
    window.Echo.private(`user.${window.userId}`)
        .listen('.approval.assigned', (e) => {
            showNotification('Yeni Onay Talebi', e.message);
        });
}
