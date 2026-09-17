describe('Módulo de Comercialización de Minerales (Flujo Completo)', () => {
  beforeEach(() => {
    cy.login();
  });

  const testId = Date.now().toString().slice(-4);
  const proveedorTest = 'ProvCypress_' + testId;

  it('1. Debe cargar la vista de minerales y navegar entre todas las pestañas', () => {
    cy.visit('/transacciones-minerales');
    cy.contains('Compras').should('be.visible');
    cy.contains('Ventas').should('be.visible');
    cy.contains('Stock').should('be.visible');
    cy.contains('Reportes').should('be.visible');

    // Cambiar a Caja
    cy.get('button.m-tab-caja').click();
    cy.contains('Saldo Actual').should('be.visible');

    // Cambiar a Stock
    cy.get('button.m-tab-stock').click();
    cy.contains('Stock').should('exist');

    // Cambiar a Reportes
    cy.get('button.m-tab-reportes').click();
    cy.contains('Reportes').should('exist');
  });

  it('2. Debe registrar un nuevo lote de mineral (Compra)', () => {
    cy.visit('/transacciones-minerales?tab=compras');

    // Abrir modal de compra
    cy.contains('button', 'Nueva Compra').click();
    cy.contains('Información General del Lote').should('be.visible');

    // Llenar datos generales en el modal
    cy.get('input[name="cliente_proveedor"]:visible').clear().type(proveedorTest);
    cy.get('input[name="fecha"]:visible').type('2026-09-11');
    cy.get('select[name="presentacion"]:visible').select('Sacos');

    // Cantidad, pesos y precios
    cy.get('input[name="cantidad"]:visible').clear().type('25');
    cy.get('input[name="peso_bruto"]:visible').clear().type('1200');
    cy.get('input[name="humedad_porcentaje"]:visible').clear().type('5');
    cy.get('input[name="precio_unidad"]:visible').clear().type('2.50');

    // Guardar lote
    cy.contains('button[type="submit"]', 'Guardar Lote').click();

    // Validar que el lote aparece en la tabla
    cy.visit('/transacciones-minerales?tab=compras');
    cy.contains(proveedorTest).should('exist');
  });

  it('3. Debe consultar la Ficha Técnica del lote', () => {
    cy.visit('/transacciones-minerales');
    cy.get('button.m-tab-compras').click();

    cy.get('div[x-show*="compras"]').contains('tr', proveedorTest).within(() => {
      cy.get('button[title*="Ficha Técnica"]').click();
    });

    cy.contains('Ficha Técnica del Lote').should('be.visible');
    cy.contains(proveedorTest).should('exist');
    cy.contains('Cerrar Ficha').click();
  });

  it('4. Debe eliminar el lote de mineral creado', () => {
    cy.visit('/transacciones-minerales');
    cy.get('button.m-tab-compras').click();

    cy.get('div[x-show*="compras"]').contains('tr', proveedorTest).within(() => {
      cy.get('button[title*="Eliminar"]').click();
    });

    cy.get('#confirm-ok-btn').should('be.visible').click();
    cy.contains('eliminada con éxito').should('exist');
    cy.get('div[x-show*="compras"]').find('table tbody').contains('tr', proveedorTest).should('not.exist');
  });
});
