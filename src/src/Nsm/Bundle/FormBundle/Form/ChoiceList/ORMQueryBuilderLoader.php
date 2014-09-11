<?php

namespace Nsm\Bundle\FormBundle\Form\ChoiceList;

use Symfony\Bridge\Doctrine\Form\ChoiceList\ORMQueryBuilderLoader as BaseORMQueryBuilderLoader;
use Symfony\Component\Form\FormTypeInterface;

/**
 * Getting Entities through the ORM QueryBuilder
 *
 * This is a custom ORMQueryBuilderLoader which caches the query for selected enties for performance. It's only called
 * when a querybuilder is required.
 *
 * The story is… in cases where the choices are loaded from an api there is no need to load all the choices
 * when first rendering the choicelist.
 *
 * However we do need to load a subset of choices when choices have been submitted so we can compare the submitted
 * choices to the possible choice list.
 *
 * Given we have already attempted to load the submitted choices by id in the ORMQueryBuilderLoader::getEntitiesByIds
 * method where we cached the results we can just return the cached results instead of performing another query.
 *
 * The retrieval of the cached results is only used when the loadEntities is set to false. It's true by default.
 */
class ORMQueryBuilderLoader extends BaseORMQueryBuilderLoader
{

    /**
     * A flag to load entities or not
     * This is used to determine if we should use the result cache from the getEntitiesByIds call or load all entities
     *
     * @var bool
     */
    protected $loadEntities;

    /**
     * @var array
     */
    protected $selectedEntitiesCache = array();

    /**
     * Construct an ORM Query Builder Loader
     *
     * @param callable|\Doctrine\ORM\QueryBuilder $queryBuilder
     * @param null                                $manager
     * @param null                                $class
     * @param FormTypeInterface                   $formType
     * @param bool                                $loadEntities
     * @param bool                                $loadEntitiesByIds
     */
    public function __construct($queryBuilder, $manager = null, $class = null, $loadEntities = true)
    {
        parent::__construct($queryBuilder, $manager, $class);

        $this->loadEntities = $loadEntities;
    }

    /**
     * {@inheritdoc}
     */
    public function getEntities()
    {
        if (false === $this->loadEntities) {
            return $this->selectedEntitiesCache;
        }

        return parent::getEntities();
    }

    /**
     * {@inheritdoc}
     */
    public function getEntitiesByIds($identifier, array $values)
    {
        $this->selectedEntitiesCache = parent::getEntitiesByIds($identifier, $values);

        return $this->selectedEntitiesCache;
    }
}
