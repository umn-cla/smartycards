describe("BlockEditor focus after Remove block", () => {
  beforeEach(() => {
    cy.refreshDatabase();
    cy.login({ umndid: "user" });

    cy.createDeckForUser("user", { name: "Deck 1" }).then((deck) => {
      cy.visit(`/decks/${deck.id}/cards/create`);
    });

    cy.get('[data-cy="front-side-input"]').as("frontSideInput");
  });

  it("moves focus to the next block's drag handle", () => {
    cy.get("@frontSideInput").contains("Add Block").click();
    cy.get("[role='menu']").contains("Hint").click();
    cy.get("@frontSideInput")
      .find('[data-cy="hint-block-input"] input')
      .should("have.focus");

    cy.get("@frontSideInput")
      .find('[data-cy="remove-content-block-button"]')
      .first()
      .focus();
    cy.realPress("Enter");

    cy.focused()
      .should("have.class", "drag-handle")
      .closest('[data-cy="content-block-container"]')
      .find('[data-cy="hint-block-input"]')
      .should("exist");
  });

  it("moves focus to Add Block when the side has no blocks left", () => {
    cy.get("@frontSideInput")
      .find('[data-cy="remove-content-block-button"]')
      .focus();
    cy.realPress("Enter");

    cy.get("@frontSideInput")
      .find('[data-cy="add-content-block-button"]')
      .should("have.focus");
  });
});
