<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class Instagram_converter implements IParser
{
    private $hasObject = false;
    private $header = '';
    private $footer = '';

    public function __construct()
    {
    }

    public function parse($element)
    {
        $instagramId = null;
        if ($this->checkInstagramEmbed($element)) {
            $instagramId = $this->getInstagramId($element);

            if (isset($instagramId)) {
                $newElement = $element->ownerDocument->createElement('amp-instagram');
                // Set default
                $newElement->setAttribute('width', '400');
                $newElement->setAttribute('height', '400');
                $newElement->setAttribute('layout', 'responsive');
                $newElement->setAttribute('data-shortcode', $instagramId);
                // append blockquote

                $element->parentNode->replaceChild($newElement, $element);

                $this->hasObject = true;
            }
        }
    }

    public function getHeader()
    {
        if ($this->hasObject) {
            $this->header = '<script async custom-element="amp-instagram" src="https://cdn.ampproject.org/v0/amp-instagram-0.1.js"></script>';
        }

        return $this->header;
    }

    public function getFooter()
    {
        return $this->footer;
    }

    private function getInstagramId($element)
    {
        $result = null;
        if ($element->nodeName == 'a') {
            $href = $element->getAttribute('href');
            if ($href != '') {
                if (1 === preg_match('/(?:instagr\.am|instagram\.com)\/p\/([^\/]+)\/?$/i', $href, $matches)) {
                        return $matches[1];
                }
            }
        }

        if ($element->hasChildNodes()) {
            foreach ($element->childNodes as $child) {
                $instagramId = $this->getInstagramId($child);
                if ($instagramId !== null) {
                    return $instagramId;
                }
            }
        }
        return $result;
    }

    private function checkInstagramEmbed($element)
    {
        $result = false;
        if ($element->hasAttributes()) {
            $class = $element->getAttribute('class');
            if (stripos($class, 'instagram-media') !== false) {
                $result = true;
            }
        }

        return $result;
    }
}
