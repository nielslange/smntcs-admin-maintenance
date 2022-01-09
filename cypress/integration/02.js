describe( 'Tester', () => {
	it( 'cannot login as editor and sees error message', () => {
		cy.viewport( 1000, 1400 );
		cy.loginAsEditor();
		cy.get( '#login_error' ).contains( 'in maintenance mode.' );
	} );
} );
