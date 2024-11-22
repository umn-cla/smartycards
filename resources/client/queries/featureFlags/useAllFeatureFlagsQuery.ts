import { useQuery } from "@tanstack/vue-query";
import * as api from "@/api";

export function useAllFeatureFlagsQuery() {
  return useQuery({
    queryKey: ["featureFlags"],
    queryFn: api.getFeatureFlags,
  });
}
