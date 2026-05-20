const CACHE_NAME = 'conge-calendar-v1';
const PRECACHE_URLS = [
  '/',
  '/employe/conge/calendar',
  '/employe/conge/events',
  'https://cdn.jsdelivr.net/npm/fullcalendar@6.1.15/index.global.min.css',
  'https://cdn.jsdelivr.net/npm/fullcalendar@6.1.15/index.global.min.js'
];

self.addEventListener('install', (event) => {
  event.waitUntil(
    caches.open(CACHE_NAME).then((cache) => cache.addAll(PRECACHE_URLS)).then(() => self.skipWaiting())
  );
});

self.addEventListener('activate', (event) => {
  event.waitUntil(self.clients.claim());
});

self.addEventListener('fetch', (event) => {
  const request = event.request;
  if (request.method !== 'GET') return;

  event.respondWith(
    caches.match(request).then((cached) => {
      if (cached) return cached;
      return fetch(request).then((response) => {
        // Mise en cache des réponses valides
        if (!response || response.status !== 200 || response.type === 'opaque') return response;
        const responseClone = response.clone();
        caches.open(CACHE_NAME).then((cache) => cache.put(request, responseClone));
        return response;
      }).catch(() => caches.match('/'));
    })
  );
});
