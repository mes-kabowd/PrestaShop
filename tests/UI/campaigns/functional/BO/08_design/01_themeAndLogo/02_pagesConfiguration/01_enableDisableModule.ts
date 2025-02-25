// Import utils
import testContext from '@utils/testContext';

import {expect} from 'chai';
import {
  boDashboardPage,
  boLoginPage,
  boThemeAndLogoPage,
  boThemePagesConfigurationPage,
  type BrowserContext,
  dataModules,
  type Page,
  utilsPlaywright,
} from '@prestashop-core/ui-testing';

const baseContext: string = 'functional_BO_design_themeAndLogo_pagesConfiguration_enableDisableModule';

describe('BO - Design - Theme & Logo : Enable/disable module', async () => {
  let browserContext: BrowserContext;
  let page: Page;

  // before and after functions
  before(async function () {
    browserContext = await utilsPlaywright.createBrowserContext(this.browser);
    page = await utilsPlaywright.newTab(browserContext);
  });

  after(async () => {
    await utilsPlaywright.closeBrowserContext(browserContext);
  });

  it('should login in BO', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'loginBO', baseContext);

    await boLoginPage.goTo(page, global.BO.URL);
    await boLoginPage.successLogin(page, global.BO.EMAIL, global.BO.PASSWD);

    const pageTitle = await boDashboardPage.getPageTitle(page);
    expect(pageTitle).to.contains(boDashboardPage.pageTitle);
  });

  it('should go to \'Design > Theme & Logo\' page', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'goToThemeAndLogoPage', baseContext);

    await boDashboardPage.goToSubMenu(
      page,
      boDashboardPage.designParentLink,
      boDashboardPage.themeAndLogoParentLink,
    );
    await boThemeAndLogoPage.closeSfToolBar(page);

    const pageTitle = await boThemeAndLogoPage.getPageTitle(page);
    expect(pageTitle).to.contains(boThemeAndLogoPage.pageTitle);
  });

  it('should go to \'Pages configuration\' page', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'goToPagesConfigurationPage', baseContext);

    await boThemeAndLogoPage.goToSubTabPagesConfiguration(page);

    const pageTitle = await boThemePagesConfigurationPage.getPageTitle(page);
    expect(pageTitle).to.contains(boThemePagesConfigurationPage.pageTitle);
  });

  [
    {
      args: {
        title: 'disable the module',
        action: 'disable',
      },
    },
    {
      args: {
        title: 'enable the module',
        action: 'enable',
      },
    },
  ].forEach((test) => {
    it(`should ${test.args.title}`, async function () {
      await testContext.addContextItem(this, 'testIdentifier', test.args.action, baseContext);

      const successMessage = await boThemePagesConfigurationPage.setActionInModule(page, dataModules.mainMenu, test.args.action);
      expect(successMessage).to.eq(boThemePagesConfigurationPage.successMessage);
    });
  });
});
