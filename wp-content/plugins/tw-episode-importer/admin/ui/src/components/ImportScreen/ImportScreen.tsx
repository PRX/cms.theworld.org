import type { ItemRow } from '@/types/state/itemRow';
import React, { useContext, useEffect, useRef, useState } from 'react';
import ConfettiExplosion from 'react-confetti-explosion';
import axios from 'axios';
import { ArrowLeft, Edit, RotateCw } from 'lucide-react';
import { Card, CardContent, CardDescription, CardHeader, CardTitle } from '@/components/ui/card';
import { Table, TableBody, TableHead, TableHeader, TableRow } from '@/components/ui/table';
import { ImportItemRow } from '@/components/ImportItemRow';
import { Progress } from '@/components/ui/progress';
import { AppContext } from '@/lib/contexts/AppContext';
import { cn } from '@/lib/utils';
import { ApiEpisode, Maybe } from '@/types/api/api';
import { Button } from '@/components/ui/button';

type ImportDataMap = Map<string, ItemRow>;

type TaxonomiesProps = {
  [k: string]: (number | string)[]
};

async function postApiData(endPoint: string, body: object): Promise<Maybe<ApiEpisode>> {
  const apiUrlBase = window?.appLocalizer.apiUrl;
  const apiUrl = new URL(endPoint, apiUrlBase);
  const options = {
    headers: {
      'X-Wp-Nonce': window.appLocalizer.nonce
    }
  };

  return axios.post<ApiEpisode>(apiUrl.toString(), body, options)
    .then((res) => {
      return res.data || null;
    })
    .catch((): null => {
      return null;
    });
}

async function putApiData(endPoint: string, body: object): Promise<Maybe<ApiEpisode>> {
  const apiUrlBase = window?.appLocalizer.apiUrl;
  const apiUrl = new URL(endPoint, apiUrlBase);
  const options = {
    headers: {
      'X-Wp-Nonce': window.appLocalizer.nonce
    }
  };

  return axios.put<ApiEpisode>(apiUrl.toString(), body, options)
    .then((res) => {
      return res.data || null;
    })
    .catch((): null => {
      return null;
    });
}

function getTaxonomiesProps(row: ItemRow) {
  const { terms, contributors } = row;
  const props = {} as TaxonomiesProps;

  for (const term of (terms || [])) {
    if (!term.taxonomy) continue;

    const {id, name} = term;

    props[term.taxonomy.name] = [
      ...(props[term.taxonomy.name] || []),
      id || name
    ]
  }

  for (const contributor of (contributors || [])) {
    const taxonomyName = 'contributor';
    const {id, name} = contributor;

    props[taxonomyName] = [
      ...(props[taxonomyName] || []),
      id || name
    ]
  }

  return props;
}

