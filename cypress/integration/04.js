describe( 'Tester', () => {
	it( 'can login as editor', () => {
		cy.viewport( 1000, 1400 );
		cy.loginAsEditor();
		cy.url().should( 'eq', 'http://localhost:8888/wp-admin/profile.php' );
	} );
} );
