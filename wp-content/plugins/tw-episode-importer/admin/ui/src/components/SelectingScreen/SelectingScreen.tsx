import type { ApiData, ApiEpisode } from '@/types/api/api';
import type { ItemRow } from '@/types/state/itemRow';
import React, { useContext, useEffect, useRef, useState } from 'react';
import axios, { CanceledError } from 'axios';
import { isSameMonth } from 'date-fns';
import { ArrowRightToLine, FileQuestion, RefreshCw } from 'lucide-react';
import { DatePicker } from '@/components/DatePicker';
import { ImportItemRow } from '@/components/ImportItemRow';
import { PlayButton } from '@/components/PlayButton';
import { Alert, AlertDescription, AlertTitle } from '@/components/ui/alert';
import { Badge } from '@/components/ui/badge';
import { Button } from '@/components/ui/button';
import { Card, CardContent, CardDescription, CardFooter, CardHeader, CardTitle } from '@/components/ui/card';
import { Checkbox } from '@/components/ui/checkbox';
import { RadioGroup, RadioGroupItem } from '@/components/ui/radio-group';
import { Table, TableBody, TableCell, TableHead, TableHeader, TableRow } from '@/components/ui/table';
import { useToast } from '@/components/ui/use-toast';
import { AppContext } from '@/lib/contexts/AppContext';
import { cn } from '@/lib/utils';

function formatDateKey(date: Date) {
  const year = date.getFullYear();
  const month = `0${date.getMonth() + 1}`.slice(-2);
  const day = `0${date.getDate()}`.slice(-2);

  return `${year}-${month}-${day}`;
}

async function getApiData(publishDate?: Date, beforeDate?: Date, noCache?: boolean, controllers?: { [k: string]: AbortController }) {
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
  const options = {
    headers: {
      'X-Wp-Nonce': window.appLocalizer.nonce
    }
  }
  const episodesOptions = {
    ...options,
    ...(controllers?.episodes && { signal: controllers.episodes.signal })
  }
  const segmentsOptions = {
    ...options,
    ...(controllers?.episodes && { signal: controllers.episodes.signal })
  }

  if (noCache) {
    params.set('cb', `${(new Date()).getUTCSeconds()}`);
  }

  episodesApiUrl.search = params.toString();
  segmentsApiUrl.search = params.toString();

  async function fetchData(): Promise<ApiData> {
    return await Promise.all([
      axios.get<ApiEpisode[]>(episodesApiUrl.toString(), episodesOptions).then((res) => res.status === 200 ? res.data : null),
      axios.get<ApiEpisode[]>(segmentsApiUrl.toString(), segmentsOptions).then((res) => res.status === 200 ? res.data : null),
    ])
    .then((res) => {
      const [episodes, segments] = res;
      return {
        date,
        episodes,
        segments
      }
    })
    .catch((res) => {
      console.log('Data Fetch Failed. Retrying...', res);
      return res instanceof CanceledError ? null : fetchData();
    });
  }

  return fetchData();
}

