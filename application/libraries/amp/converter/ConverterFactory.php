<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

interface IParser
{
    public function parse($element);
    public function getHeader();
    public function getFooter();
}

interface IStyle
{
    public function getStyle();
}

class ConverterFactory
{
    private $converter;
    private $css_prefix;
    private $header;
    private $footer;

    public function __construct(array $params)
    {
        $this->css_prefix = $params['css_prefix'];
    }

    public function convert($element)
    {
        switch ($element->nodeName) {
            case 'img':
                $this->getConverter('img')->parse($element);
                break;
            case 'iframe':
                $this->getConverter('youtube')->parse($element);
                break;
            case 'blockquote':
                $this->getConverter('twitter')->parse($element);
                $this->getConverter('instagram')->parse($element);
                break;
            case '#text':
                break;
            default:
                // this for checking inline style
                $this->getConverter('inline')->parse($element);
                break;
        }
    }

    public function getStyle()
    {
        #only inline that have this method
        return $this->getConverter('inline')->getStyle();
    }

    public function getHeader()
    {
        foreach ($this->converter as $key => $value) {
            $this->header[] = $this->getConverter($key)->getHeader();
        }
        array_filter($this->header, function ($value) {
            return $value !== '';
        });
        if (!empty($this->header)) {
            return implode($this->header, PHP_EOL);
        } else {
            return null;
        }
    }
    public function getFooter()
    {
        foreach ($this->converter as $key => $value) {
            $this->footer[] = $this->getConverter($key)->getFooter();
        }
        array_filter($this->footer, function ($value) {
            return $value !== '';
        });
        if (!empty($this->header)) {
            return implode($this->footer, PHP_EOL);
        } else {
            return null;
        }
    }

    private function getConverter($type)
    {
        if (!isset($this->converter[$type])) {
            switch ($type) {
                case 'img':
                    require_once('Image_converter.php');
                    $this->converter[$type] = new Image_converter();
                    break;
                case 'youtube':
                    require_once('Youtube_converter.php');
                    $this->converter[$type] = new Youtube_converter();
                    break;
                case 'twitter':
                    require_once('Twitter_converter.php');
                    $this->converter[$type] = new Twitter_converter();
                    break;
                case 'instagram':
                    require_once('Instagram_converter.php');
                    $this->converter[$type] = new Instagram_converter();
                    break;
                case 'inline':
                    require_once('Inline_style_converter.php');
                    $this->converter[$type] = new Inline_style_converter($this->css_prefix);
                    break;
            }
        }

        return $this->converter[$type];
    }
}
