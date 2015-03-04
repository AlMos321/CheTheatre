<?php

namespace AppBundle\Admin;

use Sonata\AdminBundle\Admin\Admin;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Form\FormMapper;

class PostAdmin extends Admin
{
    protected $baseRouteName = 'AppBundle\Entity\Post';
    protected $baseRoutePattern = 'Post';
    protected $datagridValues = [
        '_sort_order' => 'ASC',
        '_sort_by' => 'name',
    ];

    /**
     * @param \Sonata\AdminBundle\Form\FormMapper $formMapper
     *
     * @return void
     */
    protected function configureFormFields(FormMapper $formMapper)
    {
        $formMapper
            ->add('title')
            ->add('shortDescription')
            ->add('text')
            ->add('mainPicture', 'sonata_type_model_list', [
                'required' => false,
                'btn_list' => false,
            ], [
                'link_parameters' => [
                    'context' => 'default',
                    'provider' => 'sonata.media.provider.image',
                ],
            ])
            ->add('tags', 'sonata_type_model',
                array(
                    'by_reference' => true,
                    'multiple'     => true,
                    'property'     => 'title',
                ));
    }
    /**
     * @param \Sonata\AdminBundle\Datagrid\ListMapper $listMapper
     *
     * @return void
     */
    protected function configureListFields(ListMapper $listMapper)
    {
        $listMapper
            ->addIdentifier('title')
            ->add('mainPicture', 'sonata_type_model_list', [
                'required' => false,
                'btn_list' => false,
            ], [
                'link_parameters' => [
                    'context' => 'default',
                    'provider' => 'sonata.media.provider.image',
                ],
            ])
            ->add('_action', 'actions', array(
                'actions' => array(
                    'show' => array(),
                    'edit' => array(),
                    'delete' => array(),
                ),
            ));
    }

    /**
     * @param \Sonata\AdminBundle\Datagrid\DatagridMapper $datagridMapper
     *
     * @return void
     */
    protected function configureDatagridFilters(DatagridMapper $datagridMapper)
    {
        $datagridMapper
            ->add('title')
            ->add('tags', null, array(), null, array('expanded' => true, 'multiple' => true));
    }
}
