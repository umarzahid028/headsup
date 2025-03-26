import { clsx } from "clsx";
import { twMerge } from "tailwind-merge";

/**
 * Merge classes with tailwind-merge with clsx
 * @param {import('clsx').ClassValue[]} inputs
 */
export function cn(...inputs) {
    return twMerge(clsx(inputs));
}
