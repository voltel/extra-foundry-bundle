%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%
    VoltelExtraFoundryBundle
%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%


.. Glossary

.. glossary::
    delayed persistence
    delay persistence
        The practice of creating new entities with Doctrine
        when one or many entities are not immediately registered with Doctrine
        for persistence.
        When batches of entities under the same entity manager
        are finally persisted, the entity manager is then flushed
        to create entities in a database.


