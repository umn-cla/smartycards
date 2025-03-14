import { times } from "ramda";

describe("Text Block", () => {
  let deckId = null;
  beforeEach(() => {
    cy.refreshDatabase();
    cy.login({ umndid: "user" });

    cy.createDeckForUser("user", { name: "My Deck" }).then((deck) => {
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

  it("shows the TTS player if TTS is enabled", () => {
    // verify that the TTS player is not visible if not enabled
    cy.visit(`/decks/${deckId}`);
    cy.contains("Front side 0")
      .parent()
      .within(() => {
        cy.get('[data-cy="simple-tts-player"]').should("not.exist");
      });

    // enable TTS for the deck
    cy.visit(`/decks/${deckId}/edit`);

    // wait for deck info to load
    cy.get("#name").should("have.value", "My Deck");
    cy.get("[data-cy=tts-switch]").click();

    // check that the checkbox is checked
    cy.get('[data-cy="tts-switch"]').should(
      "have.attr",
      "aria-checked",
      "true",
    );

    // save the deck
    cy.intercept("PUT", `/api/decks/${deckId}`).as("updateDeck");
    cy.contains("button", "Save").click();

    // wait for the request to complete
    cy.wait("@updateDeck");

    // we should now be on the deck index page
    cy.location("pathname").should("eq", `/decks/${deckId}`);

    // check that the TTS Player is visible on the first card
    cy.intercept("POST", "/api/tts").as("ttsRequest");
    cy.contains("Front side 0")
      .parent()
      .within(() => {
        cy.get('[data-cy="simple-tts-player"]').should("be.visible").click();
        cy.wait("@ttsRequest");
        cy.get('[data-cy="simple-tts-player"]').should(
          "have.attr",
          "aria-pressed",
          "true",
        );
      });
  });

  context("when TTS is enabled for a deck", () => {
    beforeEach(() => {
      // enable TTS for the deck
      cy.visit(`/decks/${deckId}/edit`);

      // wait for deck info to load
      cy.get("#name").should("have.value", "My Deck");
      cy.get("[data-cy=tts-switch]").click();

      // save the deck
      cy.intercept("PUT", `/api/decks/${deckId}`).as("updateDeck");
      cy.contains("button", "Save").click();

      // wait for the request to complete
      cy.wait("@updateDeck");
    });

    it("defaults to an `auto` language if no language is set for the block or the deck side", () => {
      cy.contains("Front side 0")
        .parent()
        .within(() => {
          cy.get('[data-cy="simple-tts-player"] .sr-only').should(
            "have.text",
            "Auto",
          );
        });
    });

    it("permits setting a language for the block");

    it("plays the block content in the selected language");

    it("uses the deck side default language if no language is set");

    it("overrides the deck side default language if a language is set");
  });
});
