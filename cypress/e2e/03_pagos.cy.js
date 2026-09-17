describe('Módulo de Pagos y Liquidaciones (CRUD Completo)', () => {
  beforeEach(() => {
    cy.login();
  });

  const numNotaTest = 'TEST-CYP-99';

  it('1. Debe mostrar el historial de pagos y balance de caja', () => {
    cy.visit('/pagos');
    cy.contains('Control de Caja y Pagos').should('be.visible');
    cy.contains('Gastado en Pagos Totales').should('be.visible');
    cy.contains('Procesar Nuevo Pago').should('be.visible');
  });

  it('2. Debe permitir inyectar/recargar fondos a la caja chica', () => {
    cy.visit('/fondos-caja');
    cy.contains('Registrar Recarga de Caja').should('exist');

    // Llenar monto de recarga
    cy.get('#monto').clear().type('5000');
    cy.get('#observacion').clear().type('Fondeo Prueba Cypress');
    cy.get('button[type="submit"]').contains('Confirmar Recarga').click();

    // Validar que se registró en la lista
    cy.visit('/fondos-caja');
    cy.contains('Fondeo Prueba Cypress').should('exist');
    cy.contains('5,000.00').should('exist');
  });

  it('3. Debe procesar un pago de liquidación completo', () => {
    cy.visit('/pagos/crear');
    cy.contains('Procesar Pago').should('exist');

    // Seleccionar trabajador y esperar a que carguen sus datos y contratos
    cy.get('select[name="trabajador_id"]').select(1);
    cy.get('div.item-row', { timeout: 10000 }).should('be.visible');

    // Ingresar número de nota
    cy.get('input[name="numero_nota"]').clear().type(numNotaTest);

    // Llenar cantidad en el ítem de pago
    cy.get('input[name="items[0][cantidad]"]').clear().type('5');

    // Procesar y confirmar pago con click en el botón
    cy.contains('button[type="submit"]', 'Procesar y Confirmar Pago').should('not.be.disabled').click();

    // Debe redirigir al comprobante
    cy.url({ timeout: 15000 }).should('not.include', '/crear');
    cy.contains('Comprobante de Pago', { timeout: 15000 }).should('exist');
    cy.contains(/Juan P[eé]rez/i).should('exist');
    cy.contains('button', 'PDF').should('exist');
    cy.contains('button', 'Excel').should('exist');
  });

  it('4. Debe editar el pago procesado', () => {
    cy.visit('/pagos');
    cy.contains('tr', 'Juan Pérez Mamani').first().within(() => {
      cy.get('a[title*="Editar Pago"]').click();
    });

    cy.url().should('include', '/editar');
    cy.get('input[name="monto_pagado"]').clear().type('2400');
    cy.contains('button[type="submit"]', 'Guardar Cambios').click();

    cy.url().should('include', '/pagos');
    cy.contains('actualizado con éxito').should('exist');
  });

  it('5. Debe eliminar el pago de prueba y restaurar caja', () => {
    cy.visit('/pagos');

    cy.contains('tr', 'Juan Pérez Mamani').first().within(() => {
      cy.get('button[title*="Eliminar Pago"]').click();
    });

    cy.get('#confirm-ok-btn').should('be.visible').click();
    cy.contains('eliminado').should('exist');
  });
});
