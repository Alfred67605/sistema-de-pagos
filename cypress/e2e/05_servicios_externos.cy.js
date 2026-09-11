describe('Módulo de Servicios Externos', () => {
  beforeEach(() => {
    cy.login();
  });

  it('Debe mostrar la vista de Servicios Externos y tarjetas de balance', () => {
    cy.visit('/servicios-externos');
    cy.contains('Pago de Servicios Externos').should('be.visible');
    cy.contains('Total Gastado en Servicios').should('be.visible');
    cy.contains('Registrar Nuevo Servicio').should('be.visible');
  });

  it('Debe navegar a la pantalla de crear nuevo servicio externo', () => {
    cy.visit('/servicios-externos');
    cy.contains('Registrar Nuevo Servicio').click();
    cy.url().should('include', '/servicios-externos/create');
    cy.contains('Registrar Pago de Servicio Externo').should('exist');
    cy.get('select[name="tipo_servicio"]').should('exist');
  });
});
