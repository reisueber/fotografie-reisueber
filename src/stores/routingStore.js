import { routes } from '../routes';

class RoutingStore {
  constructor() {
    // Überprüfen Sie die tatsächliche Implementierung dieser Methode
    this.registerRoutes(routes);
  }
  
  // Die vollständige Implementierung dieser Methode ist wichtig
  registerRoutes(routes) {
    // Hier sollte der Code sein, der die Routen tatsächlich registriert
    // Möglicherweise ist hier ein Fehler
  }

  // Fügen Sie hier eine Hilfsmethode hinzu, um zu überprüfen, ob die Route korrekt registriert wurde
  getRouteByResourceKey(resourceKey) {
    console.log('Verfügbare Ressourcenschlüssel:', this.availableResourceKeys);
    return this.findRouteByResourceKey(resourceKey);
  }
}

export default new RoutingStore(); 