export function ImportScreen() {
  const importIsRunning = useRef(false);
  const { state, setStage } = useContext(AppContext);
  const { data } = state || {};
  const { importData } = data || {};
  const { episode, segments } = importData || {};
  const importingDataMap: ImportDataMap = new Map();

  if (episode) {
    importingDataMap.set(episode.guid, episode);
  }

  if (segments) {
    segments.forEach((segment) => {
      importingDataMap.set(segment.guid, segment);
    })
  }

  const [importedMap, setImportedMap] = useState<ImportDataMap>(new Map());
  const [importMessage, setImportMessage] = useState('Importing...');
  const [importingGuid, setImportingGuid] = useState<string>();
  const [progress, setProgress] = useState(0);
  const isImportComplete = progress >= 100;
  const importedEpisode = episode && importedMap.get(episode.guid);
  const stages: ((data: ImportDataMap, imported: ImportDataMap) => Promise<ImportDataMap>)[] = [];
  const renderEpisode = importedEpisode || episode;

  function LoadingIcon({ guid }: { guid: string }) {
    const isLoading = !importedMap?.get(guid)?.data.existingPost;
    const className = cn('text-primary', {
      'animate-spin': importingGuid === guid
    })

    if (!isLoading) return null;

    return (
      <RotateCw className={className} />
    );
  }

  useEffect(() => {
    if (importIsRunning.current) return;

    // Add stages to import or update segments.
    if (segments?.length) {
      segments.filter(({ data: { wasImported, hasUpdatedAudio } }) => !wasImported || hasUpdatedAudio).forEach(({ guid: segmentGuid }) => {
          stages.push(((guid) => (dataMap, imported) => {
            const segment = dataMap.get(guid);
            const { data, title } = segment;
            const message = `${data.hasUpdatedAudio || (!data.existingAudio && data.existingPost) ? 'Updating' : 'Importing'} Segment: "${title}"`;

            setImportMessage(() => message);
            setImportingGuid(() => guid);

            const promise = new Promise<ImportDataMap>(async (resolve) => {
              const body = {
                terms: getTaxonomiesProps(segment)
              };
              const { id, wasImported, hasUpdatedAudio } = data;
              let newData: Maybe<ApiEpisode>;

              if (!wasImported) {
                newData = await postApiData(`segments/${id}`, body);
              }

              if (hasUpdatedAudio) {
                newData = await putApiData(`segments/${id}`, body);
              }

              if (newData) {
                imported.set(guid, {
                  ...segment,
                  data: newData
                });
              }

              resolve(imported);
            });

            return promise;
          })(segmentGuid))
      });
    }

    // Add stage to import or update episode.
    if (episode) {
      stages.push(((guid) => (dataMap, imported) => {
        const episode = dataMap.get(guid);
        const { data, title } = episode;
        const message = `${!data.wasImported ? 'Updating' : 'Importing'} Episode: "${title}"`;

        setImportMessage(() => message);
        setImportingGuid(() => guid);

        const promise = new Promise<ImportDataMap>(async (resolve) => {
          const importedSegments = segments.map(({guid: segmentGuid}) => imported.get(segmentGuid) || dataMap.get(segmentGuid))
            .filter((segment) => !!segment?.data.existingPost)
            .map(({ data: { existingPost: { databaseId } } }) => databaseId);
          const body = {
            terms: getTaxonomiesProps(episode),
            segments: importedSegments
          };
          const { id, wasImported } = data;
          let newData: Maybe<ApiEpisode>;

          if (!wasImported) {
            newData = await postApiData(`episodes/${id}`, body);
          }

          newData = await putApiData(`episodes/${id}`, body);

          if (newData) {
            imported.set(guid, {
              ...episode,
              data: newData
            });
          }

          resolve(imported);
        });

        return promise;
      })(episode.guid))
    }

    importIsRunning.current = true;

    (async () => {
      let prevImportedMap = importedMap;
      let current = 0;
      const max = stages.length;

      for (const fn of stages) {
        const newImportedMap = await fn(importingDataMap, prevImportedMap);

        current++;

        setImportedMap(new Map(newImportedMap));
        setProgress(current / max * 100);

        prevImportedMap = newImportedMap;
      }

      importIsRunning.current = false;
      setImportMessage(() => 'Import Complete! 🥳')
    })()
  }, []);

  return (
    <Card>
      <CardHeader className='sticky top-8 z-10 mb-6 bg-card/60 backdrop-blur-md shadow'>
        <CardTitle className='flex gap-4 items-center'>
          {isImportComplete && (
            <Button size='icon' variant='ghost' color='' onClick={() => setStage('selecting')}>
              <ArrowLeft />
            </Button>
          )}
          <span>{importMessage}{isImportComplete && <span className='relative inline-block'><ConfettiExplosion duration={3000} width={2000} zIndex={100000} /></span>}</span>
        </CardTitle>
        <CardDescription className='!mt-4'>
          {!isImportComplete ? (
            <Progress value={progress} />
          ) : (
            importedEpisode?.data.existingPost && (
              <Button className='flex gap-2 rounded-full hover:text-white focus-visible:text-white active:text-white' asChild>
                <a href={importedEpisode.data.existingPost.editLink} target={`edit:${importedEpisode.data.existingPost.databaseId}`}>
                  Edit Episode <Edit />
                </a>
              </Button>
            )
          )}
        </CardDescription>
      </CardHeader>
      <CardContent>

        {renderEpisode && (
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
              <ImportItemRow rowData={renderEpisode}
                importAs='episode'
                selected={importingGuid === renderEpisode.guid}
                selectInputComponent={<LoadingIcon guid={renderEpisode.guid} />}
                key={[renderEpisode.guid, renderEpisode.data.existingAudio?.databaseId, renderEpisode.data.existingPost?.databaseId, renderEpisode.data.existingPosts?.length].join(':')}
              />
            </TableBody>
          </Table>
        )}

        {!!segments?.length && (
          <Table className='mt-6 border'>
            <TableHeader>
              <TableRow>
                <TableHead className='w-1' />
                <TableHead>Segments</TableHead>
                <TableHead>Contributors</TableHead>
                <TableHead className='w-1'>Filename</TableHead>
                <TableHead className='w-1'>Duration</TableHead>
                <TableHead className='w-1' />
              </TableRow>
            </TableHeader>
            <TableBody>
              {segments.map((segment) => {
                const renderSegment = importedMap.get(segment.guid) || segment;
                return (
                  <ImportItemRow rowData={renderSegment}
                    importAs='segment'
                    selected={importingGuid === renderSegment.guid}
                    selectInputComponent={<LoadingIcon guid={renderSegment.guid} />}
                    key={[renderSegment.guid, renderSegment.data.existingAudio?.databaseId, renderSegment.data.existingPost?.databaseId, renderSegment.data.existingPosts?.length].join(':')}
                  />
                );
              })}
            </TableBody>
          </Table>
        )}

      </CardContent>
    </Card>
  )
}
