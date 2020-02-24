<?php

namespace ConFoo2020\Slides;

use PhPresent\Geometry\Size;
use PhPresent\Geometry\Vector;
use PhPresent\Graphic;
use PhPresent\Presentation\Frame;
use PhPresent\Presentation\Screen;
use PhPresent\Presentation\Slide;
use PhPresent\Presentation\Sprite;
use PhPresent\Presentation\Timestamp;

class Fun implements Slide
{
    public function __construct(string $text, Graphic\Bitmap $bitmap)
    {
        $this->text = $text;
        $this->bitmap = $bitmap;
    }

    public function preload(Screen $screen, Graphic\Drawer $drawer, Graphic\Theme $theme): void
    {
        // Initial guess
        $font = Graphic\Font::createDefaultSans()
            ->withStyle(Graphic\Font::STYLE_BOLD)
        ;
        $text = $drawer->createText($this->text, $font);

        // Fix font size to fit the screen
        $font = $font->withSize(
            $font->size() * min(
                $screen->safeArea()->size()->height() / $text->area()->size()->height(),
                $screen->safeArea()->size()->width() / $text->area()->size()->width()
            )
        );
        $text = $drawer->createText($this->text, $font);
        $bitmap = $drawer->drawText($text)->toBitmap($text->area()->size());

        // Centering
        $rect = $text->area()->centeredOn($screen->safeArea()->center());
        $this->frame = new Frame(
            Sprite::fromBitmap($bitmap)->moved($rect->topLeft())
        );

        // Credits
        $drawer->clear();
        $font = Graphic\Font::createDefaultMono()
            ->withSize($font->size() / 5)
            ->withBrush($font->brush()
                ->withFillColor($urlColor = Graphic\Color::blue())
                ->withStrokeColor($urlColor)
            )
            ->withAlignment(Graphic\Font::ALIGN_CENTER);

        $text = $drawer->createText("@b_viguier", $font);
        $bitmap = $drawer->drawText($text)
            ->toBitmap($text->area()->size());
        $this->frame = $this->frame->withPushedSprites(
            Sprite::fromBitmap($bitmap)->moved(
                $screen->fullArea()->bottomRight()->movedBy($bitmap->size()->toVector()->scaledBy(-1)))
        );
    }

    public function render(Timestamp $timestamp, Screen $screen, Graphic\Drawer $drawer, Graphic\Theme $theme)
    {
        $logoWidth = $screen->safeArea()->size()->width() / self::NB_IMG;
        $logoHeight = $this->bitmap->size()->height() * $logoWidth / $this->bitmap->size()->width();
        $baseSprite = Sprite::fromBitmap($this->bitmap)
            ->resized(Size::fromDimensions(
                $logoWidth,
                $logoHeight
            ))->moved($screen->safeArea()->topLeft()->movedBy(
                Vector::fromCoordinates(0, $screen->safeArea()->size()->height() - $logoHeight))
            )
        ;

        $maxJumpHeight = $screen->safeArea()->size()->height() / 3;

        while(true) {
            $duration = $timestamp->slideRelative() ;
            $height = -abs(sin($duration)) * $maxJumpHeight;

            $sprites= [];
            for($i = 0; $i < self::NB_IMG; ++$i) {
                $sprites[] = $baseSprite->moved(
                    $baseSprite->origin()->movedBy(Vector::fromCoordinates(
                        $i * $logoWidth,
                        -abs(sin($duration / (300 + 100 * (self::NB_IMG - $i) / self::NB_IMG))) * $maxJumpHeight
                    ))
                );
            }

            $timestamp = yield $this->frame->withPushedSprites(
                ...$sprites
            );
        }
    }

    /** @var Frame */
    private $frame;
    /** @var string */
    private $text;
    /** @var Graphic\Bitmap */
    private $bitmap;

    private const NB_IMG = 16;
}
