Cypress.Commands.add( 'loginAsAdmin', () => {
	cy.visit( 'http://localhost:8888/wp-login.php' ).wait( 100 );
	cy.get( '#user_login' ).type( 'admin' );
	cy.get( '#user_pass' ).type( 'password' );
	cy.get( '#wp-submit' ).click();
} );

Cypress.Commands.add( 'loginAsEditor', () => {
	cy.visit( 'http://localhost:8888/wp-login.php' ).wait( 100 );
	cy.get( '#user_login' ).type( 'editor' );
	cy.get( '#user_pass' ).type( 'editor' );
	cy.get( '#wp-submit' ).click();
} );

Cypress.Commands.add( 'createEditorAccount', () => {
	cy.visit( 'http://localhost:8888/wp-admin/user-new.php' ).wait( 100 );
	cy.get( "input[id='user_login']" ).type( 'editor' );
	cy.get( "input[id='email']" ).type( 'editor@example.com' );
	cy.get( "input[id='pass1']" ).clear().type( 'editor' );
	cy.get( "input[class='pw-checkbox']" ).check();
	cy.get( 'input[id="createusersub"]' ).click();
} );

Cypress.Commands.add( 'deleteEditorAccount', () => {
	cy.visit( 'http://localhost:8888/wp-admin/users.php' ).wait( 100 );
	cy.get( "input[id='user-search-input']" ).type( 'editor' );
	cy.get( "input[id='search-submit']" ).click();
	cy.get( "input[id='cb-select-all-1']" ).click();
	cy.get( "select[id='bulk-action-selector-top']" ).select( 'delete' );
	cy.get( '#doaction' ).click();
	cy.get( '#submit' ).click();
} );

Cypress.Commands.add( 'activatePlugin', () => {
	cy.visit( 'http://localhost:8888/wp-admin/customize.php' ).wait( 100 );
	cy.get( '#accordion-section-smntcs_admin_maintenance_section' )
		.click()
		.wait( 500 );
	cy.get( '#_customize-input-smntcs_admin_maintenance_enable' ).check();
	cy.get( '#_customize-input-smntcs_admin_maintenance_uid' ).select( '1' );
	cy.get( '#save' ).click();
} );

Cypress.Commands.add( 'deactivatePlugin', () => {
	cy.visit( 'http://localhost:8888/wp-admin/customize.php' ).wait( 100 );
	cy.get( '#accordion-section-smntcs_admin_maintenance_section' )
		.click()
		.wait( 500 );
	cy.get( '#_customize-input-smntcs_admin_maintenance_enable' ).uncheck();
	cy.get( '#save' ).click();
} );
