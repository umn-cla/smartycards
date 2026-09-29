describe("DeckAuditHistoryPage", () => {
  const auditHistoryRoute = {
    method: "GET",
    pathname: "/api/decks/*/reports/audit-history",
  };

  beforeEach(() => {
    cy.refreshDatabase();
    cy.login({ umndid: "user" });

    cy.intercept(auditHistoryRoute).as("auditHistory");

    cy.createDeckForUser("user", { name: "Deck 1" }).then((deck) => {
      cy.visit(`/decks/${deck.id}/reports/audit-history`);
    });

    cy.wait("@auditHistory");
  });

  it("expands a row from the keyboard", () => {
    cy.get('[data-cy="toggle-audit-row-button"]').first().focus();

    cy.realPress("Enter");

    cy.focused().should("have.attr", "aria-expanded", "true");
    cy.get('[data-cy="audit-row-changes"]').should("be.visible");
  });

  it("keeps focus in a filter while filtered results load", () => {
    cy.intercept({ ...auditHistoryRoute, query: { user: "u" } }).as(
      "userFilteredAuditHistory",
    );

    cy.get('[data-cy="audit-user-filter-input"]').type("u");

    cy.wait("@userFilteredAuditHistory");
    cy.focused().should("have.attr", "data-cy", "audit-user-filter-input");
  });

  it("announces the result count after a filter change", () => {
    cy.intercept({ ...auditHistoryRoute, query: { user: "zzz" } }).as(
      "unmatchedUserAuditHistory",
    );

    cy.get('[data-cy="audit-user-filter-input"]').type("zzz");

    cy.wait("@unmatchedUserAuditHistory");
    cy.get("#announcer").should(
      "have.text",
      "No changes match the current filters.",
    );
  });
});
