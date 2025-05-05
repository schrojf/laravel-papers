/** @type {import('tailwindcss').Config} */
module.exports = {
    content: [
        "./resources/views/**/*.blade.php",
        "./resources/js/**/*.{js,ts,jsx,tsx}",
        "./workbench/resources/views/**/*.blade.php",
    ],
    theme: {
        extend: {},
    },
    plugins: [
        require('@tailwindcss/typography'),
        require('@tailwindcss/forms'),
        require('@tailwindcss/aspect-ratio'),
    ],
}
