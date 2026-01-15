import { type VariantProps, cva } from "class-variance-authority";

export { default as Button } from "./Button.vue";

export const buttonVariants = cva(
  "inline-flex items-center justify-center whitespace-nowrap rounded-md text-sm font-medium transition-colors focus-visible:outline-none focus-visible:ring-1 focus-visible:ring-neutral-950 disabled:pointer-events-none disabled:opacity-50 dark:focus-visible:ring-neutral-300",
  {
    variants: {
      variant: {
        default:
          "bg-brand-maroon-800 text-neutral-50 hover:bg-brand-maroon-800/90 dark:bg-neutral-50 dark:text-brand-maroon-800 dark:hover:bg-neutral-50/90",
        destructive:
          "bg-red-500 text-neutral-50 hover:bg-red-500/90 dark:bg-red-900 dark:text-neutral-50 dark:hover:bg-red-900/90",
        outline:
          "border border-brand-maroon-800/20 hover:bg-white brand-maroon-800/20 hover:text-brand-maroon-800 dark:border-neutral-800 dark:bg-neutral-950 dark:hover:bg-neutral-800 dark:hover:text-neutral-50",
        secondary:
          "bg-brand-maroon-800/5 text-brand-maroon-800 hover:bg-brand-maroon-800/80 hover:text-white dark:bg-neutral-800 dark:text-neutral-50 dark:hover:bg-neutral-800/80",
        ghost:
          "hover:bg-neutral-100 hover:text-brand-maroon-800 dark:hover:bg-neutral-800 dark:hover:text-neutral-50",
        link: "text-brand-maroon-800 underline-offset-4 hover:underline dark:text-neutral-50",
      },
      size: {
        default: "px-4 py-2",
        xs: "rounded px-2",
        sm: "rounded-md px-3 text-xs",
        lg: "rounded-md px-8",
        icon: "h-9 w-9",
      },
    },
    defaultVariants: {
      variant: "default",
      size: "default",
    },
  },
);

export type ButtonVariants = VariantProps<typeof buttonVariants>;
