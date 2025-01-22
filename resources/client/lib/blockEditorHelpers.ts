import { ContentBlock } from "@/types";
import invariant from "tiny-invariant";

export function getBlockSelector(block: ContentBlock) {
  return `#block-editor__block__${block.id}`;
}

export function focusBlockDragHandle(block: ContentBlock) {
  const selector = `${getBlockSelector(block)} .drag-handle`;
  const el = document.querySelector<HTMLButtonElement>(selector);
  invariant(el, `drag handle not found with selector: ${selector}`);
  el?.focus();
}

export function flashBlock(block: ContentBlock) {
  const el = document.querySelector<HTMLDivElement>(getBlockSelector(block));
  el?.classList.add("flash");
  setTimeout(() => {
    el?.classList.remove("flash");
  }, 1000);
}
