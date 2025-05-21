<?php

namespace App\Tests\Controller\Admin;

use App\Entity\User;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class AdminControllerTest extends WebTestCase
{
    private KernelBrowser $client;
    private User $adminUser;

    protected function setUp(): void
    {
        parent::setUp();

        // Create the client first
        $this->client = static::createClient();

        // The kernel is now booted, so we can access the container
        $container = static::getContainer();

        // Create admin user
        $this->adminUser = $this->createAdminUser($container);
    }

    /**
     * Helper method to create an admin user
     */
    private function createAdminUser($container): User
    {
        $entityManager = $container->get('doctrine.orm.entity_manager');
        $passwordHasher = $container->get(UserPasswordHasherInterface::class);

        // Check if admin already exists
        $userRepository = $entityManager->getRepository(User::class);
        $adminUser = $userRepository->findOneBy(['username' => 'admin_test']);

        if (!$adminUser) {
            // Create a new admin user
            $adminUser = new User();
            $adminUser->setUsername('admin_test');
            $adminUser->setRoles(['ROLE_ADMIN']);

            // Hash the password
            $hashedPassword = $passwordHasher->hashPassword($adminUser, 'test_password');
            $adminUser->setPassword($hashedPassword);

            // Persist to database
            $entityManager->persist($adminUser);
            $entityManager->flush();
        }

        return $adminUser;
    }

    public function testAdminDashboardAccess(): void
    {
        // Login the admin user
        $this->client->loginUser($this->adminUser);

        // Test admin dashboard access
        $this->client->request('GET', '/admin/');
        $this->assertResponseIsSuccessful('Admin should be able to access dashboard');
    }

    public function testAccessDeniedForUnauthenticatedUsers(): void
    {
        // Try to access admin dashboard without authentication
        $this->client->request('GET', '/admin/');

        // Assert that access is denied
        $this->assertResponseStatusCodeSame(302); // Redirect to login
    }
}
