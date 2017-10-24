<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class Twitter_converter implements IParser
{
    private $hasObject = false;
    private $header = '';
    private $footer = '';

    public function __construct()
    {
    }

    public function parse($element)
    {
        $twitterId = null;
        if ($this->checkTwitterEmbed($element)) {
            if ($element->hasChildNodes()) {
                /** @var \DOMNode $node */
                foreach ($element->childNodes as $node) {
                    $twitterId = $this->getTwitetrId($node);
                    if (isset($twitterId)) {
                        break;
                    }
                }
            }

            if (isset($twitterId)) {
                $newElement = $element->ownerDocument->createElement('amp-twitter');
                // Set default
                $newElement->setAttribute('width', '375');
                $newElement->setAttribute('height', '472');
                $newElement->setAttribute('layout', 'responsive');
                $newElement->setAttribute('data-tweetid', $twitterId);
                // append blockquote
                $twitterElement = $element->cloneNode(true);
                $attributes = $twitterElement->attributes;
                foreach ($attributes as $_attribute) {
                    $twitterElement->removeAttribute($_attribute->name);
                }
                $twitterAttr = $element->ownerDocument->createAttribute('placeholder');
                $twitterElement->appendChild($twitterAttr);
                $newElement->appendChild($twitterElement);

                $element->parentNode->replaceChild($newElement, $element);

                $this->hasObject = true;
            }
        }
    }

    public function getHeader()
    {
        if ($this->hasObject) {
            $this->header = '<script async custom-element="amp-twitter" src="https://cdn.ampproject.org/v0/amp-twitter-0.1.js"></script>';
        }

        return $this->header;
    }

    public function getFooter()
    {
        return $this->footer;
    }

    private function getTwitetrId($element)
    {
        $result = null;
        if ($element->nodeName == 'a') {
            $href = $element->getAttribute('href');
            if (stripos($href, 'twitter.com') !== false) {
                // get twitter id
                $parse = explode('/', $href);
                $result = end($parse);
            }
        }

        return $result;
    }

    private function checkTwitterEmbed($element)
    {
        $result = false;
        if ($element->hasAttributes()) {
            $class = $element->getAttribute('class');
            if (stripos($class, 'twitter-tweet') !== false) {
                $result = true;
            }
        }

        return $result;
    }
}
