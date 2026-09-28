describe("DeckAuditHistoryPage", () => {
  beforeEach(() => {
    cy.refreshDatabase();
    cy.login({ umndid: "user" });

    cy.intercept({
      method: "GET",
      pathname: "/api/decks/*/reports/audit-history",
    }).as("auditHistory");

    cy.createDeckForUser("user", { name: "Deck 1" }).then((deck) => {
      cy.visit(`/decks/${deck.id}/reports/audit-history`);
    });

    cy.wait("@auditHistory");
  });

  it("expands a row from the keyboard", () => {
    cy.get('[data-cy="audit-row-toggle"]').first().focus();

    cy.press(Cypress.Keyboard.Keys.ENTER);

    cy.focused().should("have.attr", "aria-expanded", "true");
    cy.get('[data-cy="audit-row-changes"]').should("be.visible");
  });

  it("keeps focus in a filter while filtered results load", () => {
    cy.get('[data-cy="audit-filter-user"]').type("u");

    cy.wait("@auditHistory").its("request.query.user").should("equal", "u");

    cy.focused().should("have.attr", "data-cy", "audit-filter-user");
  });
});
