/* CreatorFlow PWA service worker */
const CACHE = 'creatorflow-v1';
const SHELL = ['/', '/manifest.webmanifest', '/icons/icon.svg'];

self.addEventListener('install', (e) => {
    e.waitUntil(caches.open(CACHE).then((c) => c.addAll(SHELL)).catch(() => {}));
    self.skipWaiting();
});

self.addEventListener('activate', (e) => {
    e.waitUntil(
        caches.keys().then((keys) =>
            Promise.all(keys.filter((k) => k !== CACHE).map((k) => caches.delete(k)))
        )
    );
    self.clients.claim();
});

self.addEventListener('fetch', (e) => {
    const { request } = e;
    if (request.method !== 'GET') return;
    const url = new URL(request.url);
    if (url.origin !== location.origin) return;

    // Network-first for navigation, cache-first for static assets.
    if (request.mode === 'navigate') {
        e.respondWith(
            fetch(request).catch(() => caches.match(request).then((r) => r || caches.match('/')))
        );
        return;
    }

    e.respondWith(
        caches.match(request).then((cached) =>
            cached ||
            fetch(request).then((res) => {
                if (res.ok && (url.pathname.startsWith('/icons') || url.pathname.startsWith('/build'))) {
                    const copy = res.clone();
                    caches.open(CACHE).then((c) => c.put(request, copy));
                }
                return res;
            }).catch(() => cached)
        )
    );
});

self.addEventListener('push', (event) => {
    let data = {};
    try { data = event.data.json(); } catch (e) { data = { title: 'CreatorFlow', body: event.data && event.data.text() }; }
    event.waitUntil(
        self.registration.showNotification(data.title || 'CreatorFlow', {
            body: data.body || '',
            icon: '/icons/icon-192.png',
            badge: '/icons/icon-192.png',
            data: data.url || '/',
        })
    );
});

self.addEventListener('notificationclick', (event) => {
    event.notification.close();
    event.waitUntil(clients.openWindow(event.notification.data || '/'));
});
