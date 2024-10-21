import * as T from "@/types";
import { defineStore } from "pinia";
import { toShuffled, uuid } from "@/lib/utils";

export interface MatchingCardSide {
  id: string;
  cardId: T.Card["id"];
  blocks: T.ContentBlock[];
  label: T.CardSideName;
  status: "idle" | "selected" | "match" | "mismatch" | "disabled";
}

export const useMatchingGameStore = defineStore("matchingGame", {
  state: () => ({
    gameState: "setup" as "setup" | "playing" | "win" | "error",
    sides: [] as MatchingCardSide[],
  }),
  getters: {
    selectedSides(state) {
      return state.sides.filter((side) => side.status === "selected");
    },
  },
  actions: {
    init(cards: T.Card[]) {
      this.sides = cards.reduce((acc, card) => {
        const front: MatchingCardSide = {
          id: uuid(),
          cardId: card.id,
          blocks: card.front,
          label: "front",
          status: "idle",
        };
        const back: MatchingCardSide = {
          id: uuid(),
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

    selectSide(sideId: string) {
      this.sides = this.sides.map((side) => {
        if (side.id === sideId) {
          return { ...side, status: "selected" };
        }

        return side;
      });
      this.checkSelectedSidesForMatches();
    },

    checkSelectedSidesForMatches() {
      const selectedSides = this.selectedSides;

      if (selectedSides.length < 2) {
        return;
      }

      // if the sides are a match, then update status to "match"
      const [side1, side2] = selectedSides;
      if (side1.cardId === side2.cardId) {
        this.handleMatch(selectedSides);
      } else {
        this.handleMismatch(selectedSides);
      }
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
