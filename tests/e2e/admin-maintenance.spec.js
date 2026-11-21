/**
 * Maintenance mode blocks every login except the one granted user. Logins run in fresh visitor contexts; the admin
 * session from global-setup.mjs (a cookie, not a login) keeps the Customizer reachable to switch the mode back off.
 */
import { test, expect } from '@wordpress/e2e-test-utils-playwright';

const EDITOR = {
	username: 'editor',
	email: 'editor@example.com',
	password: 'password',
	roles: [ 'editor' ],
};
const BLOCKED = 'The Administration Screens are currently in maintenance mode.';

async function setMaintenance( page, enabled ) {
	await page.goto(
		'/wp-admin/customize.php?autofocus[control]=smntcs_admin_maintenance_enable'
	);
	const toggle = page.getByLabel( 'Enable Admin Maintenance' );
	await toggle.setChecked( enabled );
	if ( enabled ) {
		await page
			.getByLabel( 'Grant access to' )
			.selectOption( { label: 'admin' } );
	}
	const save = page.locator( '#save' );
	if ( await save.isEnabled() ) {
		await save.click();
		await expect( save ).toHaveValue( 'Published' );
	}
}

async function logIn( browser, { username, password } ) {
	const context = await browser.newContext( {
		storageState: { cookies: [], origins: [] },
	} );
	const page = await context.newPage();
	await page.goto( '/wp-login.php' );
	await page.locator( '#user_login' ).fill( username );
	await page.locator( '#user_pass' ).fill( password );
	await page.locator( '#wp-submit' ).click();
	await page.waitForLoadState();
	return { context, page };
}

test.describe( 'Admin Maintenance', () => {


	test.beforeAll( async ( { requestUtils } ) => {
		await requestUtils.deleteAllUsers();
		await requestUtils.createUser( EDITOR );
	} );

	test.afterAll( async ( { requestUtils } ) => {
		await requestUtils.deleteAllUsers();
	} );

	test.afterEach( async ( { page } ) => {
		await setMaintenance( page, false );
	} );

	// Known bug: the filter uses plugin_basename( __FILE__ ) inside includes/, so WordPress never applies it and the
	// link is missing. Remove fixme once the plugin hooks plugin_action_links_ of the main plugin file.
	test.fixme( 'adds a Settings link to the Customizer control', async ( {
		page,
	} ) => {
		await page.goto( '/wp-admin/plugins.php' );
		const link = page
			.locator( 'tr[data-slug="smntcs-admin-maintenance"]' )
			.getByRole( 'link', { name: 'Settings' } );
		await expect( link ).toHaveAttribute(
			'href',
			/customize\.php\?autofocus(%5B|\[)control(%5D|\])=smntcs_admin_maintenance_enable/
		);
	} );

	test( 'lets everyone log in while maintenance mode is off', async ( {
		browser,
	} ) => {
		const { context, page } = await logIn( browser, EDITOR );
		await expect( page ).toHaveURL( /\/wp-admin\/?$/ );
		await context.close();
	} );

	test( 'blocks other users and lets the granted admin in', async ( {
		browser,
		page,
	} ) => {
		await setMaintenance( page, true );

		const editor = await logIn( browser, EDITOR );
		await expect( editor.page.locator( '#login_error' ) ).toContainText(
			BLOCKED
		);
		await expect( editor.page ).toHaveURL( /wp-login\.php/ );
		await editor.context.close();

		const admin = await logIn( browser, {
			username: 'admin',
			password: 'password',
		} );
		await expect( admin.page ).toHaveURL( /\/wp-admin\/?$/ );
		await admin.context.close();
	} );
} );
