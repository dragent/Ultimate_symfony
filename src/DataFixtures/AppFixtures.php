<?php

namespace App\DataFixtures;

use Faker\Factory;
use App\Entity\User;
use App\Entity\Product;
use App\Entity\Category;
use Doctrine\Persistence\ObjectManager;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\String\Slugger\SluggerInterface;

class AppFixtures extends Fixture
{
    protected $slugger;
    protected $encoder;

    public function __construct(SluggerInterface $slugger, UserPasswordEncoderInterface $encoder)
    {
        $this->slugger = $slugger;
        $this->encoder = $encoder;
    }

    public function load(ObjectManager $manager): void
    {
        $faker = Factory::create("fr_FR");
        $faker->addProvider(new \Liior\Faker\Prices($faker));
        $faker->addProvider(new \Bezhanov\Faker\Provider\Commerce($faker));
        $faker->addProvider(new \Bluemmb\Faker\PicsumPhotosProvider($faker));
        for ($c = 0; $c < 3; $c++) {
            $category = new Category();
            $category->setName($faker->department)
                ->setSlug(strtolower($this->slugger->slug($category->getName())));
            $manager->persist($category);
            for ($p = 0; $p < mt_rand(15, 20); $p++) {
                $product = new Product();
                $product->setName($faker->productName)
                    ->setPrice($faker->price(4000, 20000))
                    ->setCategory($category)
                    ->setShortDescription($faker->paragraph())
                    ->setMainPicture($faker->imageUrl(400, 400, true))
                    ->setSlug(strtolower($this->slugger->slug($product->getName())));
                $manager->persist($product);
            }
        }
        for ($u = 0; $u < 5; $u++) {
            $user = new User();
            $user->setEmail("user$u@gmail.com")
                ->setFullName($faker->name())
                ->setPassword($this->encoder->encodePassword($user, "password"));
            $manager->persist($user);
        }
        $admin = new User();
        $admin->setEmail("admin@gmail.com")
            ->setPassword($this->encoder->encodePassword($admin, "password"))
            ->setFullName("admin")
            ->setRoles(["ROLE_ADMIN"]);
        $manager->persist($admin);

        $manager->flush();
    }
}
