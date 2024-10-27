const LEVEL_CONSTANT = 0.1;

export function getLevelFromTotalXP(xp: number): number {
  // Level = Constant * SQRT(XP)
  return Math.floor(LEVEL_CONSTANT * Math.sqrt(xp)) + 1;
}

export function getTotalXP(level: number): number {
  // XP = (Level / Constant) ^ 2
  return Math.pow((level - 1) / LEVEL_CONSTANT, 2);
}

export function getXPNeededAtThisLevel(level: number) {
  const currentLevelXP = getTotalXP(level);
  const nextLevelXP = getTotalXP(level + 1);
  return nextLevelXP - currentLevelXP;
}

export function getXPEarnedAtThisLevel(xp: number): number {
  const currentLevel = getLevelFromTotalXP(xp);
  return xp - getTotalXP(currentLevel);
}
