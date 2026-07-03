<?php


// interfaz para aplicar el despliegue de un 
interface IDeploymentProvider
{
    public static function entityName();

    public function export($id);

    public function apply($json);

    public function validate();

    public function priority();
}


class DeploymentRegistry
{
    private static array $adapters = [
        'Entity'   => EntityTransport::class,
        'Field'    => FieldTransport::class,
        'Plugin'   => PluginTransport::class,
        'View'     => ViewTransport::class,
        'Form'     => FormTransport::class,
    ];

    public static function get(string $type): IDeploymentProvider
    {
        $class = self::$adapters[$type];

        return new $class();
    }
}

class generic_deployment implements IDeploymentProvider
{
   
}

class EntityDeployment implements IDeploymentProvider
{
    public static function entityName(){

	}

    public function export($id){

	}

    public function apply($json){

	}

    public function validate(){

	}

    public function priority(){

	}
}
