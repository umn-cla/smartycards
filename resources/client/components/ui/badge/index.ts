import { type VariantProps, cva } from "class-variance-authority";

export { default as Badge } from "./Badge.vue";

export const badgeVariants = cva(
  "inline-flex items-center rounded-md border border-neutral-200 px-2.5 py-0.5 text-xs font-semibold transition-colors focus:outline-none focus:ring-2 focus:ring-neutral-950 focus:ring-offset-2 dark:border-neutral-800 dark:focus:ring-neutral-300",
  {
    variants: {
      variant: {
        default:
          "border-transparent bg-brand-maroon-800 text-neutral-50 shadow",
        secondary: "border-transparent bg-neutral-100 text-brand-maroon-800",
        destructive: "border-transparent bg-red-500 text-neutral-50 shadow",
        outline: "text-neutral-950",
      },
    },
    defaultVariants: {
      variant: "default",
    },
  },
);

export type BadgeVariants = VariantProps<typeof badgeVariants>;
