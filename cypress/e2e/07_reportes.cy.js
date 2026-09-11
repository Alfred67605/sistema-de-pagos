describe('Módulo de Reportes Ejecutivos', () => {
  beforeEach(() => {
    cy.login();
  });

  it('Debe cargar la vista de reportes con sus botones de exportación y tabs', () => {
    cy.visit('/reportes');
    cy.contains('Reportes del Personal').should('be.visible');
    cy.contains('button', 'Excel').should('be.visible');
    cy.contains('button', 'PDF').should('be.visible');
    cy.contains('button', 'Imprimir').should('be.visible');
    cy.contains('Resumen General').should('be.visible');
  });

  it('Debe alternar entre pestañas de Trabajadores, Bocaminas y Anticipos', () => {
    cy.visit('/reportes');

    // Tab Trabajadores
    cy.contains('button', 'Trabajadores').click();
    cy.get('button.rpt-tab-btn.rpt-active').should('contain', 'Trabajadores');

    // Tab Bocaminas
    cy.contains('button', 'Bocaminas').click();
    cy.get('button.rpt-tab-btn.rpt-active').should('contain', 'Bocaminas');

    // Tab Anticipos
    cy.contains('button', 'Anticipos').click();
    cy.get('button.rpt-tab-btn.rpt-active').should('contain', 'Anticipos');
  });
});
