<?php

namespace Kirby\Cms;

use Throwable;
use Kirby\Http\Response as BaseResponse;
use Kirby\Http\Response\Json;
use Kirby\Toolkit\F;

/**
 * Custom response object with a pretty mighty
 * Response::for method, that can convert any relevant
 * CMS object into a proper response and also provides
 * responses for the error and home pages.
 */
class Response extends BaseResponse
{
    public static function errorPage($data = [], $contentType = 'html', $code = 404)
    {
        if ($code < 400 || $code > 600) {
            $code = 500;
        }

        if (is_string($data) === true) {
            $data = ['errorMessage' => $data];
        }

        $data = array_merge([
            'errorCode'    => $code,
            'errorMessage' => null,
            'errorType'    => null
        ], $data);

        if ($errorPage = App::instance()->site()->errorPage()) {
            return static::page($errorPage, $data, $contentType, $code);
        }

        return new Response(':(', 'text/html', 404);
    }

    public static function for($input, $data = [], $contentType = 'html', $code = 200)
    {
        // Empty input
        if (empty($input) === true) {
            return static::errorPage('Not found', $contentType, $code);
        }

        // Responses
        if (is_a($input, BaseResponse::class) === true) {
            return $input;
        }

        // Pages
        if (is_a($input, Page::class)) {
            return static::page($input, $data, $contentType, $code);
        }

        // Exceptions
        if (is_a($input, Throwable::class)) {
            return static::errorPage(array_merge($data, [
                'errorMessage' => $input->getMessage(),
                'errorCode'    => $input->getCode(),
                'errorType'    => get_class($input)
            ]), $contentType, $input->getCode());
        }

        // Simple HTML response
        if (is_string($input) === true) {
            return new static($input);
        }

        // array to json conversion
        if (is_array($input) === true) {
            return static::json($input);
        }

        // Fallback
        return static::errorPage($data, $contentType, $code);
    }

    public static function homePage(array $data = [], $contentType = 'html', $code = 200)
    {
        return static::page(App::instance()->site()->homePage(), $data, $contentType, $code);
    }

    public static function json(array $data = [])
    {
        return new Json($data);
    }

    public static function page(Page $page, array $data = [], $contentType = 'html', $code = 200)
    {
        // render and optionally cache the page
        $result = $page->render($data, $contentType);

        // convert the content representation type to a usable mime type
        $mime = F::extensionToMime($contentType) ?? 'text/html';

        // create the response object for the page
        return new static($result, $mime, $code);
    }
}
