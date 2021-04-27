<?php
interface IEnvironment
{
    public function getkey($keyname);

    public function getDatabaseParameters();

    public function dumpAll();
}
?>