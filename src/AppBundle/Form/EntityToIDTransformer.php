<?php
/**
 * Created by PhpStorm.
 * User: Splinter
 * Date: 14/04/2016
 * Time: 22:09
 */

namespace AppBundle\Form;

use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\Form\DataTransformerInterface;
use Symfony\Component\Form\Exception\TransformationFailedException;

class EntityToIDTransformer  implements DataTransformerInterface {

    private $manager;
    private $type;

    public function __construct(ObjectManager $manager,$type)
    {
        $this->manager = $manager;
        $this->type = $type;
    }

    /**
     * {@inheritdoc}
     */
    public function transform($value)
    {
       if ($value === null){
           return '';
       }

       if (! $value instanceof $this->type ){
           throw new \Exception("Must be an object");
       }

       return $value->getId();

    }

    /**
     * {@inheritdoc}
     */
    public function reverseTransform($value)
    {
        if ($value === null || $value == ''){
            return null;
        }

        $object = $this->manager
            ->getRepository($this->type)
            ->find($value);

        return $object;

    }
}