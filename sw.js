// Service Worker básico para PWA
self.addEventListener('install', function(e) {
  self.skipWaiting();
});
self.addEventListener('activate', function(e) {
  return self.clients.claim();
});
self.addEventListener('fetch', function(event) {
  // Puedes personalizar el cacheo aquí si lo deseas
});
