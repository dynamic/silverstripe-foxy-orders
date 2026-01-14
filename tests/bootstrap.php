<?php

use SilverStripe\Core\CoreKernel;

// Resolve the Silverstripe CMS bootstrap file
$bootstrapPaths = [
    __DIR__ . '/../vendor/silverstripe/cms/tests/bootstrap.php', // Standalone (project root)
    __DIR__ . '/../../../silverstripe/cms/tests/bootstrap.php',  // Vendor (installed in project)
];

$bootstrapFile = null;
foreach ($bootstrapPaths as $path) {
    if (file_exists($path)) {
        $bootstrapFile = $path;
        break;
    }
}

if (!$bootstrapFile) {
    throw new Exception("Could not verify usage of silverstripe/cms. Please ensure it is installed.");
}

require $bootstrapFile;

// Note: Config/Kernel boot is handled by SapphireTest in the test suite
