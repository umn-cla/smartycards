export function pluralize(count: number, word: string, pluralForm?: string) {
  return count === 1 ? word : pluralForm || `${word}s`;
}
