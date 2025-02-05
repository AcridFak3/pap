// tailwind.config.js
module.exports = {
    content: [
        './assets/**/*.{html,js,jsx,ts,tsx}',  // Arquivos onde o Tailwind será usado
        './templates/**/*.{twig}',  // Se você usa Twig para templates no Symfony
    ],
    theme: {
        extend: {},
    },
    plugins: [],
};
