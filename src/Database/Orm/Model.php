<?php

namespace FED\Database\Orm;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Abstract Class Model
 *
 * Enterprise Active Record base model with casting and relationships.
 */
abstract class Model {

	/**
	 * @var string Database table name without prefix.
	 */
	protected $table;

	/**
	 * @var string Primary key name.
	 */
	protected $primaryKey = 'id';

	/**
	 * @var array Attributes that can be mass-assigned.
	 */
	protected $fillable = array();

	/**
	 * @var array Attribute type casts.
	 */
	protected $casts = array();

	/**
	 * @var array Model attributes.
	 */
	protected $attributes = array();

	/**
	 * @var array Original attributes loaded from DB.
	 */
	protected $original = array();

	/**
	 * @var bool Flag whether model exists in DB.
	 */
	public $exists = false;

	/**
	 * Model constructor.
	 *
	 * @param array $attributes
	 */
	public function __construct( array $attributes = array() ) {
		$this->fill( $attributes );
	}

	/**
	 * Fill the model with an array of attributes.
	 *
	 * @param array $attributes
	 * @return $this
	 */
	public function fill( array $attributes ): self {
		foreach ( $attributes as $key => $value ) {
			if ( empty( $this->fillable ) || in_array( $key, $this->fillable, true ) || $key === $this->primaryKey ) {
				$this->setAttribute( $key, $value );
			}
		}
		return $this;
	}

	/**
	 * Get table name.
	 *
	 * @return string
	 */
	public function getTable(): string {
		return $this->table;
	}

	/**
	 * Get primary key.
	 *
	 * @return string
	 */
	public function getKeyName(): string {
		return $this->primaryKey;
	}

	/**
	 * Get primary key value.
	 *
	 * @return mixed
	 */
	public function getKey() {
		return $this->getAttribute( $this->getKeyName() );
	}

	/**
	 * Get query builder for this model.
	 *
	 * @return QueryBuilder
	 */
	public static function query(): QueryBuilder {
		$instance = new static();
		return new QueryBuilder( $instance->getTable() );
	}

	/**
	 * Find model by primary key.
	 *
	 * @param int $id
	 * @return static|null
	 */
	public static function find( $id ) {
		$instance = new static();
		$row      = static::query()->where( $instance->getKeyName(), $id )->first();
		return $row ? static::hydrate( $row ) : null;
	}

	/**
	 * Create and save a new model instance.
	 *
	 * @param array $attributes
	 * @return static
	 */
	public static function create( array $attributes ) {
		$model = new static( $attributes );
		$model->save();
		return $model;
	}

	/**
	 * Retrieve all models.
	 *
	 * @return static[]
	 */
	public static function all(): array {
		$rows   = static::query()->get();
		$models = array();
		foreach ( $rows as $row ) {
			$models[] = static::hydrate( $row );
		}
		return $models;
	}

	/**
	 * Query where shorthand.
	 *
	 * @param string $column
	 * @param mixed  $operator
	 * @param mixed  $value
	 * @return QueryBuilder
	 */
	public static function where( $column, $operator = null, $value = null ): QueryBuilder {
		return static::query()->where( $column, $operator, $value );
	}

	/**
	 * Order by descending shorthand.
	 *
	 * @param string $column
	 * @return QueryBuilder
	 */
	public static function latest( string $column = 'created_at' ): QueryBuilder {
		return static::query()->latest( $column );
	}

	/**
	 * Forward dynamic static calls to query builder.
	 *
	 * @param string $method
	 * @param array  $parameters
	 * @return mixed
	 */
	public static function __callStatic( string $method, array $parameters ) {
		return static::query()->$method( ...$parameters );
	}

	/**
	 * Hydrate a raw DB row into a Model instance.
	 *
	 * @param array $row
	 * @return static
	 */
	public static function hydrate( array $row ) {
		$model             = new static();
		$model->attributes = $row;
		$model->original   = $row;
		$model->exists     = true;
		return $model;
	}

