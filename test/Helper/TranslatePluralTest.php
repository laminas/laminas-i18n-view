<?php

declare(strict_types=1);

namespace LaminasTest\I18n\View\Helper;

use Laminas\I18n\View\Helper\TranslatePlural as TranslatePluralHelper;
use Laminas\Translator\TranslatorInterface;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

final class TranslatePluralTest extends TestCase
{
    /** @var TranslatePluralHelper */
    public $helper;
    private TranslatorInterface&MockObject $translator;

    protected function setUp(): void
    {
        $this->translator = $this->createMock(TranslatorInterface::class);
        $this->helper     = new TranslatePluralHelper(
            $this->translator,
            'default',
            'en_US',
        );
    }

    public function testDefaultInvokeArguments(): void
    {
        $singularInput = 'singular';
        $pluralInput   = 'plural';
        $numberInput   = 1;
        $expected      = 'translated';

        $this->translator->expects(self::once())
            ->method('translatePlural')
            ->with($singularInput, $pluralInput, $numberInput, 'default', 'en_US')
            ->willReturn($expected);

        self::assertSame($expected, $this->helper->__invoke($singularInput, $pluralInput, $numberInput));
    }

    public function testCustomInvokeArguments(): void
    {
        $singularInput = 'singular';
        $pluralInput   = 'plural';
        $numberInput   = 1;
        $expected      = 'translated';
        $textDomain    = 'textDomain';
        $locale        = 'de_DE';

        $this->translator->expects(self::once())
            ->method('translatePlural')
            ->with($singularInput, $pluralInput, $numberInput, $textDomain, $locale)
            ->willReturn($expected);

        self::assertSame($expected, $this->helper->__invoke(
            $singularInput,
            $pluralInput,
            $numberInput,
            $textDomain,
            $locale
        ));
    }
}
