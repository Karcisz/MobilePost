<?php

/*
 * This file is part of the FOSRestBundle package.
 *
 * (c) FriendsOfSymfony <http://friendsofsymfony.github.com/>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace FOS\RestBundle\Routing\Loader;

use Symfony\Component\Config\Loader\Loader;
use Symfony\Component\Finder\Finder;
use Symfony\Component\Routing\RouteCollection;

/**
 * Parse annotated controller classes from all files of a directory.
 *
 * @author Christian Flothmann <christian.flothmann@xabbuh.de>
 */
class DirectoryRouteLoader extends Loader
{
    private $processor;

    public function __construct(RestRouteProcessor $processor)
    {
        $this->processor = $processor;
    }

    /**
     * {@inheritdoc}
     */
    public function load($resource, $type = null)
    {
        if (!is_dir($resource)) {
            throw new \InvalidArgumentException(sprintf('Given resource of type "%s" is no directory.', $resource));
        }

        $collection = new RouteCollection();

        $finder = new Finder();

        foreach ($finder->in($resource)->name('*.php')->files() as $file) {
            $imported = $this->processor->importResource($this, ClassUtils::findClassInFile($file), array(), null, null, 'rest');
            $collection->addCollection($imported);
        }

        return $collection;
    }

    /**
     * {@inheritdoc}
     */
    public function supports($resource, $type = null)
    {
        return 'rest' === $type && is_string($resource) && is_dir($resource);
    }
}