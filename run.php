#!/usr/bin/env php -d memory_limit=2048M
<?php
require __DIR__.'/vendor/autoload.php';

use PhPresent\Adapter;
use PhPresent\Geometry;
use PhPresent\Graphic;
use PhPresent\Presentation;

$defaultTheme = Graphic\Theme::createDefault();
$myTheme = $defaultTheme
    ->withFontH1(Graphic\RelativeFont::fromFont(
        Graphic\Font::createDefaultSans()->withSize(20),
        100
    ));

$presentation = new Presentation\SlideShow(
    $myTheme,
    new Presentation\Template\Simple\FullscreenColor(Graphic\Color::white())
);

$drawer = new Adapter\Imagick\Graphic\Drawer();

echo "Creating slides…\n";
require __DIR__ . '/slides.php';

$screen = Presentation\Screen::fromSizeWithExpectedRatio(Geometry\Size::fromDimensions(800, 450));
$engine = new Adapter\SDL\Render\Engine($screen);
$engine->start($presentation, $drawer);
