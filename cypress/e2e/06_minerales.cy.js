describe('Módulo de Comercialización de Minerales', () => {
  beforeEach(() => {
    cy.login();
  });

  it('Debe cargar la vista de minerales con sus pestañas y KPIs de caja', () => {
    cy.visit('/transacciones-minerales');
    cy.contains('Caja').should('be.visible');
    cy.contains('Compras').should('be.visible');
    cy.contains('Ventas').should('be.visible');
    cy.contains('Stock').should('be.visible');
    cy.contains('Reportes').should('be.visible');

    // Cambiar a pestaña Caja para verificar KPIs
    cy.get('button.m-tab-caja').click();
    cy.contains('Saldo Actual').should('be.visible');
  });

  it('Debe cambiar entre las pestañas de Compras, Ventas y Stock', () => {
    cy.visit('/transacciones-minerales');
    
    // Cambiar a Compras
    cy.get('button.m-tab-compras').click();
    cy.contains('Compras').should('exist');

    // Cambiar a Ventas
    cy.get('button.m-tab-ventas').click();
    cy.contains('Ventas').should('exist');

    // Cambiar a Stock
    cy.get('button.m-tab-stock').click();
    cy.contains('Stock').should('exist');
  });
});
