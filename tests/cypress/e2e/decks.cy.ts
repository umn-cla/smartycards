describe("deck", () => {
  const STAFF_UMNDID = "staff";
  const USER_UMNDID = "user";

  enum Role {
    OWNER = "owner",
    EDITOR = "editor",
    VIEWER = "viewer",
  }

  let deckId = null;

  beforeEach(() => {
    cy.refreshDatabase();
    cy.login({ umndid: STAFF_UMNDID });
    cy.createDeckForUser(STAFF_UMNDID, { name: "Deck 1" }).then((deck) => {
      deckId = deck.id;
    });
    cy.logout();
    cy.login({ umndid: USER_UMNDID });
  });

  context("as an `owner`", () => {
    beforeEach(() => {
      // add the test user as an owner of the deck
      cy.addUserToDeck(USER_UMNDID, deckId, Role.OWNER);
    });

    it("can edit a deck", () => {
      cy.visit(`/decks`);
      cy.get(".deck-list-item")
        .first()
        .within(() => {
          cy.get('[data-cy="more-deck-actions-button"]').click();
        });

      cy.contains("Settings").click();

      // verify that we're on the edit deck page
      cy.contains("Edit Deck");
      cy.url().should("contain", `/decks/${deckId}/edit`);

      // edit the deck title and description
      cy.get("#name").clear().type("Deck 1 Updated");
      cy.get("#description").clear().type("Description 1 Updated");

      // save the deck
      cy.contains("button", "Save").click();

      // verify that the deck has been updated
      cy.contains("Deck 1 Updated");
      cy.contains("Description 1 Updated");
    });
  });

  context("as an `editor`", () => {
    beforeEach(() => {
      cy.addUserToDeck(USER_UMNDID, deckId, Role.EDITOR);
    });

    it("shouldn't be able to change name or description", () => {
      cy.visit(`/decks`);

      // opening More Actions menu for the deck
      cy.get(".deck-list-item")
        .first()
        .within(() => {
          cy.get('[data-cy="more-deck-actions-button"]').click();
        });

      // there should be no Settings options
      cy.contains("Settings").should("not.exist");

      // trying to navigate to the deck edit page gives error
      cy.visit(`/decks/${deckId}/edit`);

      cy.contains("You do not have permission to edit this deck");
    });
  });
});