export function SelectingScreen() {
  const { updateAppData, nextStage, playingAudioUrl } = useContext(AppContext);
  const { toast, dismiss: dismissToast } = useToast();
  const [audioPlayerToast, setAudioPlayerToast] = useState<ReturnType<typeof toast>>();
  const today = new Date();
  const [queryMonthDate, setQueryMonthDate] = useState(new Date(today.getFullYear(), today.getMonth()));
  const [publishDate, setPublishDate] = useState(new Date());
  const [month, setMonth] = useState(today);
  const publishDateKey = formatDateKey(publishDate);
  const [apiData, setApiData] = useState(new Map<string, ApiData>());
  const publishDateData = apiData.get(publishDateKey);
  const publishDateDataRefreshTimeout = useRef<ReturnType<typeof setTimeout>>();
  const { episodes, segments } = publishDateData || {};
  const [loading, setLoading] = useState(false);
  const importData = useRef(new Map<string, ItemRow>());
  const [importEpisodeGuid, setImportEpisodeGuid] = useState<string>();
  const [importSegmentGuids, setImportSegmentGuids] = useState(new Set<string>());
  const playingAudioUrlInView = !![...(episodes || []), ...(segments || [])].find(({ enclosure }) => playingAudioUrl === enclosure.href);
  const playingAudioUrlItemRow = [...importData.current.values()].find((itemRow) => itemRow.audioUrl === playingAudioUrl);
  const datesData = [...apiData.values()].map(({ episodes, segments, date }) => {
    const allItems = [...(episodes || []), ...(segments || [])];
    const hasImportableItems = !!allItems.find(({ existingPost }) => !existingPost);
    const hasExistingItems = !!allItems.find(({ existingPost }) => !!existingPost);
    const hasImportedItems = !!allItems.find(({ wasImported }) => wasImported);
    const hasUpdateableItems = !!allItems.find(({ hasUpdatedAudio }) => hasUpdatedAudio);
    const playingAudioInView = !!allItems.find(({ enclosure }) => playingAudioUrl === enclosure.href);

    return {
      hasImportableItems,
      hasExistingItems,
      hasImportedItems,
      hasUpdateableItems,
      playingAudioInView,
      episodes,
      segments,
      date
    };
  })
  const importableDays = datesData
    .filter((data) => data.hasImportableItems )
    .map(({ date }) => date );
  const importableClassNames = 'border-2 border-primary';
  const existsDays = datesData
    .filter((data) => data.hasExistingItems && !data.hasImportableItems)
    .map(({ date }) => date );
  const existsClasNames = 'border-2 border-lime-500';
  const importedDays = datesData
    .filter((data) => data.hasImportedItems)
    .map(({ date }) => date );
  const importedClassNames = 'bg-lime-500';
  const updatedDays = datesData
    .filter((data) => data.hasUpdateableItems)
    .map(({ date }) => date );
  const updatedClassNames = 'border-2 border-orange-400';
  const partialyImportedDays = datesData
    .filter((data) => data.hasExistingItems && data.hasImportableItems)
    .map(({ date }) => date );
  const partialyImportedClassNames = 'border-2 border-dotted';
  const playingAudioDays = playingAudioUrlItemRow ? [new Date(playingAudioUrlItemRow.data.datePublished)] : [];
  const playingAudioClassName = 'ring-1 ring-offset-2 ring-orange-500 !rounded-full';
  const getEpisodesController = useRef<AbortController>();
  const getSegmentController = useRef<AbortController>();

  useEffect(() => {
    if (apiData && !publishDateData) {

      if (publishDateDataRefreshTimeout.current) {
        clearTimeout(publishDateDataRefreshTimeout.current);
      }

      publishDateDataRefreshTimeout.current = setTimeout(() => {
        fetchDateData(publishDate);
      }, 60000)
    }
  }, [apiData, publishDateData, publishDate])

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

    const importEpisode = dateData.episodes?.find(({ existingPost, hasUpdatedAudio, enclosure: { episodeKey } }) => {
      const isImportingOrUpdated = !existingPost || hasUpdatedAudio;
      const hasImportingSegment = !!dateData.segments?.find(({ enclosure, existingPost }) => enclosure.episodeKey === episodeKey && !existingPost);
      return isImportingOrUpdated || hasImportingSegment;
    });
    if (importEpisode) {
      setImportEpisodeGuid(importEpisode.guid);
    }
  }, [publishDate, publishDateKey, apiData]);

  useEffect(() => {
    const dateData = apiData.get(publishDateKey);

    if (!dateData) return;

    const episode = dateData.episodes?.find(({ guid }) => guid === importEpisodeGuid);

    setImportSegmentGuids((guids) => {
      guids.clear();
      const importSegments = dateData.segments?.filter(({ enclosure, existingPost, hasUpdatedAudio }) => {
        const episodeIsSelected = !!episode && enclosure.episodeKey === episode.enclosure.episodeKey;
        return episodeIsSelected || !existingPost || hasUpdatedAudio;
      });
      importSegments?.map((segment) => guids.add(segment.guid));
      return new Set(guids);
    })
  }, [publishDate, publishDateKey, apiData, importEpisodeGuid]);

  useEffect(() => {
    setLoading(true);

    if (publishDateDataRefreshTimeout.current) {
      clearTimeout(publishDateDataRefreshTimeout.current);
    }

    getEpisodesController.current?.abort();
    getSegmentController.current?.abort();

    (async () => {
      const queryAfterDate = new Date(queryMonthDate);
      const queryBeforeDate = new Date(queryMonthDate.getFullYear(), queryMonthDate.getMonth() + 1);
      const controllers = {
        episodes: new AbortController(),
        segments: new AbortController()
      }

      getEpisodesController.current = controllers.episodes;
      getSegmentController.current = controllers.segments

      const data = await getApiData(queryAfterDate, queryBeforeDate, false, controllers);
      const tempData = new Map<string, ApiData>(apiData);

      console.log('fetched api data', data, queryMonthDate);

      data?.episodes?.forEach((episode) => {
        const dateKey = episode.dateKey || formatDateKey(new Date(episode.datePublished));
        const dateData = tempData.get(dateKey) || {} as ApiData;

        dateData.date = new Date(`${dateKey}T12:00:00Z`);

        dateData.episodes = dateData?.episodes || [];
        if (!dateData.episodes.find((e) => e.guid === episode.guid)) {
          dateData.episodes.push(episode);
        }

        tempData.set(dateKey, dateData);
      });

      data?.segments?.forEach((segment) => {
        const dateKey = segment.dateKey || formatDateKey(new Date(segment.datePublished));
        const dateData = tempData.get(dateKey) || {} as ApiData;

        dateData.date = new Date(`${dateKey}T12:00:00Z`);

        dateData.segments = dateData?.segments || [];
        if (!dateData.segments.find((s) => s.guid === segment.guid)) {
          dateData.segments.push(segment);
        }

        tempData.set(dateKey, dateData);
      });

      console.log(controllers.episodes.signal.aborted, controllers.segments.signal.aborted)

      setApiData(tempData);
      setLoading(!data && controllers.episodes.signal.aborted || controllers.segments.signal.aborted);
    })()
  }, [queryMonthDate]);

  function fetchDateData(date: Date) {
    setLoading(true);

    (async () => {
      const dateKey = formatDateKey(date);
      const data = await getApiData(date, undefined, true);
      const tempData = new Map<string, ApiData>(apiData);

      console.log('fetched api data for date', date, dateKey, data, queryMonthDate);

      if (data?.episodes?.length) {
        tempData.set(dateKey, data);
        setApiData(tempData);
      }

      setLoading(false);
    })()
  }

  function sortByEnclosureFilename(ea: ApiEpisode, eb: ApiEpisode) {
    const a = ea.enclosure.segment;
    const b = eb.enclosure.segment;

    return a < b ? -1 : 1
  }

  function handleDateSelect(newDate: Date) {
    if (!newDate) return;
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

  function handleImportClick() {
    updateAppData({
      importData: {
        episode: importData.current.get(importEpisodeGuid),
        segments: [...importSegmentGuids].map((guid) => importData.current.get(guid))
      }
    });
    nextStage();
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

        <div className='flex gap-2 items-center'>
          <DatePicker
            disabled={loading}
            defaultMonth={publishDate}
            month={month}
            selected={publishDate}
            fromMonth={new Date(2020, 0)}
            toDate={today}
            onSelect={handleDateSelect}
            captionLayout='dropdown-buttons'
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
              selected: 'bg-primary text-primary-foreground hover:!bg-primary/80 hover:!text-primary-foreground',
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
          <Button
            size='icon'
            variant='ghost'
            className='rounded-full text-primary hover:bg-primary hover:text-primary-foreground'
            title='Refresh Selected Date Data'
            disabled={loading}
            onClick={() => {
            fetchDateData(publishDate);
          }}>
            <RefreshCw className={cn({ 'animate-spin': loading })} />
          </Button>
          {loading && (
            <Badge variant='outline' className=' flex gap-2'>
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
        <Button size="lg" onClick={handleImportClick}>Button</Button>
      </CardFooter>
    </Card>
  )
}
