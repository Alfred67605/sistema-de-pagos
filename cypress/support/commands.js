// Custom command to log in
Cypress.Commands.add('login', (email = 'admin@mina.com', password = 'admin123') => {
  cy.session([email, password], () => {
    cy.visit('/login');
    cy.get('#email').clear().type(email);
    cy.get('#password').clear().type(password);
    cy.get('button[type="submit"]').click();
    cy.url().should('not.include', '/login');
  });
});
