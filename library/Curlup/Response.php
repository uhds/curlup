<?php

/**
 * curlup
 *
 * @category Curlup
 * @package Curlup
 */

/**
 * Copyright © 2011, Gordon Stratton <gordon.stratton@gmail.com>
 *
 * Permission to use, copy, modify, and/or distribute this software for any
 * purpose with or without fee is hereby granted, provided that the above
 * copyright notice and this permission notice appear in all copies.
 *
 * THE SOFTWARE IS PROVIDED "AS IS" AND THE AUTHOR DISCLAIMS ALL WARRANTIES
 * WITH REGARD TO THIS SOFTWARE INCLUDING ALL IMPLIED WARRANTIES OF
 * MERCHANTABILITY AND FITNESS. IN NO EVENT SHALL THE AUTHOR BE LIABLE FOR
 * ANY SPECIAL, DIRECT, INDIRECT, OR CONSEQUENTIAL DAMAGES OR ANY DAMAGES
 * WHATSOEVER RESULTING FROM LOSS OF USE, DATA OR PROFITS, WHETHER IN AN
 * ACTION OF CONTRACT, NEGLIGENCE OR OTHER TORTIOUS ACTION, ARISING OUT OF
 * OR IN CONNECTION WITH THE USE OR PERFORMANCE OF THIS SOFTWARE.
 */

namespace Curlup;

/**
 * CouchDB HTTP response
 *
 * @category Curlup
 * @package Curlup
 */
class Response extends Message
{
    /**
     * Determine whether or not to throw exceptions on error
     *
     * @var bool
     */
    protected static $throwsExceptions = true;

    /**
     * Create a valid Response object from a raw HTTP message
     *
     * @param string $rawMessage
     * @return Response
     */
    public static function factory($rawMessage)
    {
        $response = new self();

        list($rawHeaders, $rawBody) = explode("\r\n\r\n", $rawMessage, 2);

        $response->setBody($rawBody);

        $headers = explode("\r\n", $rawHeaders);

        list($httpVersion, $statusCode, $reasonPhrase) = explode(
            ' ',
            array_shift($headers),
            3
        );

        if (self::getThrowsExceptions()) {
            if ($statusCode >= 400 && $statusCode <= 599) {
                throw new CouchDbException($rawBody, $statusCode);
            }
        }

        $response->setHttpVersion($httpVersion);
        $response->setResponseCode($statusCode);
        $response->setResponseStatus($reasonPhrase);

        foreach ($headers as $header) {
            list($key, $value) = preg_split('/:\s*/', $header, 2);
            $response->addHeader($key, $value);
        }

        return $response;
    }

    /**
     * Get the state of the throwsExceptions option
     *
     * @return bool
     */
    public static function getThrowsExceptions()
    {
        return self::$throwsExceptions;
    }

    /**
     * Set the state of the throwsExceptions option
     *
     * @param bool $flag
     * @return void
     */
    public static function setThrowsExceptions($flag)
    {
        self::$throwsExceptions = filter_var(
            $flag,
            FILTER_VALIDATE_BOOLEAN
        );
    }
}
