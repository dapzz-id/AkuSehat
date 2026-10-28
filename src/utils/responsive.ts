import { Dimensions, PixelRatio, Platform } from 'react-native';

const { width: SCREEN_WIDTH, height: SCREEN_HEIGHT } = Dimensions.get('window');

// Standard mobile baseline guideline dimensions (iPhone X / typical Android 375x812)
const guidelineBaseWidth = 375;
const guidelineBaseHeight = 812;

/**
 * Scale horizontal dimensions based on screen width
 */
export const scale = (size: number): number => {
  return (SCREEN_WIDTH / guidelineBaseWidth) * size;
};

export const horizontalScale = scale;

/**
 * Scale vertical dimensions based on screen height
 */
export const verticalScale = (size: number): number => {
  return (SCREEN_HEIGHT / guidelineBaseHeight) * size;
};

/**
 * Moderate scale with resizing factor (default: 0.5) to avoid oversized elements on tablets
 */
export const moderateScale = (size: number, factor = 0.5): number => {
  return size + (scale(size) - size) * factor;
};

/**
 * Moderate vertical scale with resizing factor
 */
export const moderateVerticalScale = (size: number, factor = 0.5): number => {
  return size + (verticalScale(size) - size) * factor;
};

/**
 * Convert width percentage string or number to dp
 */
export const wp = (widthPercent: number | string): number => {
  const elemWidth = typeof widthPercent === 'number' ? widthPercent : parseFloat(widthPercent);
  return PixelRatio.roundToNearestPixel((SCREEN_WIDTH * elemWidth) / 100);
};

/**
 * Convert height percentage string or number to dp
 */
export const hp = (heightPercent: number | string): number => {
  const elemHeight = typeof heightPercent === 'number' ? heightPercent : parseFloat(heightPercent);
  return PixelRatio.roundToNearestPixel((SCREEN_HEIGHT * elemHeight) / 100);
};

export const isSmallDevice = SCREEN_WIDTH < 360;
export const isTablet = SCREEN_WIDTH >= 600;

export { SCREEN_WIDTH, SCREEN_HEIGHT };
