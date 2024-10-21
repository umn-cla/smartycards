import { type ClassValue, clsx } from "clsx";
import { twMerge } from "tailwind-merge";
import { v4 as uuidv4 } from "uuid";

export function cn(...inputs: ClassValue[]) {
  return twMerge(clsx(inputs));
}

export function isValidUrl(maybeUrl: string) {
  try {
    new URL(maybeUrl);
    return true;
  } catch {
    return false;
  }
}

export function uuid() {
  return uuidv4();
}

/**
 * Returns a new array with the elements sorted in
 * a random order.
 */
export function toShuffled<T>(array: T[]): T[] {
  return array.toSorted(() => Math.random() - 0.5);
}

/**
 * Convert a CSS size to pixels.
 * Useful for calcualating scale factors
 *
 * @param cssSize - The size string to convert in CSS like "16rem", "24px", "50%", etc.
 * @param contextElement - The element to use as the context for em conversion. Defaults to the body element.
 * @returns The size in pixels.
 */
export function convertToPx(
  cssSize: string,
  contextElement: HTMLElement = document.body,
): number {
  // Extract the numeric value and unit from the size string
  const value = parseFloat(cssSize);
  const unit = cssSize.replace(value.toString(), "").trim();

  // Get the root font size for rem conversion
  const rootFontSize = parseFloat(
    getComputedStyle(document.documentElement).fontSize,
  );
  // Get the font size of the context element for em conversion
  const contextFontSize = parseFloat(getComputedStyle(contextElement).fontSize);

  switch (unit) {
    case "px":
      return value;
    case "rem":
      return value * rootFontSize;
    case "em":
      return value * contextFontSize;
    case "vw":
      return (value * window.innerWidth) / 100;
    case "vh":
      return (value * window.innerHeight) / 100;
    case "%":
      // Assuming percentage is relative to the width of the context element
      return (value * contextElement.clientWidth) / 100;
    default:
      throw new Error(`Unsupported unit: ${unit}`);
  }
}
