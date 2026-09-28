const pathTemplates = [
  "/",
  "/decks",
  "/decks/:deckId",
  "/decks/:deckId/share",
  "/decks/:deckId/import",
  "/decks/:deckId/clone",
  "/decks/:deckId/cards/create",
  "/community/decks",
  "/community/decks/:deckId",
  "/profile",
  "/decks/:deckId/activities/practice/embed",
  "/no-such-page",
];

describe("Page landmarks and headings", () => {
  let deckId: number;

  before(() => {
    cy.refreshDatabase();
    cy.login({ umndid: "user" });
    cy.createDeckForUser("user", {
      name: "Deck 1",
      description: "About deck 1",
    }).then((deck) => {
      deckId = deck.id;
    });
    cy.php(`\\App\\Models\\Deck::query()->update(['is_public' => true]);`);
  });

  beforeEach(() => {
    cy.login({ umndid: "user" });
  });

  pathTemplates.forEach((pathTemplate) => {
    it(`has one main with one h1 and no skipped heading levels: ${pathTemplate}`, () => {
      cy.visit(pathTemplate.replace(":deckId", String(deckId)));

      cy.get("main h1").should("have.length", 1);
      cy.get("main").should("have.length", 1);
      cy.get("main")
        .find("h1, h2, h3, h4, h5, h6")
        .then(($headings) => {
          const levels = $headings
            .toArray()
            .map((heading) => Number(heading.tagName.slice(1)));
          levels.forEach((level, index) => {
            const previousLevel = levels[index - 1] ?? 0;
            expect(level, `level of heading ${index + 1}`).to.be.at.most(
              previousLevel + 1,
            );
          });
        });
    });
  });
});
