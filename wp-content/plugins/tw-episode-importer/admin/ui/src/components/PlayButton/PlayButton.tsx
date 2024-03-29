import React, { useCallback, useContext, useEffect, useState } from "react";
import { Pause, Play } from "lucide-react";
import { Button } from "@/components/ui/button";
import { cn } from "@/lib/utils";
import { AppContext } from "@/lib/contexts/AppContext";


export type PlayButtonProps = {
  audioUrl: string
}

export function PlayButton({ audioUrl }: PlayButtonProps) {
  const { audioElm, playing, playAudio, playingAudioUrl } = useContext(AppContext);
  const audioIsQueued = playingAudioUrl === audioUrl;
  const audioIsPlaying = playing && audioIsQueued;
  const [progress, setProgress] = useState( audioIsQueued ? calculateProgress() : 0);
  const percent = 100 * progress;
  const showProgress = audioIsPlaying || !!progress && audioIsQueued && !playing;
  const className = cn(
    'rounded-full',
    {
      'bg-orange-500 hover:bg-orange-400': audioIsPlaying,
      'text-primary hover:bg-primary hover:text-primary-foreground': !audioIsQueued,
      'border-2 border-white': showProgress
    }
  );
  const progressClassName = cn(
    'p-1 rounded-full',
    {
      'bg-orange-500': audioIsPlaying,
      'bg-primary': !!progress && audioIsQueued && !playing
    }
  );
  const progressStyles = {
    backgroundImage: showProgress ? `conic-gradient(from 0deg at 50% 50%, #FFFFFF00 ${percent}%, #FFFFFF88 ${percent}%, #FFFFFF88)` : 'none'
  };

  function calculateProgress(seconds?: number) {
    const { currentTime: ct = 0, duration: d } = audioElm || {};
    const updatedPlayed = seconds || seconds === 0 ? seconds : ct;
    const newProgress = d ? updatedPlayed / d : 0;

    return newProgress;
  }


  /**
   * Update player progress visuals.
   */
  const updateProgress = useCallback(
    (seconds?: number) => {
      if (!audioIsQueued) return;

      const newProgress = calculateProgress(seconds);

      setProgress(newProgress);
    },
    [audioElm, audioIsPlaying]
  );

  /**
   * Update when audio metadata is loaded.
   */
  const handleLoadedMetadata = useCallback(() => {
    setProgress(0);
  }, [setProgress]);

  /**
   * Update when audio ended is loaded.
   */
  const handleEnded = useCallback(() => {
    setProgress(0);
  }, [setProgress]);

  /**
   * Updated on time update.
   */
  const handleUpdate = useCallback(() => {
    updateProgress();
  }, [updateProgress]);

  function addEventListeners() {
    audioElm?.addEventListener('loadedmetadata', handleLoadedMetadata);
    audioElm?.addEventListener('timeupdate', handleUpdate);
    audioElm?.addEventListener('ended', handleEnded);
  }

  function removeEventListeners() {
    audioElm?.removeEventListener('loadedmetadata', handleLoadedMetadata);
    audioElm?.removeEventListener('timeupdate', handleUpdate);
    audioElm?.removeEventListener('ended', handleEnded);
  }

  function handleClick(evt: React.MouseEvent<HTMLButtonElement, MouseEvent>) {
    playAudio(audioUrl);
  }

  /**
   * Setup audio element event handlers.
   */
  useEffect(() => {
    if (audioIsQueued) {
      addEventListeners();

      return () => {
        removeEventListeners();
      };
    } else {
      removeEventListeners();
    }
  }, [audioElm, audioIsQueued, handleLoadedMetadata, handleUpdate, handleEnded]);

  useEffect(() => {
    const { currentTime: ct = 0 } = audioElm || {};
    updateProgress(ct);
  }, []);

  return (
    <div className={progressClassName} style={progressStyles}>
      <Button className={className} variant={audioIsQueued ? 'default' : 'ghost'} size='icon' onClick={handleClick}>
        {audioIsPlaying ? (
          <Pause className='h-4 w-4' />
        ) : (
          <Play className='h-4 w-4' />
        )}
      </Button>
    </div>
  )
}
