describe( 'Admin', () => {

	before(function () {
		cy.viewport( 1000, 1000 );
		cy.visit( 'http://localhost:8888/wp-login.php' ).wait( 500 );
		cy.get( '#user_login' ).type( 'admin' );
		cy.get( '#user_pass' ).type( 'password' );
		cy.get( '#wp-submit' ).click();
	});

	it( 'can login and ensure the plugin is deactivated', () => {
		cy.viewport( 1000, 1000 );
		cy.visit( 'http://localhost:8888/wp-admin/customize.php' ).wait( 500 );
		cy.get( '#accordion-section-smntcs_admin_maintenance_section' ).click().wait( 500 );
		cy.get( '#_customize-input-smntcs_admin_maintenance_enable' ).uncheck();
		cy.get( '#save' ).click();
	});

});