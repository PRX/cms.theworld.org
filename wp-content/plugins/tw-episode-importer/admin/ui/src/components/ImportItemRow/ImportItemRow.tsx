import type { ApiAudio, ApiEpisode, ApiTerm } from '@/types/api/api';
import type { ItemRow, ItemRowTerm } from '@/types/state/itemRow';
import React, { useContext, useEffect, useState } from 'react';
import { ContributorBadge } from '@/components/ContributorBadge';
import { PlayButton } from '@/components/PlayButton';
import { Badge } from '@/components/ui/badge';
import { Skeleton } from '@/components/ui/skeleton';
import { Table, TableBody, TableCell, TableHead, TableHeader, TableRow } from '@/components/ui/table';
import { AppContext } from '@/lib/contexts/AppContext';
import { cn, formatDuration } from '@/lib/utils';
import { ArrowBigRight, CheckCircle, Edit, ExternalLink } from 'lucide-react';
import { Sheet, SheetClose, SheetContent, SheetDescription, SheetFooter, SheetHeader, SheetTitle, SheetTrigger } from '@/components/ui/sheet';
import { Button } from '@/components/ui/button';
import { Select, SelectContent, SelectItem, SelectTrigger, SelectValue } from '@/components/ui/select';

type ImportItemRowProps = {
  data?: ApiEpisode,
  rowData?: ItemRow,
  importAs?: 'episode' | 'segment',
  selected?: boolean,
  selectInputComponent?: React.JSX.Element,
  onImportDataChange?(data: ItemRow): void
};

function parseApiEpisode(episode: ApiEpisode): ItemRow {
  if (!episode) return null;

  return {
    guid: episode.guid,
    title: episode.title,
    terms: episode.categories?.map(({ name, existingTerms }) => {
      const selectedTerm = existingTerms?.find((term) => term.taxonomy.name === 'country' );
      return {
        name,
        ...(selectedTerm && {
          id: selectedTerm.id,
          taxonomy: selectedTerm.taxonomy
        })
      }
    }),
    contributors: episode.author,
    filename: episode.enclosure.href.split('/').pop(),
    duration: formatDuration(episode.enclosure.duration),
    audioUrl: episode.enclosure.href,
    data: episode
  };
}

type AudioEditLinkProps = {
  audio: ApiAudio
};

function AudioEditLink({ audio }: AudioEditLinkProps) {
  const editLink = audio?.editLink;
  const audioFilename = audio?.url?.split('/').pop();

  return (
    <a className='inline-flex gap-2 text-primary' href={editLink} target={`edit:${audio.databaseId}`} title={audio.url}>{audioFilename} <ExternalLink size={16} /></a>
  )
}

