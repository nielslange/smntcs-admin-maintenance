describe( 'Tester', () => {
	beforeEach( function () {
		cy.viewport( 1000, 1400 );
	} );

	it( 'can login as admin, can create an editor account and activate the plugin', () => {
		cy.loginAsAdmin();
		cy.createEditorAccount();
		cy.activatePlugin();
	} );

	it( 'cannot login as editor and sees error message', () => {
		cy.loginAsEditor();
		cy.get( '#login_error' ).contains( 'in maintenance mode.' );
	} );

	it( 'can login as admin and deactivate the plugin', () => {
		cy.loginAsAdmin();
		cy.deactivatePlugin();
	} );

	it( 'can login as editor', () => {
		cy.loginAsEditor();
		cy.url().should( 'eq', 'http://localhost:8888/wp-admin/profile.php' );
	} );

	it( 'can login as admin and delete the editor account', () => {
		cy.loginAsAdmin();
		cy.deleteEditorAccount();
	} );
} );
