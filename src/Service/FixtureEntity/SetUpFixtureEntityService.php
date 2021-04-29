<?php

namespace Voltel\ExtraFoundryBundle\Service\FixtureEntity;

use Zenstruck\Foundry\ModelFactory;

class SetUpFixtureEntityService
{
    public const KEY_STATES = 'states';
    public const KEY_ATTRIBUTES = 'attributes';
    public const KEY_COUNT = 'count';

    /** @var EntityProxyPersistService */
    private $persistService;

    /**
     * SetUpFixtureEntityService constructor.
     */
    public function __construct(
        EntityProxyPersistService $entityProxyPersistService
    )
    {
        $this->persistService = $entityProxyPersistService;
    }


    /**
     * Uses model factory from parameter 1 to create entities
     * according to "spawning" instructions from parameter 2.
     *
     * Parameter 2 is expected to hold an array of arrays (a "chunk"),
     * where each nested array is configuring creation of one batch of entities (a "spawn").
     *
     * A "spawn" of entities is an array of one or more entities (a batch)
     * created with the same set of factory "states" and model "attributes".
     *
     * If the third parameter is "true", the entities will be persisted/flushed
     * after instantiation.
     *
     * Returns a chunk (an array of array) of created entities:
     * for each "spawning" instruction set there will be an array of entities
     * created with this instruction set.
     * Descriptive keys from instructions array are preserved in the resulting array.
     *
     * @return array
     */
    public function createEntities(
        ModelFactory $entity_factory,
        array $a_instructions_chunk,
        // NB! See notes below
        bool $l_persist_and_flush = false
    ): array
    {
        $a_entity_chunk = []; // array or arrays

        foreach ($a_instructions_chunk as $c_description => $a_this_instruction_set) {

            $a_this_spawn_config = $this->normalizeInstructions($entity_factory, $a_this_instruction_set, $c_description);
            $n_entity_count = $a_this_spawn_config[self::KEY_COUNT];
            $a_factory_states = $a_this_spawn_config[self::KEY_STATES];
            $a_model_attributes = $a_this_spawn_config[self::KEY_ATTRIBUTES];

            if ($n_entity_count === 0) continue;

            if ($n_entity_count > 1) {
                $a_new_entity_batch = $this->persistService->createMany($entity_factory, $n_entity_count, $a_model_attributes, $a_factory_states);
                $a_entity_chunk[$c_description] = $a_new_entity_batch;

            } else {
                $entity = $this->persistService->createOne($entity_factory, $a_model_attributes, $a_factory_states);
                $a_entity_chunk[$c_description] = [$entity];
            }//endif
        }//end foreach

        // If "true" is passed in the "test" environment, and persist and flush is performed, we get a PDO Exception:
        // PDOException: SQLSTATE[42000]: Syntax error or access violation: 1305 SAVEPOINT DOCTRINE2_SAVEPOINT_3 does not exist in
        // vendor\doctrine\dbal\lib\Doctrine\DBAL\Driver\PDOConnection.php:51
        if ($l_persist_and_flush) {
            $this->persistService->persistAndFlushAll();
        }//endif

        return $a_entity_chunk;
    }//end of function


    /**
     * Takes a model factory in parameter 1 and
     * an associative array with instructions for one "spawn" in parameter 2,
     * and returns a normalized instructions array.
     *
     * Instructions in parameter 2 can be provided
     * in either an "explicit" or an "implicit" style.
     *
     * An "explicit" style of instructions in parameter 2 is determined
     * when at least one of the reserved keys (i.e. "count", "states", "attributes")
     * is present in the instructions array.
     *
     * An "implicit" (simplified) style of instructions in parameter 2 is
     * determined when none of the reserved keys are found in the instructions array.
     *
     * For "implicit" style of instructions the following logic is used:
     * - if the first array item has key "0" and its value is numeric,
     *   the numeric value will be used to populate the "count" key;
     *
     * - if a method exists on an entity factory with the name
     *   from an array item **value** (when the key in numeric),
     *   it will be interpreted as the name of the factory state to apply;
     *
     * - if a method exists on an entity factory with the name
     *   from an array item **key** (when the key in not numeric),
     *   it will be interpreted as name of the factory state to apply.
     *   In this case, this array item's value will be interpreted as
     *   value(s) for this factory state.
     *
     * - all other key-value pairs will be interpreted
     *   as attributes for the factory "create()" method.
     *
     * - notice: custom keys in the instruction array are ignored with "explicit" style
     *   and will be falsely interpreted as model attributes with "implicit" style.
     *   Do not use custom keys in simplified style "spawning" instructions array.
     */
    public function normalizeInstructions(
        ModelFactory $entity_factory,
        array $a_instructions,
        string $c_description = null
    ): array
    {
        if (array_key_exists(self::KEY_COUNT, $a_instructions)
            || array_key_exists(self::KEY_STATES, $a_instructions)
            || array_key_exists(self::KEY_ATTRIBUTES, $a_instructions)
        ) {
            $a_factory_states = $a_instructions[self::KEY_STATES] ?? [];
            $a_model_attributes = $a_instructions[self::KEY_ATTRIBUTES] ?? [];
            $n_entity_count = $a_instructions[self::KEY_COUNT] ?? 1;

        } else {
            $n_entity_count = 1;
            $a_factory_states = [];
            $a_model_attributes = [];

            foreach ($a_instructions as $c_key => $mix_value) {
                // The first array item, if numeric, denotes the number of entities to create
                if (is_numeric($c_key) && 0 == $c_key && is_numeric($mix_value)) {
                    $n_entity_count = (int) $mix_value;
                    continue;
                }//endif

                if (is_numeric($c_key) && is_string($mix_value)) {
                    if (method_exists($entity_factory, $mix_value)) {
                        // a "state" method w/o arguments
                        $a_factory_states[] = $mix_value;
                    } else {
                        throw new \InvalidArgumentException(sprintf('Bad spawning instructions at key "%s": "%s" is not a valid state for factory of class "%s"',
                            $c_description, $mix_value, get_class($entity_factory)));
                    }//endif

                } elseif (is_string($c_key)) {
                    if (method_exists($entity_factory, $c_key)) {
                        // a "state" method /w argument(s).
                        // Note: If only one argument, it can be not in an array. E.g. "withChildren" => 2 is equivalent to "withChildren" => [2]
                        $a_factory_states[$c_key] = (array) $mix_value;
                    } else {
                        $a_model_attributes[$c_key] = $mix_value;
                    }//endif

                } else {
                    throw new \InvalidArgumentException(sprintf('Bad spawning instructions at key "%s": item "%s" is not a valid state or a model attribute for factory of class "%s"',
                        $c_description, $mix_value, get_class($entity_factory)));
                }//endif

            }//endforeach
        }//endif

        // Sanity check.
        if (!is_int($n_entity_count) || $n_entity_count < 0) {
            throw new \LogicException(sprintf('Expected valid configuration for entity count in spawning instruction set "%s".', $c_description));
        }//endif

        return [
            self::KEY_COUNT => $n_entity_count,
            self::KEY_STATES => $a_factory_states,
            self::KEY_ATTRIBUTES => $a_model_attributes
        ];
    }//end of function


    public function getPersistService(): EntityProxyPersistService
    {
        return $this->persistService;
    }


}//end of class
