<?php
namespace Axstrad\ApiWrapper;

/**
 * Dependancies
 */
use Axstrad\ApiWrapper\Request;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;


/**
 * Axstrad\ApiWrapper\Request\AbstractRequest
 */
abstract class AbstractRequest implements Request
{
    /**
     * @var array
     */
    protected $options;

    /**
     * @param array $options
     */
    public function __construct(array $options = array())
    {
        $resolver = new OptionsResolver();
        $this->setDefaultOptions($resolver);
        $this->options = $resolver->resolve($options);
    }

    /**
     * @param  OptionsResolverInterface $resolver
     * @return void
     */
    protected function configureOptions(OptionsResolverInterface $resolver)
    { }

    /**
     */
    public function getMethod()
    {
        return self::METHOD_GET;
    }

    /**
     */
    public function getPath()
    {
        return '';
    }

    /**
     */
    public function getData()
    {
        return $this->options;
    }
}
