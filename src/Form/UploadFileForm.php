<?php

namespace App\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Validator\Constraints\File;

class UploadFileForm extends AbstractType {

    public function buildForm(FormBuilderInterface $builder, mixed $options) {
        $builder
            ->add('file', FileType::class, [
                'label' => 'File',
                'mapped' => false,
                'required' => true,

                'constraints' => [
                    new File([
                        'maxSize' => '128m',
                    ])
                ],
            ]);
    }
}
