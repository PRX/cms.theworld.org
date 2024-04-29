/**
 * Define App State Types.
 */

import { DocumentNode } from "@apollo/client";

export type AppState = {
  data?: object,
  revisionId?: string,
  query?: DocumentNode,
  previewSource?: Window
};

type Action = {
  type: string
}
type ActionWithPayload<T> = Action & {
  payload: T
}
export type AppAction<T = void> = T extends void ? Action : ActionWithPayload<T>;
