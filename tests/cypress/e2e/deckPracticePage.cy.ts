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
      console.log({ cardText });
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
});
