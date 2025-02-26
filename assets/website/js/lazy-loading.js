// Lazy loading for images using lazysizes
document.addEventListener('DOMContentLoaded', function() {
  // Polyfill für ältere Browser
  if ('loading' in HTMLImageElement.prototype) {
    // Browser unterstützt native lazy-loading
    const images = document.querySelectorAll('img[loading="lazy"]');
    images.forEach(img => {
      img.src = img.dataset.src;
    });
  } else {
    // Browser unterstützt kein natives lazy-loading
    // Lazysizes wird hier verwendet
    const script = document.createElement('script');
    script.src = 'https://cdnjs.cloudflare.com/ajax/libs/lazysizes/5.3.2/lazysizes.min.js';
    document.body.appendChild(script);
  }

  // Für Hintergrundbilder mit data-bg
  const bgElements = document.querySelectorAll('.lazyload');
  if (bgElements.length > 0) {
    // LazySizes mit Plugins für Hintergrundbilder laden
    const lazyBgScript = document.createElement('script');
    lazyBgScript.src = 'https://cdnjs.cloudflare.com/ajax/libs/lazysizes/5.3.2/plugins/bgset/ls.bgset.min.js';
    document.body.appendChild(lazyBgScript);
  }
});