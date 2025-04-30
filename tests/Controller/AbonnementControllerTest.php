<?php

namespace App\Tests\Controller;

use App\Entity\Abonnement;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\EntityRepository;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

final class AbonnementControllerTest extends WebTestCase
{
    private KernelBrowser $client;
    private EntityManagerInterface $manager;
    private EntityRepository $abonnementRepository;
    private string $path = '/abonnement/';

    protected function setUp(): void
    {
        $this->client = static::createClient();
        $this->manager = static::getContainer()->get('doctrine')->getManager();
        $this->abonnementRepository = $this->manager->getRepository(Abonnement::class);

        foreach ($this->abonnementRepository->findAll() as $object) {
            $this->manager->remove($object);
        }

        $this->manager->flush();
    }

    public function testIndex(): void
    {
        $this->client->followRedirects();
        $crawler = $this->client->request('GET', $this->path);

        self::assertResponseStatusCodeSame(200);
        self::assertPageTitleContains('Abonnement index');

        // Use the $crawler to perform additional assertions e.g.
        // self::assertSame('Some text on the page', $crawler->filter('.p')->first());
    }

    public function testNew(): void
    {
        $this->markTestIncomplete();
        $this->client->request('GET', sprintf('%snew', $this->path));

        self::assertResponseStatusCodeSame(200);

        $this->client->submitForm('Save', [
            'abonnement[dateInitiale]' => 'Testing',
            'abonnement[dateExpiration]' => 'Testing',
            'abonnement[type]' => 'Testing',
            'abonnement[prix]' => 'Testing',
            'abonnement[modePaiement]' => 'Testing',
            'abonnement[gym]' => 'Testing',
        ]);

        self::assertResponseRedirects($this->path);

        self::assertSame(1, $this->abonnementRepository->count([]));
    }

    public function testShow(): void
    {
        $this->markTestIncomplete();
        $fixture = new Abonnement();
        $fixture->setDateInitiale('My Title');
        $fixture->setDateExpiration('My Title');
        $fixture->setType('My Title');
        $fixture->setPrix('My Title');
        $fixture->setModePaiement('My Title');
        $fixture->setGym('My Title');

        $this->manager->persist($fixture);
        $this->manager->flush();

        $this->client->request('GET', sprintf('%s%s', $this->path, $fixture->getId()));

        self::assertResponseStatusCodeSame(200);
        self::assertPageTitleContains('Abonnement');

        // Use assertions to check that the properties are properly displayed.
    }

    public function testEdit(): void
    {
        $this->markTestIncomplete();
        $fixture = new Abonnement();
        $fixture->setDateInitiale('Value');
        $fixture->setDateExpiration('Value');
        $fixture->setType('Value');
        $fixture->setPrix('Value');
        $fixture->setModePaiement('Value');
        $fixture->setGym('Value');

        $this->manager->persist($fixture);
        $this->manager->flush();

        $this->client->request('GET', sprintf('%s%s/edit', $this->path, $fixture->getId()));

        $this->client->submitForm('Update', [
            'abonnement[dateInitiale]' => 'Something New',
            'abonnement[dateExpiration]' => 'Something New',
            'abonnement[type]' => 'Something New',
            'abonnement[prix]' => 'Something New',
            'abonnement[modePaiement]' => 'Something New',
            'abonnement[gym]' => 'Something New',
        ]);

        self::assertResponseRedirects('/abonnement/');

        $fixture = $this->abonnementRepository->findAll();

        self::assertSame('Something New', $fixture[0]->getDateInitiale());
        self::assertSame('Something New', $fixture[0]->getDateExpiration());
        self::assertSame('Something New', $fixture[0]->getType());
        self::assertSame('Something New', $fixture[0]->getPrix());
        self::assertSame('Something New', $fixture[0]->getModePaiement());
        self::assertSame('Something New', $fixture[0]->getGym());
    }

    public function testRemove(): void
    {
        $this->markTestIncomplete();
        $fixture = new Abonnement();
        $fixture->setDateInitiale('Value');
        $fixture->setDateExpiration('Value');
        $fixture->setType('Value');
        $fixture->setPrix('Value');
        $fixture->setModePaiement('Value');
        $fixture->setGym('Value');

        $this->manager->persist($fixture);
        $this->manager->flush();

        $this->client->request('GET', sprintf('%s%s', $this->path, $fixture->getId()));
        $this->client->submitForm('Delete');

        self::assertResponseRedirects('/abonnement/');
        self::assertSame(0, $this->abonnementRepository->count([]));
    }
}
