<?php 

class PluginAdminCest
{
		public function _before(AcceptanceTester $I)
		{
		}

		public function plugin_shouldBeActivated(AcceptanceTester $I)
		{
			// given
			$plugin_slug = 'aws-sns-producer-for-woocommerce';
			$I->loginAsAdmin();

			// when
			$I->amOnPluginsPage();

			// then
			$I->seePluginInstalled( $plugin_slug );
			$I->seePluginActivated( $plugin_slug );
		}

		public function plugin_shouldCreateWoocommerceSettingsIntegrationTab(AcceptanceTester $I)
		{
			// given
			$I->loginAsAdmin();
			$I->amOnAdminPage('/admin.php?page=wc-settings&tab=integration');

			// when
			$I->click('AWS SNS Topics');

			// then
			$I->see('Set up SNS Topics for WooCommerce events.');
		}
}
