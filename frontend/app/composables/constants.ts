const DARK_COLORS = {
  'rosewater': '#f5e0dc',
  'flamingo': '#f2cdcd',
  'pink': '#f5c2e7',
  'mauve': '#cba6f7',
  'red': '#f38ba8',
  'maroon': '#eba0ac',
  'peach': '#fab387',
  'yellow': '#f9e2af',
  'green': '#a6e3a1',
  'teal': '#94e2d5',
  'sky': '#89dceb',
  'sapphire': '#74c7ec',
  'blue': '#89b4fa',
  'lavender': '#b4befe',
  'text': '#cdd6f4',
  'subtext-100': '#bac2de',
  'subtext-200': '#a6adc8',
  'overlay-100': '#9399b2',
  'overlay-200': '#7f849c',
  'overlay-300': '#6c7086',
  'surface-100': '#585b70',
  'surface-200': '#45475a',
  'surface-300': '#313244',
  'base': '#1e1e2e',
  'mantle': '#181825',
  'crust': '#11111b'
} as const;

const LIGHT_COLORS: Record<keyof typeof DARK_COLORS, string> = {
  'rosewater': '#dc8a78',
  'flamingo': '#dd7878',
  'pink': '#ea76cb',
  'mauve': '#8839ef',
  'red': '#d20f39',
  'maroon': '#e64553',
  'peach': '#fe640b',
  'yellow': '#df8e1d',
  'green': '#40a02b',
  'teal': '#179299',
  'sky': '#04a5e5',
  'sapphire': '#209fb5',
  'blue': '#1e66f5',
  'lavender': '#7287fd',
  'text': '#4c4f69',
  'subtext-100': '#6c6f85',
  'subtext-200': '#5c5f77',
  'overlay-100': '#9ca0b0',
  'overlay-200': '#8c8fa1',
  'overlay-300': '#7c7f93',
  'surface-100': '#ccd0da',
  'surface-200': '#bcc0cc',
  'surface-300': '#acb0be',
  'base': '#eff1f5',
  'mantle': '#e6e9ef',
  'crust': '#dce0e8'
} as const;

const COLORS = DARK_COLORS;

const SHIMMER_COLORS = [
  'purple',
  'blue',
  'green',
  'yellow',
  'orange',
  'red',
  'pink',
  'rgb',
  'cmy',
  'trans'
] as const;

export function useConstants() {   
  return {
    COLORS,
    DARK_COLORS,
    LIGHT_COLORS,
    SHIMMER_COLORS
  };
}