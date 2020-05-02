<?php 

class PluginAdminCest
{
		public function _before(AcceptanceTester $I)
		{
		}

		public function shouldBeActivated(AcceptanceTester $I)
		{
			// given
			$plugin_slug = 'aws-integration-for-woocommerce';
			$I->loginAsAdmin();
			$I->amOnAdminPage('/');

			// when
			$I->amOnPluginsPage();

			// then
			$I->seePluginInstalled( $plugin_slug );
			$I->seePluginActivated( $plugin_slug );
		}

		public function shouldCreateWoocommerceSettingsIntegrationTab(AcceptanceTester $I)
		{
			// given
			$I->loginAsAdmin();
			$I->amOnAdminPage('/');

			// when
			$I->amOnAdminPage('/admin.php?page=wc-settings&tab=integration');

			// then
			$I->see('AWS Integration');
		}
}
