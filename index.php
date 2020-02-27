<?php
require 'vendor/autoload.php';

$res = get_declared_classes();
$autoloaderClassName = '';
foreach ( $res as $className) {
  if (strpos($className, 'ComposerAutoloaderInit') === 0) {
    $autoloaderClassName = $className;
    break;
  }
}

$classLoader = $autoloaderClassName::getLoader();

foreach ($classLoader->getClassMap() as $path) {
  require_once $path;
}

use Imagine\Image\Box;

function createLogo($mask) {
    $imagine = new Imagine\Gd\Imagine();
    $image = null;

    if (isset($_FILES['file']['tmp_name'])) {
        $image = $imagine->open($_FILES['file']['tmp_name']);
    }
    
    $watermark = $imagine->open($mask);
    $size      = $image->getSize();
    $wSize     = $watermark->getSize();
    
    if ($size->getWidth() < $wSize->getWidth() || $size->getHeight() < $wSize->getHeight()) {
        $maxSize = $size->getWidth() < $size->getHeight() ? $size->getWidth() : $size->getHeight();
        $watermark->resize(new Box($maxSize, $maxSize));
    }
    
    $wSize = $watermark->getSize();
    $center = new Imagine\Image\Point\Center($size);
    $watermarkToCenter = new Imagine\Image\Point($center->getX() - $wSize->getWidth() / 2, $center->getY() - $wSize->getHeight() / 2);
    $image = $image->paste($watermark, $watermarkToCenter)->crop(
        new Imagine\Image\Point($watermarkToCenter->getX(), $watermarkToCenter->getY()),
        new Box($watermarkToCenter->getX() + $wSize->getWidth(), 0 + $wSize->getHeight())
    )->save('img/' . $mask);

    return 'img/' . $mask;
}
?>

<form method="post" enctype="multipart/form-data">
  <input type="file" name="file">
  <input type="submit"> 
</form>

<img src="<?php echo createLogo('logo_w.png'); ?>"><img src="<?php echo createLogo('logo_b.png'); ?>">
