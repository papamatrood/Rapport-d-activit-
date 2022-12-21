import React from 'react';
import ReactDOM from 'react-dom';
import * as ReactDOMClient from 'react-dom/client';
import App from './app';

const root = ReactDOMClient.createRoot(document.getElementById('apiRoot'));
root.render(
  <React.StrictMode>
    <App />
  </React.StrictMode>
);