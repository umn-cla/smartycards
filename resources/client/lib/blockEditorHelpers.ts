import { ContentBlock } from "@/types";

export function getBlockSelector(block: ContentBlock) {
  return `#block-editor__block__${block.id}`;
}

export function focusBlockDragHandle(block: ContentBlock) {
  const el = document.querySelector<HTMLButtonElement>(
    `${getBlockSelector(block)} .drag-handle`,
  );
  el?.focus();
}

export function flashBlock(block: ContentBlock) {
  const el = document.querySelector<HTMLDivElement>(getBlockSelector(block));
  el?.classList.add("flash");
  setTimeout(() => {
    el?.classList.remove("flash");
  }, 1000);
}
