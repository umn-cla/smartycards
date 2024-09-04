import TailwindCssForms from "@tailwindcss/forms";
import animate from "tailwindcss-animate";
import TailwindScrollbar from "tailwind-scrollbar";
import colors from "tailwindcss/colors";

const umnColors = {
  maroon: {
    600: "#90021",
    700: "#7a0019",
    800: "hsl(0, 80%, 16%)",
    900: "hsl(0, 90%, 8%)",
    950: "hsl(0, 100%, 4%)",
  },
  gold: {
    300: "#ffde7a",
    500: "#ffcc33", // Official UMN Gold
    700: "#ffb71e",
  },
};

const brandColors = {
  oatmeal: {
    50: "#fffbf3",
    100: "#F8F0DF",
    300: "#E7CAC0",
    500: "#D6AA9B",
  },
  maroon: {
    500: "#81001E", // umn maroon
    600: "#700822",
    700: "#530F20",
    800: "#451022",
    900: "#2F0A1F", // dark bg
    950: "#1F0012", // text dark
  },
  gold: {
    300: "#f7e6af",
    500: "#FFDE7A",
    shadow: "#eacb6b",
  },
  orange: {
    500: "#DE3B04",
  },
  teal: {
    700: "#09594D",
    500: "#14917E",
    300: "#27BEA7",
    shadow: "#09594D",
  },
};

/** @type {import('tailwindcss').Config} */
const tailwindConfig = {
  darkMode: ["class", '[data-theme="dark"]'],
  safelist: ["dark"],
  prefix: "",

  content: ["./resources/**/*.{ts,tsx,vue}"],

  theme: {
    container: {
      center: true,
      padding: "2rem",
      screens: {
        "2xl": "1400px",
      },
    },
    extend: {
      fontFamily: {
        serif: ["Rokkitt Variable", "serif"],
        sans: ["Nunito Variable", "sans-serif"],
      },
      boxShadow: {
        "solid-gold-1px": `0 1px 0 0 ${brandColors.gold.shadow}`,
        "solid-gold-1": `0 0.25rem 0 0 ${brandColors.gold.shadow}`,
        "solid-gold-2": `0 0.5rem 0 0 ${brandColors.gold.shadow}`,
        "solid-gold-3": `0 0.75rem 0 0 ${brandColors.gold.shadow}`,
        "solid-gold-4": `0 1rem 0 0 ${brandColors.gold.shadow}`,
        "solid-teal-2": `0 0.5rem 0 0 ${brandColors.teal.shadow}`,
      },
      colors: {
        "black/1": "hsla(0,0%,0%,0.01)",
        "black/2": "hsla(0,0%,0%,0.02)",
        umn: umnColors,
        brand: brandColors,
        background: {
          DEFAULT: "hsl(0 0% 98%)",
          dark: "hsl(0 0% 3.9%)",
        },
        foreground: {
          DEFAULT: "hsl(0 0% 3.9%)",
          dark: "hsl(0 0% 98%)",
        },
        card: {
          DEFAULT: "hsl(0 0% 100%)",
          foreground: "hsl(0 0% 3.9%)",
          dark: "hsl(0 0% 3.9%)",
          "dark-foreground": "hsl(0 0% 98%)",
        },
        popover: {
          DEFAULT: "hsl(0 0% 100%)",
          foreground: "hsl(0 0% 3.9%)",
          dark: "hsl(0 0% 3.9%)",
          "dark-foreground": "hsl(0 0% 98%)",
        },
        primary: {
          DEFAULT: "hsl(0 0% 9%)",
          foreground: "hsl(0 0% 98%)",
          dark: "hsl(0 0% 98%)",
          "dark-foreground": "hsl(0 0% 9%)",
        },
        secondary: {
          DEFAULT: "hsl(0 0% 96.1%)",
          foreground: "hsl(0 0% 9%)",
          dark: "hsl(0 0% 14.9%)",
          "dark-foreground": "hsl(0 0% 98%)",
        },
        muted: {
          DEFAULT: "hsl(0 0% 96.1%)",
          foreground: "hsl(0 0% 45.1%)",
          dark: "hsl(0 0% 14.9%)",
          "dark-foreground": "hsl(0 0% 63.9%)",
        },
        accent: {
          DEFAULT: "hsl(0 0% 96.1%)",
          foreground: "hsl(0 0% 9%)",
          dark: "hsl(0 0% 14.9%)",
          "dark-foreground": "hsl(0 0% 98%)",
        },
        destructive: {
          DEFAULT: "hsl(0 84.2% 60.2%)",
          foreground: "hsl(0 0% 98%)",
          dark: "hsl(0 62.8% 30.6%)",
          "dark-foreground": "hsl(0 0% 98%)",
        },
        border: {
          DEFAULT: "hsl(0 0% 89.8%)",
          dark: "hsl(0 0% 14.9%)",
        },
        input: {
          DEFAULT: "hsl(0 0% 89.8%)",
          dark: "hsl(0 0% 14.9%)",
        },
        ring: {
          DEFAULT: "hsl(0 0% 3.9%)",
          dark: "hsl(0 0% 83.1%)",
        },
      },
      borderRadius: {
        xl: "1rem",
        lg: "0.75rem",
        md: "0.5rem",
        sm: "0.25rem",
      },
      keyframes: {
        "accordion-down": {
          from: { height: 0 },
          to: { height: "var(--radix-accordion-content-height)" },
        },
        "accordion-up": {
          from: { height: "var(--radix-accordion-content-height)" },
          to: { height: 0 },
        },
        "collapsible-down": {
          from: { height: 0 },
          to: { height: "var(--radix-collapsible-content-height)" },
        },
        "collapsible-up": {
          from: { height: "var(--radix-collapsible-content-height)" },
          to: { height: 0 },
        },
      },
      animation: {
        "accordion-down": "accordion-down 0.2s ease-out",
        "accordion-up": "accordion-up 0.2s ease-out",
        "collapsible-down": "collapsible-down 0.2s ease-in-out",
        "collapsible-up": "collapsible-up 0.2s ease-in-out",
      },
      screens: {
        landscape: { raw: "(min-aspect-ratio: 1/1)" }, // width > height
        portrait: { raw: "(max-aspect-ratio: 1/1)" },
      },
    },
  },
  plugins: [TailwindCssForms, animate, TailwindScrollbar],
};

export default tailwindConfig;
