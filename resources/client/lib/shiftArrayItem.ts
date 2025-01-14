import { clamp, remove, insert } from "ramda";

interface ItemWithId {
  id: number | string;
}

/**
 * returns a new array with the item shifted by the given amount
 */
export function shiftArrayItem<T extends ItemWithId = ItemWithId>(
  array: T[],
  itemId: number | string,
  shiftBy: number,
): T[] {
  const currentIndex = array.findIndex((item) => item.id === itemId);

  // if not found, return the array as is
  if (currentIndex === -1) {
    return array;
  }

  // get the new index, clamped to the array bounds
  const newIndex = currentIndex + shiftBy;
  const clampedNewIndex = clamp(0, array.length - 1, newIndex);

  const item = array[currentIndex];
  const arrayWithoutItem = remove(currentIndex, 1, array);
  const newArray = insert(clampedNewIndex, item, arrayWithoutItem);

  return newArray;
}
