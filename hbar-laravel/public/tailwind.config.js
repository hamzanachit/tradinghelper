/** @type {import('tailwindcss').Config} */
export default {
  content: [
    "./index.html",
    "./src/**/*.{js,ts,jsx,tsx}",
  ],
  theme: {
    extend: {
      colors: {
        'chart-bg': '#060606',
        'chart-grid': '#222',
        'bullish': '#26a69a',
        'bearish': '#ef5350',
        'signal-buy': '#00C853',
        'signal-sell': '#FF1744',
        'signal-neutral': '#FFA726',
        'strong-buy': '#00C853',
        'strong-sell': '#FF1744',
      }
    },
  },
  plugins: [],
}
