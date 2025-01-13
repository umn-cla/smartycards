import { ContentBlock, ContentBlockType } from "@/types";
import {
  isAudioBlock,
  isEmbedBlock,
  isHintBlock,
  isImageBlock,
  isMathBlock,
  isTextBlock,
  isVideoBlock,
} from "./isBlockOfType";

/**
 * makes an empty content block of a particular type
 */
export function makeContentBlock(type: ContentBlockType) {
  const block: ContentBlock = {
    id: crypto.randomUUID(),
    type,
    content: "",
    meta: null,
  };

  if (isImageBlock(block)) {
    block.meta = { alt: "" };
    return block;
  }

  if (isHintBlock(block)) {
    block.meta = { label: "Hint" };
    return block;
  }

  if (isTextBlock(block)) {
    block.meta = { lang: null };
    return block;
  }

  if (isAudioBlock(block)) {
    // using `isBlockOfType()` type guards to return the correct block type
    return block;
  }

  if (isVideoBlock(block)) {
    return block;
  }

  if (isEmbedBlock(block)) {
    return block;
  }

  if (isMathBlock(block)) {
    return block;
  }

  throw new Error(`Unknown block type: ${type}`);
}
