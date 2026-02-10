/** @type {import('tailwindcss').Config} */
export default {
   content: ["./src/**/*.{html,js}"],
   theme: {
      extend: {
         screens: {
            '2xl': '1536px',
            '3xl': '1700px',
         },
         fontSize: {
            '3.5xl': '2rem',
         },
         maxWidth: {
            '8xl': '95rem',
         },
      },
   },
   plugins: [],
}