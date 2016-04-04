<?php
namespace Sayenko;

/**
 * Class Stylesheet
 * @package Sayenko
 *
 * create and register stylesheets
 */
class Stylesheet
{
    const MEDIA_ALL = "all";
    const MEDIA_SCREEN = "screen";
    const MEDIA_HANDHELD = "handheld";
    const MEDIA_LIST = "list";
    protected $handle;
    protected $source;
    protected $deps = [];
    protected $version = false;
    protected $media = 'all';

    /**
     * Stylesheet constructor.
     * @param $handle
     * @param $url
     * @param array $deps
     * @param string $version
     */
    public function __construct($handle, $url, $deps = [], $version = false)
    {
        $this->handle = $handle;
        $this->source = $url;
        $this->deps = $deps;
        $this->version = $version;
    }

    /**
     * @return mixed
     */
    public function getHandle()
    {
        return $this->handle;
    }

    /**
     *
     */
    public function alwaysUpdate()
    {
        $this->version = sha1(time());
    }

    /**
     *
     */
    public function register()
    {
        add_action('wp_enqueue_scripts', function() {
            wp_register_style($this->handle, $this->source, $this->deps, $this->version, $this->media);
            wp_enqueue_style($this->handle);
        });
    }
}