describe('Módulo de Autenticación', () => {
  it('Debe mostrar la página de inicio de sesión con sus elementos principales', () => {
    cy.visit('/login');
    cy.contains('Iniciar Sesión').should('be.visible');
    cy.get('#email').should('be.visible');
    cy.get('#password').should('be.visible');
    cy.get('button[type="submit"]').should('exist');
  });

  it('Debe rechazar credenciales incorrectas y mostrar error', () => {
    cy.visit('/login');
    cy.get('#email').clear().type('usuario_invalido@mina.com');
    cy.get('#password').clear().type('password_falso');
    cy.get('button[type="submit"]').click();
    cy.url().should('include', '/login');
  });

  it('Debe iniciar sesión exitosamente con credenciales válidas y redirigir al dashboard', () => {
    cy.visit('/login');
    cy.get('#email').clear().type('admin@mina.com');
    cy.get('#password').clear().type('admin123');
    cy.get('button[type="submit"]').click();
    cy.url().should('not.include', '/login');
    cy.contains('Tablero').should('exist');
  });
});
