<?php

namespace App\Form;

use App\Repository\LocationsRepository;
use App\Repository\StationsRepository;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class FilterFormType extends AbstractType
{
    private $locationsRepository;
    private $stationsRepository;

    public function __construct(LocationsRepository $locationsRepository, StationsRepository $stationsRepository){
        $this->locationsRepository = $locationsRepository;
        $this->stationsRepository = $stationsRepository;
    }

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('City', ChoiceType::class, [
                'choices' => $this->locationsRepository->getLocationOptions(),
                'choice_label' => function ($value) {
                    return $value;
                }
            ],
            )
            ->add('Type', ChoiceType::class, [
                'choices' => $this->stationsRepository->getStationTypeOptions(),
                'choice_label' => function ($value) {
                    return $value;
                }
            ],
            )
            ->add('save', SubmitType::class, ['label' => 'Filter'])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            // Configure your form options here
        ]);
    }
}
