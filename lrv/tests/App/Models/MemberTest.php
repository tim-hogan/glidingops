<?php

namespace App\Models;

use TestCase;
use DateTime;
use DateTimeZone;

use App\Models\Member;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class MemberTest extends TestCase
{
    use DatabaseTransactions;

    private $organisation;

    protected function setUp()
    {
        parent::setUp();
        $this->organisation = factory(Organisation::class)->create();
    }

    public function testBirthdayDate()
    {
        $member = factory(Member::class)->create([
            'org' => $this->organisation->id,
            'date_of_birth' => '1915-05-30',
        ]);

        $now = new DateTime('1920-05-29 00:00:00', new DateTimeZone('UTC'));
        $this->assertEquals(4, $member->age($now));

        $now = new DateTime('1920-05-30 00:00:00', new DateTimeZone('UTC'));
        $this->assertEquals(5, $member->age($now));
    }

    public function testIsJunior()
    {
        $member = factory(Member::class)->create([
            'org' => $this->organisation->id,
            'date_of_birth' => '1915-05-30',
        ]);

        $now = new DateTime('1935-05-29 00:00:00', new DateTimeZone('UTC'));
        $this->assertTrue($member->isJunior($now), 'Should be junior until 26 years of age');

        $now = new DateTime('1945-05-30 00:00:00', new DateTimeZone('UTC'));
        $this->assertFalse($member->isJunior($now), 'Should not be junior after 26 years of age');
    }
}