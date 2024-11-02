import type { ApiData, ApiEpisode } from '@/types/api/api';
import type { ItemRow } from '@/types/state/itemRow';
import React, { useContext, useEffect, useRef, useState } from 'react';
import axios, { CanceledError } from 'axios';
import { isSameMonth, isSameDay } from 'date-fns';
import { ArrowRight, ArrowRightToLine, FileQuestion, RefreshCw } from 'lucide-react';
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
  const date = new Date(publishDate);
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
  };
  const episodesOptions = {
    ...options,
    ...(controllers?.episodes && { signal: controllers.episodes.signal })
  };
  const segmentsOptions = {
    ...options,
    ...(controllers?.episodes && { signal: controllers.episodes.signal })
  };

  if (noCache) {
    params.set('cb', `${(new Date()).getTime()}`);
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
      return res instanceof CanceledError ? null : fetchData();
    });
  }

  return fetchData();
}

export function SelectingScreen() {
  const { updateAppData, nextStage, playingAudioUrl, playing } = useContext(AppContext);
  const { toast, dismiss: dismissToast } = useToast();
  const [audioPlayerToast, setAudioPlayerToast] = useState<ReturnType<typeof toast>>();
  const today = new Date();
  const [queryMonthDate, setQueryMonthDate] = useState(new Date(today.getFullYear(), today.getMonth()));
  const [publishDate, setPublishDate] = useState(today);
  const [month, setMonth] = useState(today);
  const publishDateKey = formatDateKey(publishDate);
  const [apiData, setApiData] = useState<Map<string, ApiData>>(new Map());
  const publishDateData = apiData?.get(publishDateKey);
  const publishDateDataRefreshTimeout = useRef<ReturnType<typeof setTimeout>>();
  const { episodes, segments } = publishDateData || {};
  const [loading, setLoading] = useState(false);
  const [importRowsMap, setImportRowsMap] = useState(new Map<string, ItemRow>());
  const [importEpisodeGuid, setImportEpisodeGuid] = useState<string>();
  const [importSegmentGuids, setImportSegmentGuids] = useState<Set<string>>();
  const importEpisode = importRowsMap.get(importEpisodeGuid);
  const importSegments = [...(importSegmentGuids || [])].map((guid) => importRowsMap.get(guid));
  const allImports = [
    ...(importEpisode ? [importEpisode] : []),
    ...importSegments
  ];
  const haveImports = allImports.find(({ data: { existingPost } }) => !existingPost);
  const haveUpdates = allImports.find(({ data: { hasUpdatedAudio } }) => hasUpdatedAudio);
  const buttonLabel = (haveImports && haveUpdates && 'Import & Update') || (haveImports && 'Import') || (haveUpdates && 'Update') || null;
  const playingAudioUrlInView = !![...(episodes || []), ...(segments || [])].find(({ enclosure }) => playingAudioUrl === enclosure.href);
  const playingAudioUrlItemRow = [...importRowsMap.values()].find((itemRow) => itemRow.audioUrl === playingAudioUrl);
  const datesData = [...(apiData?.values() || [])].map(({ episodes, segments, date }) => {
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
  const loadedDays = datesData.map(({ date }) => date);
  const loadedClassNames = '!animate-none after:hidden';
  const importableDays = datesData
    .filter((data) => data.hasImportableItems )
    .map(({ date }) => date );
  const importableClassNames = 'border-2 border-primary hover:bg-primary/60 ';
  const existsDays = datesData
    .filter((data) => data.hasExistingItems && !data.hasImportableItems)
    .map(({ date }) => date );
  const existsClassNames = 'border-2 border-lime-500';
  const importedDays = datesData
    .filter((data) => data.hasImportedItems)
    .map(({ date }) => date );
  const importedClassNames = 'bg-lime-500 hover:!bg-lime-500/60';
  const updatedDays = datesData
    .filter((data) => data.hasUpdateableItems)
    .map(({ date }) => date );
  const updatedClassNames = 'border-2 border-orange-400 bg-orange-400 hover:!bg-orange-400/60';
  const partialyImportedDays = datesData
    .filter((data) => data.hasExistingItems && data.hasImportableItems)
    .map(({ date }) => date );
  const partialyImportedClassNames = 'border-2 border-dotted';
  const playingAudioDays = playingAudioUrlItemRow ? [new Date(playingAudioUrlItemRow.data.datePublished)] : [];
  const playingAudioClassName = cn(`before:place-content-center before:absolute before:top-[-4px] before:right-[-4px] before:w-4 before:h-4 before:bg-orange-400 before:text-white before:rounded-full before:leading-[0]`, {
    "before:content-['⏵']": !playing,
    "before:content-['⏸']": playing,
  });
  const getEpisodesController = useRef<AbortController>();
  const getSegmentController = useRef<AbortController>();

  console.log('Rendering...', publishDateData);

  /**
   * Clean up stuff.
   */
  useEffect(() => () => {
    // Clear refresh timeout.
    if (publishDateDataRefreshTimeout.current) {
      clearTimeout(publishDateDataRefreshTimeout.current);
    }

    // Abort data fetches.
    getEpisodesController.current?.abort();
    getSegmentController.current?.abort();
  }, []);

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
    const dateData = apiData?.get(publishDateKey);

    if (!dateData) {
      setImportEpisodeGuid(null);
      return;
    };

    const importEpisode = dateData.episodes?.find(({ existingPost, hasUpdatedAudio, enclosure: { episodeKey } }) => {
      const isImportingOrUpdated = !existingPost || hasUpdatedAudio;
      const hasImportingSegment = !!dateData.segments?.find(({ enclosure, existingPost }) => enclosure.episodeKey === episodeKey && !existingPost);
      return isImportingOrUpdated || hasImportingSegment;
    });

    setImportEpisodeGuid(importEpisode?.guid || null);

  }, [publishDate, publishDateKey, apiData]);

  useEffect(() => {
    const dateData = apiData?.get(publishDateKey);

    if (!dateData) {
      setImportSegmentGuids(null);
      return;
    };

    const episode = dateData.episodes?.find(({ guid }) => guid === importEpisodeGuid);

    setImportSegmentGuids((guids) => {
      const newGuids = new Set(guids);

      newGuids.clear();

      const importSegments = dateData.segments?.filter(({ enclosure, existingPost, hasUpdatedAudio }) => {
        const episodeIsSelected = !!episode && enclosure.episodeKey === episode.enclosure.episodeKey;
        return episodeIsSelected || !existingPost || hasUpdatedAudio;
      });

      importSegments?.map((segment) => newGuids.add(segment.guid));

      return newGuids;
    })
  }, [publishDate, publishDateKey, apiData, importEpisodeGuid]);

  useEffect(() => {
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
      getSegmentController.current = controllers.segments;

      queryBeforeDate.setDate(queryBeforeDate.getDate() - 1);

      const requestDate = new Date(Math.min(...[queryBeforeDate.getTime(), today.getTime()]));
      const requests = [];

      while (requestDate >= queryAfterDate) {
        requests.push( getApiData(requestDate, undefined, false, controllers).then(handleData) );
        requestDate.setDate(requestDate.getDate() - 1);
      }

      function handleData(data: ApiData) {
        if (!data) return;

        const tempData = new Map<string, ApiData>(apiData);
        const dateKey = formatDateKey(new Date(data.date));

        if (!tempData.has(dateKey)) {
          tempData.set(dateKey, { date: data.date, episodes: [], segments: [] });
        }

        data?.episodes?.forEach((episode) => {
          const dateData = tempData.get(dateKey) || {} as ApiData;

          dateData.date = new Date(`${dateKey}T12:00:00Z`);

          dateData.episodes = dateData?.episodes || [];
          if (!dateData.episodes.find((e) => e.guid === episode.guid)) {
            dateData.episodes.push(episode);
          }

          tempData.set(dateKey, dateData);
        });

        data?.segments?.forEach((segment) => {
          const dateData = tempData.get(dateKey) || {} as ApiData;

          dateData.date = new Date(`${dateKey}T12:00:00Z`);

          dateData.segments = dateData?.segments || [];
          if (!dateData.segments.find((s) => s.guid === segment.guid)) {
            dateData.segments.push(segment);
          }

          tempData.set(dateKey, dateData);
        });

        console.log('date data recieved', {...data});

        setApiData((prevData) => new Map([...(prevData || []), ...tempData]));
      }

      await Promise.all(requests);
    })()
  }, [queryMonthDate]);

  useEffect(() => {
    if (!loading && isSameDay(publishDate, today)) {

      if (publishDateDataRefreshTimeout.current) {
        clearTimeout(publishDateDataRefreshTimeout.current);
      }

      publishDateDataRefreshTimeout.current = setTimeout(() => {
        fetchDateData(publishDate);
      }, 60000)
    }
  }, [loading, publishDateData, publishDate]);

  function fetchDateData(date: Date) {
    setLoading(true);

    (async () => {
      const dateKey = formatDateKey(date);
      const data = await getApiData(date, undefined, true);

      if (data?.episodes?.length) {
        setApiData((currentApiData) => {
          currentApiData.set(dateKey, data);
          return new Map(currentApiData);
        });
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
    setImportRowsMap((currentMap) => {
      currentMap.set(newData.guid, newData);
      return currentMap;
    });
  }

  function handleImportClick() {
    updateAppData({
      importData: {
        episode: importEpisode,
        segments: importSegments
      }
    });
    nextStage();
  }

  return (
    <Card className='overflow-clip'>
      <CardHeader className='pb-0'>
        <CardTitle>Select Epsiode and Segments</CardTitle>
        <CardDescription className='max-w-[120ch]'>
            Select the Dovetail Publish Date of the episode and segments you want to import. If multiple episodes were publish on the date, select which episode to import. Deselect any segments that should not be imported for the selected episode.
        </CardDescription>
      </CardHeader>
      <CardContent className="flex justify-between sticky top-8 z-10 p-6 bg-card/60 backdrop-blur-md shadow">
        <div className='flex gap-2 items-center'>
          <DatePicker
            // disabled={loading}
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
              loaded: loadedDays,
              exists: existsDays,
              imported: importedDays,
              partialyImported: partialyImportedDays,
              updated: updatedDays,
              importable: importableDays,
              playingAudio: playingAudioDays
            }}
            modifiersClassNames={{
              // selected: 'bg-primary text-primary-foreground hover:!bg-primary/80 hover:!text-primary-foreground',
              loaded: loadedClassNames,
              exists: existsClassNames,
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
            disabled={loading || !publishDateData}
            onClick={() => {
            fetchDateData(publishDate);
          }}>
            <RefreshCw className={cn({ 'animate-spin': loading || !publishDateData })} />
          </Button>
          {(loading || !publishDateData) && (
            <Badge variant='outline' className=' flex gap-2'>
              Loading episodes and segments from Dovetail...
            </Badge>
          )}
        </div>
        {(haveImports || haveUpdates) && (
          <Button size="lg" onClick={handleImportClick}> {buttonLabel} <ArrowRight /></Button>
        )}
      </CardContent>
      <CardContent className='pt-6'>

        <RadioGroup defaultValue={importEpisodeGuid} onValueChange={(guid) => {
          setImportEpisodeGuid(guid);
        } }>

          <Table className='border'>
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
              {publishDateData ? (
                !!episodes?.length ? episodes.map((episode) => {
                  const selected = episode.guid === importEpisodeGuid;
                  return (
                    <ImportItemRow data={episode} rowData={importRowsMap.get(episode.guid)}
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
            {publishDateData ? (
              !!segments?.length ? segments.sort(sortByEnclosureFilename).map((segment) => {
                const selected = !!importSegmentGuids?.has(segment.guid);
                return (
                  <ImportItemRow data={segment} rowData={importRowsMap.get(segment.guid)}
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
    </Card>
  )
}
