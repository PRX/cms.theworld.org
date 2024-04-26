/**
 * Define App Context.
 */

import type { AppState } from "@/types/state/app";
import { createContext } from "react";


export type AppContextValue = {
  state: AppState
}

export const AppContext = createContext<AppContextValue>(null);
