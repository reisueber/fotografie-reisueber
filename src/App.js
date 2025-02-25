import { routes } from './routes';
import { resources } from './resources';

const App = () => {
  return (
    <AdminProvider 
      routes={routes}
      resources={resources}
    >
      {/* Ihre App-Komponenten */}
    </AdminProvider>
  );
};

export default App; 