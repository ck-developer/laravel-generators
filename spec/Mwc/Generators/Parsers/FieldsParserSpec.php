<?php

namespace spec\Mwc\Generators\Parsers;

use PhpSpec\ObjectBehavior;
use Prophecy\Argument;

class FieldsParserSpec extends ObjectBehavior
{
    function it_is_initializable()
    {
        $this->shouldHaveType('Mwc\Generators\Parsers\FieldsParser');
    }

    function it_parses_a_string_of_fields()
    {
        $this->parse('name:string')->shouldReturn([
            ['field' => 'name', 'type' => 'string']
        ]);

        $this->parse('name:string(255)')->shouldReturn([
            ['field' => 'name', 'type' => 'string', 'length' => 255]
        ]);

        $this->parse('name:string(255):unique')->shouldReturn([
            ['field' => 'name', 'type' => 'string', 'length' => 255, 'decorators' => ['unique']]
        ]);
    }
}
