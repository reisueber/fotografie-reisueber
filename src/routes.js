import Dashboard from './components/Dashboard';
import AlbumsPage from './components/albums/AlbumsPage';

const routes = [
  // Bestehende Routen
  {
    path: '/dashboard',
    component: Dashboard,
  },
  // Neue Route für albums hinzufügen
  {
    path: '/albums',
    resourceKey: 'albums',
    component: AlbumsPage,
  },
  // ... existing code ...
]; 

export { routes }; 