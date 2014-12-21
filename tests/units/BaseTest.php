<?php

namespace Marmotz\WallIrc\tests\units;

use atoum;
use Faker;

class BaseTest extends atoum {
    public function getResourcePath($path)
    {
        return realpath(__DIR__ . '/../resources') . '/' . $path;
    }

    /**
     * @param atoum\test\assertion\manager $assertionManager
     *
     * @return $this
     */
    public function setAssertionManager(atoum\test\assertion\manager $assertionManager = null)
    {
        parent::setAssertionManager($assertionManager)
            ->getAssertionManager()
                ->setHandler(
                    'faker',
                    function ($locale = 'en_US') {
                        return $this->getFaker($locale);
                    }
                )
        ;

        return $this;
    }

    /**
     * @param string $locale
     *
     * @return Faker\Generator
     */
    public function getFaker($locale = 'en_US')
    {
        return Faker\Factory::create($locale);
    }
}
