describe("QuizDeckPage", () => {
  const stubQuiz = {
    questions: [
      {
        sourceCardId: 1,
        sourceCard: { front: [], back: [] },
        sourceCardSide: "front",
        prompt: "What is the capital of France?",
        choices: ["Paris", "Berlin", "Rome"],
        correctChoiceIndex: 0,
      },
      {
        sourceCardId: 2,
        sourceCard: { front: [], back: [] },
        sourceCardSide: "front",
        prompt: "What color is a clear daytime sky?",
        choices: ["Red", "Green", "Blue"],
        correctChoiceIndex: 2,
      },
    ],
  };

  beforeEach(() => {
    cy.refreshDatabase();
    cy.login({ umndid: "user" });

    cy.intercept("POST", "/api/decks/*/quiz", { body: stubQuiz }).as(
      "createQuiz",
    );

    cy.createDeckForUser("user", { name: "Deck 1" }).then((deck) => {
      cy.createTextCardInDeck(deck.id, { front: "France", back: "Paris" });
      cy.createTextCardInDeck(deck.id, { front: "Sky", back: "Blue" });

      cy.visit(`/decks/${deck.id}/quiz`);
    });

    cy.contains("button", "Start Quiz").click();
    cy.wait("@createQuiz");
  });

  it("moves the selection with arrow keys without committing an answer", () => {
    cy.get("#quiz-q0-choice0").focus();

    cy.realPress("ArrowDown");

    cy.get("#quiz-q0-choice1").should("have.attr", "aria-checked", "true");
    cy.get('[role="radiogroup"]').should("not.have.attr", "data-disabled");
    cy.contains(/Correct!|Incorrect/).should("not.exist");

    cy.realPress("ArrowDown");

    cy.get("#quiz-q0-choice2").should("have.attr", "aria-checked", "true");

    cy.realPress("Tab");
    cy.focused().should("contain.text", "Check answer");

    cy.realPress("Enter");

    cy.contains("❌ Incorrect");
    cy.get("#announcer").should(
      "contain",
      "Incorrect. The correct answer is choice 1.",
    );
    cy.focused().should("contain.text", "Next");
  });
});
