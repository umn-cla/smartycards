const deckName = "Deck 1";
const deckDescription = "About deck 1";

const pages: { pathTemplate: string; readyText: string | null }[] = [
  { pathTemplate: "/", readyText: null },
  { pathTemplate: "/decks", readyText: deckName },
  { pathTemplate: "/decks/:deckId", readyText: deckName },
  { pathTemplate: "/decks/:deckId/share", readyText: deckName },
  { pathTemplate: "/decks/:deckId/import", readyText: deckName },
  { pathTemplate: "/decks/:deckId/clone", readyText: deckName },
  { pathTemplate: "/decks/:deckId/cards/create", readyText: deckName },
  { pathTemplate: "/community/decks", readyText: deckName },
  { pathTemplate: "/community/decks/:deckId", readyText: deckName },
  { pathTemplate: "/profile", readyText: null },
  {
    pathTemplate: "/decks/:deckId/activities/practice/embed",
    readyText: deckName,
  },
  { pathTemplate: "/no-such-page", readyText: null },
];

describe("Page landmarks and headings", () => {
  let deckId: number;

  before(() => {
    cy.refreshDatabase();
    cy.login({ umndid: "user" });
    cy.createDeckForUser("user", {
      name: deckName,
      description: deckDescription,
    }).then((deck) => {
      deckId = deck.id;
    });
    cy.php(`\\App\\Models\\Deck::query()->update(['is_public' => true]);`);
  });

  beforeEach(() => {
    cy.login({ umndid: "user" });
  });

  pages.forEach(({ pathTemplate, readyText }) => {
    it(`has one main with one h1 and no skipped heading levels: ${pathTemplate}`, () => {
      cy.visit(pathTemplate.replace(":deckId", String(deckId)));

      if (readyText) {
        cy.contains("main", readyText);
      }

      cy.get("main h1").should("have.length", 1);
      cy.get("main").should("have.length", 1);
      cy.get("main")
        .find("h1, h2, h3, h4, h5, h6")
        .should("not.contain", deckDescription)
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
