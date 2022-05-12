<?php

function env($name, $defaultValue = null)
{
    return $_ENV[$name] ?? $defaultValue;
}
