/** @type {import('tailwindcss').Config} */
export default {
  content: [
    "./src/**/*.php",
    "./templates/**/*.php",
    "./includes/**/*.php",
    "./assets/js/**/*.js",
    "../frontend-dashboard-extra/**/*.php",
    "../frontend-dashboard-custom-post/**/*.php",
  ],
  theme: {
    extend: {
      maxWidth: {
        '8xl': '90rem',
      },
    },
  },
  plugins: [],
};
