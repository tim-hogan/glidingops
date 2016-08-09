<?php
require_once __DIR__ . '/vendor/autoload.php';

$klein = new \Klein\Klein();

$mapping = [
  "home"            => "home.php",
  "AssignRoles"     => "role_member-list.php",
  "AssignRole"      => "role_member.php",
  "Documents"       => "documents.php",
  "FlightTypes"     => "flighttypes-list.php",
  "FlightType"      => "flighttypes.php",
  "FlyingNow"       => "FlyingNow.php",
  "MessagingPage"   => "MessagingPage.php",
  "AircraftTypes"   => "aircrafttype-list.php",
  "AircraftType"    => "aircrafttype.php",
  "AllAircraft"     => "aircraft-list.php",
  "Aircraft"        => "aircraft.php",
  "AllMembers"      => "members-list.php",
  "BillingOptions"  => "billingoptions-list.php",
  "BillingOption"   => "billingoptions.php",
  "BookingTypes"    => "bookingtypes-list.php",
  "BookingType"     => "bookingtypes.php",
  "DailySheet"      => "dailysheet.php",
  "DutyTypes"       => "dutytypes-list.php",
  "DutyType"        => "dutytypes.php",
  "IncentiveSchemes"  => "incentive_schemes-list.php",
  "IncentiveScheme"   => "incentive_schemes.php",
  "LaunchTypes"       => "launchtypes-list.php",
  "LaunchType"        => "launchtypes.php",
  "Organisations"     => "organisations-list.php",
  "Organisation"      => "organisations.php",
  "Member"            => "members.php",
  "Aircraft"          => "aircraft.php",
  "RegisterMe"        => "Register.php",
  "Roles"             => "roles-list.php",
  "Role"              => "roles.php",
  "Rosters"           => "duty-list.php",
  "Roster"            => "duty.php",
  "SubsToSchemes"     => "scheme_subs-list.php",
  "SubsToScheme"      => "scheme_subs.php",
  "OtherCharges"      => "charges-list.php",
  "OtherCharge"       => "charges.php",
  "TowCharges"        => "towcharges-list.php",
  "TowCharge"         => "towcharges.php",
  "MyFlights"         => "MyFlights.php",
  "ViewBookings"      => "ViewBookings.php",
  "GlideAccounts.csv" => "Treasurer2.php",
  "Engineering.csv"   => "Engineer2.php",
  "OlcFile.igc"       => "igcgenerate.php",
  "PasswordChange"    => "PasswordChange.php",
  "agc"               => "MasterDisplay.php?org=4",
  "cgc"               => "MasterDisplay.php?org=3",
  "ssb"               => "MasterDisplay.php?org=2",
  "wgc"               => "MasterDisplay.php?org=1"
];

$klein->respond('GET', '/', function () {
    require __DIR__ . '/home.php';
});

foreach ($mapping as $uri => $file) {

  $klein->respond('GET', '/' . $uri, function () use($uri, $file) {
    $path = __DIR__ . '/' . $file;
    require $path;
  });
}

$klein->dispatch();