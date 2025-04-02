import { test, expect } from '@playwright/test';
import { exec } from 'child_process';
import util from 'util';

const execPromise = util.promisify( exec );

test.describe( 'SMNTCS Maintenance Mode', () => {
	test.beforeAll( async () => {
		await execPromise(
			'wp-env run cli wp user create editor editor@example.com --role=editor --user_pass=password'
		);
	} );

	test.afterAll( async () => {
		await execPromise( 'wp-env run cli wp user delete editor --yes' );
	} );

	test( 'Check restrictions', async ( { page } ) => {
		await test.step( 'Admin can log in and enable maintenance mode', async () => {
			await page.goto( 'http://localhost:8888/wp-login.php' );
			await page.fill( '#user_login', 'admin' );
			await page.fill( '#user_pass', 'password' );
			await page.click( '#wp-submit' );
			await expect( page.locator( 'body' ) ).toContainText( 'Dashboard' );

			await page.goto(
				'http://localhost:8888/wp-admin/customize.php?autofocus[control]=smntcs_admin_maintenance_enable'
			);
			await page.getByLabel( 'Enable Admin Maintenance' ).check();
			await page
				.getByLabel( 'Grant access to' )
				.selectOption( { label: 'admin' } );
			await page.click( '#save' );
		} );

		await test.step( 'Editor cannot log in and sees restriction message', async () => {
			await page.goto( 'http://localhost:8888/wp-login.php' );
			await page.fill( '#user_login', 'editor' );
			await page.fill( '#user_pass', 'password' );
			await page.click( '#wp-submit' );

			await expect( page.locator( 'body' ) ).toContainText(
				'The Administration Screens are currently in maintenance mode. Please try again later.'
			);
		} );

		await test.step( 'Admin can still log in and access dashboard', async () => {
			await page.goto( 'http://localhost:8888/wp-login.php' );
			await page.fill( '#user_login', 'admin' );
			await page.fill( '#user_pass', 'password' );
			await page.click( '#wp-submit' );
			await expect( page.locator( 'body' ) ).toContainText( 'Dashboard' );
		} );
	} );
} );
