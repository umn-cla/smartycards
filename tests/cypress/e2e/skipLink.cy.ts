describe("Skip link", () => {
  beforeEach(() => {
    cy.refreshDatabase();
    cy.login({ umndid: "user" });
    cy.visit("/decks");
  });

  it("is the first focusable element on the page", () => {
    cy.get("a[href], button, input, select, textarea, [tabindex]")
      .not('[tabindex="-1"]')
      .first()
      .invoke("text")
      .invoke("trim")
      .should("equal", "Skip to main content");
  });

  it("moves focus to the main content", () => {
    cy.contains("button", "Skip to main content").focus();

    cy.realPress("Enter");

    cy.focused().should("have.id", "main-content");

    cy.realPress("Tab");

    cy.focused().closest("#main-content").should("exist");
  });
});
