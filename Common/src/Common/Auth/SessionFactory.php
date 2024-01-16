<?php
declare(strict_types=1);

namespace Common\Auth;

use Exception;
use Interop\Container\ContainerInterface;
use Laminas\Authentication\Storage\Session;
use Laminas\ServiceManager\Factory\FactoryInterface;
use RunTimeException;

class SessionFactory implements FactoryInterface
{
    /**
     * @param ContainerInterface $container
     * @param $requestedName
     * @param array|null $options
     * @return Session
     * @throws Exception
     */
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null): Session
    {
        $sessionName = $container->get('config')['auth']['session_name'] ?? '';
        if (empty($sessionName)) {
            throw new RunTimeException("Missing auth.session_name from config");
        }

        return new Session($sessionName);
    }
}
