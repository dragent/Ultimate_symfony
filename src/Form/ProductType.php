<?php

namespace App\Form;

use App\Entity\Category;
use App\Entity\Product;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\MoneyType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\UrlType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ProductType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add("name", TextType::class, [
                "label" => "Nom du produit",
                "attr" => [
                    "placeholder" => "Tapez le nom du produit"
                ],
                "required" => true,
            ])
            ->add("shortDescription", TextareaType::class, [
                "label" => "Description courte",
                "attr" => [
                    "placeholder" => "Tapez une description assez courte mais parlante pour le visiteur"
                ],
                "required" => true
            ])
            ->add("price", MoneyType::class, [
                "label" => "Prix du produit",
                "attr" => [
                    "placeholder" => "Tapez le prix du produit en euro"
                ],
                "required" => true
            ])
            ->add("mainPicture", UrlType::class, [
                "label" => "image du produit",
                "attr" => [
                    "placeholder" => "Tapez l'url d'une image"
                ]
            ])
            ->add("category", EntityType::class, [
                "label" => "Catégorie",
                "placeholder" => "-- Choisir une catégorie",
                "class" => Category::class,
                "choice_label" => function (Category $category) {
                    return ucfirst($category->getName());
                }

            ]);;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Product::class,
        ]);
    }
}
