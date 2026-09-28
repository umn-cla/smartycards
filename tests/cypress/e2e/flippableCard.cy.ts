describe("FlippableCard", () => {
  const frontFace = '[data-cy="card-side-view--Front"]';
  const backFace = '[data-cy="card-side-view--Back"]';
  const moreCardActionsButton = '[data-cy="more-card-actions-button"]';

  beforeEach(() => {
    cy.refreshDatabase();
    cy.login({ umndid: "user" });

    cy.createDeckForUser("user", { name: "Deck 1" }).then((deck) => {
      cy.createTextCardInDeck(deck.id, {
        front: "Front side",
        back: "Back side",
      });
      cy.visit(`/decks/${deck.id}`);
    });

    cy.get('[data-cy="flippable-card"]')
      .contains("button", "Flip")
      .as("flipButton");
  });

  it("marks only the hidden face inert", () => {
    cy.get(frontFace).should("not.have.attr", "inert");
    cy.get(backFace).should("have.attr", "inert");

    cy.get("@flipButton").click();

    cy.get(frontFace).should("have.attr", "inert");
    cy.get(backFace).should("not.have.attr", "inert");
  });

  it("does not Tab into the hidden back face", () => {
    cy.get(frontFace).find(moreCardActionsButton).focus();

    cy.realPress("Tab");

    cy.get("@flipButton").should("have.focus");
  });

  it("does not Shift+Tab into the hidden front face", () => {
    cy.get("@flipButton").focus();
    cy.realPress("Enter");
    cy.get(backFace).find(moreCardActionsButton).focus();

    cy.realPress(["Shift", "Tab"]);

    cy.document().should((doc) => {
      const hiddenFace = doc.querySelector(frontFace);
      expect(hiddenFace?.contains(doc.activeElement)).to.equal(false);
    });
  });

  it("has one Flip button that keeps focus and names the next face", () => {
    cy.get('[data-cy="flippable-card"] button:contains("Flip")').should(
      "have.length",
      1,
    );
    cy.get("@flipButton")
      .should("have.attr", "aria-label", "Flip to back")
      .focus();

    cy.realPress("Enter");

    cy.get("@flipButton")
      .should("have.focus")
      .and("have.attr", "aria-label", "Flip to front");
    cy.get("#announcer").should("have.text", "Showing back");
  });
});
