/**
 * Define App Context.
 */

import type { AppData, AppState } from "@/types/state/app";
import { createContext } from "react";


export type AppContextValue = {
  state: AppState,
  setAppData(data: AppData): void,
  nextStage(): void,
  audioElm: HTMLAudioElement | null,
  playing: boolean,
  playingAudioUrl?: string,
  playAudio(url: string): void
}

export const AppContext = createContext<AppContextValue>(null);
