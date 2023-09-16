<?php

namespace App\DataFixtures;

use App\Entity\Election;
use App\Entity\ElectionCode;
use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class AppFixtures extends Fixture
{

    private UserPasswordHasherInterface $hasher;

    public function __construct(UserPasswordHasherInterface $hasher)
    {
        $this->hasher = $hasher;
    }

    private function generateCode($length): string
    {
        //Mögliche Zeichen für den String
        // $chars = '0123456789';
        $chars = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ';

        //String wird generiert
        $str = '';
        $anz = strlen($chars);
        for ($i=0; $i<$length; $i++) {
            $str .= $chars[rand(0,$anz-1)];
        }
        return $str;
    }

    public function load(ObjectManager $manager): void
    {
        $elections = [
            'Kollegsprecher:innen' => 4,
            'Jahrgangssprecher:innen Q3/4' => 4,
            'Jahrgangssprecher:innen Q1/2' => 4,
            'Klassensprecher:innen E-Phase' => 0,
            'Lehrkräfte des Vertrauens' => 2
        ];

        foreach ($elections as $electionEntry => $electionVoices) {
            $election = new Election();
            $election->setLabel($electionEntry);
            $election->setVoices($electionVoices);

            $manager->persist($election);

            for ($i = 1; $i <= 100; $i++) {
                $code = new ElectionCode();
                $code->setCode($this->generateCode(5));
                $code->setElection($election);
                $manager->persist($code);
            }
            $manager->flush();
        }

        $manager->flush();

        $user = new User();
        $user->setUsername('admin');

        $password = $this->hasher->hashPassword($user, 'Camilla@23');
        $user->setPassword($password);

        $manager->persist($user);
        $manager->flush();

    }
}
