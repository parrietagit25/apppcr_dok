const CACHE_NAME = 'pcr-cache-v1';
const urlsToCache = [
  '/',
  '/index.php',
  '/icons/ico-192_v2.png',
  '/icons/ico-512_v2.png',
  '/manifest.json'
];

self.addEventListener('install', function(event) {
  event.waitUntil(
    caches.open(CACHE_NAME)
      .then(cache => cache.addAll(urlsToCache))
  );
});

self.addEventListener('fetch', function(event) {
  event.respondWith(
    caches.match(event.request)
      .then(response => response || fetch(event.request))
  );
});
