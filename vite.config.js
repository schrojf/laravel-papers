import laravel from "laravel-vite-plugin";
import tailwindcss from "@tailwindcss/vite";

/** @type {import('vite').UserConfig} */
export default {
    // Relative URLs, so published assets work from any public path
    base: "./",
    // Load .env from the workbench application, e.g. APP_URL for the development server's CORS origins
    envDir: "workbench",
    plugins: [
        laravel({
            input: ["resources/js/papers.js", "resources/css/papers.css"],
            hotFile: "vite.hot",
            refresh: ["resources/views/**", "routes/**", "workbench/resources/views/**", "workbench/routes/**"],
        }),
        tailwindcss(),
    ],
    build: {
        outDir: "dist",
        assetsDir: "",
    },
};
