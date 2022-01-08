describe("Non-admin", () => {
	it("can login", () => {
		cy.viewport(1000, 1000);
		cy.visit("http://localhost:8888/wp-login.php").wait(500);
		cy.get("#user_login").type("editor");
		cy.get("#user_pass").type("editor");
		cy.get("#wp-submit").click();
		cy.visit("http://localhost:8888/wp-admin/");
	});
});
