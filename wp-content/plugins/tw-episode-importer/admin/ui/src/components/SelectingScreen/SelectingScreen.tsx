import type { ApiData, ApiEpisode, ApiTerm } from '@/types/api/api';
import React, { useContext, useEffect, useRef, useState } from 'react';
import axios from 'axios';
import { FileQuestion, Loader2, Pause, Play } from 'lucide-react';
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

async function getApiData(publishDate?: Date) {
  const date = (publishDate || new Date());
  const params = new URLSearchParams({
    d: `${date.getFullYear()}-${date.getMonth() + 1}-${date.getDate()}`
  })
  const apiUrlBase = window?.appLocalizer.apiUrl;
  const episodesApiUrl = new URL('episodes', apiUrlBase);
  const segmentsApiUrl = new URL('segments', apiUrlBase);

  episodesApiUrl.search = params.toString();
  segmentsApiUrl.search = params.toString();

  console.log(apiUrlBase, params.toString());

  const [episodes, segments] = await Promise.all([
    axios.get<ApiEpisode[]>(episodesApiUrl.toString()).then((res) => res.status === 200 ? res.data : null),
    axios.get<ApiEpisode[]>(segmentsApiUrl.toString()).then((res) => res.status === 200 ? res.data : null),
  ]);

  return {
    episodes,
    segments
  };
}

export function SelectingScreen() {
  const { nextStage, playAudio, playingAudioUrl } = useContext(AppContext)
  const [publishDate, setPublishDate] = useState(new Date());
  const [apiData, setApiData] = useState<ApiData>();
  const [loading, setLoading] = useState(false);
  const [importEpisodeGuid, setImportEpisodeGuid] = useState<string>();
  const [importSegmentGuids, setImportSegmentGuids] = useState(new Set<string>());
  const { episodes: apiEpisodes, segments: apiSegments } = apiData || {};

  const episodes = apiEpisodes?.map(parseApiEpisode)
  const segments = apiSegments?.map(parseApiEpisode).sort((a, b) => a.filename < b.filename ? -1 : 1 )

  useEffect(() => {
    setLoading(true);
    (async () => {
      const apiData = await getApiData(publishDate);
      setApiData(apiData);
      setLoading(false);
      setImportEpisodeGuid(apiData.episodes?.[0]?.guid);
      setImportSegmentGuids((guids) => {
        guids.clear();
        apiData.segments?.map((segment) => guids.add(segment.guid));
        return new Set(guids)
      })
    })()
  }, [publishDate]);

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

  function handleDateSelect(newDate: Date) {
    setPublishDate(newDate);
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
          <DatePicker selected={publishDate} onSelect={handleDateSelect} />
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
              {episodes ? (
                !!episodes.length ? episodes.map(({guid, title, terms, contributors, filename, duration, audioUrl}, index) => (
                  <TableRow key={guid}>
                    <TableCell className='ps-6 leading-none'>
                      <RadioGroupItem value={guid} id={`ep${index}`} checked={guid === importEpisodeGuid} />
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
                    <TableCell>{filename}</TableCell>
                    <TableCell className='text-center'>{duration}</TableCell>
                    <TableCell className='pe-6'>
                      <PlayButton onClick={() => { playAudio(audioUrl) }} playing={playingAudioUrl === audioUrl} />
                    </TableCell>
                  </TableRow>
                )) : (
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
                <TableRow>
                  <TableCell className='ps-6 pr-0'>
                    <Skeleton className='h-4 w-4 rounded-full' />
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
            {segments ? (
              !!segments.length ? segments.map(({guid, title, terms, contributors, filename, duration, audioUrl}, index) => (
                <TableRow key={guid}>
                  <TableCell className='ps-6 leading-none'>
                    <Checkbox value={guid} checked={importSegmentGuids.has(guid)} onCheckedChange={(checked) => {
                      console.log(checked, guid, importSegmentGuids);
                      if (checked) {
                        setImportSegmentGuids((guids) => {
                          guids.add(guid);
                          return new Set(guids);
                        });
                      } else {
                        setImportSegmentGuids((guids) => {
                          guids.delete(guid);
                          return new Set(guids);
                        });
                      }
                    }} />
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
                  <TableCell>{filename}</TableCell>
                  <TableCell className='text-center'>{duration}</TableCell>
                  <TableCell className='pe-6'>
                    <PlayButton onClick={() => { playAudio(audioUrl) }} playing={playingAudioUrl === audioUrl} />
                  </TableCell>
                </TableRow>
              )) : (
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
              <TableRow>
                <TableCell className='ps-6 pr-0'>
                  <Skeleton className='h-4 w-4 rounded-full' />
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
