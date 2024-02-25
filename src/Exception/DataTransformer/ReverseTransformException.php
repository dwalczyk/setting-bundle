<?php

declare(strict_types=1);

namespace DWalczyk\SettingBundle\Exception\DataTransformer;

use DWalczyk\SettingBundle\SettingDefinition;

final class ReverseTransformException extends \RuntimeException
{
    public function __construct(string $value, SettingDefinition $item, ?\Throwable $previous = null)
    {
        if (null !== $previous) {
            $message = \sprintf(
                'Unable to reverse transform value %s typed "%s" for setting "%s". Reason: %s',
                $value,
                $item->type,
                $item->name,
                $previous->getMessage()
            );
        } else {
            $message = \sprintf(
                'Unable to reverse transform value %s typed "%s" for setting "%s".',
                $value,
                $item->type,
                $item->name,
            );
        }

        parent::__construct($message, previous: $previous);
    }
}
