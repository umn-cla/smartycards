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

    it("uses deck side locale defaults if no card language is set", () => {
      // set the deck side default language
      cy.visit(`/decks/${deckId}/edit`);

      // wait for deck info to load
      cy.get("#name").should("have.value", "My Deck");

      // set the deck side default language
      cy.visit(`/decks/${deckId}/edit`);

      // wait for deck info to load
      cy.get("#name").should("have.value", "My Deck");

      // Click to open the select dropdown
      cy.get("#default-front-locale")
        .select("fr-FR")
        .should("have.value", "fr-FR");

      cy.get("#default-back-locale")
        .select("es-MX")
        .should("contain.text", "Spanish (Mexico)");

      // save the deck
      cy.intercept("PUT", `/api/decks/${deckId}`).as("updateDeck");
      cy.contains("button", "Save").click();

      // wait for the request to complete
      cy.wait("@updateDeck");

      // verify that the change was saved
      // set the deck side default language
      cy.visit(`/decks/${deckId}/edit`);

      // wait for deck info to load
      cy.get("#name").should("have.value", "My Deck");

      // verify that the default languages are set and rendering correcly
      cy.get("#default-front-locale").should("have.value", "fr-FR");
      cy.get("#default-back-locale").should("have.value", "es-MX");

      // back to the deck index page
      cy.visit(`/decks/${deckId}`);

      // check that the TTS Player is visible on the first card
      // and is using French as the language
      cy.intercept("POST", "/api/tts").as("ttsRequest");
      cy.contains("Front side 0")
        .parent()
        .within(() => {
          cy.get('[data-cy="simple-tts-player"]')
            .should("be.visible")
            .should("contain.text", "French")
            .click();
          cy.wait("@ttsRequest");
          cy.get('[data-cy="simple-tts-player"]').should(
            "have.attr",
            "aria-pressed",
            "true",
          );
        });

      // flip the card
      cy.contains("Front side 0")
        .closest('[data-cy="flippable-card"]')
        .within(() => {
          cy.contains("Flip").click();
        });

      // check the back side of the card is using Spanish
      cy.contains("Back side 0")
        .closest('[data-cy="card-side-view--Back"]')
        // workaround an issue where cypress thinks the tts
        // player is not visible when the card is flipped
        .invoke("css", "backface-visibility", "visible")
        .within(() => {
          cy.get('[data-cy="simple-tts-player"]')
            .should("be.visible")
            .should("contain.text", "Spanish (Mexico)")
            .click();

          cy.wait("@ttsRequest");
          cy.get('[data-cy="simple-tts-player"]').should(
            "have.attr",
            "aria-pressed",
            "true",
          );
        });
    });

    it("overrides the default language when a specific language for the text block is chosen", () => {
      cy.visit(`/decks/${deckId}`);

      // set the deck side default language
      cy.visit(`/decks/${deckId}/edit`);

      // wait for deck info to load
      cy.get("#name").should("have.value", "My Deck");

      // set the deck side default language
      cy.visit(`/decks/${deckId}/edit`);

      // wait for deck info to load
      cy.get("#name").should("have.value", "My Deck");

      // Click to open the select dropdown
      cy.get("#default-front-locale")
        .select("fr-FR")
        .should("have.value", "fr-FR");

      // save the deck
      cy.intercept("PUT", `/api/decks/${deckId}`).as("updateDeck");

      cy.contains("button", "Save").click();

      // wait for the request to complete
      cy.wait("@updateDeck");

      // we should now be on the deck index page
      cy.location("pathname").should("eq", `/decks/${deckId}`);

      // now let's set the language for the text block on the first card
      cy.contains("Front side 0")
        .closest('[data-cy="card-side-view--Front"]')
        .within(() => {
          cy.get('[data-cy="more-card-actions-button"]').click();
        });

      cy.contains("Edit Card").click();

      // we should now be on the card edit page
      cy.location("pathname").should("contain", `/edit`);

      // set the language for the text block
      cy.get(
        '[data-cy="front-side-input"] [data-cy="text-block-input-container"]',
      ).within(() => {
        cy.get('[data-cy="set-language-toggle"]').click();

        // by default the language should be set to the default deck side lang
        cy.get('[data-cy="select-language"]').should("have.value", "fr-FR");

        // select the language
        cy.get('[data-cy="select-language"]').select("es-MX");
      });

      // save the card
      cy.intercept("PUT", `/api/cards/*`).as("updateCard");
      cy.contains("button", "Save").click();

      // wait for the request to complete
      cy.wait("@updateCard");

      // we should now be on the deck show page
      cy.location("pathname").should("eq", `/decks/${deckId}`);

      // check that the TTS Player is visible on the first card
      // and is using Spanish as the language
      cy.intercept("POST", "/api/tts").as("ttsRequest");
      cy.contains("Front side 0")
        .parent()
        .within(() => {
          cy.get('[data-cy="simple-tts-player"]')
            .should("be.visible")
            .should("contain.text", "Spanish (Mexico)")
            .click();
          cy.wait("@ttsRequest");
          cy.get('[data-cy="simple-tts-player"]').should(
            "have.attr",
            "aria-pressed",
            "true",
          );
        });

      // go to the edit page for the card
      cy.contains("Front side 0")
        .closest('[data-cy="card-side-view--Front"]')
        .within(() => {
          cy.get('[data-cy="more-card-actions-button"]').click();
        });

      cy.contains("Edit Card").click();

      // verify that the language override is visible and set correctly
      cy.get(
        '[data-cy="front-side-input"] [data-cy="text-block-input-container"]',
      ).within(() => {
        cy.get('[data-cy="select-language"]')
          .should("be.visible")
          .should("have.value", "es-MX");
      });
    });
  });
});
