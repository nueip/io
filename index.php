<?php
include_once 'vendor/autoload.php';

$data = array(array(111,22,333,444,555));

$io = new \marshung\io\NueipIO();
// $io->export($data, $config = 'AddIns', $builder = 'Excel', $style = 'Nueip');
$io->export($data, $config = 'Empty', $builder = 'Excel', $style = 'Nueip');
