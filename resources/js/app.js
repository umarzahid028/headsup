import "./bootstrap";

import Alpine from "alpinejs";

// Import Shadcn UI components and utilities
import { buttonVariants } from "./components/ui/button";
import { Input } from "./components/ui/input";
import { Label } from "./components/ui/label";
import {
    Card,
    CardHeader,
    CardTitle,
    CardDescription,
    CardContent,
    CardFooter,
} from "./components/ui/card";
import { ThemeToggle } from "./components/ui/theme-toggle";
import { cn } from "./lib/utils";

window.Alpine = Alpine;

// Register Alpine components
document.addEventListener("alpine:init", () => {
    Alpine.data("themeToggle", ThemeToggle);
});

Alpine.start();

// Make components available globally (if needed)
window.ShadcnUI = {
    buttonVariants,
    Input,
    Label,
    Card,
    CardHeader,
    CardTitle,
    CardDescription,
    CardContent,
    CardFooter,
    ThemeToggle,
    cn,
};
