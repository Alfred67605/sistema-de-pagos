describe('Módulo de Anticipos y Adelantos', () => {
  beforeEach(() => {
    cy.login();
  });

  it('Debe mostrar el historial de anticipos y los filtros', () => {
    cy.visit('/anticipos');
    cy.contains('Historial de Anticipos (Adelantos)').should('be.visible');
    cy.contains('Registrar Anticipo').should('be.visible');
    cy.get('#bocamina_id_filter').should('exist');
    cy.get('#trabajador_id_filter').should('exist');
    cy.get('#estado_filter').should('exist');
  });

  it('Debe abrir el modal de registro de anticipo con los campos de días debe y observación', () => {
    cy.visit('/anticipos');
    cy.contains('Registrar Anticipo').click();
    cy.get('#modalAnticipo').should('be.visible');
    cy.get('select[name="trabajador_id"]').should('be.visible');
    cy.get('input[name="monto"]').should('be.visible');
    cy.get('input[name="dias_debe"]').should('be.visible');
    cy.get('textarea[name="observacion"]').should('be.visible');
  });

  it('Debe permitir cerrar el modal al pulsar Cancelar', () => {
    cy.visit('/anticipos');
    cy.contains('Registrar Anticipo').click();
    cy.contains('button', 'Cancelar').click();
    cy.get('#modalAnticipo').should('not.be.visible');
  });
});
