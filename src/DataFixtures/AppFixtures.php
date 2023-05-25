<?php

namespace App\DataFixtures;

use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use App\Entity\Serie;
use App\Entity\Episode;

class AppFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        for ($i = 0; $i < 5; $i++) {

            $serie = new Serie();
            $serie->setTitre("serie $i");
            $serie->setDescription("description $i");
            $serie->setNote(mt_rand(0, 20));
            for ($j=0; $j < 11; $j++) { 
                $episode = new Episode();
                $episode->setTitre("episode $j de la série $i");
                $episode->setNumero($i);
                $episode->setResume("Dans cette episode il se passe quelque chose");
                $episode->setSerie($serie);
                $manager->persist($episode);
            }
            $manager->persist($serie);
        }     

        $manager->flush();
    }
}
