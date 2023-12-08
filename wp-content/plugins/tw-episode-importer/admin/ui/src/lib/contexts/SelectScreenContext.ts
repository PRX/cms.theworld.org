/**
 * Define Select Screen Context.
 */

import { ApiTaxonomies } from "@/types/api/api";
import type { AppData, AppState } from "@/types/state/app";
import { createContext } from "react";


export type SelectScreenContextValue = {
  taxonomies: ApiTaxonomies
}

export const SelectScreenContext = createContext<SelectScreenContextValue>(null);