export function ImportItemRow({ data, rowData: rd, importAs, selectInputComponent, selected, onImportDataChange }: ImportItemRowProps) {
  const { state } = useContext(AppContext);
  const [rowData, setRowData] = useState(rd || parseApiEpisode(data));
  const { existingPosts, existingPost, existingAudio, enclosure, wasImported, categories } = rowData?.data || data || {};
  const { data: appData } = state || {};
  const { taxonomies } = appData || {};
  const existingTermsMap = new Map<string, ApiTerm[]>();
  const hasExisitingPosts = !!existingPosts;
  const hasExisitingPost = !!existingPost;
  const hasExistingAudio = !!existingAudio;
  const existingAudioMatches = hasExistingAudio && existingAudio.url === enclosure?.href;
  const completed = hasExisitingPost && existingAudioMatches;
  const fadeOutRow = !(completed) && !selected;
  const hilightUpdatedRow = hasExisitingPost && !existingAudioMatches && selected;

  categories?.map(({ name, existingTerms }) => {
    if (existingTerms) {
      existingTermsMap.set(name, existingTerms);
    }
  });

  function updateTerm(name: string, newTerm: ItemRowTerm) {
    if (!rowData) return;

    const updatedRowData = {
      ...rowData,
      terms: terms.map((term) => term.name === name ? newTerm : term)
    };

    setRowData(updatedRowData);
  }

  useEffect(() => {
    if (onImportDataChange) {
      onImportDataChange(rowData);
    }
  }, [rowData]);

  useEffect(() => {
    if (rd) {
      setRowData(rd);
    }
  }, [rd]);

  function StatusIconOrInput() {
    if (completed) {
      return <CheckCircle className='text-lime-500' />
    }
    return selectInputComponent;
  }

  function Filename() {
    if (hasExistingAudio && !existingAudioMatches) {
      return (
        <div className='flex gap-1'>
          {existingAudio?.url ? (
            <AudioEditLink audio={existingAudio} />
          ) : (
            <>
              <Badge variant='secondary' className='whitespace-nowrap capitalize'>No Audio</Badge>
            </>
          )}
          <ArrowBigRight className='text-orange-400' />
          <span title={audioUrl}>{filename}</span>
        </div>
      )
    }

    if (hasExistingAudio) {
      return <AudioEditLink audio={existingAudio} key={existingAudio.databaseId} />
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
  const ignoredTermsCount = terms?.filter((term) => !term.taxonomy).length;

  return (
    <TableRow className={cn({ 'opacity-40': fadeOutRow, 'bg-orange-100/50 hover:bg-orange-100': hilightUpdatedRow })}>
      <TableCell className='ps-6 leading-none'>
        <StatusIconOrInput />
      </TableCell>
      <TableCell>
        <div className='inline-grid content-start gap-2'>
          <div className='font-bold text-wrap-balance'>{title}</div>
          {(terms || existingPosts) && (
            <div className='inline-flex flex-wrap content-start items-center gap-2'>
              {existingPosts?.map(({ databaseId, editLink, type }) => (
                <a href={editLink} target={`edit:${databaseId}`} key={databaseId}><Badge className='capitalize inline-flex gap-2'>{type} <ExternalLink className='inline-block' size={16} /></Badge></a>
              ))}
              {terms?.filter((term) => !!term.taxonomy).map((term) => (
                <Badge variant='secondary' className='capitalize' key={term.name}>{term.name} ({term.taxonomy.label})</Badge>
              ))}
              {!!ignoredTermsCount && (
                  <Badge variant='outline'>
                    {terms.filter((term) => !term.taxonomy).length} Ignored
                  </Badge>
              )}
              {!hasExisitingPost && !!onImportDataChange && (
                <Sheet>
                  <SheetTrigger asChild>
                    <Button variant='ghost' size='icon' className='rounded-full hover:bg-secondary'>
                      <Edit size={16} />
                    </Button>
                  </SheetTrigger>
                  <SheetContent className='top-8 bottom-0 h-auto flex flex-col gap-0 p-0'>
                    <SheetHeader className='p-4 border-b'>
                      <SheetTitle className='me-12'>Edit Terms For "{title}"</SheetTitle>
                      <SheetDescription>Select a taxonomy for each term. Set to "Ignore" if the term should not be imported.</SheetDescription>
                    </SheetHeader>
                    <div className='p-4 overflow-y-auto'>
                      <Table className='border'>
                        <TableHeader className='sticky top-0'>
                          <TableRow>
                            <TableHead>Term</TableHead>
                            <TableHead>Taxonomy</TableHead>
                          </TableRow>
                        </TableHeader>
                        <TableBody>
                          {terms.map((term, index) => {
                            const { name, id, taxonomy } = term;
                            const selectedTaxonomy = taxonomy ? [taxonomy.name, id].filter((v) => !!v).join(':') : null;
                            const existingTermKeys = new Map<string, string>();
                            const existingTerms = existingTermsMap.get(name) || [];

                            existingTerms.forEach(({ id, taxonomy }) => {
                              existingTermKeys.set(taxonomy.name, `${taxonomy.name}:${id}`)
                            });

                            const options = Object.values(taxonomies).map(({name, label}) => {
                              const existingTermKey = existingTermKeys.get(name);
                              const value = existingTermKey || name;

                              return {
                                label,
                                value
                              };
                            })

                            function handleValueChange(value: string) {
                              if (!value) {
                                updateTerm(name, { name });
                                return;
                              }

                              const [newTaxonomy, newId] = value.split(':');

                              updateTerm(name, {
                                name,
                                id: newId && parseInt(newId, 10),
                                taxonomy: taxonomies[newTaxonomy]
                              })
                            }

                            return (
                              <TableRow key={name}>
                                <TableCell>{name}</TableCell>
                                <TableCell>
                                  <Select value={selectedTaxonomy} onValueChange={handleValueChange}>
                                    <SelectTrigger>
                                      <SelectValue />
                                    </SelectTrigger>
                                    <SelectContent>
                                      <SelectItem value={null}>(Ignored)</SelectItem>
                                      {options.map(({label, value}) => (
                                        <SelectItem value={value} key={value}>{label}</SelectItem>
                                      ))}
                                    </SelectContent>
                                  </Select>
                                </TableCell>
                              </TableRow>
                            )
                          })}
                        </TableBody>
                      </Table>
                    </div>
                    <SheetFooter className='p-4 border-t'>
                      <SheetClose asChild>
                        <Button>Done</Button>
                      </SheetClose>
                    </SheetFooter>
                  </SheetContent>
                </Sheet>
              )}
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
      <TableCell className='whitespace-nowrap'>
        <Filename />
      </TableCell>
      <TableCell className='text-center'>{duration}</TableCell>
      <TableCell className='pe-6'>
        <PlayButton audioUrl={audioUrl} />
      </TableCell>
    </TableRow>
  );
}
