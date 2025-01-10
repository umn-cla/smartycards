describe("Happy Path", () => {
  beforeEach(() => {
    cy.refreshDatabase();
  });

  it("signs in", () => {
    cy.visit("/");

    cy.contains("SmartyCards");

    cy.contains("Sign In").click();

    cy.get("#username").type("admin");
    cy.get("#password").type("admin");
    cy.contains("input", "Login").click();

    cy.contains("Decks");
  });

  it("creates a deck", () => {
    cy.login({ umndid: "admin" });

    cy.visit("/decks");

    cy.contains("Create Deck").click();

    cy.get("#name").type("Deck 1");
    cy.get("#description").type("Description 1");

    cy.contains("button", "Create").click();

    // we should be on the deck page
    cy.contains("Deck 1");
    cy.contains("Description 1");

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
});
