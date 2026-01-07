import { computed } from "vue";
import { useRoute } from "vue-router";

export type LaunchType = "deep_link" | "resource";

/**
 * Composable for detecting and working with LTI launch context
 */
export function useLtiContext() {
  const route = useRoute();

  /**
   * The LTI launch ID from the current route (deep link only)
   */
  const launchId = computed(() => {
    const id = route.query.launch_id;
    return typeof id === "string" ? id : null;
  });

  /**
   * Whether this is an LTI resource launch (from ?lti_launch=true)
   */
  const isLtiResourceLaunch = computed(() => {
    return route.query.lti_launch === "true";
  });

  /**
   * The type of LTI launch (deep_link or resource)
   * Returns null if not in an LTI context
   */
  const launchType = computed((): LaunchType | null => {
    const type = route.query.launch_type;
    if (type === "deep_link") {
      return "deep_link";
    }
    if (isLtiResourceLaunch.value) {
      return "resource";
    }
    return null;
  });

  /**
   * Whether the current page is in any LTI launch context
   */
  const isLtiLaunch = computed(
    () => !!launchId.value || isLtiResourceLaunch.value,
  );

  /**
   * Whether we're in a deep link launch (instructor selecting/configuring content)
   */
  const isDeepLinkLaunch = computed(() => launchType.value === "deep_link");

  /**
   * Whether we're in a resource launch (student/instructor accessing configured assignment)
   */
  const isResourceLaunch = computed(() => launchType.value === "resource");

  return {
    /** The LTI launch ID for the current session (deep link only) */
    launchId,

    /** The type of LTI launch ('deep_link' | 'resource' | null) */
    launchType,

    /** True if in any LTI launch context */
    isLtiLaunch,

    /** True if in deep link launch (instructor configuring) */
    isDeepLinkLaunch,

    /** True if in resource launch (student/instructor in activity) */
    isResourceLaunch,
  };
}
