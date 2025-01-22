import { times } from "ramda";

describe("DeckShowPage", () => {
  let deckId = null;
  beforeEach(() => {
    cy.refreshDatabase();
    cy.login({ umndid: "user" });

    cy.createDeckForUser("user", { name: "Deck 1" }).then((deck) => {
      deckId = deck.id;

      times(
        (i) =>
          cy.createTextCardInDeck(deckId, {
            front: `Front side ${i}`,
            back: `Back side ${i}`,
          }),
        3,
      );
    });
  });

  it("practices a deck", () => {
    cy.visit(`/decks/${deckId}/practice`);

    const cardsSeen = new Set<string>();

    // card 1
    cy.contains(/Front side \d/).then(($el: JQuery<HTMLElement>) => {
      cardsSeen.add($el.text());
    });

    cy.contains("Flip").click();
    cy.contains(/Back side \d/);
    cy.contains("✅").click();
    cy.wait(500);

    // card 2
    cy.contains(/Front side \d/).then(($el: JQuery<HTMLElement>) => {
      // we should not see the same card again
      const cardText = $el.text();
      expect(cardsSeen.has(cardText)).to.be.false;
      cardsSeen.add(cardText);
    });
    cy.contains("✅").click();
    cy.wait(500);

    // card 3
    cy.contains(/Front side \d/).then(($el: JQuery<HTMLElement>) => {
      // we should not see the same card again
      const cardText = $el.text();
      expect(cardsSeen.has(cardText)).to.be.false;
      cardsSeen.add(cardText);
    });
    cy.contains("✅").click();
    cy.wait(500);

    // we should see the end message
    cy.contains("You have completed").then(() => {
      expect(cardsSeen.size).to.eq(3);
    });

    // we should see the retry button
    cy.contains("Practice Again").click();

    // we should see the first card again
    cy.contains(/Front side \d/);
  });

  it("changes the initial card side", () => {
    cy.visit(`/decks/${deckId}/practice`);

    // expect front side by default
    cy.contains(/Front side \d/);

    // select back
    cy.get("#starting-side-select").select("Back");

    // expect back side now
    cy.contains(/Back side \d/);

    // expect it to persist to next cart
    cy.contains("✅").click();

    // expect back side again
    cy.contains(/Back side \d/);
  });
});
