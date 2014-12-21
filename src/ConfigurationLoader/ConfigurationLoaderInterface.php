<?php

namespace Marmotz\WallIrc\ConfigurationLoader;

interface ConfigurationLoaderInterface {
    public function loadFrom($from);
    public function getData();
}
