import '../../vendor/masmerise/livewire-toaster/resources/js';
import { createIcons, Menu, X, CornerDownLeft, Undo, Undo2 } from 'lucide';
import Chart from 'chart.js/auto';

// Make Chart globally available
window.Chart = Chart;

function registerIcons(root = document) {
  createIcons({
    icons: { Menu, X, CornerDownLeft, Undo2, Undo },
    root,
  });
}

if (document.readyState === 'loading') {
  window.addEventListener('DOMContentLoaded', () => registerIcons(document));
} else {
  registerIcons(document);
}

if (window.Livewire) {
  window.Livewire.hook('message.processed', (el) => {
    registerIcons(el);
  });
}

// expose for manual initialization from Blade/Alpine
window.createLucideIcons = registerIcons;