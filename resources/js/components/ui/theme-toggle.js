export const ThemeToggle = () => {
    const toggleTheme = () => {
        const html = document.querySelector("html");
        if (html.classList.contains("dark")) {
            html.classList.remove("dark");
            localStorage.setItem("theme", "light");
        } else {
            html.classList.add("dark");
            localStorage.setItem("theme", "dark");
        }
    };

    // Initialize theme on load
    const initTheme = () => {
        const html = document.querySelector("html");
        const theme = localStorage.getItem("theme") || "light";
        if (theme === "dark") {
            html.classList.add("dark");
        } else {
            html.classList.remove("dark");
        }
    };

    if (typeof window !== "undefined") {
        initTheme();
    }

    return {
        toggleTheme,
    };
};
