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

    cy.realPress("ArrowDown", { pressDelay: 100 });

    cy.get("#quiz-q0-choice1").should("have.attr", "aria-checked", "true");
    cy.get('[role="radiogroup"]').should("not.have.attr", "data-disabled");
    cy.contains(/Correct!|Incorrect/).should("not.exist");

    cy.realPress("ArrowDown", { pressDelay: 100 });

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

  it("names radios by their choice text and moves focus after Next and Finish", () => {
    cy.get("#quiz-q0-choice1").click();
    cy.get("#quiz-q0-choice1").should("have.attr", "aria-checked", "true");
    cy.get("#quiz-q0-choice0").click();
    cy.contains("button", "Check answer").click();

    cy.contains("✅ Correct!");
    cy.focused().should("contain.text", "Next");

    cy.realPress("Enter");

    cy.focused().should("match", "h3").and("contain.text", "Question 2 of 2");
    ["Red", "Green", "Blue"].forEach((choiceText, choiceIndex) => {
      cy.get(`#quiz-q1-choice${choiceIndex}`)
        .should("have.attr", "aria-label")
        .and("contain", choiceText);
    });
    cy.get('[role="radiogroup"]')
      .invoke("attr", "aria-labelledby")
      .then((promptId) => {
        cy.get(`#${promptId}`).should(
          "contain.text",
          "What color is a clear daytime sky?",
        );
      });

    cy.get("#quiz-q1-choice0").click();
    cy.contains("button", "Check answer").click();
    cy.focused().should("contain.text", "Finish");

    cy.realPress("Enter");

    cy.focused().should("match", "h2").and("contain.text", "Complete");
    cy.get("#announcer").should("contain", "Quiz complete. 1 of 2 correct.");
  });
});
