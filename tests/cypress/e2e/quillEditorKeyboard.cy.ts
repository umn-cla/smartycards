describe("QuillEditor keyboard", () => {
  beforeEach(() => {
    cy.refreshDatabase();
    cy.login({ umndid: "user" });

    cy.createDeckForUser("user", { name: "Deck 1" }).then((deck) => {
      cy.visit(`/decks/${deck.id}/cards/create`);
    });

    cy.get(
      '[data-cy="front-side-input"] [data-cy="text-block-input-container"]',
    ).as("frontTextBlock");

    cy.get("@frontTextBlock").within(() => {
      cy.get(".ql-editor").type("x = 1");
      cy.get(".ql-toolbar .ql-code-block").click();
      cy.get(".ql-editor .ql-code-block").should("have.text", "x = 1");
    });

    cy.focused().should("have.class", "ql-editor");
  });

  it("moves focus out of a code block on Tab", () => {
    cy.realPress("Tab");

    cy.focused().should("not.have.class", "ql-editor");
    cy.get("@frontTextBlock")
      .find(".ql-editor .ql-code-block")
      .should("have.text", "x = 1");
  });

  it("moves focus out of a code block on Shift+Tab", () => {
    cy.realPress(["Shift", "Tab"]);

    cy.focused().should("not.have.class", "ql-editor");
    cy.get("@frontTextBlock")
      .find(".ql-editor .ql-code-block")
      .should("have.text", "x = 1");
  });
});
