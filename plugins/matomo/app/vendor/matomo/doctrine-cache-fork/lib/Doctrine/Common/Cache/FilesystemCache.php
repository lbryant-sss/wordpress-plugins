<?php

namespace Doctrine\Common\Cache;

use const PHP_EOL;
use function fclose;
use function fgets;
use function fopen;
use function is_file;
use function serialize;
use function time;
use function unserialize;
/**
 * Filesystem cache driver.
 */
class FilesystemCache extends \Doctrine\Common\Cache\FileCache
{
    public const EXTENSION = '.doctrinecache.data';
    /**
     * {@inheritdoc}
     */
    public function __construct($directory, $extension = self::EXTENSION, $umask = 02)
    {
        parent::__construct($directory, $extension, $umask);
    }
    /**
     * {@inheritdoc}
     */
    protected function doFetch($id)
    {
        $data = '';
        $lifetime = -1;
        $filename = $this->getFilename($id);
        if (!is_file($filename)) {
            return \false;
        }
        $resource = fopen($filename, 'r');
        $line = fgets($resource);
        if ($line !== \false) {
            $lifetime = (int) $line;
        }
        if ($lifetime !== 0 && $lifetime < time()) {
            fclose($resource);
            return \false;
        }
        while (($line = fgets($resource)) !== \false) {
            $data .= $line;
        }
        fclose($resource);
        $allowedClasses = \false;
        if (!empty($GLOBALS['MATOMO_CACHE_ALLOWED_CLASSES_UNSERIALIZE']) && is_array($GLOBALS['MATOMO_CACHE_ALLOWED_CLASSES_UNSERIALIZE'])) {
            $allowedClasses = $GLOBALS['MATOMO_CACHE_ALLOWED_CLASSES_UNSERIALIZE'];
        }
        return unserialize($data, ['allowed_classes' => $allowedClasses]);
    }
    /**
     * {@inheritdoc}
     */
    protected function doContains($id)
    {
        $lifetime = -1;
        $filename = $this->getFilename($id);
        if (!is_file($filename)) {
            return \false;
        }
        $resource = fopen($filename, 'r');
        $line = fgets($resource);
        if ($line !== \false) {
            $lifetime = (int) $line;
        }
        fclose($resource);
        return $lifetime === 0 || $lifetime > time();
    }
    /**
     * {@inheritdoc}
     */
    protected function doSave($id, $data, $lifeTime = 0)
    {
        if ($lifeTime > 0) {
            $lifeTime = time() + $lifeTime;
        }
        $data = serialize($data);
        $filename = $this->getFilename($id);
        return $this->writeFile($filename, $lifeTime . PHP_EOL . $data);
    }
}
