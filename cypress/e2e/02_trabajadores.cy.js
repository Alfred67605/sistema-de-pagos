describe('Módulo de Personal y Contratos (CRUD Completo)', () => {
  beforeEach(() => {
    cy.login();
  });

  const workerName = 'Minero Test Cypress';

  it('1. Debe listar el personal y mostrar la tabla', () => {
    cy.visit('/trabajadores');
    cy.contains('Personal y Contratos').should('be.visible');
    cy.get('table').should('exist');
    cy.contains('Nuevo Personal').should('be.visible');
  });

  it('2. Debe crear un nuevo trabajador con todos sus campos', () => {
    cy.visit('/trabajadores');
    cy.contains('button', 'Nuevo Personal').click();
    cy.contains('Ficha de Personal').should('be.visible');

    // Llenar campos obligatorios
    cy.get('#modal_nombre').clear().type(workerName);
    cy.get('#modal_ci').clear().type('9988776-LP');
    cy.get('#modal_telefono').clear().type('70011223');

    // Seleccionar bocamina y rol
    cy.get('#modal_bocamina').select(1);
    cy.get('#modal_rol').select('contratista');

    // Seleccionar tipo de contrato y fecha
    cy.get('#modal_tipo_contrato').select(1);
    cy.get('#modal_fecha_contrato').type('2026-09-11');

    // Tarifa acordada
    cy.get('#modal_tarifa').clear().type('350');

    // Guardar
    cy.get('button[type="submit"]').contains('Guardar Personal').click();

    // Validar presencia
    cy.visit('/trabajadores?buscar=' + encodeURIComponent(workerName));
    cy.contains(workerName).should('exist');
  });

  it('3. Debe editar el trabajador creado y actualizar su tarifa', () => {
    cy.visit('/trabajadores?buscar=' + encodeURIComponent(workerName));

    cy.contains('tr', workerName).within(() => {
      cy.get('button:has(i.fa-pen-to-square)').click();
    });

    cy.contains('Ficha de Personal').should('be.visible');
    cy.get('#modal_tarifa').clear().type('480');

    cy.get('button[type="submit"]').contains('Guardar Personal').click();

    cy.visit('/trabajadores?buscar=' + encodeURIComponent(workerName));
    cy.contains(workerName).should('exist');
    cy.contains('480').should('exist');
  });

  it('4. Debe eliminar el trabajador de prueba', () => {
    cy.visit('/trabajadores?buscar=' + encodeURIComponent(workerName));

    cy.contains('tr', workerName).within(() => {
      cy.get('button:has(i.fa-trash)').click();
    });

    cy.get('#confirm-ok-btn').should('be.visible').click();
    cy.contains('Personal eliminado').should('be.visible');
    cy.contains(workerName).should('not.exist');
  });
});
