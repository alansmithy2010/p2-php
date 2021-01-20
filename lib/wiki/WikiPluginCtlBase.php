<?php

abstract class WikiPluginCtlBase
{
    protected $isLoaded = false;

    abstract public function save($data);
    abstract public function load();
    abstract public function clear();

    public function setup()
    {
        if (!$this->isLoaded) {
            $this->load();
            $this->isLoaded = true;
        }
    }
}
