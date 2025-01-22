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

  it("deletes a card", () => {
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

  it("filters the list of cards given a search term", () => {
    cy.createTextCardInDeck(deckId, { front: "Front side", back: "Back side" });
    cy.createTextCardInDeck(deckId, {
      front: "Another card",
      back: "Back side",
    });

    cy.visit(`/decks/${deckId}`);

    // verify that we see all the cards
    cy.get('[data-cy="flippable-card"]').should("have.length", 2);

    // search for a card
    cy.get('[data-cy="card-search-input"]').type("Another");

    // verify that we see only the card that matches the search term
    cy.get('[data-cy="flippable-card"]').should("have.length", 1);
    cy.contains("Another card");
  });

  it.only("'Create and Add Another' button uses previous card's structure", () => {
    // setup intercept for creating a card
    cy.intercept("POST", "/api/decks/*/cards").as("createCard");

    cy.visit(`/decks/${deckId}/cards/create`);

    // set up aliases
    cy.get('[data-cy="front-side-input"]').as("frontSideInput");
    cy.get('[data-cy="back-side-input"]').as("backSideInput");

    // remove old front-side block
    cy.get("@frontSideInput").within(() => {
      cy.get('[data-cy="remove-content-block-button"]').click();
    });

    // add a new image block
    cy.get("@frontSideInput").contains("Add Block").click();
    cy.get("[role='menu']").contains("Image").click();
    cy.get("@frontSideInput").within(() => {
      cy.get('[data-cy="image-block-input"] [data-cy="image-url-text-input"]')
        .should("exist")
        .should("be.visible");
      cy.get(
        '[data-cy="image-block-input"] [data-cy="image-url-text-input"]',
      ).type("image.jpg");
    });

    // // add a hint block to front side
    // cy.get("@frontSideInput").contains("Add Block").click();
    // cy.get("[role='menu']").contains("Hint").click();
    // cy.get("@frontSideInput").within(() => {
    //   cy.getInputByLabel("Hint Text").type("This is a hint", { delay: 0 });
    // });

    // // remove the default text block from back side
    // cy.get("@backSideInput").within(() => {
    //   cy.get('[data-cy="remove-content-block-button"]').click();
    // });

    // // add an image block to back side
    // cy.get("@backSideInput").contains("Add Block").click();
    // cy.get("[role='menu']").contains("Image").click();
    // cy.get("@backSideInput").within(() => {
    //   cy.getInputByLabel("Image Url").type("image.jpg", {
    //     delay: 0,
    //     waitForAnimations: true,
    //   });
    // });

    // // CREATE AND ADD ANOTHER
    // cy.contains("Create + Another").click();
    // cy.wait("@createCard");

    // cy.get("[data-cy='front-side-input']").within(() => {
    //   // verify that the front side has the image block and hint block
    //   cy.get("[data-cy='image-block-input']").should("exist");
    //   cy.get("[data-cy='hint-block-input']").should("exist");

    //   // and that it doesn't have a text block
    //   cy.get("[data-cy='text-block-input-container']").should("not.exist");
    // });

    // cy.get("[data-cy='back-side-input']").within(() => {
    //   // and that the back side has the image block
    //   cy.get("[data-cy='image-block-input']").should("exist");
    //   // and that it doesn't have a text block
    //   cy.get("[data-cy='text-block-input-container']").should("not.exist");
    // });
  });
});
