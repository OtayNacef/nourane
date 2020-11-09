<?php

namespace AppBundle\Form;

use AppBundle\Entity\Gallery;
use Comur\ImageBundle\Form\Type\CroppableGalleryType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class GalleryType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    { $galery = new Gallery();
        $builder->add('title')

            ->add('gallery', CroppableGalleryType::class, array(
                'uploadConfig' => array(
                    'uploadRoute' => 'comur_api_upload',     //optional
                    'uploadDir' => $galery->getUploadDir(), // required - see explanation below (you can also put just a dir name relative to your public dir)
                    // 'uploadUrl' => $myEntity->getUploadRootDir(),       // DEPRECATED due to security issue !!! Please use uploadDir. required - see explanation below (you can also put just a dir path)
                    'webDir' => $galery->getUploadDir(),        // required - see explanation below (you can also put just a dir path)
                    'fileExt' => '*.jpg;*.gif;*.png;*.jpeg',   //optional
                    'maxFileSize' => 50, //optional
                    'libraryDir' => null,             //optional
                    'libraryRoute' => 'comur_api_image_library', //optional
                    'showLibrary' => true,             //optional
                    'saveOriginal' => 'originalImage',      //optional
                    'generateFilename' => true      //optional
                ),
                'cropConfig' => array(
                    'disable' => false,      //optional
                    'minWidth' => 588,
                    'minHeight' => 300,
                    'aspectRatio' => true,         //optional
                    'cropRoute' => 'comur_api_crop',   //optional
                    'forceResize' => false,       //optional
                    'thumbs' => array(           //optional
                        array(
                            'maxWidth' => 180,
                            'maxHeight' => 400,
                            'useAsFieldImage' => true  //optional
                        )
                    )
                )
            ))
        ;
    }/**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'AppBundle\Entity\Gallery'
        ));
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix()
    {
        return 'appbundle_gallery';
    }


}
