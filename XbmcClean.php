#!/usr/bin/env php
<?php
require_once(dirname(__FILE__) . '/XbmcClean.class.php');
$xc = new XbmcClean();
$filenames = $xc->getViewedFiles();
$args = array();
foreach ($filenames as $filename)
{
	$args[] = escapeshellarg($filename);
}
$output = array();
exec('du -hc ' . implode(' ', $args), $output);
print_r($output);
// foreach ($filenames as $filename)
// {
// 	unlink($filename);
// }