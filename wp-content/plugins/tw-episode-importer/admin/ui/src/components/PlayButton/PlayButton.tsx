import React from "react";
import { Pause, Play } from "lucide-react";
import { Button } from "@/components/ui/button";
import { cn } from "@/lib/utils";


export type PlayButtonProps = {
  playing: boolean,
  onClick(): void
}

export function PlayButton({ playing, onClick }: PlayButtonProps) {
  const className = cn(
    'rounded-full',
    { 'bg-orange-500 hover:bg-orange-500/90': playing },
    { 'text-primary hover:bg-primary hover:text-primary-foreground': !playing }
  );

  return (
    <Button className={className} variant={playing ? 'default' : 'ghost'} size='icon' onClick={() => { onClick && onClick() }}>
      {playing ? (
        <Pause className='h-4 w-4' />
      ) : (
        <Play className='h-4 w-4' />
      )}
    </Button>
  )
}
