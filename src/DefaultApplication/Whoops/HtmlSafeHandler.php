<?php
/**
 * @license see LICENSE
 */

namespace Hatcher\DefaultApplication\Whoops;

use Hatcher\Application;
use Whoops\Handler\Handler;

/**
 * A handler for whoops that will return a nice error message for the user,
 * without any development information (that's why it's called safe)
 */
class HtmlSafeHandler extends Handler
{

    /**
     * @var Application
     */
    protected $application;

    /**
     * HtmlSafeHandler constructor.
     * @param Application $application
     */
    public function __construct(Application $application)
    {
        $this->application = $application;
    }


    public function handle()
    {

        require $this->application->resolvePath('templates/error.html');

        return Handler::QUIT;
    }
}
