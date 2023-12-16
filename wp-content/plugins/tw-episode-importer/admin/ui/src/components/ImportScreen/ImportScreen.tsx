import type { ItemRow } from '@/types/state/itemRow';
import React, { useContext, useEffect, useRef, useState } from 'react';
import { AppContext } from '@/lib/contexts/AppContext';
import { Card, CardContent, CardDescription, CardFooter, CardHeader, CardTitle } from '@/components/ui/card';
import { Alert, AlertDescription, AlertTitle } from '@/components/ui/alert';
import { Table, TableBody, TableCell, TableHead, TableHeader, TableRow } from '@/components/ui/table';
import { AlertTriangle, RotateCw } from 'lucide-react';
import { ImportItemRow } from '@/components/ImportItemRow';
import { Progress } from '@/components/ui/progress';
import ConfettiExplosion from 'react-confetti-explosion';

type ImportDataMap = Map<string, ItemRow>;

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

  console.log(episode, segments);

  if (segments?.length) {
    // Add stages to import or update segments.
    segments.forEach(({ data }) => {
      const doAudioImport = !data.existingAudio;
      const doAudioUpdate = !doAudioImport && data.hasUpdatedAudio;
      const doSegmentImport = !data.existingPost;

      if (doAudioImport) {
        stages.push(((guid) => async (dataMap, imported) => {
          console.log()
          const segment = dataMap.get(guid);
          const message = `Importing Segment Audio: ${segment.filename}`;

          console.log(message, segment, dataMap);

          setImportMessage(() => message);
          setImportingGuid(() => guid);

          await new Promise<void>((resolve) => {
            setTimeout(() => {
              resolve();
            }, 2000);
          });

          // imported.set(guid, segment);

          return imported;
        })(data.guid))
      } else if (doAudioUpdate) {
        stages.push(((guid) => async (dataMap, imported) => {
          const segment = dataMap.get(guid);
          const message = `Updating Segment Audio: ${segment.filename}`;

          console.log(message, segment, dataMap);

          setImportMessage(() => message);
          setImportingGuid(() => guid);

          await new Promise<void>((resolve) => {
            setTimeout(() => {
              resolve();
            }, 2000);
          });

          // imported.set(guid, segment);

          return imported;
        })(data.guid))
      }

      if (doSegmentImport) {
        stages.push(((guid) => async (dataMap, imported) => {
          const segment = dataMap.get(guid);
          const message = `Importing Segment: "${segment.title}"`;

          console.log(message, segment, dataMap);

          setImportMessage(() => message);
          setImportingGuid(() => guid);

          await new Promise<void>((resolve) => {
            setTimeout(() => {
              resolve();
            }, 2000);
          });

          imported.set(guid, segment);

          return imported;
        })(data.guid))
      }
    });
  }

  if (episode) {
    const { data } = episode;
    const doAudioImport = !data.existingAudio;
    const doAudioUpdate = !doAudioImport && data.hasUpdatedAudio;
    const doEpisodeImport = !data.existingPost;

    if (doAudioImport) {
      stages.push(((guid) => async (dataMap, imported) => {
        const episode = dataMap.get(guid);
        const message = `Importing Episode Audio: "${episode.filename}"`;

        console.log(message, episode, dataMap);

        setImportMessage(() => message);
        setImportingGuid(() => guid);

        await new Promise<void>((resolve) => {
          setTimeout(() => {
            resolve();
          }, 2000);
        });

        // imported.set(guid, episode);

        return imported;
      })(data.guid))
    } else if (doAudioUpdate) {
      stages.push(((guid) => async (dataMap, imported) => {
        const episode = dataMap.get(guid);
        const message = `Updating Episode Audio: "${episode.filename}"`;

        console.log(message, episode, dataMap);

        setImportMessage(() => message);
        setImportingGuid(() => guid);

        await new Promise<void>((resolve) => {
          setTimeout(() => {
            resolve();
          }, 2000);
        });

        // imported.set(guid, episode);

        return imported;
      })(data.guid))
    }

    if (doEpisodeImport) {
      stages.push(((guid) => async (dataMap, imported) => {
        const episode = dataMap.get(guid);
        const message = `Importing Episode: "${episode.title}"`;

        console.log(message, episode, dataMap);

        setImportMessage(() => message);
        setImportingGuid(() => guid);

        await new Promise<void>((resolve) => {
          setTimeout(() => {
            resolve();
          }, 2000);
        });

        imported.set(guid, episode);

        return imported;
      })(data.guid))
    }

    // Add stage to attach update audio to attach it to the episode.
  }

  function LoadingIcon({ guid }: { guid: string }) {
    const isLoading = !importedMap?.get(guid);

    if (!isLoading) return null;

    return (
      <RotateCw className='text-primary animate-spin' />
    );
  }

  useEffect(() => {
    if (importIsRunning.current) return;

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
        setImportedMap(() => newImportedMap);
        setProgress(() => current / max * 100);
        prevImportedMap = newImportedMap;
      }
      console.log('import complete...', prevImportedMap);
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
              <ImportItemRow data={(importedMap.get(episode.guid) || episode).data} rowData={episode}
                importAs='episode'
                selected
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
                <ImportItemRow data={(importedMap.get(segment.guid) || segment).data} rowData={segment}
                  importAs='episode'
                  selected
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
