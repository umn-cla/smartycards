import { createRouter, createWebHistory } from "vue-router";
import HomePage from "../pages/HomePage/HomePage.vue";
import { useQueryClient } from "@tanstack/vue-query";
import * as api from "@/api";
import { PROFILE_QUERY_KEY } from "@/queries/queryKeys";

function includeDevRoutesIfDev() {
  if (!import.meta.env.DEV) {
    return [];
  }

  return [
    {
      path: "/tests/editor",
      name: "tests.editor",
      component: () => import("@/pages/TestPages/EditorPage.vue"),
      props: true,
      meta: { requireAuth: false },
    },
    {
      path: "/tests/tts",
      name: "tests.tts",
      component: () => import("@/pages/TestPages/TTSPage.vue"),
      props: true,
      meta: { requireAuth: false },
    },
    {
      path: "/tests/embed",
      name: "tests.embed",
      component: () => import("@/pages/TestPages/EmbedPage.vue"),
      props: true,
      meta: { requireAuth: false },
    },
  ];
}

const router = createRouter({
  history: createWebHistory(),
  scrollBehavior(to, from, savedPosition) {
    // always scroll to top
    return { top: 0 };
  },
  routes: [
    {
      path: "/",
      name: "home",
      component: HomePage,
      meta: {
        requireAuth: false,
      },
    },
    {
      path: "/decks",
      name: "decks.index",
      component: () => import("../pages/Decks/DeckIndexPage"),
    },
    {
      path: "/community/decks",
      name: "community.decks.index",
      component: () =>
        import("@/pages/CommunityDecks/CommunityDecksIndexPage.vue"),
    },
    {
      path: "/community/decks/:deckId",
      name: "community.decks.show",
      component: () =>
        import("@/pages/CommunityDecks/CommunityDeckShowPage.vue"),
      props: (route) => ({
        deckId: Number(route.params.deckId),
      }),
    },
    {
      path: "/decks/:deckId",
      name: "decks.show",
      component: () => import("../pages/Decks/DeckShowPage/DeckShowPage.vue"),
      props: (route) => ({
        deckId: Number(route.params.deckId),
      }),
    },
    {
      path: "/decks/:deckId/reports/summary",
      name: "decks.reports.summary",
      component: () =>
        import(
          "../pages/Decks/DeckSummaryReportPage/DeckSummaryReportPage.vue"
        ),
      props: (route) => ({
        deckId: Number(route.params.deckId),
      }),
    },
    {
      path: "/decks/create",
      name: "decks.create",
      component: () => import("../pages/Decks/CreateOrEditDeckPage.vue"),
      props: () => ({
        deckId: null,
      }),
    },
    {
      path: "/decks/:deckId/edit",
      name: "decks.edit",
      component: () => import("../pages/Decks/CreateOrEditDeckPage.vue"),
      props: (route) => ({
        deckId: Number(route.params.deckId),
      }),
    },
    {
      path: "/decks/:deckId/import",
      name: "decks.import",
      component: () => import("../pages/Decks/ImportDeckCardsPage.vue"),
      props: (route) => ({
        deckId: Number(route.params.deckId),
      }),
    },
    {
      path: "/decks/:deckId/clone",
      name: "decks.clone",
      component: () => import("@/pages/Decks/CloneDeckPage.vue"),
      props: (route) => ({
        deckId: Number(route.params.deckId),
      }),
    },
    {
      path: "/decks/:deckId/share",
      name: "decks.share",
      component: () => import("../pages/Decks/ShareDeckPage.vue"),
      props: (route) => ({
        deckId: Number(route.params.deckId),
      }),
    },
    {
      path: "/decks/:deckId/invite",
      name: "decks.invite",
      component: () => import("../pages/Decks/InviteToDeckPage.vue"),
      props: (route) => ({
        deckId: Number(route.params.deckId),
        url: route.query.url,
      }),
    },
    {
      path: "/profile",
      name: "profile",
      component: () => import("../pages/ProfilePage.vue"),
    },
    {
      path: "/decks/:deckId/cards/create",
      name: "cards.create",
      component: () => import("../pages/Cards/CreateOrEditCardPage.vue"),
      props: (route) => ({
        deckId: Number(route.params.deckId),
      }),
    },
    {
      path: "/decks/:deckId/cards/:cardId/edit",
      name: "cards.edit",
      component: () => import("../pages/Cards/CreateOrEditCardPage.vue"),
      props: (route) => ({
        deckId: Number(route.params.deckId),
        cardId: Number(route.params.cardId),
      }),
    },
    {
      path: "/decks/:deckId/practice",
      name: "decks.practice",
      component: () =>
        import("../pages/Activities/PracticeDeckPage/PracticeDeckPage.vue"),
      props: (route) => ({
        deckId: Number(route.params.deckId),
      }),
    },
    {
      path: "/decks/:deckId/practice/embed",
      name: "decks.practice.embed",
      component: () =>
        import("@/pages/Activities/PracticeDeckPage/PracticeDeckEmbedPage.vue"),
      props: (route) => ({
        deckId: Number(route.params.deckId),
      }),
    },
    {
      path: "/decks/:deckId/quiz",
      name: "decks.quiz",
      component: () =>
        import("@/pages/Activities/QuizDeckPage/QuizDeckPage.vue"),
      props: (route) => ({
        deckId: Number(route.params.deckId),
      }),
    },
    {
      path: "/decks/:deckId/quiz/embed",
      name: "decks.quiz.embed",
      component: () =>
        import("@/pages/Activities/QuizDeckPage/QuizDeckEmbedPage.vue"),
      props: (route) => ({
        deckId: Number(route.params.deckId),
      }),
    },
    {
      path: "/decks/:deckId/games/matching",
      name: "decks.games.matching",
      component: () =>
        import("@/pages/Activities/MatchingGamePage/MatchingGamePage.vue"),
      props: (route) => ({
        deckId: Number(route.params.deckId),
      }),
    },
    {
      path: "/decks/:deckId/games/matching/embed",
      name: "decks.games.matching.embed",
      component: () =>
        import("@/pages/Activities/MatchingGamePage/MatchingGameEmbedPage.vue"),
      props: (route) => ({
        deckId: Number(route.params.deckId),
      }),
    },
    {
      path: "/decks/:deckId/practice/summary",
      name: "decks.practice.summary",
      component: () => import("@/pages/Decks/PracticeSummaryPage.vue"),
      props: (route) => ({
        deckId: Number(route.params.deckId),
      }),
    },
    {
      path: "/admin",
      name: "admin",
      component: () => import("@/pages/AdminPage.vue"),
    },
    ...includeDevRoutesIfDev(),

    {
      path: "/errors/403",
      name: "errors.403",
      component: () => import("../pages/Errors/403Page.vue"),
    },

    // catch all 404
    {
      path: "/:pathMatch(.*)*",
      name: "error.404",
      component: () => import("../pages/Errors/404Page.vue"),
    },
  ],
});

router.beforeEach(async (to, from, next) => {
  const queryClient = useQueryClient();

  // unless explicitly set to false, require auth
  const isAuthRequired = to.meta?.requireAuth ?? true;
  if (!isAuthRequired) {
    return next();
  }

  // Use queryClient to fetch auth status
  // we can't use `useQuery` here because it needs
  // to be called inside of a component's setup function
  // also: we use ensureQueryData rather than fetchData so
  // that we don't re-fetch if the data is already in cache
  const isAuthenticated = await queryClient.ensureQueryData({
    queryKey: [PROFILE_QUERY_KEY],
    queryFn: async () => {
      try {
        const user = await api.getCurrentUser({ skipErrorNotifications: true });
        return user;
      } catch (error) {
        console.error("Route guard: error getting current user", error);
        return null;
      }
    },
    staleTime: 5 * 60 * 1000,
    revalidateIfStale: true,
  });

  if (!isAuthenticated) {
    window.location.href = to.fullPath;
  }

  next();
});

export default router;
