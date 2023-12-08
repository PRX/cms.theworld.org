import type { ApiData, ApiEpisode, ApiTaxonomies } from '@/types/api/api';
import React, { useContext, useEffect, useRef, useState } from 'react';
import axios from 'axios';
import { ArrowRight, ArrowRightToLine, FileQuestion, Loader2 } from 'lucide-react';
import { ContributorBadge } from '@/components/ContributorBadge';
import { DatePicker } from '@/components/DatePicker';
import { PlayButton } from '@/components/PlayButton';
import { Badge } from '@/components/ui/badge';
import { Button } from '@/components/ui/button';
import { Card, CardContent, CardDescription, CardFooter, CardHeader, CardTitle } from '@/components/ui/card';
import { Checkbox } from '@/components/ui/checkbox';
import { RadioGroup, RadioGroupItem } from '@/components/ui/radio-group';
import { Skeleton } from '@/components/ui/skeleton';
import { Table, TableBody, TableCell, TableHead, TableHeader, TableRow } from '@/components/ui/table';
import { AppContext } from '@/lib/contexts/AppContext';
import { cn, formatDuration, generateAudioUrl } from '@/lib/utils';
import { Alert, AlertDescription, AlertTitle } from '@/components/ui/alert';
import { ImportItemRow, ItemRow } from '@/components/ImportItemRow';
import { isSameDay, isSameMonth } from 'date-fns';
import { useToast } from '@/components/ui/use-toast';

type TableTerm = {
  id: number,
  name: string,
  taxononomy: {
    name: string,
    label: string
  }
}

type TableEpisode = {
  guid: string,
  title: string,
  terms: TableTerm[],
}

type SelectingState = {
  episodes: TableEpisode[],
  segments: TableEpisode[],
  playing: boolean,
  loading: boolean,
  selected: {
    episode: string,
    segments: string[]
  }
}

function formatDateKey(date: Date) {
  const year = date.getFullYear();
  const month = `0${date.getMonth() + 1}`.slice(-2);
  const day = `0${date.getDate()}`.slice(-2);

  return `${year}-${month}-${day}`;
}

async function getApiData(publishDate?: Date, beforeDate?: Date) {
  const date = (publishDate || new Date());
  const params = new URLSearchParams( !beforeDate ? {
    on: formatDateKey(date)
  } : {
    after: formatDateKey(date),
    before: formatDateKey(beforeDate)
  })
  const apiUrlBase = window?.appLocalizer.apiUrl;
  const episodesApiUrl = new URL('episodes', apiUrlBase);
  const segmentsApiUrl = new URL('segments', apiUrlBase);

  episodesApiUrl.search = params.toString();
  segmentsApiUrl.search = params.toString();

  const [episodes, segments] = await Promise.all([
    axios.get<ApiEpisode[]>(episodesApiUrl.toString()).then((res) => res.status === 200 ? res.data : null),
    axios.get<ApiEpisode[]>(segmentsApiUrl.toString()).then((res) => res.status === 200 ? res.data : null),
  ]);

  return {
    episodes,
    segments
  } as ApiData;
}

