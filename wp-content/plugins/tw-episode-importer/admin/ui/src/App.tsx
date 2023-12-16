import type { ApiTaxonomies } from '@/types/api/api';
import type { KeyboardEventWithTarget } from '@/types/dom/events';
import { appStages, type AppAction, type AppState, type AppData } from '@/types/state/app';
import React, { useCallback, useEffect, useLayoutEffect, useReducer, useRef, useState } from 'react';
import axios from 'axios';
import { SelectingScreen } from '@/components/SelectingScreen';
import { ImportScreen } from '@/components/ImportScreen';
import { Toaster } from '@/components/ui/toaster';
import { AppContext, AppContextValue } from '@/lib/contexts/AppContext';
import { generateAudioUrl } from '@/lib/utils';

const initialAppState = {
  stage: 'selecting',
  publishDate: new Date()
} as AppState;

function appStateReducer(state: AppState, action: AppAction) {
  const { data } = state;

  switch (action.type) {
    case 'NEXT_STAGE':
      return {
        ...state,
        stage: appStages.at(appStages.findIndex((s) => s === state.stage) + 1)
      }

    case 'SET_DATA':
      return {
        ...state,
        data: (action as AppAction<AppData>).payload
      }

    case 'UPDATE_DATA':
      return {
        ...state,
        data: {
          ...data,
          ...(action as AppAction<Partial<AppData>>).payload
        }
      }

    default:
      return state;
  }
}

async function getAppData() {
  const apiUrlBase = window?.appLocalizer.apiUrl;
  const taxonomiesApiUrl = new URL('taxonomies', apiUrlBase);

  const [taxonomies] = await Promise.all([
    axios.get<ApiTaxonomies>(taxonomiesApiUrl.toString()).then((res) => res.status === 200 ? res.data : null),
  ]);

  return {
    taxonomies
  } as AppData;
}

function App() {
  const audioElm = useRef<HTMLAudioElement>(new Audio());
  const [audioUrl, setAudioUrl] = useState<string>();
  const [playing, setPlaying] = useState(false);
  const [state, dispatch] = useReducer(appStateReducer, initialAppState);
  const { stage } = state;
  const playAudio = useCallback((url: string) => {
    if (url !== audioUrl) {
      setPlaying(true);
      setAudioUrl(url);
    } else {
      togglePlayPause();
    }
  }, [audioUrl])
  const contextValue = {
    state,
    setAppData,
    updateAppData,
    nextStage,
    audioElm: audioElm.current,
    playAudio,
    playing,
    playingAudioUrl: audioUrl
  } as AppContextValue;

  console.log('App State', state);

  function setAppData(data: AppData) {
    dispatch({ type: 'SET_DATA', payload: data} as AppAction<AppData>);
  }

  function updateAppData(data: Partial<AppData>) {
    dispatch({ type: 'UPDATE_DATA', payload: data} as AppAction<Partial<AppData>>);
  }

  function nextStage() {
    dispatch({type: 'NEXT_STAGE'});
    window.scrollTo({ top: 0 });
  }

  function togglePlayPause() {
    setPlaying((isPlaying) => {
      return !isPlaying
    });
  };

  function loadAudio(src: string, isPlaying: boolean) {
    console.log('loading audio...', src, isPlaying);

    if (audioElm.current && !audioElm.current?.src.startsWith(src)) {
      audioElm.current.preload = isPlaying ? 'auto' : 'none';
      audioElm.current.src = src ? generateAudioUrl(src) : null;
    }
  };

  function startPlaying() {
    if (!audioElm.current?.src) return;

    audioElm.current
      ?.play()
      .catch((e) => {
        // eslint-disable-next-line no-console
        // console.error(e);
      });
  }

  const pauseAudio = useCallback(() => {
    audioElm.current?.pause();
  }, []);

  const handlePlay = useCallback(() => {
    if (!playing) {
      setPlaying(true);
    }
  }, [playing]);

  const handlePause = useCallback(() => {
    if (audioElm.current && !audioElm.current.ended) {
      setPlaying(false);
    }
  }, []);

  const handleLoadedMetadata = useCallback(() => {
    console.log('handleLoadedMetadata', playing);

    if (playing) {
      startPlaying();
    }
  }, [playing]);

  const handleEnded = useCallback(() => {
    setAudioUrl(null);
  }, [setAudioUrl]);

  const handleHotkey = useCallback(
    (event: KeyboardEventWithTarget) => {
      const key = event.code || event.key;
      const hasModifier =
        event.altKey || event.shiftKey || event.ctrlKey || event.metaKey;
      const fromInput = ['INPUT', 'TEXTAREA'].includes(event.target.nodeName);

      // Bail if modifier key is pressed to allow browser shortcuts to function,
      // or event originated from a form input.
      if (hasModifier || fromInput) return;

      switch (key) {
        case 'Space':
          // Only toggle playback when space key is not used on a button.
          if (event.target.nodeName !== 'BUTTON') {
            togglePlayPause();
          }
          break;
        case 'KeyK':
          togglePlayPause();
          break;
        default:
          break;
      }
    },
    []
  );

  useEffect(() => {
    (async () => {
      const data = await getAppData();
      setAppData(data);
    })()
  }, [])

  useEffect(() => {
    // Setup event handlers on audio element.
    audioElm.current?.addEventListener('play', handlePlay);
    audioElm.current?.addEventListener('pause', handlePause);
    audioElm.current?.addEventListener('loadedmetadata', handleLoadedMetadata);
    audioElm.current?.addEventListener('ended', handleEnded);

    window.addEventListener('keydown', handleHotkey);

    return () => {
      // Cleanup event handlers between dependency changes.
      audioElm.current?.removeEventListener('play', handlePlay);
      audioElm.current?.removeEventListener('pause', handlePause);
      audioElm.current?.removeEventListener(
        'loadedmetadata',
        handleLoadedMetadata
      );
      audioElm.current?.removeEventListener('ended', handleEnded);

      window.removeEventListener('keydown', handleHotkey);
    };
  }, [
    handleEnded,
    handleHotkey,
    handleLoadedMetadata,
    handlePause,
    handlePlay
  ]);

  /**
   * Have to use `useLayoutEffect` so Safari can understand the `startPlay` call
   * is a result of a user interaction. `useEffect` seems to disconnect that inference.
   * See https://lukecod.es/2020/08/27/ios-cant-play-youtube-via-react-useeffect/
   * Solution was for video playback, but same issue seems to apply to audio.
   */
  if (typeof window !== 'undefined') {
    // eslint-disable-next-line react-hooks/rules-of-hooks
    useLayoutEffect(() => {
      if (!audioElm.current) return;

      if (!playing) {
        pauseAudio();
      } else {
        startPlaying();
      }
    }, [pauseAudio, playing, startPlaying]);
  }

  useEffect(() => {
    loadAudio(audioUrl, playing);
  }, [audioUrl, playing]);

  function renderStage() {
    switch (stage) {
      case 'selecting':
        return (
          <SelectingScreen />
        );

      case 'importing':
        return (
          <ImportScreen />
        );

        default:
          return null;
    }
  }

  return (
    <div className='p-4'>
      <h2 className='text-4xl font-bold mb-6'>Episode Importer</h2>
      <AppContext.Provider value={contextValue}>
        {renderStage()}
        <Toaster />
      </AppContext.Provider>
    </div>
  )
}
export default App;
