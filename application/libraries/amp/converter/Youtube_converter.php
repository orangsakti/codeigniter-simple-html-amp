<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class Youtube_converter implements IParser
{
    private $hasObject = false;
    private $header = '';
    private $footer = '';

    public function __construct()
    {
    }

    public function parse($element)
    {
        $embedCode = null;
        if (1 === preg_match('/youtube\.com\/(?:v|embed)\/([a-zA-z0-9_-]+)/i', $element->getAttribute('src'), $match)) {
            $embedCode = $match[1];
        }
        if (isset($embedCode)) {
            $newElement = $element->ownerDocument->createElement('amp-youtube');
            // Set default
            // 16:9 Ratio
            $newElement->setAttribute('data-videoid', $embedCode);
            $newElement->setAttribute('layout', 'responsive');
            $newElement->setAttribute('width', '560');
            $newElement->setAttribute('height', '315');

            if ($element->hasAttributes()) {
                $newElement->setAttribute('width', $element->getAttribute('width'));
                $newElement->setAttribute('height', $element->getAttribute('height'));
            }
            $element->parentNode->replaceChild($newElement, $element);

            $this->hasObject = true;
        }
    }

    public function getHeader()
    {
        if ($this->hasObject) {
            $this->header = '<script async custom-element="amp-youtube" src="https://cdn.ampproject.org/v0/amp-youtube-0.1.js"></script>';
        }

        return $this->header;
    }

    public function getFooter()
    {
        return $this->footer;
    }
}
