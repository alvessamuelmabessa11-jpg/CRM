/// <reference types="cypress" />

describe("Brand shell and dashboard", () => {
    describe("as an admin", () => {
        beforeEach(() => cy.setupAdminSession());

        it("shows the church name and slogan in the sidebar", () => {
            cy.visit("/v2/dashboard");
            cy.get("#sidebar .gel-brand-name").should("not.be.empty");
            cy.get("#sidebar .gel-brand-slogan").should("contain", "Chamados para manifestar as virtudes de Deus.");
        });

        it("draws the membership chart and lists quick actions the admin can use", () => {
            cy.visit("/v2/dashboard");
            cy.get("#growthChart .apexcharts-canvas, #growthChart .empty").should("exist");
            cy.get("#quickActionsCard").within(() => {
                cy.contains("a", "Add Person").should("have.attr", "href").and("include", "/PersonEditor.php");
                cy.contains("a", "Add Payment").should("have.attr", "href").and("include", "/finance/pledge/new");
            });
        });

        it("collapses the sidebar to an icon rail and remembers it", () => {
            cy.viewport(1440, 900);
            cy.visit("/v2/dashboard");
            cy.get("#sidebarCollapseToggle").should("have.attr", "aria-pressed", "false").click();
            cy.get("html").should("have.class", "sidebar-collapsed");
            cy.get("#sidebar .nav-link-title").first().should("not.be.visible");
            cy.reload();
            cy.get("html").should("have.class", "sidebar-collapsed");
            cy.get("#sidebarCollapseToggle").should("have.attr", "aria-pressed", "true").click();
            cy.get("html").should("not.have.class", "sidebar-collapsed");
        });
    });

    describe("as a user without finance rights", () => {
        beforeEach(() => cy.setupNoFinanceSession());

        it("offers no finance quick action", () => {
            cy.visit("/v2/dashboard");
            cy.get('.page-body a[href$="/finance/pledge/new"]').should("not.exist");
            cy.get("#depositChartRow").should("not.exist");
        });
    });
});
