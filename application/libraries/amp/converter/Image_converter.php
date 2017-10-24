<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class Image_converter implements IParser
{
    private $hasObject = false;
    private $header = '';
    private $footer = '';

    public function __construct()
    {
    }

    public function parse($element)
    {
        $newElement = $element->ownerDocument->createElement('amp-img');

        while ($element->hasChildNodes()) {
            $child = $element->childNodes->item(0);
            $child = $element->ownerDocument->importNode($child, true);
            $newElement->appendChild($child);
        }

        $width = 280;
        $src = '';
        $srcWidth = 0;
        $srcHeight = 0;
        foreach ($element->attributes as $attr) {
            $attrName = $attr->nodeName;
            $attrValue = $attr->nodeValue;
            if ($attrName == 'src') {
                $newElement->setAttribute($attrName, $attrValue);
                $src = $attrValue;
            } elseif ($attrName == 'width') {
                $srcWidth = $attrValue;
            } elseif ($attrName == 'height') {
                $srcHeight = $attrValue;
            } elseif ($attrName != 'style') {
                $newElement->setAttribute($attrName, $attrValue);
            }
        }
        if ($srcWidth < 1  || $srcHeight < 1) {
            list($srcWidth, $srcHeight) = getimagesize($src);
        }

        $a = 1;
        if ((isset($srcWidth) && $srcWidth > 0) && (isset($srcHeight) && $srcHeight > 0)) {
            $a = $this->divideFloat($width, $srcWidth, 3);
        }
        $newElement->setAttribute('layout', 'responsive');
        $newElement->setAttribute('width', $width);
        $newElement->setAttribute('height', ($srcHeight*$a));
        $element->parentNode->replaceChild($newElement, $element);
    }

    public function getHeader()
    {
        return $this->header;
    }

    public function getFooter()
    {
        return $this->footer;
    }

    private function divideFloat($a, $b, $precision = 3)
    {
        $a *= pow(10, $precision);
        $result=(int)($a / $b);
        if (strlen($result)==$precision) {
            return '0.' . $result;
        } else {
            return preg_replace('/(\d{' . $precision . '})$/', '.\1', $result);
        }
    }
}
