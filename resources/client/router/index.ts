import { createRouter, createWebHistory } from "vue-router";
import HomePage from "../pages/HomePage/HomePage.vue";
import { useQueryClient } from "@tanstack/vue-query";
import * as api from "@/api";
import { PROFILE_QUERY_KEY } from "@/queries/queryKeys";

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
    },
    {
      path: "/auth/login",
      name: "auth.login",
      component: () => import("../pages/Auth/LoginPage.vue"),
    },
    {
      path: "/auth/logout",
      name: "auth.logout",
      component: () => import("../pages/Auth/LogoutPage.vue"),
    },
    {
      path: "/auth/callback",
      name: "auth.callback",
      redirect: "/decks",
    },
    {
      path: "/decks",
      name: "decks.index",
      component: () => import("../pages/Decks/DeckIndexPage"),
      meta: {
        requireAuth: true,
      },
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
      path: "/decks/create",
      name: "decks.create",
      component: () => import("../pages/Decks/CreateDeckPage.vue"),
    },
    {
      path: "/decks/:deckId/edit",
      name: "decks.edit",
      component: () => import("../pages/Decks/EditDeckPage.vue"),
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
      component: () => import("../pages/Decks/PracticeDeckPage.vue"),
      props: (route) => ({
        deckId: Number(route.params.deckId),
      }),
    },
    {
      path: "/decks/:deckId/quiz",
      name: "decks.quiz",
      component: () => import("../pages/Decks/QuizDeckPage.vue"),
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
    {
      path: "/test/editor",
      name: "test.editor",
      component: () => import("@/pages/Test/EditorPage.vue"),
      props: true,
    },
    // catch all 404
    {
      path: "/:pathMatch(.*)*",
      name: "not-found",
      component: () => import("../pages/NotFoundPage.vue"),
    },
  ],
});

router.beforeEach(async (to, from, next) => {
  const queryClient = useQueryClient();
  if (!to.meta?.requireAuth) {
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
    // Redirect to login if not authenticated
    return next({
      name: "auth.login",
      query: { redirect: to.fullPath },
    });
  }

  next();
});

export default router;
