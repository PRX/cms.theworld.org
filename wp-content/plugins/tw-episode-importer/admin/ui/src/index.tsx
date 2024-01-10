import React from 'react';
import ReactDOM from 'react-dom/client';
import App from './App';
import '@/styles/global.scss';

const { appContainerId } = window?.appLocalizer || {};
const el = appContainerId && document.getElementById(appContainerId);

if (el) {
  ReactDOM.createRoot(el).render(
    <React.StrictMode>
      <App />
    </React.StrictMode>
  );
}
