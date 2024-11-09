import type { ApiData, ApiEpisode } from '@/types/api/api';
import type { ItemRow } from '@/types/state/itemRow';
import React, { useContext, useEffect, useRef, useState } from 'react';
import { isSameMonth, isSameDay } from 'date-fns';
import { ArrowRight, ArrowRightToLine, Check, FileQuestion, RefreshCw, Repeat2, EllipsisVertical, RotateCcw } from 'lucide-react';
import { DatePicker } from '@/components/DatePicker';
import { ImportItemRow } from '@/components/ImportItemRow';
import { PlayButton } from '@/components/PlayButton';
import { Alert, AlertDescription, AlertTitle } from '@/components/ui/alert';
import { Badge } from '@/components/ui/badge';
import { Button } from '@/components/ui/button';
import { Card, CardContent, CardDescription, CardHeader, CardTitle } from '@/components/ui/card';
import { Checkbox } from '@/components/ui/checkbox';
import { RadioGroup, RadioGroupItem } from '@/components/ui/radio-group';
import { Table, TableBody, TableCell, TableHead, TableHeader, TableRow } from '@/components/ui/table';
import { useToast } from '@/components/ui/use-toast';
import { AppContext } from '@/lib/contexts/AppContext';
import { cn } from '@/lib/utils';
import { formatDateKey } from '@/lib/utils/format/formatDateKey';
import { getApiData } from '@/lib/api';
import { DropdownMenu, DropdownMenuContent, DropdownMenuGroup, DropdownMenuItem, DropdownMenuPortal, DropdownMenuSub, DropdownMenuSubContent, DropdownMenuSubTrigger, DropdownMenuTrigger } from '@/components/ui/dropdown-menu';

const today = new Date();

