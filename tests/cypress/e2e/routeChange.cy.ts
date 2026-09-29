describe("Route change", () => {
  function cacheDeckQuery(deckId: number, deckName: string): void {
    cy.visit(`/decks/${deckId}`);
    cy.get("#main-content").should("contain.text", deckName);
  }

  beforeEach(() => {
    cy.refreshDatabase();
    cy.login({ umndid: "user" });
  });

  it("titles a deck page and the page after it", () => {
    cy.createDeckForUser("user", { name: "Spanish 101" }).then((deck) => {
      cy.visit(`/decks/${deck.id}`);
    });
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

  it("announces a deck page once its deck has loaded", () => {
    cy.createDeckForUser("user", { name: "Spanish 101" }).then((deck) => {
      cy.intercept(
        { method: "GET", pathname: `/api/decks/${deck.id}` },
        (request) => {
          request.on("response", (response) => {
            response.setDelay(1500);
          });
        },
      ).as("slowDeck");
      cy.visit("/decks");

      cy.get(`nav a[href="/decks/${deck.id}"]`).click();
    });

    cy.wait("@slowDeck");
    cy.get("#announcer").should(
      "have.text",
      "Deck - Spanish 101 - SmartyCards",
    );
  });

  it("announces a deck page whose deck request fails", () => {
    cy.createDeckForUser("user", { name: "Spanish 101" }).then((deck) => {
      cy.intercept(
        { method: "GET", pathname: `/api/decks/${deck.id}` },
        { statusCode: 404 },
      ).as("missingDeck");
      cy.visit("/decks");

      cy.get(`nav a[href="/decks/${deck.id}"]`).click();
    });

    cy.wait("@missingDeck");
    cy.get("#announcer").should("have.text", "Deck - SmartyCards");
  });

  it("announces a deck name containing HTML as text", () => {
    const markupDeckName = '<img src="x" data-cy="injected">';
    cy.createDeckForUser("user", { name: markupDeckName }).then((deck) => {
      cacheDeckQuery(deck.id, markupDeckName);
      cy.contains("nav a", "Decks").click();
      cy.location("pathname").should("equal", "/decks");

      cy.get(`nav a[href="/decks/${deck.id}"]`).click();
    });

    cy.get("#announcer").should("contain.text", markupDeckName);
    cy.get("#announcer img").should("not.exist");
  });
});
