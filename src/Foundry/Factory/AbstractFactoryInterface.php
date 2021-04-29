<?php

namespace Voltel\ExtraFoundryBundle\Foundry\Factory;

interface AbstractFactoryInterface
{
    public static function getClassName(): string;
}