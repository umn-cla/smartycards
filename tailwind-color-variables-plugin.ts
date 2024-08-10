// tailwind-color-variables-plugin.ts
import plugin from 'tailwindcss/plugin';

type ColorValue = string | Record<string, string>;
type ColorObject = Record<string, ColorValue>;

function extractColorVars(colorObj: ColorObject, colorGroup: string = ''): Record<string, string> {
  return Object.entries(colorObj).reduce((vars, [key, value]) => {
    const newVars =
      typeof value === 'string'
        ? { [`--color${colorGroup}-${key}`]: value }
        : extractColorVars(value as ColorObject, `${colorGroup}-${key}`);

    return { ...vars, ...newVars };
  }, {});
}

export default plugin(function ({ addBase, theme }) {
  addBase({
    ':root': extractColorVars(theme('colors') as ColorObject),
  });
});
