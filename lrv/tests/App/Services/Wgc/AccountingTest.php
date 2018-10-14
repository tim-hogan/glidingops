<?php

namespace App\Services\Wgc;

use TestCase;
use DateTimeImmutable;
use Illuminate\Foundation\Testing\DatabaseTransactions;

use App\Models\Organisation;
use App\Models\Flight;
use App\Models\LaunchType;
use App\Models\BillingOption;
use App\Models\Aircraft;
use App\Models\Member;
use App\Models\Charge;

use App\Services\Wgc\Accounting;

class AccountingTest extends TestCase
{

  use DatabaseTransactions;

  private $dateTime;
  private $organisation;
  private $glider;
  private $launchtype;
  private $billingOption;
  private $flight;
  private $gliderPerMinuteCharge;
  private $juniorGliderPerMinuteCharge;
  private $WinchCharge;

  protected function setUp()
  {
    parent::setUp();
    $this->organisation = factory(Organisation::class)->create();
    $this->glider = factory(Aircraft::class)->create([
      'org' => $this->organisation->id,
      'registration' => 'ZK-GGR',
      'rego_short' => 'GGR',
      'charge_per_minute' => 1.0,
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

    $this->flight = factory(Flight::class)->create([
      'org' => $this->organisation->id,
      'glider' => 'GGR',
      'launchtype' => $this->launchtype->id,
      'billing_option' => $this->billingOption->id,
      'pic' => function() {
        return factory(Member::class)->create(['org' => $this->organisation->id])->id;
      },
      'p2' => function() {
        return factory(Member::class)->create(['org' => $this->organisation->id])->id;
      },
    ]);
    $this->dateTime = DateTimeImmutable::createFromMutable($this->flight->getStartDateTime());

    $this->flight->picMember->date_of_birth = date(
      'Y-m-d',
      $this->dateTime->sub(date_interval_create_from_date_string('50 years'))->getTimestamp()
    );
    $this->flight->picMember->save();

    $this->flight->p2Member->date_of_birth = date(
      'Y-m-d',
      $this->dateTime->sub(date_interval_create_from_date_string('40 years'))->getTimestamp()
    );
    $this->flight->p2Member->save();

    $this->gliderPerMinuteCharge = factory(Charge::class)->create([
      'org'=> $this->organisation->id,
      'name' => 'Glider per minute',
      'validfrom' => $this->dateTime->sub(date_interval_create_from_date_string('10 days')),
      'amount' => "1.0",
    ]);

    $this->juniorGliderPerMinuteCharge = factory(Charge::class)->create([
      'org'=> $this->organisation->id,
      'name' => 'Junior Glider per minute',
      'validfrom' => $this->dateTime->sub(date_interval_create_from_date_string('10 days')),
      'amount' => "0.5",
    ]);

    $this->winchCharge = factory(Charge::class)->create([
      'org'=> $this->organisation->id,
      'name' => 'Winch',
      'validfrom' => $this->dateTime->sub(date_interval_create_from_date_string('10 days')),
      'amount' => "45",
    ]);

    $this->juniorGliderPerMinuteCharge = factory(Charge::class)->create([
      'org'=> $this->organisation->id,
      'name' => 'Junior Winch',
      'validfrom' => $this->dateTime->sub(date_interval_create_from_date_string('10 days')),
      'amount' => "25",
    ]);
  }

  public function testChargeP2() {
    $this->assertFalse($this->flight->p2Member->isJunior($this->dateTime));
    $charges = Accounting::calcFlightCharges($this->flight);

    $this->assertEquals(1, count($charges));
    $this->assertEquals(2, count($charges[0]));
    $this->assertEquals($this->flight->p2Member, $charges[0]['member']);
    $this->assertEquals(['glider' => 120, 'winchLaunch' => 45], $charges[0]['charges']);

    $this->flight->land = ($this->flight->land - 60 * 60);
    $this->flight->save();

    $this->flight = $this->flight->fresh();

    $charges = Accounting::calcFlightCharges($this->flight);
    $this->assertEquals($this->flight->p2Member, $charges[0]['member']);
    $this->assertEquals(['glider' => 60, 'winchLaunch' => 45], $charges[0]['charges']);
  }

  public function testChargeP2Junior() {
    $this->flight->p2Member->date_of_birth = date(
      'Y-m-d',
      $this->dateTime->sub(date_interval_create_from_date_string('20 years'))->getTimestamp()
    );
    $this->assertTrue($this->flight->p2Member->isJunior($this->dateTime));

    $charges = Accounting::calcFlightCharges($this->flight);

    $this->assertEquals(1, count($charges));
    $this->assertEquals(2, count($charges[0]));
    $this->assertEquals($this->flight->p2Member, $charges[0]['member']);
    $this->assertEquals(['glider' => 60, 'winchLaunch' => 25], $charges[0]['charges']);
  }

  public function testUsesWinchCharge() {
    $this->winchCharge->amount = 40;
    $this->winchCharge->save();

    $charges = Accounting::calcFlightCharges($this->flight);
    $this->assertEquals(1, count($charges));
    $this->assertEquals(2, count($charges[0]));
    $this->assertEquals(40, $charges[0]['charges']['winchLaunch']);
  }
}