describe( 'Tester', () => {
	it( 'can login as admin, can create an editor account and activate the plugin', () => {
		cy.viewport( 1000, 1400 );
		cy.loginAsAdmin();
		cy.createEditorAccount();
		cy.activatePlugin();
	} );
} );
