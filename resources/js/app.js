// resources/js/app.js
import { showToast } from "./toast.js";
window.showToast = showToast; // rende la funzione globale per poterla chiamare da HTML/Livewire

// ── service worker ────────────────────────────────────────────────────────────

if ("serviceWorker" in navigator) {
  navigator.serviceWorker.register("/sw.js").then(() => {
    // console.log("PWA pronta");
  });
}