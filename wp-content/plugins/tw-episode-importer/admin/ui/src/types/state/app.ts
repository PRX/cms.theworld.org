/**
 * Define App State Types.
 */

import { ApiTaxonomies } from "@/types/api/api";
import { ItemRow } from "@/types/state/itemRow";

export const appStages = ['selecting', 'importing'] as const;
export type AppStage = (typeof appStages)[number];

export type  AppImportData = {
  episode: ItemRow,
  segments: ItemRow[]
}

export type AppData = {
  importData?: AppImportData
  taxonomies: ApiTaxonomies,
};

export type AppState = {
  stage: AppStage,
  data?: AppData
};

type Action = {
  type: string
}
type ActionWithPayload<T> = Action & {
  payload: T
}
export type AppAction<T = void> = T extends void ? Action : ActionWithPayload<T>;
