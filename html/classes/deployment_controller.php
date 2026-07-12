<?php


// interfaz para aplicar el despliegue de un 
interface IDeploymentProvider
{
    public function init($entityname, $priority);

    public function export($id);

    public function apply($json);

    public function validate();

    public function priority();
}


class DeploymentRegistry
{
    private static array $adapters = [
        'app_modules'   =>  ['class' => ModuleDeployment::class, 'priority' => 0 ],
        'app_entities'  =>  ['class' => EntityDeployment::class, 'priority' => 1 ]
    ];

    public static function get(string $type): IDeploymentProvider
    {
        $class = generic_deployment::class;
        $priority = 999999;
        if (key_exists($type, self::$adapters)){
            $class = self::$adapters[$type]['class'];
            $priority = self::$adapters[$type]['priority'];
        }
        $obj = new $class();
        $obj->init($type,$priority);

        return $obj;
    }
}

class generic_deployment implements IDeploymentProvider
{
    public $entityname = "";
    public $priority = 1000000;

    public function init($entityname , $priority){
        $this->entityName = $entityname;
        $this->priority = $priority;
    }

    public function export($id){
        return json_encode( dbgetbyid( $this->entityName, $id ) );
    }

    public function apply($json){
        $record = json_decode($json, true);
        dbupsert($this->entityName, $record);
    }

    public function validate(){
        return true;
    }

    public function priority(){
        return $this->priority;
    }
}

class EntityDeployment extends generic_deployment
{

    public function export($id){

        $entity_obj = [];
        $entity_obj["entity"] = dbgetbyid( $this->entityName, $id ) ;
        $entity_obj["columns"] = query("select * from app_entity_columns where id_entity = " . quote($id ));

        return json_encode($entity_obj);
    }

    public function apply($json){

        $record = json_decode($json, true);
        dbupsert($this->entityName, $record["entity"]);

        foreach($record["columns"] as $colunm){
            dbupsert("app_entity_columns", $colunm);
        }

        self::deployEntity($record);

    }
    
    /*
	FUNCIONES AUXILIARES
	*/

	public static function deployEntity(array $definition)
	{
		$pdo = get_DB();
		
		$table = $definition["entity"]["entity"];

		if (!self::tableExists($pdo, $table))
		{
			//crea la tabla con la estructura básica: id, campo principal y fechas de auditoría
            self::createTable($definition["entity"]);
		}

        //ahora hay que replicar las columnas que faltam. 
        
		$dbColumns = self::getTableColumns($table);
        $ignore_columns = ['id', 'fecha_creacion', 'fecha_modificacion'];

		foreach ($definition["columns"] as $column)
		{
			$name = $column["dbcolumn"];

            if (in_array($name, $ignore_columns, true)){
                continue;
            }

			if (!isset($dbColumns[$name]))
			{
				//trace("creando ".$column["dbcolumn"]);
                self::addColumn($pdo, $table, $column);
				continue;
			}

			$newType = EntityManager::getDBtype($column['type'],$column['max']);

			if (strtoupper($dbColumns[$name]["Type"]) != strtoupper($newType))
			{
				//trace("modificando ".$column["dbcolumn"]);
                self::modifyColumn($pdo, $table, $column);
			}
		}

	}


    public static function getTableColumns($table)
    {
        $stmt=query("SHOW COLUMNS FROM `$table`");

        $cols=[];

        foreach($stmt as $row)
        {
            if ( $row["Type"] == "int(11)" ) $row["Type"] = "int";
            $cols[$row["Field"]]=$row;
        }

        return $cols;
    }
    public static function tableExists(PDO $pdo,$table)
	{
		$stmt=$pdo->prepare("
			SELECT COUNT(*)
			FROM information_schema.tables
			WHERE table_schema=DATABASE()
			AND table_name=?
		");

		$stmt->execute([$table]);

		return $stmt->fetchColumn()>0;
	}

    public static function createTable($datos){
        $tabla = $datos['entity'];
        $campo_principal = $datos['campo_principal'];
        
        $sql = "create table $tabla ( 
                id char(32) not null primary KEY,";
        if ($campo_principal != ""){
            $sql .= " $campo_principal varchar(100) null,";
        } 

        $sql .= " fecha_creacion TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                fecha_modificacion TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP  )";
        query($sql);
    }

    public static function addColumn(PDO $pdo,$table,$column)
	{
		$sql="ALTER TABLE `$table`
			ADD ". self::columnSql($column);

		$pdo->exec($sql);
	}
    public static function modifyColumn(PDO $pdo,$table,$column)
	{
		$sql="ALTER TABLE `$table`
			MODIFY ".self::columnSql($column);

		$pdo->exec($sql);
	}
    public static function columnSql(array $c)
	{
		$name=$c["dbcolumn"];

		$sql="`$name` ".EntityManager::getDBtype($c['type'],$c['max']);
		/*
		if($c["required"])
			$sql.=" NOT NULL";
		else
			$sql.=" NULL";

		if($c["value"]!==null)
		{
			$sql.=" DEFAULT ".defaultValue($c);
		}
		*/
		return $sql;
	}
}

class ModuleDeployment extends generic_deployment
{
   
}


