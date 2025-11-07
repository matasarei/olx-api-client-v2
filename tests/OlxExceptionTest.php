<?php

use Gentor\Olx\Api\OlxException;
use PHPUnit\Framework\TestCase;

class OlxExceptionTest extends TestCase
{
    public function testGetDetailsAsString()
    {
        $details = (object)[
            'error' => 'invalid_request',
            'error_description' => 'Missing parameter: "code" is required',
            'validation' => [
                (object)[
                    'field' => 'title',
                    'title' => 'Musisz podać tytuł',
                    'detail' => 'Musisz podać tytuł'
                ],
                (object)[
                    'field' => 'description',
                    'title' => 'Musisz podać opis',
                    'detail' => 'Musisz podać opis'
                ]
            ]
        ];

        $exception = new OlxException('Test exception', 400, $details);

        $expectedString = <<<EOT
error: invalid_request
error_description: Missing parameter: "code" is required
validation.0.field: title
validation.0.title: Musisz podać tytuł
validation.0.detail: Musisz podać tytuł
validation.1.field: description
validation.1.title: Musisz podać opis
validation.1.detail: Musisz podać opis

EOT;

        $this->assertEquals($expectedString, $exception->getDetailsAsString());
    }
}