export function SelectingScreen() {
  const { updateAppData, nextStage, playingAudioUrl, playing } = useContext(AppContext);
  const { toast, dismiss: dismissToast } = useToast();
  const [audioPlayerToast, setAudioPlayerToast] = useState<ReturnType<typeof toast>>();
  const [queryMonthDate, setQueryMonthDate] = useState(new Date(today.getFullYear(), today.getMonth()));
  const [publishDate, setPublishDate] = useState(today);
  const [month, setMonth] = useState(today);
  const publishDateKey = formatDateKey(publishDate);
  const [apiData, setApiData] = useState<Map<string, ApiData>>(new Map());
  const publishDateData = apiData?.get(publishDateKey);
  const publishDateDataRefreshTimeout = useRef<ReturnType<typeof setTimeout>>();
  const { episodes, segments } = publishDateData || {};
  console.log(publishDateKey, episodes, segments);
  const canRollbackEpisodes = !!(episodes || []).find(({ existingAudio, existingPost }) => existingPost?.imported || existingAudio?.imported);
  const canRollbackSegments = !!(segments || []).find(({ existingAudio, existingPost }) => existingPost?.imported || existingAudio?.imported);
  const canRollback = canRollbackEpisodes || canRollbackSegments;
  const [loading, setLoading] = useState<string>();
  const [importRowsMap, setImportRowsMap] = useState<Map<string, ItemRow>>(new Map());
  const [importEpisodeGuid, setImportEpisodeGuid] = useState<string>();
  const [importSegmentGuids, setImportSegmentGuids] = useState<Set<string>>();
  const importEpisode = importRowsMap.get(importEpisodeGuid);
  const importSegments = [...(importSegmentGuids || [])].map((guid) => importRowsMap.get(guid)).filter(v => !!v);
  const allImports = [
    ...(importEpisode ? [importEpisode] : []),
    ...importSegments
  ];
  const haveImports = !!allImports.find(({ data: { wasImported } }) => !wasImported);
  const haveUpdates = !!allImports.find(({ data: { hasUpdatedAudio, existingAudio, existingPost } }) => hasUpdatedAudio || (existingPost && !existingAudio));
  const buttonLabel = (haveImports && haveUpdates && 'Import & Update') || (haveImports && 'Import') || (haveUpdates && 'Update') || null;
  const showMenu = canRollback;
  const playingAudioUrlInView = !![...(episodes || []), ...(segments || [])].find(({ enclosure }) => playingAudioUrl === enclosure.href);
  const playingAudioUrlItemRow = [...importRowsMap.values()].find((itemRow) => itemRow.audioUrl === playingAudioUrl);
  const datesData = [...(apiData?.values() || [])].map(({ episodes, segments, date }) => {
    const allItems = [...(episodes || []), ...(segments || [])];
    const hasImportableItems = !!allItems.find(({ wasImported }) => !wasImported);
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
  const playingAudioClassName = cn(`before:place-content-center before:absolute before:top-[-4px] before:right-[-4px] before:w-3.5 before:h-3.5 before:bg-orange-400 before:text-white before:rounded-full before:leading-[0]`, {
    "before:content-['⏵']": !playing,
    "before:content-['⏸']": playing,
  });
  const getEpisodesController = useRef<AbortController>();
  const getSegmentController = useRef<AbortController>();

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
    // Make sure import selections are cleared between date changes.
    setImportRowsMap(new Map());
  }, [publishDate])

  useEffect(() => {
    if (!publishDateData) {
      setImportEpisodeGuid(null);
      return;
    };

    const importEpisode = publishDateData.episodes?.find(({ wasImported, hasUpdatedAudio, enclosure: { episodeKey } }) => {
      const isImportingOrUpdated = !wasImported || hasUpdatedAudio;
      const hasImportingSegment = !!publishDateData.segments?.find(({ enclosure, wasImported: segmentImported }) => enclosure.episodeKey === episodeKey && !segmentImported);
      return isImportingOrUpdated || hasImportingSegment;
    });
    const importSegments = publishDateData.segments?.filter(({ enclosure, wasImported, hasUpdatedAudio }) => {
      const episodeIsSelected = !!importEpisode && enclosure.episodeKey === importEpisode.enclosure.episodeKey;
      return episodeIsSelected || !wasImported || hasUpdatedAudio;
    });

    setImportEpisodeGuid(importEpisode?.guid || null);
    setImportSegmentGuids(new Set(importSegments.map(({ guid }) => guid)));

  }, [publishDateData]);

  useEffect(() => {
    if (!episodes?.length) {
      setImportEpisodeGuid(null);
      return;
    };

    const selectableSegments = importSegments?.filter(({ data: { wasImported }}) => !wasImported);

    const importEpisode = episodes.find(({ enclosure: { episodeKey } }) => {
      const hasImportingSegment = !!selectableSegments?.find(({ data: { enclosure, wasImported } }) => enclosure.episodeKey === episodeKey && !wasImported);

      return hasImportingSegment;
    });

    setImportEpisodeGuid((currentGuid) => importEpisode?.guid || currentGuid || null);

  }, [episodes, importSegments])

  useEffect(() => {
    if (!segments?.length) {
      setImportSegmentGuids(null);
      return;
    };

    const episode = publishDateData.episodes?.find(({ guid }) => guid === importEpisodeGuid);

    const importSegments = segments.filter(({ enclosure }) => {
      const episodeIsSelected = !!episode && enclosure.episodeKey === episode.enclosure.episodeKey;
      return episodeIsSelected;
    });

    if (importSegments.length) {
      setImportSegmentGuids(new Set(importSegments.map(({ guid }) => guid)));
    }

  }, [segments, importEpisodeGuid]);

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
  }, [loading, publishDateData, publishDate, today]);

  function fetchDateData(date: Date) {
    setLoading(`Refreshing data for ${publishDate.toLocaleDateString(undefined, { dateStyle: 'medium' })}`);

    (async () => {
      const dateKey = formatDateKey(date);
      const data = await getApiData(date, undefined, true);

      if (data?.episodes?.length) {
        setApiData((currentApiData) => {
          currentApiData.set(dateKey, data);
          return new Map(currentApiData);
        });
        setImportRowsMap((currentMap) => {
          const { episodes : e, segments: s } = data;
          [...(e || []), ...(s || [])].forEach((d) => {
            const { guid } = d;
            const ir = currentMap.get(guid);
            if (ir) {
              ir.data = d;
              currentMap.set(guid, ir);
            }
          });
          return new Map(currentMap);
        })
      }

      setLoading(null);
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
      return new Map(currentMap);
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

  console.log('To be imported >>>', importEpisode, importSegments);

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
            //   // selected: 'bg-primary text-primary-foreground hover:!bg-primary/80 hover:!text-primary-foreground',
              loaded: loadedClassNames,
            //   exists: existsClassNames,
            //   imported: importedClassNames,
            //   partialyImported: partialyImportedClassNames,
            //   updated: updatedClassNames,
            //   importable: importableClassNames,
              playingAudio: playingAudioClassName
            }}
            footer={(
              <div className='divide-y'>
                <div className='flex gap-2 flex-wrap max-w-[280px] mt-4'>
                  <Badge variant='outline' className='text-xs capitalize ps-1'><span className='w-4 h-4 rounded-full bg-primary me-2'></span> Has imports</Badge>
                  <Badge variant='outline' className='text-xs capitalize ps-1'><span className='inline-grid place-items-center w-4 h-4 rounded-full bg-green-500 me-2 text-white'><Check className="w-3 h-3" /></span> Completed imports</Badge>
                  <Badge variant='outline' className='text-xs capitalize ps-1'><span className='inline-grid place-items-center w-4 h-4 rounded-full bg-orange-400 me-2 text-white'><Repeat2 className="w-3 h-3" /></span> Has updates</Badge>
                </div>
                {!isSameMonth(today, month) && (
                  <div className='flex justify-end mt-4 pt-2'>
                    <Button
                      size='sm'
                      className='flex gap-2'
                      onClick={() => {
                        handleMonthChange(today);
                      }}
                    >Most Recent <ArrowRightToLine /></Button>
                  </div>
                )}
              </div>
            )}
            // components={{
            //   Day: ({ ...props }) => <CalendarDay {...props} />,
            // }}
          />
          <Button
            size='icon'
            variant='ghost'
            className='rounded-full text-primary hover:bg-primary hover:text-primary-foreground'
            title={`Refresh data for ${publishDate.toLocaleDateString(undefined, { dateStyle: 'medium' })}`}
            disabled={!!loading || !publishDateData}
            onClick={() => {
              fetchDateData(publishDate);
            }}
          >
            <RefreshCw className={cn({ 'animate-spin': loading || !publishDateData })} />
          </Button>
          {(loading) && (
            <Badge variant='outline' className='flex gap-2'>
              {loading}...
            </Badge>
          )}
        </div>
        <div className='flex gap-2 items-center'>
          {(haveImports || haveUpdates) && (
            <Button onClick={handleImportClick}> {buttonLabel} <ArrowRight /></Button>
          )}
          {showMenu && (
            <DropdownMenu>
              <DropdownMenuTrigger asChild>
                <Button size='icon' variant='ghost'>
                  <EllipsisVertical />
                </Button>
              </DropdownMenuTrigger>
              <DropdownMenuContent align='end' side='right' className='w-56'>
                <DropdownMenuGroup>
                  {canRollback && (
                    <DropdownMenuSub>
                      <DropdownMenuSubTrigger>
                        <RotateCcw />
                        <span>Rollback...</span>
                      </DropdownMenuSubTrigger>
                      <DropdownMenuPortal>
                        <DropdownMenuSubContent>
                          {canRollbackEpisodes && canRollbackSegments && (
                            <DropdownMenuItem>
                              <span>All</span>
                            </DropdownMenuItem>
                          )}
                          {canRollbackEpisodes && (
                            <DropdownMenuItem>
                              <span>Episodes</span>
                            </DropdownMenuItem>
                          )}
                          {canRollbackSegments && (
                            <DropdownMenuItem>
                              <span>Segments</span>
                            </DropdownMenuItem>
                          )}
                        </DropdownMenuSubContent>
                      </DropdownMenuPortal>
                    </DropdownMenuSub>
                  )}
                </DropdownMenuGroup>
              </DropdownMenuContent>
            </DropdownMenu>
          )}
        </div>
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
                  const selected =  episode.guid === importEpisodeGuid;
                  return (
                    <ImportItemRow data={episode} rowData={importRowsMap.get(episode.guid)}
                      importAs='episode'
                      selectInputComponent={<RadioGroupItem value={episode.guid} checked={selected} />}
                      selected={selected}
                      onImportDataChange={handleRowChange}
                      key={[episode.guid, episode.existingAudio?.databaseId, episode.existingPost?.databaseId, episode.existingPosts?.length].join(':')}
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
                const key = [segment.guid, segment.existingAudio?.databaseId, segment.existingPost?.databaseId, segment.existingPosts?.length].join(':');
                console.log(key);
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
                    key={key}
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
