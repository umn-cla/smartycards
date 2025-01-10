import * as T from "../../../resources/client/types";

describe("DeckShowPage", () => {
  let deckId = null;
  beforeEach(() => {
    cy.refreshDatabase();
    cy.login({ umndid: "user" });

    cy.createDeckForUser("user", { name: "Deck 1" }).then((deck) => {
      deckId = deck.id;
    });
  });

  it("creates a card", () => {
    cy.visit(`/decks/${deckId}`);
    // create a card
    cy.contains("Create Card").click();

    // add front side content
    cy.get('[data-cy="front-side-input"]').within(() => {
      cy.get(
        '[data-cy="text-block-input-container"] [data-cy="text-block-input"] .ql-editor',
      ).type("Front side");
    });

    // add back side content
    cy.get('[data-cy="back-side-input"]').within(() => {
      cy.get(
        '[data-cy="text-block-input-container"] [data-cy="text-block-input"] .ql-editor',
      ).type("Back side");
    });

    // save the card
    cy.get('[data-cy="save-card-button"]').click();

    // we should be on the deck page
    cy.contains("Deck 1");

    // we should see the card
    cy.get('[data-cy="flippable-card"]').within(() => {
      cy.contains("Front side");
      cy.contains("Flip").click();

      cy.contains("Back side");
    });
  });

  it("edits a card", () => {
    // create a card
    cy.createTextCardInDeck(deckId, { front: "Front side", back: "Back side" });

    cy.visit(`/decks/${deckId}`);

    cy.contains("Front side")
      .closest('[data-cy="card-side-view--Front"]')
      .within(() => {
        cy.get('[data-cy="more-card-actions-button"]').click();
      });

    cy.contains("Edit Card").click();
    // edit the front side
    cy.get('[data-cy="front-side-input"]').within(() => {
      cy.get(
        '[data-cy="text-block-input-container"] [data-cy="text-block-input"] .ql-editor',
      ).type(" edited");
    });

    // save the card
    cy.get('[data-cy="save-card-button"]').click();

    // we should be on the deck page
    cy.contains("Deck 1");

    // we should see the card
    cy.get('[data-cy="flippable-card"]').within(() => {
      cy.contains("Front side edited");
      cy.contains("Flip").click();

      cy.contains("Back side");
    });
  });

  it.only("deletes a card", () => {
    cy.createTextCardInDeck(deckId, { front: "Front side", back: "Back side" });

    cy.visit(`/decks/${deckId}`);

    cy.contains("Front side")
      .closest('[data-cy="card-side-view--Front"]')
      .within(() => {
        cy.get('[data-cy="more-card-actions-button"]').click();
      });

    cy.contains("Delete Card").click();

    // confirm the deletion
    cy.get('[data-cy="dialog-content"]').within(() => {
      // expect a confirm modal
      cy.contains("Are you sure").should("be.visible");
      cy.get('button[type="submit"]').should("have.text", "Delete").click();
    });

    cy.contains("Front side").should("not.be.visible");
  });

  it("filters the list of cards given a search term");
});
