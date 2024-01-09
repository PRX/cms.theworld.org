import type { ItemRow } from '@/types/state/itemRow';
import React, { useContext, useEffect, useRef, useState } from 'react';
import ConfettiExplosion from 'react-confetti-explosion';
import axios, { AxiosResponse } from 'axios';
import { RotateCw } from 'lucide-react';
import { Alert, AlertDescription, AlertTitle } from '@/components/ui/alert';
import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card';
import { Table, TableBody, TableHead, TableHeader, TableRow } from '@/components/ui/table';
import { ImportItemRow } from '@/components/ImportItemRow';
import { Progress } from '@/components/ui/progress';
import { AppContext } from '@/lib/contexts/AppContext';
import { cn } from '@/lib/utils';
import { ApiEpisode } from '@/types/api/api';
import { Maybe } from '@/types/api/graphql';

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

      console.log('POST OK', res);

      return res.data || null;
    })
    .catch((res) => {
      console.log('POST ERROR', res);
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
  const { state } = useContext(AppContext);
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
  const stages: ((data: ImportDataMap, imported: ImportDataMap) => Promise<ImportDataMap>)[] = [];

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

    console.log('Queueing stages...', episode, segments);

    // Add stages to import or update segments.
    if (segments?.length) {
      segments.forEach(({ guid: segmentGuid }) => {
          stages.push(((guid) => (dataMap, imported) => {
            const segment = dataMap.get(guid);
            const { data, title } = segment;
            const doEpisodeImport = !data.existingPost;
            const message = `${doEpisodeImport ? 'Importing' : 'Updating'} Segment: "${title}"`;

            console.log(message, segment, dataMap, imported);

            setImportMessage(() => message);
            setImportingGuid(() => guid);

            const promise = new Promise<ImportDataMap>(async (resolve) => {
              const body = {
                terms: getTaxonomiesProps(segment)
              };
              const { id } = segment.data;
              const data = await postApiData(`segments/${id}/import`, body);

              if (data) {
                imported.set(guid, {
                  ...segment,
                  data: data || segment.data
                });

                console.log('updated imported map', imported);
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
        const doEpisodeImport = !data.existingPost;
        const message = `${doEpisodeImport ? 'Importing' : 'Updating'} Episode: "${title}"`;

        console.log(message, episode, dataMap);

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
          const { id } = episode.data;
          const data = await postApiData(`episodes/${id}/import`, body)

          if (data) {
            imported.set(guid, {
              ...episode,
              data: data || episode.data
            });

            console.log('updated imported map', imported);
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

      console.log('starting import stages...', prevImportedMap);
      for (const fn of stages) {
        console.log('starting stage:', current + 1);
        const newImportedMap = await fn(importingDataMap, prevImportedMap);
        console.log('storing new data...', newImportedMap)
        current++;
        setImportedMap(new Map(newImportedMap));
        setProgress(current / max * 100);
        prevImportedMap = newImportedMap;
      }
      console.log('import complete...', prevImportedMap);
      importIsRunning.current = false;
      setImportMessage(() => 'Import Complete! 🥳')
    })()
  }, []);

  return (
    <Card>
      <CardHeader>
        <CardTitle>{importMessage}{progress >= 100 && <span className='relative inline-block'><ConfettiExplosion duration={3000} width={2000} zIndex={100000} /></span>}</CardTitle>
      </CardHeader>
      <CardContent>
          <Progress value={progress} />
      </CardContent>
      <CardContent>

        {episode && (
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
              <ImportItemRow rowData={importedMap.get(episode.guid) || episode}
                importAs='episode'
                selected={importingGuid === episode.guid}
                selectInputComponent={<LoadingIcon guid={episode.guid} />}
                key={episode.guid}
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
              {segments.map((segment) => (
                <ImportItemRow rowData={importedMap.get(segment.guid) || segment}
                  importAs='segment'
                  selected={importingGuid === segment.guid}
                  selectInputComponent={<LoadingIcon guid={segment.guid} />}
                  key={segment.guid}
                />
              ))}
            </TableBody>
          </Table>
        )}

      </CardContent>
    </Card>
  )
}
