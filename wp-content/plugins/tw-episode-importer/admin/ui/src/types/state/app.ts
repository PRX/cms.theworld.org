/**
 * Define App State Types.
 */

import { Episode, Segment } from "../api/graphql";

export const appStages = ['selecting', 'importing', 'summary'] as const;
export type AppStage = (typeof appStages)[number];

export type AppData = {
  episodes: Episode[],
  segments: Segment[]
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
