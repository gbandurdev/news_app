<?php

namespace App\Tests\Controller\Public;

use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\Routing\RouterInterface;

class CommentsTest extends KernelTestCase
{
    private RouterInterface $router;

    protected function setUp(): void
    {
        self::bootKernel();
        $this->router = static::getContainer()->get('router');
    }

    // Test 1: Check if routes exist
    public function testCommentRoutesExist(): void
    {
        // Test comment creation route exists
        $route = $this->router->getRouteCollection()->get('app_comment_new');
        $this->assertNotNull($route, 'Route app_comment_new should exist');

        // Test AJAX comment route exists
        $route = $this->router->getRouteCollection()->get('app_comment_create_ajax');
        $this->assertNotNull($route, 'Route app_comment_create_ajax should exist');
    }

    // Test 2: Generate URLs for routes
    public function testGenerateCommentUrls(): void
    {
        // Test generating URL for comment form
        $url = $this->router->generate('app_comment_new', ['id' => 123]);
        $this->assertEquals('/comment/new/123', $url);

        // Test generating URL for AJAX comment
        $ajaxUrl = $this->router->generate('app_comment_create_ajax', ['id' => 123]);
        $this->assertEquals('/comment/ajax/new/123', $ajaxUrl);
    }

    // Test 3: Check route requirements
    public function testCommentRouteRequirements(): void
    {
        $route = $this->router->getRouteCollection()->get('app_comment_new');

        if ($route) {
            // Check if route has {id} parameter
            $this->assertContains('id', $route->compile()->getVariables());

            // Check route path
            $this->assertEquals('/comment/new/{id}', $route->getPath());
        }
    }

    // Test 4: Check route methods
    public function testCommentRouteMethods(): void
    {
        $ajaxRoute = $this->router->getRouteCollection()->get('app_comment_create_ajax');

        if ($ajaxRoute) {
            $methods = $ajaxRoute->getMethods();
            $this->assertContains('POST', $methods, 'AJAX route should accept POST method');
        }
    }
}
