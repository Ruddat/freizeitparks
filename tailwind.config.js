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
        keyframes: {
          fadeIn: {
            from: { opacity: 0 },
            to: { opacity: 1 },
          },
        },
      },
    },
    plugins: [],
  }
