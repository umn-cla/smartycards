/**
 * a helper to generate consistent ids for inputs/labels
 * without a lot of boilerplate
 * @example
 * const { makeInputId } = useMakeInputId('image-block-editor');
 * <input :id="makeInputId('alt-text')" />
 */
export const useMakeInputId = (
  componentName: string,
  componentId?: string | number,
) => {
  // generate a random uuid if needed
  componentId ??= crypto.randomUUID();

  const makeInputId = (inputName: string) =>
    `${componentName}__${inputName}__${componentId}`;

  return { makeInputId };
};
