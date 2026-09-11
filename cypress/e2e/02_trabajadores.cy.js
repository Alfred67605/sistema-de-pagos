describe('Módulo de Personal y Contratos', () => {
  beforeEach(() => {
    cy.login();
  });

  it('Debe listar el personal y mostrar la tabla', () => {
    cy.visit('/trabajadores');
    cy.contains('Personal y Contratos').should('be.visible');
    cy.get('table').should('exist');
    cy.contains('Nuevo Personal').should('be.visible');
  });

  it('Debe abrir el modal de Nuevo Personal', () => {
    cy.visit('/trabajadores');
    cy.contains('button', 'Nuevo Personal').click();
    cy.contains('Ficha de Personal').should('be.visible');
    cy.get('#modal_nombre').should('be.visible');
  });

  it('Debe permitir filtrar el personal por búsqueda', () => {
    cy.visit('/trabajadores');
    cy.get('#buscar').type('Juan');
    cy.url().should('include', '/trabajadores');
  });
});
