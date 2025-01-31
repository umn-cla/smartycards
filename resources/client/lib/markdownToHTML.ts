import { marked, MarkedOptions } from "marked";
import markedKatex, { MarkedKatexOptions } from "marked-katex-extension";

const options: MarkedOptions & MarkedKatexOptions = {
  throwOnError: false,
  async: false,
};

marked.use(markedKatex(options));

export function markdownToHTML(markdown: string): string {
  // not using async so we can cast as string instead
  // of Promise<string>
  return marked.parse(markdown) as string;
}
