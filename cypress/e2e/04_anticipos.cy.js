describe('Módulo de Anticipos y Adelantos (Flujo Completo)', () => {
  beforeEach(() => {
    cy.login();
  });

  const observacionTest = 'Adelanto Prueba Cypress 2026';

  it('1. Debe mostrar el historial de anticipos y los filtros', () => {
    cy.visit('/anticipos');
    cy.contains('Historial de Anticipos (Adelantos)').should('be.visible');
    cy.contains('Registrar Anticipo').should('be.visible');
    cy.get('#bocamina_id_filter').should('exist');
    cy.get('#trabajador_id_filter').should('exist');
    cy.get('#estado_filter').should('exist');
  });

  it('2. Debe registrar un nuevo anticipo con días que debe y observación', () => {
    cy.visit('/anticipos');
    cy.contains('Registrar Anticipo').click();
    cy.get('#modalAnticipo').should('be.visible');

    // Seleccionar trabajador
    cy.get('#modalAnticipo select[name="trabajador_id"]').select(1);

    // Fecha de hoy
    cy.get('#modalAnticipo input[name="fecha"]').type('2026-09-11');

    // Monto
    cy.get('#modalAnticipo input[name="monto"]').clear().type('750');

    // Días de trabajo que debe
    cy.get('#modalAnticipo input[name="dias_debe"]').clear().type('2');

    // Observación
    cy.get('#modalAnticipo textarea[name="observacion"]').clear().type(observacionTest);

    // Guardar
    cy.get('#modalAnticipo button[type="submit"]').click();

    // Comprobar que aparece en la tabla
    cy.visit('/anticipos');
    cy.contains(observacionTest).should('exist');
    cy.contains('día(s)').should('exist');
    cy.contains('750.00').should('exist');
  });

  it('3. Debe poder consultar e imprimir el recibo oficial del anticipo', () => {
    cy.visit('/anticipos');
    cy.contains('tr', observacionTest).within(() => {
      cy.get('a[title*="Imprimir"]').invoke('removeAttr', 'target').click();
    });

    // Validar elementos del recibo
    cy.contains('Comprobante de Anticipo').should('exist');
    cy.contains('750.00').should('exist');
    cy.contains('button', 'PDF').should('exist');
  });

  it('4. Debe eliminar el anticipo de prueba y restaurar el saldo', () => {
    cy.visit('/anticipos');

    cy.contains('tr', observacionTest).within(() => {
      cy.get('button[title*="Eliminar"]').click();
    });

    cy.get('#confirm-ok-btn').should('be.visible').click();
    cy.contains('eliminado').should('exist');
    cy.contains(observacionTest).should('not.exist');
  });
});
