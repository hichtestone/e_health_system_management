<?php

namespace App\ESM\Service\Utils;

trait RoutableTrait
{
    /**
     * @var string
     */
    protected $route;
    /**
     * @var array
     */
    protected $routeArgs = [];

    public function getRoute(): string
    {
        return $this->route;
    }

    public function setRoute(string $route): void
    {
        $this->route = $route;
    }

    public function getRouteArgs(): array
    {
        return $this->routeArgs;
    }

    public function setRouteArgs(array $routeArgs): void
    {
        $this->routeArgs = $routeArgs;
    }
}
