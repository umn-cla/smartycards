import { createRouter, createWebHistory } from "vue-router";
import HomePage from "../pages/HomePage/HomePage.vue";
import { useQueryClient } from "@tanstack/vue-query";
import * as api from "@/api";
import { PROFILE_QUERY_KEY } from "@/queries/queryKeys";
import { toDocumentTitle } from "@/lib/documentTitle";

declare module "vue-router" {
  interface RouteMeta {
    title?: string;
  }
}

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
      meta: { requireAuth: false, title: "Test Editor" },
    },
    {
      path: "/tests/tts",
      name: "tests.tts",
      component: () => import("@/pages/TestPages/TTSPage.vue"),
      props: true,
      meta: { requireAuth: false, title: "Test TTS" },
    },
    {
      path: "/tests/embed",
      name: "tests.embed",
      component: () => import("@/pages/TestPages/EmbedPage.vue"),
      props: true,
      meta: { requireAuth: false, title: "Test Embed" },
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
      meta: { title: "Decks" },
      component: () => import("../pages/Decks/DeckIndexPage"),
    },
    {
      path: "/community/decks",
      name: "community.decks.index",
      meta: { title: "Community Decks" },
      component: () =>
        import("@/pages/CommunityDecks/CommunityDecksIndexPage.vue"),
    },
    {
      path: "/community/decks/:deckId",
      name: "community.decks.show",
      meta: { title: "Preview Deck" },
      component: () =>
        import("@/pages/CommunityDecks/CommunityDeckShowPage.vue"),
      props: (route) => ({
        deckId: Number(route.params.deckId),
      }),
    },
    {
      path: "/decks/:deckId",
      name: "decks.show",
      meta: { title: "Deck" },
      component: () => import("../pages/Decks/DeckShowPage/DeckShowPage.vue"),
      props: (route) => ({
        deckId: Number(route.params.deckId),
      }),
    },
    {
      path: "/decks/:deckId/reports/summary",
      name: "decks.reports.summary",
      meta: { title: "Summary Report" },
      component: () =>
        import(
          "../pages/Decks/DeckSummaryReportPage/DeckSummaryReportPage.vue"
        ),
      props: (route) => ({
        deckId: Number(route.params.deckId),
      }),
    },
    {
      path: "/decks/:deckId/reports/audit-history",
      name: "decks.reports.auditHistory",
      meta: { title: "Deck History" },
      component: () =>
        import(
          "../pages/Decks/DeckAuditHistoryPage/DeckAuditHistoryPage.vue"
        ),
      props: (route) => ({
        deckId: Number(route.params.deckId),
      }),
    },
    {
      path: "/decks/:deckId/assignments",
      name: "decks.assignments",
      meta: { title: "Assignments" },
      component: () =>
        import("../pages/Decks/DeckAssignmentsPage/DeckAssignmentsPage.vue"),
      props: (route) => ({
        deckId: Number(route.params.deckId),
      }),
    },
    {
      path: "/decks/create",
      name: "decks.create",
      meta: { title: "Create Deck" },
      component: () => import("../pages/Decks/CreateOrEditDeckPage.vue"),
      props: () => ({
        deckId: null,
      }),
    },
    {
      path: "/decks/:deckId/edit",
      name: "decks.edit",
      meta: { title: "Edit Deck" },
      component: () => import("../pages/Decks/CreateOrEditDeckPage.vue"),
      props: (route) => ({
        deckId: Number(route.params.deckId),
      }),
    },
    {
      path: "/decks/:deckId/import",
      name: "decks.import",
      meta: { title: "Import Cards" },
      component: () => import("../pages/Decks/ImportDeckCardsPage.vue"),
      props: (route) => ({
        deckId: Number(route.params.deckId),
      }),
    },
    {
      path: "/decks/:deckId/clone",
      name: "decks.clone",
      meta: { title: "Clone Deck" },
      component: () => import("@/pages/Decks/CloneDeckPage.vue"),
      props: (route) => ({
        deckId: Number(route.params.deckId),
      }),
    },
    {
      path: "/decks/:deckId/share",
      name: "decks.share",
      meta: { title: "Share Deck" },
      component: () => import("../pages/Decks/ShareDeckPage.vue"),
      props: (route) => ({
        deckId: Number(route.params.deckId),
      }),
    },
    {
      path: "/decks/:deckId/invite",
      name: "decks.invite",
      meta: { title: "Deck Invite" },
      component: () => import("../pages/Decks/InviteToDeckPage.vue"),
      props: (route) => ({
        deckId: Number(route.params.deckId),
        url: route.query.url,
      }),
    },
    {
      path: "/profile",
      name: "profile",
      meta: { title: "Profile" },
      component: () => import("../pages/ProfilePage.vue"),
    },
    {
      path: "/decks/:deckId/cards/create",
      name: "cards.create",
      meta: { title: "Create Card" },
      component: () => import("../pages/Cards/CreateOrEditCardPage.vue"),
      props: (route) => ({
        deckId: Number(route.params.deckId),
      }),
    },
    {
      path: "/decks/:deckId/cards/:cardId/edit",
      name: "cards.edit",
      meta: { title: "Edit Card" },
      component: () => import("../pages/Cards/CreateOrEditCardPage.vue"),
      props: (route) => ({
        deckId: Number(route.params.deckId),
        cardId: Number(route.params.cardId),
      }),
    },
    {
      path: "/decks/:deckId/activities/practice",
      alias: "/decks/:deckId/practice",
      name: "decks.practice",
      meta: { title: "Practice" },
      component: () =>
        import("../pages/Activities/PracticeDeckPage/PracticeDeckPage.vue"),
      props: (route) => ({
        deckId: Number(route.params.deckId),
      }),
    },
    {
      path: "/decks/:deckId/activities/practice/embed",
      alias: "/decks/:deckId/practice/embed",
      name: "decks.practice.embed",
      meta: { title: "Practice" },
      component: () =>
        import("@/pages/Activities/PracticeDeckPage/PracticeDeckEmbedPage.vue"),
      props: (route) => ({
        deckId: Number(route.params.deckId),
      }),
    },
    {
      path: "/decks/:deckId/activities/quiz",
      alias: "/decks/:deckId/quiz",
      name: "decks.quiz",
      meta: { title: "Quiz" },
      component: () =>
        import("@/pages/Activities/QuizDeckPage/QuizDeckPage.vue"),
      props: (route) => ({
        deckId: Number(route.params.deckId),
      }),
    },
    {
      path: "/decks/:deckId/activities/quiz/embed",
      alias: "/decks/:deckId/quiz/embed",
      name: "decks.quiz.embed",
      meta: { title: "Quiz" },
      component: () =>
        import("@/pages/Activities/QuizDeckPage/QuizDeckEmbedPage.vue"),
      props: (route) => ({
        deckId: Number(route.params.deckId),
      }),
    },
    {
      path: "/decks/:deckId/activities/matching",
      alias: "/decks/:deckId/games/matching",
      name: "decks.games.matching",
      meta: { title: "Matching" },
      component: () =>
        import("@/pages/Activities/MatchingGamePage/MatchingGamePage.vue"),
      props: (route) => ({
        deckId: Number(route.params.deckId),
      }),
    },
    {
      path: "/decks/:deckId/activities/matching/embed",
      alias: "/decks/:deckId/games/matching/embed",
      name: "decks.games.matching.embed",
      meta: { title: "Matching" },
      component: () =>
        import("@/pages/Activities/MatchingGamePage/MatchingGameEmbedPage.vue"),
      props: (route) => ({
        deckId: Number(route.params.deckId),
      }),
    },
    {
      path: "/decks/:deckId/activities/practice/summary",
      alias: "/decks/:deckId/practice/summary",
      name: "decks.practice.summary",
      meta: { title: "Practice Summary" },
      component: () => import("@/pages/Decks/PracticeSummaryPage.vue"),
      props: (route) => ({
        deckId: Number(route.params.deckId),
      }),
    },

    // LTI Routes
    {
      path: "/lti/deep-link",
      name: "lti.deep_link",
      component: () => import("@/pages/Lti/LtiDeepLinkPage.vue"),
      meta: { requireAuth: false, title: "Create Assignment" },
    },

    ...includeDevRoutesIfDev(),

    {
      path: "/errors/403",
      name: "errors.403",
      meta: { title: "Forbidden" },
      component: () => import("../pages/Errors/403Page.vue"),
    },

    // catch all 404
    {
      path: "/:pathMatch(.*)*",
      name: "error.404",
      meta: { title: "Page Not Found" },
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

router.afterEach((to, _from, failure) => {
  if (failure) return;
  document.title = toDocumentTitle([to.meta.title]);
});

export default router;
