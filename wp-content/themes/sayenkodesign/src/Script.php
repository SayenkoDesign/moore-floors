<?php
namespace Sayenko;

/**
 * Class Stylesheet
 * @package Sayenko
 *
 * create and register stylesheets
 */
class Script
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
    protected $footer = true;

    /**
     * Stylesheet constructor.
     * @param $handle
     * @param $url
     * @param array $deps
     * @param string $version
     */
    public function __construct($handle, $url, $deps = [], $version = false, $footer = true)
    {
        $this->handle = $handle;
        $this->source = $url;
        $this->deps = $deps;
        $this->version = $version;
        $this->footer = $footer;
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
            wp_register_script($this->handle, $this->source, $this->deps, $this->version, $this->media, $this->footer);
            wp_enqueue_script($this->handle);
        });
    }
}