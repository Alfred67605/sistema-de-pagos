describe('Módulo de Catálogos: Bocaminas y Tipos de Trabajo (CRUD)', () => {
  beforeEach(() => {
    cy.login();
  });

  const bocaminaTest = 'Bocamina Cypress Norte';
  const tipoTrabajoTest = 'Perforación Profunda Cypress';

  it('1. Debe listar las bocaminas y permitir crear una nueva', () => {
    cy.visit('/bocaminas');
    cy.contains('Administración de Bocaminas').should('be.visible');

    // Abrir modal
    cy.contains('button', 'Nueva Bocamina').click();
    cy.get('input#modal_nombre').clear().type(bocaminaTest);
    cy.get('textarea#modal_descripcion').clear().type('Descripción de prueba automatizada');
    cy.get('button[type="submit"]').contains('Guardar').click();

    // Validar que aparece en la lista
    cy.visit('/bocaminas');
    cy.contains(bocaminaTest).should('exist');
  });

  it('2. Debe editar la bocamina creada', () => {
    cy.visit('/bocaminas');
    cy.contains('div.glass-card', bocaminaTest).within(() => {
      cy.get('button:has(i.fa-pen-to-square)').click();
    });

    cy.get('textarea#modal_descripcion').clear().type('Descripción modificada por Cypress');
    cy.get('button[type="submit"]').contains('Guardar').click();

    cy.visit('/bocaminas');
    cy.contains('Descripción modificada por Cypress').should('exist');
  });

  it('3. Debe eliminar la bocamina de prueba', () => {
    cy.visit('/bocaminas');

    cy.contains('div.glass-card', bocaminaTest).within(() => {
      cy.get('button:has(i.fa-trash)').click();
    });

    cy.get('#confirm-ok-btn').should('be.visible').click();
    cy.contains('Bocamina eliminada').should('exist');
    cy.contains(bocaminaTest).should('not.exist');
  });

  it('4. Debe listar y crear un nuevo Tipo de Trabajo', () => {
    cy.visit('/tipos-trabajo');
    cy.contains('Catálogo de Tipos de Trabajo').should('be.visible');

    cy.contains('button', 'Nuevo Tipo de Trabajo').click();
    cy.get('input#modal_nombre').clear().type(tipoTrabajoTest);
    cy.get('textarea#modal_descripcion').clear().type('Actividad minera de prueba');
    cy.get('button[type="submit"]').contains('Guardar').click();

    cy.visit('/tipos-trabajo');
    cy.contains(tipoTrabajoTest).should('exist');
  });

  it('5. Debe desactivar y eliminar el Tipo de Trabajo de prueba', () => {
    cy.visit('/tipos-trabajo');

    // Primer clic: desactiva (estado -> inactivo)
    cy.contains('tr', tipoTrabajoTest).within(() => {
      cy.get('button[type="submit"]').click();
    });
    cy.get('#confirm-ok-btn').should('be.visible').click();

    cy.visit('/tipos-trabajo');
    cy.contains('tr', tipoTrabajoTest).should('contain', 'Inactivo');

    // Segundo clic: elimina definitivamente
    cy.contains('tr', tipoTrabajoTest).within(() => {
      cy.get('button[type="submit"]').click();
    });
    cy.get('#confirm-ok-btn').should('be.visible').click();

    cy.visit('/tipos-trabajo');
    cy.contains(tipoTrabajoTest).should('not.exist');
  });
});
