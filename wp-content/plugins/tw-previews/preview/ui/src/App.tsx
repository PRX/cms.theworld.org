import type { ContentNode } from '@/types/api/graphql';
import { type AppAction, type AppState } from '@/types/state/app';
import React, { useEffect, useReducer, useRef } from 'react';
import { CheckCircle, Loader } from 'lucide-react';
import { AppContext, type AppContextValue } from '@/lib/contexts/AppContext';
import { DocumentNode, gql, useQuery } from '@apollo/client';
import { Toaster } from '@/components/ui/toaster';
import { useToast } from '@/components/ui/use-toast';

const PREVIEW_REVISION_ID_QUERY = gql`
  query defaultQuery($id: ID!) {
    default: contentNode(id: $id) {
      contentTypeName
      id
      previewRevisionId
      modified
    }
  }
`;

const initialAppState = {
  query: PREVIEW_REVISION_ID_QUERY
} as AppState;

function appStateReducer(state: AppState, action: AppAction) {
  switch (action.type) {
    case 'SET_QUERY':
      const query = (action as AppAction<DocumentNode>).payload
      return {
        ...state,
        ...(action as AppAction<AppState>).payload
      } as AppState;

    case 'SET_REVISION_ID':
      return {
        ...state,
        revisionId: (action as AppAction<string>).payload
      } as AppState;

      case 'SET_DATA':
        return {
          ...state,
          data: (action as AppAction<object>).payload
        } as AppState;

    default:
      return state;
  }
}

function App() {
  const { toast } = useToast();
  const iframeRef = useRef<HTMLIFrameElement>();
  const { previewUrl, nonce, id } = window?.appLocalizer || {};
  const [state, dispatch] = useReducer(appStateReducer, initialAppState);
  const contextValue: AppContextValue = {
    state
  };
  const { data, query, previewSource, revisionId } = state;
  const { data: revisionData, startPolling } = useQuery<{default: ContentNode}>(PREVIEW_REVISION_ID_QUERY, { variables: { id } });
  const { data: queryData } = useQuery<{[k: string]: ContentNode}>(query, { variables: { id: revisionId }, skip: !revisionId });

  console.log('DATA >> ', id, data);
  console.log('QUERY >> ', id, queryData);
  console.log('REVISION >> ', revisionId, revisionData);

  useEffect(() => {
    function handlePostMessage(e: MessageEvent<{ query: DocumentNode }>) {
      const { query } = e.data;

      if (query) {
        console.log('New query recieved...', query, e);
        dispatch({type: 'SET_QUERY', payload: {query, previewSource: e.source}} as AppAction<AppState>);
      }
    }

    window.addEventListener('message', handlePostMessage);

    return () => {
      window.removeEventListener('message', handlePostMessage);
    }

  }, [dispatch]);

  useEffect(() => {
    if (!queryData || queryData.default) return;

    dispatch({ type: 'SET_DATA', payload: queryData } as AppAction<object>);


    toast({
      title: 'Revision loaded.',
      action: <CheckCircle />,
      duration: 8000
    });

  }, [queryData, dispatch]);

  useEffect(() => {
    if (!data) return;

    previewSource?.postMessage(data, '*');

    startPolling(5000);
  }, [data, previewSource, dispatch]);

  useEffect(() => {
    if (revisionData?.default) {
      toast({
        title: 'New revision detected...',
        action: <Loader />,
        duration: 8000
      });
      dispatch({ type: 'SET_REVISION_ID', payload: revisionData.default.previewRevisionId || id } as AppAction<string>);
    }
  }, [id, revisionData, dispatch]);

  return (
    <div className="w-screen h-screen">
      <AppContext.Provider value={contextValue}>
        <iframe ref={iframeRef} src={previewUrl} className="w-full h-full" />
      </AppContext.Provider>
      <Toaster />
    </div>
  )
}
export default App;
