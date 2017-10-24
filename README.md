# Codeigniter-Simple-HTML-AMP

Simple HTML to AMP implementation on Codeigniter 3.0

## Usage
```php
$this->load->library('amp/Amp_lib', ['css_prefix' => 'amp-inline'], 'amp_lib');
$this->amp_lib->createDocument($html_content);
$amp = $this->amp_lib->convert();

print_r($amp['content']);
print_r($amp['header']);
print_r($amp['footer']);
print_r($amp['style']);
```

## Change Log

#### 1.0.0 - Oct 24, 2017
Initial release.

## Credit
 1. [Codeigniter 3.0.0] (http://www.codeigniter.com)
 1. [html-to-amp] (https://github.com/paulredmond/html-to-amp)
 1. If you think your name should be here, feel free to contact me

## Contact:
 + [Facebook](https://www.facebook.com/pindo.sasongko)
 + [Twitter](https://twitter.com/pindo_s)
 + [LinkedIn](https://www.linkedin.com/in/pindosasongko/)
 + pindosasongko@gmail.com
