describe('Módulo de Servicios Externos (CRUD Completo)', () => {
  beforeEach(() => {
    cy.login();
  });

  const choferTest = 'Chofer Test Cypress';
  const placaTest = '4082-CYP';

  it('1. Debe mostrar la vista de Servicios Externos y tarjetas de balance', () => {
    cy.visit('/servicios-externos');
    cy.contains('Pago de Servicios Externos').should('be.visible');
    cy.contains('Total Gastado en Servicios').should('be.visible');
    cy.contains('Registrar Nuevo Servicio').should('be.visible');
  });

  it('2. Debe crear un nuevo registro de servicio externo con cálculo automático', () => {
    cy.visit('/servicios-externos/create');
    cy.contains('Registrar Pago de Servicio Externo').should('exist');

    // Llenar datos de chofer y maquinaria
    cy.get('input[name="chofer_operador"]').clear().type(choferTest);
    cy.get('input[name="placa_maquinaria"]').clear().type(placaTest);
    cy.get('input[name="fecha"]').type('2026-09-11');

    // Cantidad y precio
    cy.get('input[name="cantidad"]').clear().type('4');
    cy.get('input[name="precio_unitario"]').clear().type('150');

    // Guardar
    cy.contains('button[type="submit"]', 'Guardar').click();

    // Validar redirección a la lista y presencia del registro
    cy.visit('/servicios-externos');
    cy.contains(choferTest).should('exist');
    cy.contains(placaTest).should('exist');
    cy.contains('600.00').should('exist');
  });

  it('3. Debe ver el comprobante y recibo oficial del servicio', () => {
    cy.visit('/servicios-externos');
    cy.contains('tr', choferTest).within(() => {
      cy.get('a[title*="Comprobante"]').click();
    });

    cy.contains('COMPROBANTE').should('exist');
    cy.contains(choferTest).should('exist');
    cy.contains(placaTest).should('exist');
    cy.contains('button', 'PDF').should('exist');
  });

  it('4. Debe editar el servicio externo y actualizar la placa y monto', () => {
    cy.visit('/servicios-externos');
    cy.contains('tr', choferTest).within(() => {
      cy.get('a[title*="Editar Servicio"]').click();
    });

    cy.get('input[name="placa_maquinaria"]').clear().type('9999-MOD');
    cy.contains('button[type="submit"]', 'Actualizar').click();

    cy.visit('/servicios-externos');
    cy.contains('9999-MOD').should('exist');
  });

  it('5. Debe eliminar el servicio externo de prueba', () => {
    cy.visit('/servicios-externos');

    cy.contains('tr', choferTest).within(() => {
      cy.get('button[title*="Eliminar"]').click();
    });

    cy.get('#confirm-ok-btn').should('be.visible').click();
    cy.contains('eliminado').should('exist');
    cy.contains(choferTest).should('not.exist');
  });
});