export function SelectingScreen() {
  const { nextStage, playing, playingAudioUrl, audioElm } = useContext(AppContext);
  const { toast, dismiss: dismissToast } = useToast();
  const [audioPlayerToast, setAudioPlayerToast] = useState<ReturnType<typeof toast>>();
  const today = new Date();
  const maxMonthDate = new Date(today.getFullYear(), today.getMonth());
  const [queryMonthDate, setQueryMonthDate] = useState(new Date(today.getFullYear(), today.getMonth()));
  const [publishDate, setPublishDate] = useState(new Date());
  const [month, setMonth] = useState(today);
  const publishDateKey = formatDateKey(publishDate);
  const [apiData, setApiData] = useState(new Map<string, ApiData>());
  const [loading, setLoading] = useState(false);
  const importData = useRef(new Map<string, ItemRow>());
  const [importEpisodeGuid, setImportEpisodeGuid] = useState<string>();
  const [importSegmentGuids, setImportSegmentGuids] = useState(new Set<string>());
  const { episodes, segments } = apiData.get(publishDateKey) || {};
  const playingAudioUrlInView = !![...(episodes || []), ...(segments || [])].find(({ enclosure }) => playingAudioUrl === enclosure.href);
  const playingAudioUrlItemRow = [...importData.current.values()].find((itemRow) => itemRow.audioUrl === playingAudioUrl);
  const audioRemainingDuration = audioElm.duration ? audioElm.duration - audioElm.currentTime : 0;
  const dateData = [...apiData.values()].map(({ episodes, segments }) => {
    const allItems = [...episodes, ...segments];
    const hasImportableItems = !!allItems.find(({ post }) => !post);
    const hasExistingItems = !!allItems.find(({ post }) => !!post);
    const hasImportedItems = !!allItems.find(({ wasImported }) => wasImported);
    const hasUpdateableItems = !!allItems.find(({ post, enclosure }) => post?.audio && post.audio.url !== enclosure.href || !post?.audio);
    const playingAudioInView = !!allItems.find(({ enclosure }) => playingAudioUrl?.startsWith(enclosure.href));

    return {
      hasImportableItems,
      hasExistingItems,
      hasImportedItems,
      hasUpdateableItems,
      playingAudioInView,
      episodes,
      segments
    };
  })
  const importableDays = dateData
    .filter((data) => !data.hasExistingItems )
    .map((data) => new Date(data.episodes[0].datePublished));
  const importableClassNames = 'border-2 border-primary';
  const existsDays = dateData
    .filter((data) => data.hasExistingItems && !data.hasImportableItems)
    .map((data) => new Date(data.episodes[0].datePublished));
  const existsClasNames = 'border-2 border-lime-500';
  const importedDays = dateData
    .filter((data) => data.hasImportedItems)
    .map((data) => new Date(data.episodes[0].datePublished));
  const importedClassNames = 'bg-lime-500';
  const updatedDays = dateData
    .filter((data) => data.hasUpdateableItems)
    .map((data) => new Date(data.episodes[0].datePublished));
  const updatedClassNames = 'border-2 border-orange-400';
  const partialyImportedDays = dateData
    .filter((data) => data.hasExistingItems && data.hasImportableItems)
    .map((data) => new Date(data.episodes[0].datePublished));
  const partialyImportedClassNames = 'border-2 border-dotted border-lime-500';
  const playingAudioDays = playingAudioUrlItemRow ? [new Date(playingAudioUrlItemRow.data.datePublished)] : [];
  const playingAudioClassName = 'ring-1 ring-offset-2 ring-orange-500 !rounded-full';

  useEffect(() => {
    if (playingAudioUrlItemRow && !playingAudioUrlInView && !audioPlayerToast) {
      const { title, audioUrl, duration, data } = playingAudioUrlItemRow;
      const filename = audioUrl.split('/').pop();

      setAudioPlayerToast(toast({
        title,
        description: `${filename} | ${duration}`,
        action: <PlayButton audioUrl={audioUrl} />,
        className: 'cursor-pointer',
        onClick: (evt) => {
          if ((evt.target as Element).nodeName !== 'DIV') return;
          setPublishDate(new Date(data.datePublished));
        }
      }))
    } else if (!playingAudioUrlItemRow && audioPlayerToast || playingAudioUrlInView && audioPlayerToast) {
      dismissToast();
      setAudioPlayerToast(null);
    }
  }, [playingAudioUrlItemRow, playingAudioUrlInView, audioPlayerToast]);

  useEffect(() => {
    const dateData = apiData.get(publishDateKey);

    if (!dateData) return;

    const importEpisode = dateData.episodes?.find(({ post, enclosure }) => !post || post.audio?.url !== enclosure.href);
    if (importEpisode) {
      setImportEpisodeGuid(importEpisode.guid);
    }

    setImportSegmentGuids((guids) => {
      guids.clear();
      const importSegments = dateData.segments?.filter(({ post, enclosure }) => !post || post.audio?.url !== enclosure.href);
      importSegments?.map((segment) => guids.add(segment.guid));
      return new Set(guids)
    })
  }, [publishDate, publishDateKey, apiData]);

  useEffect(() => {
    setLoading(true);
    (async () => {
      const data = await getApiData(queryMonthDate, new Date(queryMonthDate.getFullYear(), queryMonthDate.getMonth() + 1));
      // TODO: fetch post data for audio url's.
      const tempData = new Map<string, ApiData>(apiData);

      console.log('fetched api data', data, queryMonthDate);

      data.episodes?.forEach((episode) => {
        const dateKey = formatDateKey(new Date(episode.datePublished));
        const dateData = tempData.get(dateKey) || {} as ApiData;

        dateData.episodes = dateData?.episodes || [];
        if (!dateData.episodes.find((e) => e.guid === episode.guid)) {
          dateData.episodes.push(episode);
        }

        tempData.set(dateKey, dateData);
      });

      data.segments?.forEach((segment) => {
        const dateKey = formatDateKey(new Date(segment.datePublished));
        const dateData = tempData.get(dateKey) || {} as ApiData;

        dateData.segments = dateData?.segments || [];
        if (!dateData.segments.find((s) => s.guid === segment.guid)) {
          dateData.segments.push(segment);
        }

        tempData.set(dateKey, dateData);
      });

      setApiData(tempData);
      setLoading(false);
    })()
  }, [queryMonthDate])

  function parseApiEpisode(episode: ApiEpisode) {
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

  function parseDigitsOfFilename(href: string) {
    return [...href.split('/').pop().split('.').shift().matchAll(/\d/g)].join('')
  }

  function sortByEnclosureFilename(ea: ApiEpisode, eb: ApiEpisode) {
    const a = parseDigitsOfFilename(ea.enclosure.href);
    const b = parseDigitsOfFilename(eb.enclosure.href);

    return a < b ? -1 : 1
  }

  function handleDateSelect(newDate: Date) {
    setPublishDate(newDate);
  }

  function handleMonthChange(newDate: Date) {
    setMonth(newDate);
    setQueryMonthDate(new Date(newDate.getFullYear(), newDate.getMonth()));
  }

  function handleRowChange(newData: ItemRow) {
    console.log('New row data', newData);
    importData.current.set(newData.guid, newData);
  }

  return (
    <Card>
      <CardHeader>
        <CardTitle>Select Epsiode and Segments</CardTitle>
        <CardDescription className='max-w-[120ch]'>
            Select the Dovetail Publish Date of the episode and segments you want to import. If multiple episodes were publish on the date, select which episode to import. Deselect any segments that should not be imported for the selected episode.
        </CardDescription>
      </CardHeader>
      <CardContent>

        <div className='flex gap-4 items-center'>
          <DatePicker
            disabled={loading}
            defaultMonth={publishDate}
            month={month}
            selected={publishDate}
            toMonth={maxMonthDate}
            onSelect={handleDateSelect}
            onMonthChange={handleMonthChange}
            showOutsideDays={false}
            modifiers={{
              exists: existsDays,
              imported: importedDays,
              partialyImported: partialyImportedDays,
              updated: updatedDays,
              importable: importableDays,
              playingAudio: playingAudioDays
            }}
            modifiersClassNames={{
              exists: existsClasNames,
              imported: importedClassNames,
              partialyImported: partialyImportedClassNames,
              updated: updatedClassNames,
              importable: importableClassNames,
              playingAudio: playingAudioClassName
            }}
            footer={!isSameMonth(today, month) && (
              <div className='flex justify-end pt-2'>
                <Button
                  size='sm'
                  className='flex gap-2'
                  onClick={() => {
                    setMonth(today);
                  }}
                >Most Recent <ArrowRightToLine /></Button>
              </div>
            )}
          />
          {loading && (
            <Badge variant='outline' className=' flex gap-2'>
              <Loader2 className='animate-spin text-primary' />
              Loading episodes and segments from Dovetail...
            </Badge>
          )}
        </div>

        <RadioGroup defaultValue={importEpisodeGuid} onValueChange={(guid) => setImportEpisodeGuid(guid) }>

          <Table className='mt-6 border'>
            <TableHeader>
              <TableRow>
                <TableHead className='w-1' />
                <TableHead>Episode</TableHead>
                <TableHead>Contributors</TableHead>
                <TableHead className='w-1'>Filename</TableHead>
                <TableHead className='w-1'>Duration</TableHead>
                <TableHead className='w-1' />
              </TableRow>
            </TableHeader>
            <TableBody>
              {!loading || episodes ? (
                !!episodes?.length ? episodes.map((episode) => {
                  const selected = episode.guid === importEpisodeGuid;
                  return (
                    <ImportItemRow data={episode} rowData={importData.current.get(episode.guid)}
                      importAs='episode'
                      selectInputComponent={<RadioGroupItem value={episode.guid} checked={selected} />}
                      selected={selected}
                      onImportDataChange={handleRowChange}
                      key={episode.guid}
                    />
                  )
                }) : (
                  <TableRow>
                    <TableCell colSpan={6}>
                      <Alert>
                        <FileQuestion className='h-6 w-6' />
                        <AlertTitle>No Episode Published On {publishDate.toLocaleDateString('en-US', { year: 'numeric', month: 'long', day: 'numeric' })}</AlertTitle>
                        <AlertDescription>
                          Check <a href="https://dovetail.podcasts.prx.org/" target="Dovetail">Dovetail</a> to make sure episode was published in the "The World" podcast. If it was published today, it should show up here shortly.
                        </AlertDescription>
                      </Alert>
                    </TableCell>
                  </TableRow>
                )
              ) : (
                <ImportItemRow importAs='episode' />
              )}
            </TableBody>
          </Table>

        </RadioGroup>

        <Table className='mt-6 border'>
          <TableHeader>
            <TableRow>
              <TableHead className='w-1' />
              <TableHead>Segment</TableHead>
              <TableHead>Contributors</TableHead>
              <TableHead className='w-1'>Filename</TableHead>
              <TableHead className='w-1'>Duration</TableHead>
              <TableHead className='w-1' />
            </TableRow>
          </TableHeader>
          <TableBody>
            {!loading || segments ? (
              !!segments?.length ? segments.sort(sortByEnclosureFilename).map((segment) => {
                const selected = importSegmentGuids.has(segment.guid);
                return (
                  <ImportItemRow data={segment} rowData={importData.current.get(segment.guid)}
                    importAs='segment'
                    selectInputComponent={(
                      <Checkbox value={segment.guid} checked={selected} onCheckedChange={(checked) => {
                        if (checked) {
                          setImportSegmentGuids((guids) => {
                            guids.add(segment.guid);
                            return new Set(guids);
                          });
                        } else {
                          setImportSegmentGuids((guids) => {
                            guids.delete(segment.guid);
                            return new Set(guids);
                          });
                        }
                      }} />
                    )}
                    selected={selected}
                    onImportDataChange={handleRowChange}
                    key={segment.guid}
                  />
                )
              }) : (
                <TableRow>
                  <TableCell colSpan={6}>
                    <Alert>
                      <FileQuestion className='h-6 w-6' />
                      <AlertTitle>No Segments Published On {publishDate.toLocaleDateString('en-US', { year: 'numeric', month: 'long', day: 'numeric' })}</AlertTitle>
                      <AlertDescription>
                        Check <a href="https://dovetail.podcasts.prx.org/" target="Dovetail">Dovetail</a> to make sure episodes were published in the "The World: Latest Stories" podcast. If they were published today, they should show up here shortly.
                      </AlertDescription>
                    </Alert>
                  </TableCell>
                </TableRow>
              )
            ) : (
              <ImportItemRow importAs='segment' />
            )}
          </TableBody>
        </Table>
      </CardContent>
      <CardFooter className="flex justify-end">
        <Button size="lg" onClick={() => { nextStage(); }}>Button</Button>
      </CardFooter>
    </Card>
  )
}
