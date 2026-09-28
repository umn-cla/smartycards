import * as T from "@/types";
import { defineStore } from "pinia";
import { toShuffled } from "@/lib/utils";

export interface MatchingCardSide {
  id: string;
  cardId: T.Card["id"];
  blocks: T.ContentBlock[];
  label: T.CardSideName;
  status: "idle" | "selected" | "match" | "mismatch" | "disabled";
}

export function isMatchedSide(side: MatchingCardSide): boolean {
  return side.status === "match" || side.status === "disabled";
}

export type SelectSideResult =
  | { type: "pairIncomplete" }
  | { type: "pairMatched"; unmatchedPairCount: number }
  | { type: "pairMismatched" };

export const useMatchingGameStore = defineStore("matchingGame", {
  state: () => ({
    gameState: "setup" as "setup" | "playing" | "win" | "error",
    sides: [] as MatchingCardSide[],
  }),
  getters: {
    selectedSides(state) {
      return state.sides.filter((side) => side.status === "selected");
    },
    unmatchedPairCount(state): number {
      const unmatchedSides = state.sides.filter((side) => !isMatchedSide(side));
      return unmatchedSides.length / 2;
    },
  },
  actions: {
    init(cards: T.Card[]) {
      const gameCards = toShuffled(cards).slice(0, Math.min(cards.length, 8));

      this.sides = gameCards.reduce((acc, card) => {
        const front: MatchingCardSide = {
          id: crypto.randomUUID(),
          cardId: card.id,
          blocks: card.front,
          label: "front",
          status: "idle",
        };
        const back: MatchingCardSide = {
          id: crypto.randomUUID(),
          cardId: card.id,
          blocks: card.back,
          label: "back",
          status: "idle",
        };

        return [...acc, front, back];
      }, [] as MatchingCardSide[]);

      // shuffle the sides
      this.sides = toShuffled(this.sides);

      this.gameState = "playing";
    },

    selectSide(sideId: string): SelectSideResult {
      this.sides = this.sides.map((side) => {
        if (side.id === sideId && ["idle", "selected"].includes(side.status)) {
          return {
            ...side,
            status: side.status === "selected" ? "idle" : "selected",
          };
        }

        return side;
      });

      return this.checkSelectedSidesForMatches();
    },

    checkSelectedSidesForMatches(): SelectSideResult {
      const selectedSides = this.selectedSides;

      if (selectedSides.length < 2) {
        return { type: "pairIncomplete" };
      }

      // if the sides are a match, then update status to "match"
      const [side1, side2] = selectedSides;
      if (side1.cardId === side2.cardId) {
        this.handleMatch(selectedSides);
        return {
          type: "pairMatched",
          unmatchedPairCount: this.unmatchedPairCount,
        };
      }

      this.handleMismatch(selectedSides);
      return { type: "pairMismatched" };
    },

    handleMatch(selectedSides: MatchingCardSide[]) {
      const selectedSideIds = selectedSides.map((side) => side.id);
      this.sides = this.sides.map((side) => {
        if (selectedSideIds.includes(side.id)) {
          return { ...side, status: "match" };
        }

        return side;
      });

      setTimeout(() => {
        this.sides = this.sides.map((side) => {
          if (selectedSideIds.includes(side.id)) {
            return { ...side, status: "disabled" };
          }

          return side;
        });

        if (this.sides.every((side) => side.status !== "idle")) {
          this.gameState = "win";
        }
      }, 500);
    },

    handleMismatch(selectedSides: MatchingCardSide[]) {
      const selectedSideIds = selectedSides.map((side) => side.id);
      this.sides = this.sides.map((side) => {
        if (selectedSideIds.includes(side.id)) {
          return { ...side, status: "mismatch" };
        }

        return side;
      });

      setTimeout(() => {
        this.sides = this.sides.map((side) => {
          if (selectedSideIds.includes(side.id)) {
            return { ...side, status: "idle" };
          }

          return side;
        });
      }, 500);
    },
  },
});
