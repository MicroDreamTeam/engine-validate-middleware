<?php
namespace Itwmw\Validate\Middleware;

class ValidateMiddlewareConfig
{
    /**
     * Automatic loading of validator rules
     * @var array
     */
    protected $autoValidatePath = [];

    /**
     * Validator specific association
     * @var array
     */
    protected $validateLink = [];

    /**
     * Validate Factory
     * @var ValidateFactoryInterface
     */
    protected $validateFactory;

    /**
     * Stored single instance objects
     * @var ValidateMiddlewareConfig
     */
    protected static $instance;

    public static function instance(): ValidateMiddlewareConfig
    {
        if (empty(self::$instance)) {
            self::$instance = new static();
        }

        return self::$instance;
    }

    public function __construct()
    {
        $this->validateFactory = new ValidateFactory();
    }

    /**
     * Set up auto-load validator rules
     *
     * @param string $controllerPath Controller path
     * @param string $validatePath   Validator path
     * @return $this
     */
    public function setAutoValidatePath(string $controllerPath, string $validatePath): ValidateMiddlewareConfig
    {
        if ('\\' !== substr($controllerPath, -1)) {
            $controllerPath = $controllerPath . '\\';
        }

        if ('\\' !== substr($validatePath, -1)) {
            $validatePath = $validatePath . '\\';
        }

        $this->autoValidatePath[$controllerPath] = $validatePath;
        return $this;
    }

    /**
     * Set Validator Association
     *
     * @param string|string[] $controller Controller namespace
     *                                    To specify a method, pass an array with the second element being the method name
     * @param string|string[] $validate   Validator namespace
     *                                    To specify a scene, pass an array with the second element being the scene name
     * @return $this
     */
    public function setValidateLink($controller, $validate): ValidateMiddlewareConfig
    {
        if (is_array($controller)) {
            $controllers = $controller;
            $controller  = $controllers[0];
            $method      = $controllers[1];
            # The "\" symbol must not be present in the array
            $controller = md5($controller);
            if (count($controllers) >= 2) {
                if (isset($this->validateLink[$controller])) {
                    $_validate = $this->validateLink[$controller];
                    $_validate = array_merge($_validate, [
                        $method => $validate
                    ]);
                    $this->validateLink[$controller] = $_validate;
                } else {
                    $this->validateLink[$controller] = [
                        $method => $validate
                    ];
                }
            }
        } else {
            $controller = md5($controller);
            if (isset($this->validateLink[$controller])) {
                $this->validateLink[$controller]['!__other__'] = $validate;
            } else {
                $this->validateLink[$controller] = [
                    '!__other__' => $validate
                ];
            }
        }
        return $this;
    }

    /**
     * Get validator specific associations
     *
     * @param string|null $controller Validator full namespace
     * @return array
     */
    public function getValidateLink(?string $controller = null): array
    {
        if (null === $controller) {
            return $this->validateLink;
        }
        return $this->validateLink[md5($controller)] ?? [];
    }

    /**
     * Get auto-load validator rules
     *
     * @return array
     */
    public function getAutoValidatePath(): array
    {
        return $this->autoValidatePath;
    }

    /**
     * Provide a validate factory
     *
     * @param ValidateFactoryInterface $validateFactory
     * @return $this
     */
    public function setValidateFactory(ValidateFactoryInterface $validateFactory): ValidateMiddlewareConfig
    {
        $this->validateFactory = $validateFactory;
        return $this;
    }

    /**
     * Get a validate factory
     *
     * @return ValidateFactoryInterface
     */
    public function getValidateFactory(): ValidateFactoryInterface
    {
        return $this->validateFactory;
    }
}
