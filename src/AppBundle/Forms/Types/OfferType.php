<?php
/**
 * @author Boris Guéry <guery.b@gmail.com>
 */

namespace AppBundle\Forms\Types;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\MoneyType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;

class OfferType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('proposedPrice', MoneyType::class, [
                'divisor' => 100,
                'currency' => 'EUR',
            ])
            ->add('buyerMessage', TextareaType::class)
            ->add('submit', SubmitType::class, ['label' => 'Submit'])
        ;
    }
}
