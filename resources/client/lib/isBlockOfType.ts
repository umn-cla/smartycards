import * as T from "@/types";

export function isImageBlock(
  block: T.ContentBlock,
): block is T.ImageContentBlock {
  return block.type === "image";
}

export function isTextBlock(
  block: T.ContentBlock,
): block is T.TextContentBlock {
  return block.type === "text";
}

export function isVideoBlock(
  block: T.ContentBlock,
): block is T.VideoContentBlock {
  return block.type === "video";
}

export function isEmbedBlock(
  block: T.ContentBlock,
): block is T.EmbedContentBlock {
  return block.type === "embed";
}

export function isAudioBlock(
  block: T.ContentBlock,
): block is T.AudioContentBlock {
  return block.type === "audio";
}

export function isHintBlock(
  block: T.ContentBlock,
): block is T.HintContentBlock {
  return block.type === "hint";
}

export function isMathBlock(
  block: T.ContentBlock,
): block is T.MathContentBlock {
  return block.type === "math";
}
