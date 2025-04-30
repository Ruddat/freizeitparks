import defaultTheme from 'tailwindcss/defaultTheme';
import forms from '@tailwindcss/forms';

// Add this line
import typography from '@tailwindcss/typography';

content: [
    "./resources/**/*.blade.php",
    "./resources/**/*.js",
    "./resources/**/*.vue",
  ]
  module.exports = {
    content: ["./resources/**/*.blade.php", "./resources/**/*.js"],
    theme: {
      extend: {
        animation: {
          fadeIn: "fadeIn 0.5s ease-in-out",
        },
        colors: {
          primary: "#3490dc",
          secondary: "#ffed4a",
          accent: "#e3342f",
        },
        safelist: [
            'group-hover:scale-105',
            'group-hover:scale-110',
            'hover:shadow-2xl',
            'bg-gradient-to-br',
            // usw. alle Utilities, die verschwinden
          ],

        fontFamily: {
            bowlby: ['"Bowlby One"', 'sans-serif'],
          },
        keyframes: {
          fadeIn: {
            from: { opacity: 0 },
            to: { opacity: 1 },
          },
        },
      },
    },
    plugins: [require('@tailwindcss/typography')],
    plugins: [forms, typography],

  }
