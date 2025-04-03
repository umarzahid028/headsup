import "./bootstrap";

import Alpine from "alpinejs";

window.Alpine = Alpine;

Alpine.start();

import { NotificationListener } from "./components/NotificationListener.js";

// Make NotificationListener available globally
window.NotificationListener = NotificationListener;
