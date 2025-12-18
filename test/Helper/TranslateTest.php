<?php

declare(strict_types=1);

namespace LaminasTest\I18n\View\Helper;

use Laminas\I18n\View\Helper\Translate as TranslateHelper;
use Laminas\Translator\TranslatorInterface;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

final class TranslateTest extends TestCase
{
    public TranslateHelper $helper;
    private TranslatorInterface&MockObject $translator;

    protected function setUp(): void
    {
        $this->translator = $this->createMock(TranslatorInterface::class);
        $this->helper     = new TranslateHelper(
            $this->translator,
            'default',
            'en_US',
        );
    }

    public function testDefaultInvokeArguments(): void
    {
        $input    = 'input';
        $expected = 'translated';

        $this->translator->expects(self::once())
            ->method('translate')
            ->with($input, 'default', 'en_US')
            ->willReturn($expected);

        self::assertSame($expected, $this->helper->__invoke($input));
    }

    public function testCustomInvokeArguments(): void
    {
        $input      = 'input';
        $expected   = 'translated';
        $textDomain = 'textDomain';
        $locale     = 'de_DE';

        $this->translator->expects(self::once())
            ->method('translate')
            ->with($input, $textDomain, $locale)
            ->willReturn($expected);

        self::assertSame($expected, $this->helper->__invoke($input, $textDomain, $locale));
    }
}
