describe("FlippableCard", () => {
  const frontSide = '[data-cy="card-side-view--Front"]';
  const backSide = '[data-cy="card-side-view--Back"]';
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

  it("marks only the hidden side inert", () => {
    cy.get(frontSide).should("not.have.attr", "inert");
    cy.get(backSide).should("have.attr", "inert");

    cy.get("@flipButton").click();

    cy.get(frontSide).should("have.attr", "inert");
    cy.get(backSide).should("not.have.attr", "inert");
  });

  it("does not Tab into the hidden back side", () => {
    cy.get(frontSide).find(moreCardActionsButton).focus();

    cy.realPress("Tab");

    cy.get("@flipButton").should("have.focus");
  });

  it("does not Shift+Tab into the hidden front side", () => {
    cy.get("@flipButton").focus();
    cy.realPress("Enter");
    cy.get(backSide).find(moreCardActionsButton).focus();

    cy.realPress(["Shift", "Tab"]);

    cy.document().should((doc) => {
      const hiddenSide = doc.querySelector(frontSide);
      expect(hiddenSide?.contains(doc.activeElement)).to.equal(false);
    });
  });

  it("has one Flip button that keeps focus and names the next side", () => {
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
