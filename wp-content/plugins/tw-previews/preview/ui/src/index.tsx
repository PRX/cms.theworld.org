import React from 'react';
import ReactDOM from 'react-dom/client';
import { ApolloClient, InMemoryCache, ApolloProvider } from '@apollo/client';
import App from './App';
import '@/styles/global.scss';

const { appContainerId, gqlUrl } = window?.appLocalizer || {};
const el = appContainerId && document.getElementById(appContainerId);

const client = new ApolloClient({
  uri: gqlUrl,
  cache: new InMemoryCache(),
});

if (el) {
  ReactDOM.createRoot(el).render(
    <React.StrictMode>
      <ApolloProvider client={client}>
        <App />
      </ApolloProvider>
    </React.StrictMode>
  );
}
