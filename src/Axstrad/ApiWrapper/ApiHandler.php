<?php
namespace Axstrad\ApiWrapper;

/**
 * Dependancies
 */
use Axstrad\ApiWrapper\Request;
use Buzz\Browser;
use Buzz\Client\AbstractCurl;
use Buzz\Client\Curl;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;


/**
 * Axstrad\ApiWrapper\ApiHandler
 */
abstract class ApiHandler
{
    /**
     * @var Buzz\Browser
     */
    private $browser;

    /**
     * @var Buzz\Client\Curl
     */
    private $client;

    /**
     * @var array
     */
    protected $options = array();


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
     * Set default options
     *
     * @param OptionsResolverInterface $resolver
     */
    protected function configureOptions(OptionsResolverInterface $resolver)
    {
        $resolver
            ->setRequired(array(
                'host',
                'ssl',
            ))
            ->setAllowedTypes(array(
                'host' => 'string',
                'ssl' => 'boolean',
            ))
            ->setDefaults(array(
                'ssl' => false,
            ))
            ->setNormalizers(array(
                'host' => function (Options $options, $value) {
                    if (!preg_match('~^https?://~i', substr($value, 0, 8))) {
                        $value = ($options['ssl'] ? 'https://' : 'http://') . $value;
                    }

                    return $value;
                },
            ))
        ;
    }

    /**
     * @param AbstractCurl $client
     * $return self
     */
    public function setClient(AbstractCurl $client)
    {
        $this->client = $client;
        return $this;
    }

    /**
     * @return Curl
     */
    protected function getClient()
    {
        if ($this->client = null) {
            $this->client = new Curl;
            $this->client->setVerifyPeer(false);
        }

        return $this->client;
    }

    /**
     * @param Browser $browser
     * @return self
     */
    public function setBrowser(Browser $browser)
    {
        $this->browser = $browser;
        $this->setClient(
            $this->browser->getClient()
        );
        return $this;
    }

    /**
     * @return Browser
     */
    protected function getBrowser()
    {
        if ($this->browser = null) {
            $this->browser = new Browser(
                $this->getClient()
            );
        }

        return $this->browser;
    }

    /**
     * @param  Request $request
     * @return string
     */
    protected function getUri(Request $request)
    {
        return $this->options['host'].$request->getPath();
    }


    /**
     * Submit a request to the API.
     *
     * @param Request $request
     * @return object
     */
    public function submit(Request $request)
    {
        $response = $this
            ->getBrowser()
            ->submit(
                $this->getUri($request),
                $request->getData(),
                $request->getMethod()
            )
        ;

        return $response->getContent();
    }

}
