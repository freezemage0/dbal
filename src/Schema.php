<?php

namespace Freezemage\DBAL;

use Freezemage\DBAL\Schema\Column;
use Freezemage\DBAL\Schema\ColumnCollection;
use Freezemage\DBAL\Schema\Definition;
use Freezemage\DBAL\Schema\Table;
use ReflectionClass;
use ReflectionException;
use RuntimeException;

final class Schema
{
    /** @var array<string, Schema> */
    private static array $schemas = [];
    private string $entityClass;
    private ReflectionClass $reflection;
    private Table $table;
    private ColumnCollection $columns;
    /** @var array<string, Definition> */
    private array $definitions = [];

    /**
     * @param class-string $entityClass
     * @param Table $table
     * @param ColumnCollection|null $columns
     */
    public function __construct(string $entityClass, Table $table, ColumnCollection $columns = null)
    {
        try {
            $this->entityClass = $entityClass;
            $this->table = $table;
            $this->columns = $columns ?? new ColumnCollection();
            $this->reflection = new ReflectionClass($entityClass);
        } catch (ReflectionException $e) {
            throw new RuntimeException($e->getMessage(), $e->getCode(), $e);
        }
    }

    /**
     * @param class-string $entityClass
     * @return Schema
     */
    public static function createFromClass(string $entityClass): Schema
    {
        if (isset(Schema::$schemas[$entityClass])) {
            return Schema::$schemas[$entityClass];
        }

        try {
            $reflection = new ReflectionClass($entityClass);
        } catch (ReflectionException $e) {
            throw new RuntimeException($e->getMessage(), $e->getCode(), $e);
        }
        $tableAttributes = $reflection->getAttributes(Table::class);

        if (count($tableAttributes) > 1) {
            throw new RuntimeException('Invalid schema'); // custom exception
        }

        $table = array_shift($tableAttributes);

        $schema = new Schema($entityClass, $table->newInstance());
        $schema->reflection = $reflection;

        foreach ($reflection->getProperties() as $property) {
            $column = $property->getAttributes(Column::class);
            if (count($column) > 1) {
                throw new RuntimeException('Invalid schema.');
            }

            $column = array_shift($column);
            $c = $column->newInstance();
            $c->propertyName = $property->name;
            $schema->columns = $schema->columns->withColumn($c);
        }

        return Schema::$schemas[$entityClass] = $schema;
    }

    /**
     * @param array<string, scalar> $data
     * @return object
     */
    public function createEntity(array $data = []): object
    {
        try {
            $entity = $this->reflection->newInstanceWithoutConstructor();

            foreach ($data as $name => $value) {
                $column = $this->getColumn($name) ?? $this->getColumnByDefinition($name);
                if ($column == null) {
                    continue; // data key is not defined, skip.
                }

                if (!$this->reflection->hasProperty($column->property())) {
                    continue;
                }

                $property = $this->reflection->getProperty($column->property());
                $property->setValue($entity, $value);
            }

            return $entity;
        } catch (ReflectionException $e) {
            throw new RuntimeException($e->getMessage(), $e->getCode(), $e);
        }
    }

    public function getColumn(string $name): ?Column
    {
        return $this->columns->findByName($name);
    }

    public function getColumnByDefinition(string $name): ?Column
    {
        foreach ($this->columns as $column) {
            $definition = $this->definitions[$column->name] = new Definition($this->table, $column);

            if ($definition->getAlias() == $name) {
                return $column;
            }
        }

        return null;
    }

    public function getColumns(): ColumnCollection
    {
        return $this->columns;
    }

    public function getTable(): Table
    {
        return $this->table;
    }

    public function getTableName(): string
    {
        return $this->table->name;
    }

    public function getColumnDefinition(string $name): Definition
    {
        if (isset($this->definitions[$name])) {
            return clone $this->definitions[$name];
        }

        $column = $this->columns->findByName($name);
        if ($column == null) {
            throw new RuntimeException("Unable to find column {$name}.");
        }

        return $this->definitions[$name] = new Definition($this->table, $column);
    }

    public function getEntityIdentifier(object $entity): string
    {
        $entityClass = $this->entityClass;
        if (!($entity instanceof $entityClass)) {
            throw new RuntimeException('Unsupported entity.');
        }

        try {
            $column = $this->getPrimaryColumn();
            if (!isset($column->propertyName)) {
                throw new RuntimeException("Column {$column->name} doesn't have a matching property.");
            }
            $property = $this->reflection->getProperty($column->propertyName);
        } catch (ReflectionException $e) {
            throw new RuntimeException($e->getMessage(), $e->getCode(), $e);
        }
        return (string)$property->getValue($entity);
    }

    public function getPrimaryColumn(): Column
    {
        $primary = $this->columns->find(fn(Column $c): bool => $c->isPrimary);
        if (empty($primary)) {
            throw new RuntimeException("Unable to find primary column for `{$this->entityClass}`");
        }
        return $primary;
    }

    public function getEntityClass(): string
    {
        return $this->entityClass;
    }
}