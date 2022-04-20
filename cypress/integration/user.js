describe( 'User', () => {
	before( () => {
		cy.viewport( 1000, 1400 );
	} );

	it( 'can login as an admin, create an editor account and activate the plugin', () => {
		cy.loginAsAdmin();
		cy.createEditorAccount();
		cy.activatePlugin();
	} );

	it( 'cannot login as an editor and sees error message', () => {
		cy.loginAsEditor();
		cy.get( '#login_error' ).contains( 'in maintenance mode.' );
	} );

	it( 'can login as an admin and deactivate the plugin', () => {
		cy.loginAsAdmin();
		cy.deactivatePlugin();
	} );

	it( 'can login as an editor', () => {
		cy.loginAsEditor();
		cy.url().should( 'eq', 'http://localhost:8888/wp-admin/profile.php' );
	} );

	it( 'can login as an admin and delete the editor account', () => {
		cy.loginAsAdmin();
		cy.deleteEditorAccount();
	} );
} );
