import AlbumsList from './components/albums/AlbumsList';
import AlbumsEdit from './components/albums/AlbumsEdit';
import AlbumsCreate from './components/albums/AlbumsCreate';
import AlbumsShow from './components/albums/AlbumsShow';

export const resources = [
  // Bestehende Ressourcen
  {
    name: 'users',
    // Konfiguration
  },
  // Albums-Ressource hinzufügen
  {
    name: 'albums',
    list: AlbumsList,
    edit: AlbumsEdit,
    create: AlbumsCreate,
    show: AlbumsShow,
  },
  // ... existing code ...
]; 