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

final class LegacyFormHelper
{
    private static $map = [
        'Symfony\Cmf\Bundle\MediaBundle\Form\Type\FileType' => 'cmf_media_file',
        'Symfony\Cmf\Bundle\MediaBundle\Form\Type\ImageType' => 'cmf_media_image',
        'Symfony\Component\Form\Extension\Core\Type\FileType' => 'file',
        'Symfony\Component\Form\Extension\Core\Type\FormType' => 'form',
    ];

    public static function getType($class)
    {
        if (!self::isLegacy()) {
            return $class;
        }

        if (!isset(self::$map[$class])) {
            throw new \InvalidArgumentException(sprintf('Form type with class "%s" can not be found. Please check for typos or add it to the map in LegacyFormHelper', $class));
        }

        return self::$map[$class];
    }

    public static function isLegacy()
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
