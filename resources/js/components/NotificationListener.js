import Pusher from "pusher-js";
import Swal from "sweetalert2";

export class NotificationListener {
    constructor(userId) {
        this.userId = userId;
        this.notificationCount = 0;
        this.vehicleCount = 0;
        this.initialize();
    }

    initialize() {
        if (!window.Echo) {
            window.Pusher = Pusher;
            window.Echo = new window.Echo({
                broadcaster: "pusher",
                key: import.meta.env.VITE_PUSHER_APP_KEY,
                cluster: import.meta.env.VITE_PUSHER_APP_CLUSTER,
                forceTLS: true,
            });
        }

        window.Echo.private(`users.${this.userId}`).notification(
            (notification) => {
                this.showNotification(notification);
                this.updateUI(notification);
            }
        );

        // Initialize existing counts
        this.notificationCount = parseInt(
            document.getElementById("notification-counter")?.textContent || "0"
        );
        this.vehicleCount = parseInt(
            document.getElementById("vehicle-notification-counter")
                ?.textContent || "0"
        );
    }

    showNotification(notification) {
        const notificationObj = this.createNotification(notification);
        notificationObj.showToast();
    }

    createNotification(data) {
        let title = "New Notification";
        let icon = "info";

        // Customize notification based on type
        if (
            data.type.includes("NewVehicleArrival") ||
            data.type.includes("NewVehicleImported")
        ) {
            title = "New Vehicle";
            icon = "car";
        }

        return Swal.mixin({
            toast: true,
            position: "top-end",
            showConfirmButton: false,
            timer: 3000,
            timerProgressBar: true,
            icon: icon,
            title: title,
            text: data.message || "You have a new notification",
            didOpen: (toast) => {
                toast.addEventListener("mouseenter", Swal.stopTimer);
                toast.addEventListener("mouseleave", Swal.resumeTimer);
            },
        });
    }

    updateUI(data) {
        // Increment counters
        this.notificationCount++;
        if (
            data.type.includes("NewVehicleArrival") ||
            data.type.includes("NewVehicleImported")
        ) {
            this.vehicleCount++;
        }

        // Update notification counters in the UI
        this.updateNotificationCounter();
        this.updateHeaderCounter();
        this.updateVehicleCounter();

        // Add notification to the dropdown list
        this.addNotificationToList(data);
    }

    updateNotificationCounter() {
        const counter = document.getElementById("notification-counter");
        if (counter) {
            counter.textContent = this.notificationCount;
            counter.classList.remove("hidden");
        }
    }

    updateHeaderCounter() {
        const headerCounter = document.getElementById(
            "header-notification-counter"
        );
        if (headerCounter) {
            headerCounter.textContent = this.notificationCount;
            headerCounter.classList.remove("hidden");
        }
    }

    updateVehicleCounter() {
        const vehicleCounter = document.getElementById(
            "vehicle-notification-counter"
        );
        if (vehicleCounter) {
            vehicleCounter.textContent = this.vehicleCount;
            vehicleCounter.classList.remove("hidden");
        }
    }

    addNotificationToList(data) {
        const dropdownList = document.getElementById("notification-list");
        if (!dropdownList) return;

        const notificationItem = document.createElement("a");
        notificationItem.href = "#";
        notificationItem.className =
            "block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100";
        notificationItem.textContent = data.message || "New notification";

        dropdownList.insertBefore(notificationItem, dropdownList.firstChild);
    }
}
