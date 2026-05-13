<?php

declare(strict_types=1);

namespace Common\Controller\Traits;

use Laminas\Http\Request;
use Laminas\Http\Response;
use Laminas\Session\Container;

/**
 * Manual Post-Redirect-Get implementation used by controllers that previously
 * relied on the discontinued `laminas-mvc-plugin-prg`. Behaves identically:
 *
 *  - On POST: stores submitted data in a session container keyed by the
 *    request URI (1-hop expiration) and returns a 303 redirect to the current
 *    route. Caller should return this Response.
 *  - On GET with previously-stored data: returns the data array and clears
 *    the container.
 *  - On GET with no stored data: returns false.
 *
 * @method \Laminas\Mvc\Controller\Plugin\Redirect redirect()
 * @method \Laminas\Mvc\Controller\Plugin\Params params()
 * @method \Laminas\Mvc\MvcEvent getEvent()
 * @method Request getRequest()
 */
trait PostRedirectGetTrait
{
    private ?Container $prgSessionContainer = null;

    /**
     * @return Response|array<string, mixed>|false
     */
    public function prg(): Response|array|false
    {
        /** @var Request $request */
        $request = $this->getRequest();
        $container = $this->getPrgSessionContainer($request);

        if ($request->isPost()) {
            $container->setExpirationHops(1, 'post');
            $container->post = $request->getPost()->toArray();

            $routeMatch = $this->getEvent()->getRouteMatch();
            $response = $this->redirect()->toRoute(
                $routeMatch->getMatchedRouteName(),
                [],
                ['query' => $this->params()->fromQuery()],
                true,
            );
            $response->setStatusCode(303);
            return $response;
        }

        if ($container->post !== null) {
            $post = $container->post;
            unset($container->post);
            return $post;
        }

        return false;
    }

    /**
     * Visible to tests so they can inject a pre-seeded container.
     */
    public function setPrgSessionContainer(Container $container): void
    {
        $this->prgSessionContainer = $container;
    }

    private function getPrgSessionContainer(Request $request): Container
    {
        if ($this->prgSessionContainer === null) {
            $this->prgSessionContainer = new Container(md5((string) $request->getUri()));
        }
        return $this->prgSessionContainer;
    }
}
