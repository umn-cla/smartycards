describe("MatchingGame keyboard", () => {
  function selectButtonFor(text: string) {
    return cy.contains(".matching-side", text).siblings("button[aria-pressed]");
  }

  beforeEach(() => {
    cy.refreshDatabase();
    cy.login({ umndid: "user" });

    cy.createDeckForUser("user", { name: "Deck 1" }).then((deck) => {
      cy.createTextCardInDeck(deck.id, { front: "Front 1", back: "Back 1" });
      cy.createTextCardInDeck(deck.id, { front: "Front 2", back: "Back 2" });
      cy.visit(`/decks/${deck.id}/activities/matching`);
    });
  });

  it("plays a game to the end with the keyboard", () => {
    cy.get("button[aria-pressed]").first().focus();
    cy.realPress("Tab");
    cy.get("button[aria-pressed]").eq(1).should("have.focus");

    selectButtonFor("Front 1").focus();
    cy.realPress("Space");
    selectButtonFor("Front 1").should("have.attr", "aria-pressed", "true");

    selectButtonFor("Back 1").focus();
    cy.realPress("Enter");
    cy.get("#announcer").should("have.text", "Match. 1 pair left.");

    cy.contains(".matching-side", "Back 1").should("have.class", "opacity-25");
    selectButtonFor("Back 1")
      .should("have.focus")
      .and("have.attr", "aria-disabled", "true");
    cy.realPress("Enter");
    selectButtonFor("Back 1").should("have.attr", "aria-pressed", "false");

    selectButtonFor("Front 2").focus();
    cy.realPress("Space");
    selectButtonFor("Back 2").focus();
    cy.realPress("Enter");
    cy.get("#announcer").should("have.text", "You win!");
    cy.focused().should("contain.text", "Play Again");

    cy.realPress("Enter");
    cy.focused().should("have.attr", "aria-pressed", "false");
  });

  it("announces a mismatch", () => {
    selectButtonFor("Front 1").focus();
    cy.realPress("Space");
    selectButtonFor("Back 2").focus();
    cy.realPress("Enter");

    cy.get("#announcer").should("have.text", "Not a match. Try again.");
    selectButtonFor("Front 1").should("have.attr", "aria-pressed", "false");
    selectButtonFor("Back 2").should("have.attr", "aria-pressed", "false");
  });
});
