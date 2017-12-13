<?php
/**
 * Created by PhpStorm.
 * User: victorynox
 * Date: 13.12.17
 * Time: 12:33
 */

namespace rollun\Services\ApiGateway\Installers;


use Psr\Container\ContainerExceptionInterface;
use Psr\Container\NotFoundExceptionInterface;
use rollun\installer\Install\InstallerAbstract;
use rollun\Services\ApiGateway\Factory\ServicesPluginManagerFactory;
use rollun\Services\ApiGateway\Services\ExampleGoogleServices;
use rollun\Services\ApiGateway\Services\Factory\ServiceConfigAbstractFactory;
use Zend\ServiceManager\Factory\InvokableFactory;

class GatewayServicePluginManagerConfigInstaller extends InstallerAbstract
{

    /**
     * install
     * @return array
     */
    public function install()
    {
        $config = [
            ServicesPluginManagerFactory::KEY => [
                'abstract_factories' => [
                    ServiceConfigAbstractFactory::class,
                ],
            ],
            ServiceConfigAbstractFactory::KEY => [

            ],
        ];
        if ($this->consoleIO->askConfirmation("You wont add default google service(yes/no, No by default)? ", false)) {
            $config = array_merge_recursive($config, [
                ServicesPluginManagerFactory::KEY => [
                    "aliases" => [
                        "google" => ExampleGoogleServices::class,
                    ],
                    "factories" => [
                        ExampleGoogleServices::class => InvokableFactory::class,
                    ],
                ]
            ]);
        }
        return $config;
    }

    /**
     * Clean all installation
     * @return void
     */
    public function uninstall()
    {
        // TODO: Implement uninstall() method.
    }

    /**
     * Return true if install, or false else
     * @return bool
     */
    public function isInstall()
    {
        try {
            $config = $this->container->get("config");
            return (
            isset($config[ServicesPluginManagerFactory::KEY])
            );
        } catch (NotFoundExceptionInterface | ContainerExceptionInterface $e) {
            return false;
        }
    }

    /**
     * Return string with description of installable functional.
     * @param string $lang ; set select language for description getted.
     * @return string
     */
    public function getDescription($lang = "en")
    {
        switch ($lang) {
            case "ru":
                $description = "Создает конфиг-файл для конфигурирования GatewayServicePluginManager.";
                break;
            default:
                $description = "Does not exist.";
        }
        return $description;
    }
}