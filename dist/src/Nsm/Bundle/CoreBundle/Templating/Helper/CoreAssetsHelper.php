<?php

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Nsm\Bundle\CoreBundle\Templating\Helper;

use Symfony\Component\Templating\Asset\PackageInterface;
use Symfony\Component\Templating\Helper\CoreAssetsHelper as BaseCoreAssetsHelper;

/**
 * CoreAssetsHelper helps manage asset URLs.
 *
 * Usage:
 *
 * <code>
 *   <img src="<?php echo $view['assets']->getUrl('foo.png') ?>" />
 * </code>
 *
 * @author Fabien Potencier <fabien@symfony.com>
 * @author Kris Wallsmith <kris@symfony.com>
 */
class CoreAssetsHelper extends BaseCoreAssetsHelper
{
    public $assetMap;

    /**
     * Constructor.
     *
     * @param PackageInterface $defaultPackage The default package
     * @param array            $namedPackages  Additional packages indexed by name
     */
    public function __construct(
        PackageInterface $defaultPackage,
        array $namedPackages = array(),
        $assetMap
    ) {
        parent::__construct($defaultPackage, $namedPackages);
        $this->assetMap = $assetMap;
    }


    /**
     * Returns the public path.
     *
     * Absolute paths (i.e. http://...) are returned unmodified.
     *
     * @param string           $path A public path
     * @param string           $packageName The name of the asset package to use
     * @param string|bool|null $version A specific version
     *
     * @return string A public path which takes into account the base path and URL path
     */
    public function getUrl($path, $packageName = null, $version = null)
    {
        $path = isset($this->assetMap[$path]) ? $this->assetMap[$path] : $path;

        return $this->getPackage($packageName)->getUrl($path, $version);
    }
}
