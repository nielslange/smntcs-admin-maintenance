describe( 'Tester', () => {
	it( 'can login as admin and deactivate the plugin', () => {
		cy.viewport( 1000, 1400 );
		cy.loginAsAdmin();
		cy.deactivatePlugin();
	} );
} );
