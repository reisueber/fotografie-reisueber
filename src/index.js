import React from 'react';
import ReactDOM from 'react-dom';
import App from './App';
import routingStore from './stores/routingStore';

// Stellen Sie sicher, dass der Store korrekt initialisiert wird, bevor die App geladen wird
console.log('Verfügbare Routen:', routingStore.getRegisteredRoutes());

ReactDOM.render(<App />, document.getElementById('root')); 