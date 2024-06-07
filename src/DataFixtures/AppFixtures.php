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
            'Kollegsprecher:innen 2024/25' => 4,
            'Jahrgangssprecher:innen Q3/4 2024/25' => 4,
            'Jahrgangssprecher:innen Q1/2 2024/25' => 4,
            'Lehrkräfte des Vertrauens 2024/25' => 2,
            'Bundeskanzler:in 2024/25' => 1,
            '14. Gesamtkonferenz TOP 3' => 1
        ];

        foreach ($elections as $electionEntry => $electionVoices) {
            $election = new Election();
            $election->setLabel($electionEntry);
            $election->setVoices($electionVoices);

            $manager->persist($election);

            for ($i = 1; $i <= 50; $i++) {
                $code = new ElectionCode();
                $code->setCode($this->generateCode(5));
                $code->setElection($election);
                $manager->persist($code);
            }
            $manager->flush();
        }

        $manager->flush();

        $user = new User();
        $user->setUsername('superadmin');
        $password = $this->hasher->hashPassword($user, 'Camilla@23');
        $user->setPassword($password);
        $manager->persist($user);

        $user = new User();
        $user->setUsername('sladmin');
        $password = $this->hasher->hashPassword($user, 'EVc7AdTwduJz0tRunqMR');
        $user->setPassword($password);
        $manager->persist($user);

        $user = new User();
        $user->setUsername('kvadmin');
        $password = $this->hasher->hashPassword($user, 'wcM9LnvizkdkYN61VFCw');
        $user->setPassword($password);
        $manager->persist($user);

        $manager->flush();

    }
}
