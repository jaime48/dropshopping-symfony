<?php

namespace App\DataFixtures;

use App\Entity\Categories;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class AppFixtures extends Fixture
{
    public function load(ObjectManager $manager)
    {
         foreach ($this->getCategories() as $category) {
             $categories = new Categories();
             $categories->setName($category);
             $categories->setCreatedAt(new \DateTime());
             $categories->setUpdatedAt(new \DateTime());
             $manager->persist($categories);
         }
        $manager->flush();
    }

    public function getCategories() {
        return ['Vegetables', 'Fruit', 'Ocean Foods', 'Butter & Eggs', 'Fastfood', 'Oatmeal'];
    }

}
