import { useQuery } from "@tanstack/vue-query";
import * as api from "@/api";
import { ACTIVITY_TYPES_QUERY_KEY } from "../queryKeys";

export function useActivityTypesQuery() {
  return useQuery({
    queryKey: [ACTIVITY_TYPES_QUERY_KEY],
    queryFn: api.getActivityTypes,
  });
}
