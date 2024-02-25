<?php

declare(strict_types=1);

namespace DWalczyk\SettingBundle\Exception\DataTransformer;

use DWalczyk\SettingBundle\SettingDefinition;

final class TransformException extends \RuntimeException
{
    public function __construct(string $storageValue, SettingDefinition $item, ?\Throwable $previous = null)
    {
        if (null !== $previous) {
            $message = \sprintf(
                'Unable to transform value %s typed "%s" for setting "%s". Reason: %s',
                $storageValue,
                $item->type,
                $item->name,
                $previous->getMessage()
            );
        } else {
            $message = \sprintf(
                'Unable to transform value %s typed "%s" for setting "%s".',
                $storageValue,
                $item->type,
                $item->name,
            );
        }

        parent::__construct($message, previous: $previous);
    }
}
