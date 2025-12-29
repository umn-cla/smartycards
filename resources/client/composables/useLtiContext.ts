import { computed } from "vue";
import { useRoute } from "vue-router";

export type LaunchType = "deep_link" | "resource" | "submission_review";

/**
 * for detecting and working with LTI launch contexts
 */
export function useLtiContext() {
  const route = useRoute();

  /**
   * The LTI launch ID from the current route
   */
  const launchId = computed(() => {
    const id = route.query.launch_id;
    return typeof id === "string" ? id : null;
  });

  /**
   * The type of LTI launch (deep_link, resource, or submission_review)
   * Returns null if not in an LTI context
   */
  const launchType = computed((): LaunchType | null => {
    const type = route.query.launch_type;
    if (
      type === "deep_link" ||
      type === "resource" ||
      type === "submission_review"
    ) {
      return type;
    }
    return null;
  });

  const isLtiLaunch = computed(() => !!launchId.value);

  const isDeepLinkLaunch = computed(() => launchType.value === "deep_link");

  const isResourceLaunch = computed(() => launchType.value === "resource");

  const isSubmissionReviewLaunch = computed(
    () => launchType.value === "submission_review",
  );

  return {
    /** The LTI launch ID for the current session */
    launchId,

    /** The type of LTI launch ('deep_link' | 'resource' | 'submission_review' | null) */
    launchType,

    /** True if in any LTI launch context */
    isLtiLaunch,

    /** True if in deep link launch (instructor configuring) */
    isDeepLinkLaunch,

    /** True if in resource launch (student/instructor in activity) */
    isResourceLaunch,

    /** True if in submission review launch (instructor reviewing) */
    isSubmissionReviewLaunch,
  };
}
