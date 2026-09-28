describe("Route change", () => {
  let deckId: number;

  beforeEach(() => {
    cy.refreshDatabase();
    cy.login({ umndid: "user" });
    cy.createDeckForUser("user", { name: "Spanish 101" }).then((deck) => {
      deckId = deck.id;
    });
  });

  it("titles a deck page and the page after it", () => {
    cy.visit(`/decks/${deckId}`);
    cy.title().should("equal", "Deck - Spanish 101 - SmartyCards");

    cy.contains("nav a", "Community").click();
    cy.title().should("equal", "Community Decks - SmartyCards");
  });

  it("focuses the main content and announces the page after keyboard navigation", () => {
    cy.visit("/decks");
    cy.contains("nav a", "Community").focus();

    cy.realPress("Enter");

    cy.focused().should("have.id", "main-content");
    cy.get("#announcer").should("have.text", "Community Decks - SmartyCards");
  });

  it("announces a deck name containing HTML as text", () => {
    const markupDeckName = '<img src="x" data-cy="injected">';
    cy.createDeckForUser("user", { name: markupDeckName }).then((deck) => {
      cy.visit(`/decks/${deck.id}`);
      cy.get("#main-content").should("contain.text", markupDeckName);
      cy.contains("nav a", "Decks").click();

      cy.get(`nav a[href="/decks/${deck.id}"]`).click();
    });

    cy.get("#announcer").should("contain.text", markupDeckName);
    cy.get("#announcer img").should("not.exist");
  });
});