	/**
	 * Save model to database.
	 *
	 * @return bool
	 */
	public function save(): bool {
		$data = $this->prepareForSave();

		if ( $this->exists ) {
			$status = static::query()->where( $this->getKeyName(), $this->getKey() )->update( $data );
			return false !== $status;
		} else {
			$id = static::query()->insert( $data );
			if ( $id ) {
				$this->setAttribute( $this->getKeyName(), $id );
				$this->exists   = true;
				$this->original = $this->attributes;
				return true;
			}
			return false;
		}
	}

	/**
	 * Delete model from database.
	 *
	 * @return bool
	 */
	public function delete(): bool {
		if ( ! $this->exists ) {
			return false;
		}

		$status = static::query()->where( $this->getKeyName(), $this->getKey() )->delete();
		if ( false !== $status ) {
			$this->exists = false;
			return true;
		}
		return false;
	}

	/**
	 * Prepare attributes for DB insertion/update (serialization & casting).
	 *
	 * @return array
	 */
	protected function prepareForSave(): array {
		$data = array();
		foreach ( $this->attributes as $key => $value ) {
			if ( isset( $this->casts[ $key ] ) ) {
				if ( 'json' === $this->casts[ $key ] || 'array' === $this->casts[ $key ] ) {
					$data[ $key ] = is_array( $value ) || is_object( $value ) ? wp_json_encode( $value ) : $value;
					continue;
				}
			}
			$data[ $key ] = $value;
		}
		return $data;
	}

	/**
	 * Set attribute value with casting.
	 *
	 * @param string $key
	 * @param mixed  $value
	 * @return void
	 */
	public function setAttribute( string $key, $value ): void {
		$this->attributes[ $key ] = $value;
	}

	/**
	 * Get attribute value with casting.
	 *
	 * @param string $key
	 * @return mixed
	 */
	public function getAttribute( string $key ) {
		if ( ! array_key_exists( $key, $this->attributes ) ) {
			return null;
		}

		$value = $this->attributes[ $key ];

		if ( isset( $this->casts[ $key ] ) ) {
			switch ( $this->casts[ $key ] ) {
				case 'int':
				case 'integer':
					return (int) $value;
				case 'bool':
				case 'boolean':
					return filter_var( $value, FILTER_VALIDATE_BOOLEAN );
				case 'float':
				case 'decimal':
					return (float) $value;
				case 'json':
				case 'array':
					if ( is_string( $value ) ) {
						$decoded = json_decode( $value, true );
						return json_last_error() === JSON_ERROR_NONE ? $decoded : maybe_unserialize( $value );
					}
					return (array) $value;
			}
		}

		return $value;
	}

	/**
	 * Define a one-to-many relationship.
	 *
	 * @param string $relatedClass
	 * @param string $foreignKey
	 * @param string $localKey
	 * @return array
	 */
	public function hasMany( string $relatedClass, string $foreignKey, string $localKey = 'id' ): array {
		/** @var Model $instance */
		$instance = new $relatedClass();
		$rows     = $instance::query()->where( $foreignKey, $this->getAttribute( $localKey ) )->get();

		$models = array();
		foreach ( $rows as $row ) {
			$models[] = $relatedClass::hydrate( $row );
		}
		return $models;
	}

	/**
	 * Define an inverse one-to-one or one-to-many relationship.
	 *
	 * @param string $relatedClass
	 * @param string $foreignKey
	 * @param string $ownerKey
	 * @return Model|null
	 */
	public function belongsTo( string $relatedClass, string $foreignKey, string $ownerKey = 'id' ): ?Model {
		return $relatedClass::find( $this->getAttribute( $foreignKey ) );
	}

	/**
	 * Convert model attributes to array.
	 *
	 * @return array
	 */
	public function toArray(): array {
		$array = array();
		foreach ( $this->attributes as $key => $val ) {
			$array[ $key ] = $this->getAttribute( $key );
		}
		return $array;
	}

	public function __get( $key ) {
		return $this->getAttribute( $key );
	}

	public function __set( $key, $value ) {
		$this->setAttribute( $key, $value );
	}

	public function __isset( $key ) {
		return isset( $this->attributes[ $key ] );
	}
}
