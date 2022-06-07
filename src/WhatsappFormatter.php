<?php

namespace Bagoesz21\LaravelNotifWaWeb;

use Illuminate\Support\Arr;

/**
 * Format text into whatsapp format
 *
 * @see https://faq.whatsapp.com/556797335179788/?locale=id_ID
 */
class WhatsappFormatter
{
    protected $config = [
        'bold' => '*',
        'italic' => '_',
        'strike' => '~',
        'monospace' => '```',
    ];

    public function __construct()
    {
    }

    /**
     * Static
     *
     * @return static
     */
    public static function make(){
        $class = get_called_class();
        return (new $class());
    }

    public function build($text, $format)
    {
        return sprintf("%s$text%s", $format, $format);
    }

    public function bold($text)
    {
        $format = Arr::get($this->config, 'bold');
        return $this->build($text, $format);
    }

    public function italic($text)
    {
        $format = Arr::get($this->config, 'italic');
        return $this->build($text, $format);
    }

    public function strike($text)
    {
        $format = Arr::get($this->config, 'strike');
        return $this->build($text, $format);
    }

    public function monospace($text)
    {
        $format = Arr::get($this->config, 'monospace');
        return $this->build($text, $format);
    }
}
