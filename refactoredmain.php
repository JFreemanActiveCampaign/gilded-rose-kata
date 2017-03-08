<?php

require_once "vendor/autoload.php";

$days = isset($argv[1]) ? (int) $argv[1] : 1;

GildedRose\RefactoredProgram::main($days);
