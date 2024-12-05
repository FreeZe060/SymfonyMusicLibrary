<?php

namespace App\DataFixtures;

use App\Entity\Artist;
use App\Entity\Album;
use App\Entity\Song;
use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Faker\Factory;

class AppFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {

        $connection = $manager->getConnection();
        $platform = $connection->getDatabasePlatform();

        if ($platform->getName() === 'sqlite') {
            $connection->executeQuery('DELETE FROM sqlite_sequence WHERE name="artist"');
            $connection->executeQuery('DELETE FROM sqlite_sequence WHERE name="album"');
            $connection->executeQuery('DELETE FROM sqlite_sequence WHERE name="song"');
            $connection->executeQuery('DELETE FROM sqlite_sequence WHERE name="user"');
        }
        
        $faker = Factory::create();

        $user = new User();
        $user->setEmail('Tim_V@outlook.fr');
        $user->setUsername('TestUser');
        $user->setPlainPassword('password');
        $user->setPassword('$2y$10$o.pzwGwxUMdk1sPVL5eKweR4okx8jmvnfE6jG3662I0UBzLT7W2PO');
        $user->setRoles(['ROLE_USER']);
        $manager->persist($user);

        // Generate fake artists
        for ($i = 0; $i < 10; $i++) {
            // Create an artist
            $artist = new Artist();
            $artist->setName($faker->company);
            $artist->setStyle($faker->word);

            $manager->persist($artist);

            // Generate albums for each artist
            for ($j = 0; $j < 3; $j++) {
                $album = new Album();
                $album->setTitle($faker->word . " " . $faker->word);
                $album->setDate(new \DateTime($faker->dateTimeThisCentury()->format('Y-m-d')));
                $album->setArtist($artist);

                $manager->persist($album);

                // Generate songs for each album
                for ($k = 0; $k < 5; $k++) {
                    $song = new Song();
                    $song->setTitle($faker->word);
                    $song->setGenre($faker->word);
                    $song->setLength(rand(180, 300));
                    $song->setAlbum($album);

                    $manager->persist($song);
                }
            }
        }

        $manager->flush();
    }
}
