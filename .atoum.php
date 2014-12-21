<?php

use mageekguy\atoum;
use mageekguy\atoum\visibility\extension as visibilityExtension;

$report = $script->addDefaultReport();

$runner->addTestsFromDirectory(__DIR__ . '/tests');

$runner->addExtension(new visibilityExtension($script));

// CODE COVERAGE SETUP
$coverageField = new atoum\report\fields\runner\coverage\html('WallIrc', __DIR__ . '/tests/coverage');
$coverageField->setRootUrl('file://' . __DIR__ . '/tests/coverage');

$report->addField($coverageField);
