import type { ApiEpisode } from '@/types/api/api';
import type { Episode, Segment } from '@/types/api/graphql';
import React, { useContext, useEffect, useState } from 'react';
import { ContributorBadge } from '@/components/ContributorBadge';
import { PlayButton } from '@/components/PlayButton';
import { Badge } from '@/components/ui/badge';
import { Skeleton } from '@/components/ui/skeleton';
import { TableCell, TableRow } from '@/components/ui/table';
import { AppContext } from '@/lib/contexts/AppContext';
import { cn, formatDuration, generateAudioUrl } from '@/lib/utils';
import { ArrowBigDown, ArrowBigRight, CheckCircle } from 'lucide-react';

type ImportEpisodeRowProps = {
  importAs: 'episode',
  onImportDataChange?(data: Episode): void
}

type ImportSegmentRowProps = {
  importAs: 'segment',
  onImportDataChange?(data: Segment): void
}

type ImportItemRowProps = {
  data?: ApiEpisode,
  selected?: boolean,
  selectInputComponent?: React.JSX.Element,
} & (ImportEpisodeRowProps | ImportSegmentRowProps);

function parseApiEpisode(episode: ApiEpisode) {
  if (!episode) return null;

  return {
    guid: episode.guid,
    title: episode.title,
    terms: episode.categories?.map((category) => ({
      name: category.name,
      taxonomy: category.existingTerms?.find((term) => term.taxonomy.name === 'country' )?.taxonomy
    })),
    contributors: episode.author && [
      episode.author
    ],
    filename: episode.enclosure.href.split('/').pop(),
    duration: formatDuration(episode.enclosure.duration),
    audioUrl: generateAudioUrl(episode.enclosure.href)
  };
}

export function ImportItemRow({ data, importAs, selectInputComponent, selected }: ImportItemRowProps) {
  const { playAudio, playingAudioUrl } = useContext(AppContext);
  const [rowData, setRowData] = useState(parseApiEpisode(data));
  const { post, enclosure, wasImported } = data || {};
  const hasPost = !!post;
  const existingAudioMatches = hasPost && !!(post.audio?.url === enclosure?.href);
  const completed = wasImported || existingAudioMatches;
  const fadeOutRow = !(wasImported || existingAudioMatches) && !selected;
  const hilightUpdatedRow = hasPost && !existingAudioMatches && selected;

  function StatusIconOrInput() {
    if (completed) {
      return <CheckCircle className='text-lime-500' />
    }
    return selectInputComponent;
  }

  function Filename() {
    if (!existingAudioMatches && post) {
      const postFilename = post.audio?.url?.split('/').pop();
      const importFilename = enclosure.href.split('/').pop();

      return (
        <div className='flex gap-1'>
          {postFilename ? (
            <span className='opacity-50'>{postFilename}</span>
          ) : (
            <Badge variant='secondary' className='whitespace-nowrap'>No Audio</Badge>
          )}
          <ArrowBigRight className='text-orange-400' />
          <span>{importFilename}</span>
        </div>
      )
    }
    return filename;
  }

  if (!rowData) return (
    <TableRow>
      <TableCell className='ps-6 pr-0'>
        <Skeleton className={cn('h-4 w-4', { 'rounded-full': importAs === 'episode'})} />
      </TableCell>
      <TableCell>
        <Skeleton className='h-4 w-[50ch] rounded-full' />
      </TableCell>
      <TableCell>
        <ContributorBadge />
      </TableCell>
      <TableCell>
        <Skeleton className='h-4 w-[12ch] rounded-full' />
      </TableCell>
      <TableCell className='text-center'>
        <Skeleton className='h-4 w-[6ch] rounded-full' />
      </TableCell>
      <TableCell className='pe-6'>
        <Skeleton className='h-[36px] w-[36px] rounded-full' />
      </TableCell>
    </TableRow>
  );

  const {
    guid,
    title,
    terms,
    contributors,
    filename,
    duration,
    audioUrl
  } = rowData;

  return (
    <TableRow className={cn({ 'opacity-40': fadeOutRow, 'bg-orange-100/50 hover:bg-orange-100': hilightUpdatedRow })}>
      <TableCell className='ps-6 leading-none'>
        <StatusIconOrInput />
      </TableCell>
      <TableCell>
        <div className='inline-grid content-start gap-2 text-wrap-balance'>
          <div className='font-bold'>{title}</div>
          {terms && (
            <div className='inline-flex flex-wrap content-start items-center gap-2'>
              {terms.filter((term) => !!term.taxonomy).map((term) => (
                <Badge variant='secondary' key={term.name}>{term.name} ({term.taxonomy.label})</Badge>
              ))}
              <Badge variant='outline'>{terms.filter((term) => !term.taxonomy).length} Ignored</Badge>
            </div>
          )}
        </div>
      </TableCell>
      <TableCell>
        <div className='inline-flex flex-wrap gap-2'>
          {contributors ? contributors.map((contributor) => (
            <ContributorBadge data={contributor} key={contributor.name} />
          )) : (
            <Badge variant='secondary' className='whitespace-nowrap'>No Contributors</Badge>
          )}
        </div>
      </TableCell>
      <TableCell>
        <Filename />
      </TableCell>
      <TableCell className='text-center'>{duration}</TableCell>
      <TableCell className='pe-6'>
        <PlayButton onClick={() => { playAudio(audioUrl) }} playing={playingAudioUrl === audioUrl} />
      </TableCell>
    </TableRow>
  );
}
