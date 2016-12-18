<?php
/**
 * @license see LICENSE
 */

namespace Hatcher;

use Psr\Http\Message\ResponseInterface;

/**
 * Response builder, inspired by laravel's one
 */
class ResponseBuilder
{
    /**
     * @var AbstractModule
     */
    protected $module;

    /**
     * @var ResponseInterface
     */
    protected $response;

    /**
     * @param ResponseInterface $response
     */
    public function __construct(AbstractModule $module)
    {
        $this->module = $module;
        $this->response = $module->getDI()->get('response');
    }

    /**
     * @return ResponseInterface
     */
    public function getResponse()
    {
        return $this->response;
    }

    /**
     * @param ResponseInterface $response
     */
    public function setResponse(ResponseInterface $response)
    {
        $this->response = $response;
    }

    /**
     * Add a header to the response
     * @param string $headerName
     * @param string $headerValue
     * @return $this
     */
    public function header(string $headerName, string $headerValue)
    {
        $this->response = $this->response->withHeader($headerName, $headerValue);

        return $this;
    }

    /**
     * Set the response status code
     * @param int $status
     * @return $this
     */
    public function statusCode(int $status)
    {
        $this->response = $this->response->withStatus($status);

        return $this;
    }

    /**
     * Set the response to redirect to the given uri
     * @param string $path
     * @param int $status
     * @return $this
     */
    public function redirect(string $path, int $status = 302)
    {
        $this->response = $this->response->withStatus($status)->withHeader('Location', $path);

        return $this;
    }

    /**
     * Generate a uri for the given route and set the response to redirect to this uri
     * @param string $routeName
     * @param int $status
     */
    public function redirectToRoute(string $routeName, int $status = 302)
    {
        // TODO
    }

    public function plainText($text)
    {
        $this->response = $this->response->withHeader('Content-Type', 'text/plain');
        $this->response->getBody()->write($text);

        return $this;
    }

    public function html($text)
    {
        $this->response = $this->response->withHeader('Content-Type', 'text/html');
        $this->response->getBody()->write($text);

        return $this;
    }

    public function view(string $viewName, array $data = [])
    {
        if (isset($viewName{0}) && '@' !== $viewName{0}) {
            $viewName = '@Module:' . $this->module->getName() . '/' . $viewName;
        }
        $viewRendered = $this->module->getApplication()->getDI()->get('view')->render($viewName, $data);


        $this->response = $this->response->withHeader('Content-Type', 'text/html');
        $this->response->getBody()->write($viewRendered);

        return $this;
    }
}
