<?php

namespace App\Services;

class HelloService
{
    /**
     * Say hello.
     *
     * @param string $firstName
     * @return string $greetings
     */
    public function sayHello($firstName)
    {
        return 'Hello ' . $firstName;
    }

    /**
     * Test fn.
     *
     * @param string $firstName
     * @return string $greetings
     */
    public function testCall($firstName)
    {
        return 'qwe ' . $firstName;
    }
}