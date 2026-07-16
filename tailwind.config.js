import defaultTheme from "tailwindcss/defaultTheme";
import forms from "@tailwindcss/forms";

export default {
    content: [
        "./resources/**/*.blade.php",
        "./resources/**/*.js",
        "./app/Filament/**/*.php",
        "./resources/views/filament/**/*.blade.php",
    ],
    theme: {
        extend: {
            fontFamily: {
                sans: ["Inter", ...defaultTheme.fontFamily.sans],
            },
            colors: {
                primary: "#003366",
                secondary: "#3b82f6",
                title: "#111827",
                body: "#6b7280",
                placeholder: "#9ca3af",
                page: "#f0f2f5",
                surface: "#fbfbfd",
                inverse: "#111827",
                subtle: "#e5e7eb",
                success: "#10B981",
                warning: "#f59e0b",
                error: "#ef4444",
            },
        },
    },
    plugins: [forms],
};
