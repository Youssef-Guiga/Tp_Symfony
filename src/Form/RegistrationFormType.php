<?php

namespace App\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\TelType;
use App\Entity\User;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Validator\Constraints\IsTrue;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;

class RegistrationFormType extends AbstractType
{
public function buildForm(FormBuilderInterface $builder, array $options): void
{
$builder
->add('username')
->add('email', EmailType::class)
->add('phonenumber', TelType::class, [
'required' => false,


])
->add('password', RepeatedType::class, [
'type' => PasswordType::class,
'first_options' => ['label' => 'Password'],
'second_options' => ['label' => 'Repeat Password'],
'invalid_message' => 'The password fields must match.',
'options' => ['attr' => ['class' => 'password-field']],
'required' => true,
'constraints' => [
new NotBlank([
'message' => 'Please enter a password',
]),
new Length([
'min' => 6,
'minMessage' => 'Your password should be at least {{ limit }} characters',
// max length allowed by Symfony for security reasons
'max' => 4096,
]),
],
])
->add('agreeTerms', CheckboxType::class, [
'mapped' => false,
'constraints' => [
new IsTrue([
'message' => 'You should agree to our terms.',
]),
],
])
;
}

public function configureOptions(OptionsResolver $resolver): void
{
$resolver->setDefaults([
'data_class' => User::class,
]);
}
}
