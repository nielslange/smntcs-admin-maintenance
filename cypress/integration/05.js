describe( 'Tester', () => {
	it( 'can login as admin and delete the editor account', () => {
		cy.viewport( 1000, 1400 );
		cy.loginAsAdmin();
		cy.deleteEditorAccount();
	} );
} );
