<?php

declare(strict_types=1);

namespace DWalczyk\SettingBundle\Exception;

final class DefinitionDoesNotExistException extends \Exception
{
    public function __construct(string $name)
    {
        parent::__construct(\sprintf('Setting definition named "%s" does not exist.', $name));
    }
}
