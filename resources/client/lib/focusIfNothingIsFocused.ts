export function focusIfNothingIsFocused(element: HTMLElement | null): void {
  const isNothingFocused =
    document.activeElement === document.body ||
    document.activeElement === document.documentElement;

  if (isNothingFocused) {
    element?.focus();
  }
}
