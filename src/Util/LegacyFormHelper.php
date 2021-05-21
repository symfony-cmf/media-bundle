<?php

/*
 * This file is part of the Symfony CMF package.
 *
 * (c) 2011-2017 Symfony CMF
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Symfony\Cmf\Bundle\MediaBundle\Util;

use Symfony\Cmf\Bundle\MediaBundle\Form\Type\FileType;
use Symfony\Cmf\Bundle\MediaBundle\Form\Type\ImageType;
use Symfony\Component\Form\Extension\Core\Type\FileType as SfFileType;
use Symfony\Component\Form\Extension\Core\Type\FormType as SfFormType;

final class LegacyFormHelper
{
    private static $map = [
        FileType::class   => 'cmf_media_file',
        ImageType::class  => 'cmf_media_image',
        SfFileType::class => 'file',
        SfFormType::class => 'form',
    ];

    public static function getType($class): string
    {
        if (!self::isLegacy()) {
            return $class;
        }

        if (!isset(self::$map[$class])) {
            throw new \InvalidArgumentException(sprintf('Form type with class "%s" can not be found. Please check for typos or add it to the map in LegacyFormHelper', $class));
        }

        return self::$map[$class];
    }

    public static function isLegacy(): bool
    {
        return !method_exists('Symfony\Component\Form\AbstractType', 'getBlockPrefix');
    }

    private function __construct()
    {
    }

    private function __clone()
    {
    }
}
