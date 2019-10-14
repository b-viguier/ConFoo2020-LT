<?php
require __DIR__.'/vendor/autoload.php';

use PhPresent\Adapter;
use PhPresent\Geometry;
use PhPresent\Graphic;
use PhPresent\Presentation;

$bitmapLoader = new Adapter\Imagick\Graphic\BitmapLoader();
$presentation = new Presentation\SlideShow(
    Graphic\Theme::createDefault(),
    new Presentation\Template\Simple\FullscreenColor(Graphic\Color::white())
);

$presentation
    ->addSlide(new \ForumPhp2019\Slides\Mysterious())
    ->addSlide(new Presentation\Template\Simple\TitleAndSubtitle('Lightning Talk', 'ForumPhp 2019'))
    ->addSlide(new Presentation\Template\Simple\BigTitle("What do you do\nwith PHP?"))
    ->addSlide(new Presentation\Template\Simple\BigTitle("Have Fun!"));

$screen = Presentation\Screen::fromSizeWithExpectedRatio(Geometry\Size::fromDimensions(800, 450));
$engine = new Adapter\SDL\Render\Engine($screen);
$drawer = new Graphic\Drawer\Cache(new Adapter\Imagick\Graphic\Drawer());
$engine->start($presentation, $drawer);
