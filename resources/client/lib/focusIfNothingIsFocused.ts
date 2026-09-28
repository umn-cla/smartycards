export function focusIfNothingIsFocused(element: HTMLElement | null): void {
  const isNothingFocused =
    document.activeElement === null || document.activeElement === document.body;

  if (isNothingFocused) {
    element?.focus();
  }
}
