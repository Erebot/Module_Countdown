<?php

/**
 * Extra package.xml settings such as dependencies.
 * More information: http://pear.php.net/manual/en/pyrus.commands.make.php#pyrus.commands.make.packagexmlsetup
 */

$apiVersion = "0.1.0";

$deps = array(
    'required' => array(
        'pear.erebot.net/Erebot',
    ),
    'optional' => array(
    ),
);

// This only applies to Pyrus (PEAR2).
$package->dependencies['required']->pearinstaller->min = '2.0.0a3';

foreach (array($package, $compatible) as $obj) {
    $obj->dependencies['required']->php->min = '5.2.2';
    $obj->stability['api'] = 'stable';
    $obj->license['name'] = 'GPL';
    $obj->license['uri'] = 'http://www.gnu.org/licenses/gpl-3.0.txt';

    // Add dependency on pear.erebot.net/Erebot_API with fixed version.
    $obj->dependencies['required']
        ->package['pear.erebot.net/Erebot_API']
        ->min($apiVersion)
        ->max($apiVersion)
        ->save();

    // Add other dependencies.
    foreach ($deps as $req => $data)
        foreach ($data as $dep)
            $obj->dependencies[$req]->package[$dep]->save();
}

