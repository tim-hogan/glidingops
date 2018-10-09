<?php

namespace App\Services\Wgc;

use TestCase;
use Illuminate\Foundation\Testing\DatabaseTransactions;

use App\Models\Organisation;
use App\Models\Flight;
use App\Models\LaunchType;
use App\Models\BillingOption;
use App\Models\Aircraft;
use App\Models\Member;

use App\Services\Wgc\Accounting;

class AccountingTest extends TestCase
{

  use DatabaseTransactions;

  private $organisation;
  private $glider;
  private $launchtype;
  private $billingOption;

  protected function setUp()
  {
    parent::setUp();
    $this->organisation = factory(Organisation::class)->create();
    $this->glider = factory(Aircraft::class)->create([
      'org' => $this->organisation->id,
      'registration' => 'ZK-GGR',
      'rego_short' => 'GGR',
    ]);
    $this->launchtype = factory(LaunchType::class)->create([
      'name' => "Winch",
      'acronym' => "W",
    ]);
    $this->billingOption = factory(BillingOption::class)->create([
      'name' => "Charge P2",
      'bill_pic' => 0,
      'bill_p2' => 1,
      'bill_other' => 0
    ]);
  }

  public function testChargeP2() {
    $flight = factory(Flight::class)->create([
      'org' => $this->organisation->id,
      'glider' => 'GGR',
      'launchtype' => $this->launchtype->id,
      'billing_option' => $this->billingOption->id,
      'pic' => function() {
        return factory(Member::class)->create()->id;
      },
      'p2' => function() {
        return factory(Member::class)->create()->id;
      },
    ]);

    $charges = Accounting::calcFlightCharges($flight);

    $this->assertEquals(1, count($charges));
    $this->assertEquals(2, count($charges[0]));
    $this->assertEquals($flight->p2Member, $charges[0]['member']);
    $this->assertEquals(['glider' => 120, 'winchLaunch' => 45], $charges[0]['charges']);

    $flight->land = ($flight->land - 60 * 60);
    $flight->save();

    $flight = $flight->fresh();

    $charges = Accounting::calcFlightCharges($flight);
    $this->assertEquals(['glider' => 60, 'winchLaunch' => 45], $charges[0]['charges']);
  }
}