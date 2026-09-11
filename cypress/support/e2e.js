// Custom commands for Cypress
import './commands';

// Ignore uncaught exceptions from 3rd party scripts/animations
Cypress.on('uncaught:exception', (err, runnable) => {
  return false;
});

// Hide fetch/XHR requests from Cypress command log to keep output clean
const app = window.top;
if (app && !app.document.head.querySelector('[data-hide-command-log-request]')) {
  const style = app.document.createElement('style');
  style.innerHTML = '.command-name-request, .command-name-xhr { display: none }';
  style.setAttribute('data-hide-command-log-request', '');
  app.document.head.appendChild(style);
}
