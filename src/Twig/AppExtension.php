<?php
/*
 * Copyright (c) 2020. | All Rights Reserved
 * Ce code source est la propriété de <faso-dev> http://faso-dev.com
 * Ce code source ne saurait être reproduit sans une autorisation expresse de <faso-dev>
 * @Author <faso-dev> jeromeonadja28@gmail.com
 */

namespace App\Twig;

use Symfony\Component\HttpFoundation\RequestStack;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

/**
 * Class AppExtension
 * @package App\Twig
 */
class AppExtension extends AbstractExtension
{
    /**
     * @var RequestStack
     */
    private $request;

    /**
     * ActiveNavBarExtension constructor.
     * @param RequestStack $request
     */
    public function __construct(RequestStack $request)
    {
        $this->request = $request;
    }

    /**
     * {@inheritdoc}
     */
    public function getFunctions(): array
    {
        return [
            new TwigFunction('active_route', [$this, 'setActiveRoute']),
            new TwigFunction('isActive', [$this, 'isActive']),
        ];
    }

    /**
     * The default class that will be used when when no class is passed
     * as an argument is active the boostrap active classe .active
     * @param string|array $route The route or route array to check to apply the active class
     * @param string|null $activeClass The css class that will be used when the current route matches the active menu
     * @return string|null Return the css class if the route matches, null otherwise
     */
    public function setActiveRoute($route, ?string $activeClass = 'active')
    {
        return $this->isActive($route) ? $activeClass : '';
    }

    /**
     * @param $route
     * @return bool
     */
    public function isActive($route)
    {
        $activeRoute = $this->request->getCurrentRequest()->attributes->get('_route');

        if (is_string($route)){
            return $activeRoute  === $route;
        }
        return in_array($activeRoute, $route);
    }
}