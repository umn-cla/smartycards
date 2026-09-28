import { convertLatexToSpeakableText } from "mathlive";
import { markdownToHTML } from "@/lib/markdownToHTML";

export function markdownToSpeechText(markdown: string): string {
  const html = markdownToHTML(markdown);
  const doc = new DOMParser().parseFromString(html, "text/html");
  // KaTeX renders each formula as MathML and as HTML, so
  // its plain text reads x² as "x2x^2x2".
  doc.querySelectorAll(".katex").forEach((formula) => {
    const latex = formula.querySelector("annotation")?.textContent ?? "";
    formula.replaceWith(convertLatexToSpeakableText(latex));
  });
  return (doc.body.textContent ?? "")
    .replace(/\s+/g, " ")
    .replace(/ ([,.])/g, "$1")
    .trim();
}
