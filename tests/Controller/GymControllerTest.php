<?php

namespace App\Tests\Controller;

use App\Entity\Gym;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\EntityRepository;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

final class GymControllerTest extends WebTestCase
{
    private KernelBrowser $client;
    private EntityManagerInterface $manager;
    private EntityRepository $gymRepository;
    private string $path = '/gym/';

    protected function setUp(): void
    {
        $this->client = static::createClient();
        $this->manager = static::getContainer()->get('doctrine')->getManager();
        $this->gymRepository = $this->manager->getRepository(Gym::class);

        foreach ($this->gymRepository->findAll() as $object) {
            $this->manager->remove($object);
        }

        $this->manager->flush();
    }

    public function testIndex(): void
    {
        $this->client->followRedirects();
        $crawler = $this->client->request('GET', $this->path);

        self::assertResponseStatusCodeSame(200);
        self::assertPageTitleContains('Gym index');

        // Use the $crawler to perform additional assertions e.g.
        // self::assertSame('Some text on the page', $crawler->filter('.p')->first());
    }

    public function testNew(): void
    {
        $this->markTestIncomplete();
        $this->client->request('GET', sprintf('%snew', $this->path));

        self::assertResponseStatusCodeSame(200);

        $this->client->submitForm('Save', [
            'gym[nom]' => 'Testing',
            'gym[localisation]' => 'Testing',
            'gym[photo]' => 'Testing',
            'gym[services]' => 'Testing',
            'gym[horaires]' => 'Testing',
            'gym[contact]' => 'Testing',
            'gym[prixMensuel]' => 'Testing',
            'gym[prixTrimestriel]' => 'Testing',
            'gym[prixSemestriel]' => 'Testing',
            'gym[prixAnnuel]' => 'Testing',
        ]);

        self::assertResponseRedirects($this->path);

        self::assertSame(1, $this->gymRepository->count([]));
    }

    public function testShow(): void
    {
        $this->markTestIncomplete();
        $fixture = new Gym();
        $fixture->setNom('My Title');
        $fixture->setLocalisation('My Title');
        $fixture->setPhoto('My Title');
        $fixture->setServices('My Title');
        $fixture->setHoraires('My Title');
        $fixture->setContact('My Title');
        $fixture->setPrixMensuel('My Title');
        $fixture->setPrixTrimestriel('My Title');
        $fixture->setPrixSemestriel('My Title');
        $fixture->setPrixAnnuel('My Title');

        $this->manager->persist($fixture);
        $this->manager->flush();

        $this->client->request('GET', sprintf('%s%s', $this->path, $fixture->getId()));

        self::assertResponseStatusCodeSame(200);
        self::assertPageTitleContains('Gym');

        // Use assertions to check that the properties are properly displayed.
    }

    public function testEdit(): void
    {
        $this->markTestIncomplete();
        $fixture = new Gym();
        $fixture->setNom('Value');
        $fixture->setLocalisation('Value');
        $fixture->setPhoto('Value');
        $fixture->setServices('Value');
        $fixture->setHoraires('Value');
        $fixture->setContact('Value');
        $fixture->setPrixMensuel('Value');
        $fixture->setPrixTrimestriel('Value');
        $fixture->setPrixSemestriel('Value');
        $fixture->setPrixAnnuel('Value');

        $this->manager->persist($fixture);
        $this->manager->flush();

        $this->client->request('GET', sprintf('%s%s/edit', $this->path, $fixture->getId()));

        $this->client->submitForm('Update', [
            'gym[nom]' => 'Something New',
            'gym[localisation]' => 'Something New',
            'gym[photo]' => 'Something New',
            'gym[services]' => 'Something New',
            'gym[horaires]' => 'Something New',
            'gym[contact]' => 'Something New',
            'gym[prixMensuel]' => 'Something New',
            'gym[prixTrimestriel]' => 'Something New',
            'gym[prixSemestriel]' => 'Something New',
            'gym[prixAnnuel]' => 'Something New',
        ]);

        self::assertResponseRedirects('/gym/');

        $fixture = $this->gymRepository->findAll();

        self::assertSame('Something New', $fixture[0]->getNom());
        self::assertSame('Something New', $fixture[0]->getLocalisation());
        self::assertSame('Something New', $fixture[0]->getPhoto());
        self::assertSame('Something New', $fixture[0]->getServices());
        self::assertSame('Something New', $fixture[0]->getHoraires());
        self::assertSame('Something New', $fixture[0]->getContact());
        self::assertSame('Something New', $fixture[0]->getPrixMensuel());
        self::assertSame('Something New', $fixture[0]->getPrixTrimestriel());
        self::assertSame('Something New', $fixture[0]->getPrixSemestriel());
        self::assertSame('Something New', $fixture[0]->getPrixAnnuel());
    }

    public function testRemove(): void
    {
        $this->markTestIncomplete();
        $fixture = new Gym();
        $fixture->setNom('Value');
        $fixture->setLocalisation('Value');
        $fixture->setPhoto('Value');
        $fixture->setServices('Value');
        $fixture->setHoraires('Value');
        $fixture->setContact('Value');
        $fixture->setPrixMensuel('Value');
        $fixture->setPrixTrimestriel('Value');
        $fixture->setPrixSemestriel('Value');
        $fixture->setPrixAnnuel('Value');

        $this->manager->persist($fixture);
        $this->manager->flush();

        $this->client->request('GET', sprintf('%s%s', $this->path, $fixture->getId()));
        $this->client->submitForm('Delete');

        self::assertResponseRedirects('/gym/');
        self::assertSame(0, $this->gymRepository->count([]));
    }
}
