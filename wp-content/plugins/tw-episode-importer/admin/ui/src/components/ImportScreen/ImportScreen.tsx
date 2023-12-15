import React, { useContext, useEffect, useState } from 'react';
import { AppContext } from '@/lib/contexts/AppContext';
import { Card, CardContent, CardDescription, CardFooter, CardHeader, CardTitle } from '@/components/ui/card';
import { Alert, AlertDescription, AlertTitle } from '@/components/ui/alert';
import { Table, TableBody, TableCell, TableHead, TableHeader, TableRow } from '@/components/ui/table';
import { AlertTriangle, RotateCw } from 'lucide-react';
import { ImportItemRow } from '@/components/ImportItemRow';

export function ImportScreen() {
  const { updateAppData, nextStage, state } = useContext(AppContext);
  const { data } = state || {};
  const { importData } = data || {};
  const { episode, segments } = importData || {};
  const [importMessage, setImportMessage] = useState('Importing...')
  const [importingGuid, setImportingGuid] = useState<string>();
  const [progress, setProgress] = useState(0);

  function LoadingIcon() {
    return (
      <RotateCw className='text-primary animate-spin' />
    )
  }

  useEffect(() => {
    //
  }, []);

  return (
    <Card>
      <CardHeader>
        <CardTitle>{importMessage}</CardTitle>
        <CardDescription>
        </CardDescription>
      </CardHeader>
      <CardContent>

        {episode && (
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
              <ImportItemRow data={episode.data} rowData={episode}
                importAs='episode'
                selected
                selectInputComponent={<LoadingIcon />}
                key={episode.guid}
              />
            </TableBody>
          </Table>
        )}

      </CardContent>
    </Card>
  )
}
