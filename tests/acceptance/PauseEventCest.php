<?php
/**
 * Acceptance tests for pausing and resuming cron events.
 */

/**
 * Test class.
 */
class PauseEventCest {
	public function _before( AcceptanceTester $I ) {
		$I->loginAsAdmin();
	}

	public function PausingAnEvent( AcceptanceTester $I ) {
		$row = $I->amWorkingWithACronEvent( 'pause_me_soon' );

		$I->click( 'Pause', $row );
		$I->seeAdminSuccessNotice( 'Paused the pause_me_soon hook.' );
		$I->see( 'Paused', $row );

		$I->click( 'Paused events (1)' );
		$I->see( 'Paused', $row );
		$I->see( 'Edit', $row );
		$I->dontSee( 'Run Now', $row );
		$I->see( 'Resume', $row );
		$I->see( 'Delete', $row );

		$I->click( 'Resume', $row );
		$I->seeAdminSuccessNotice( 'Resumed the pause_me_soon hook.' );
		$I->see( 'Edit', $row );
		$I->see( 'Run Now', $row );
		$I->see( 'Pause', $row );
		$I->dontSee( 'Resume', $row );
		$I->see( 'Delete', $row );
	}
}
