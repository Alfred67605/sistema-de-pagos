describe('Módulo de Pagos y Caja', () => {
  beforeEach(() => {
    cy.login();
  });

  it('Debe mostrar el historial de pagos y las tarjetas de balance de caja', () => {
    cy.visit('/pagos');
    cy.contains('Control de Caja y Pagos').should('be.visible');
    cy.contains('Gastado en Pagos Totales').should('be.visible');
    cy.contains('Gastado en Anticipos').should('be.visible');
    cy.contains('Procesar Nuevo Pago').should('be.visible');
  });

  it('Debe navegar a la pantalla de crear nuevo pago', () => {
    cy.visit('/pagos');
    cy.contains('Procesar Nuevo Pago').click();
    cy.url().should('include', '/pagos/crear');
    cy.contains('Seleccionar Trabajador').should('exist');
  });

  it('Debe permitir acceder al módulo de gestión de fondos de caja', () => {
    cy.visit('/fondos-caja');
    cy.contains('Caja').should('exist');
    cy.contains('Recargar').should('exist');
  });
});
