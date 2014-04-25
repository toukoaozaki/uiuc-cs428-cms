<?php
namespace UiucCms\Bundle\TestUtilityBundle\Tests\Translation\Translator;

use UiucCms\Bundle\TestUtilityBundle\Translation\Translator\IdentityTranslator;

class IdentityTranslatorTest extends \PHPUnit_Framework_TestCase
{
    const IDENTIFIER = 'test.my_resource.text';

    public function testTrans()
    {
        $translator = new IdentityTranslator();
        $this->assertEquals(
            self::IDENTIFIER,
            $translator->trans(self::IDENTIFIER)
        );
    }

    public function testTransChoice()
    {
        $translator = new IdentityTranslator();
        $this->assertEquals(
            self::IDENTIFIER,
            $translator->transChoice(self::IDENTIFIER, 0)
        );
    }
}
