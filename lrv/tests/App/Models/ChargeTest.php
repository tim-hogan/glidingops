<?php

namespace App\Models;

use TestCase;
use DateTimeImmutable;
use App\Models\Organisation;
use App\Models\Charge;

use Illuminate\Foundation\Testing\DatabaseTransactions;

class ChargeTest extends TestCase
{
    use DatabaseTransactions;

    private $organisation;
    private $winchChargePast;
    private $winchChargeCurrent;
    private $winchChargeFuture;

    private $now;

    protected function setUp()
    {
      parent::setUp();

      $this->organisation = factory(Organisation::class)->create();
      $this->now = new DateTimeImmutable('now', $this->organisation->timeZone());

      $this->winchChargePast = factory(Charge::class)->create([
        'org' => $this->organisation->id,
        'validfrom' => $this->now->sub(date_interval_create_from_date_string('10 days')),
        'amount' => 45,
      ]);
      $this->winchChargeCurrent = factory(Charge::class)->create([
        'org' => $this->organisation->id,
        'validfrom' => $this->now,
        'amount' => 55,
      ]);
      $this->winchChargeFuture = factory(Charge::class)->create([
        'org' => $this->organisation->id,
        'validfrom' => $this->now->add(date_interval_create_from_date_string('10 days')),
        'amount' => 25,
      ]);
    }

    public function testFindByNameUsingDefaultDate() {
      $charge = Charge::findByName('Winch', 'Papawai', $this->organisation);
      $this->assertEquals(Charge::find($this->winchChargeCurrent->id), $charge);
    }

    public function testFindByName() {
      $date = $this->now->sub(date_interval_create_from_date_string('1 day'));
      $charge = Charge::findByName('Winch', 'Papawai', $this->organisation, $date);
      $this->assertEquals(Charge::find($this->winchChargePast->id), $charge);

      $date = $this->now->add(date_interval_create_from_date_string('11 day'));
      $charge = Charge::findByName('Winch', 'Papawai', $this->organisation, $date);
      $this->assertEquals(Charge::find($this->winchChargeFuture->id), $charge);
    }
}