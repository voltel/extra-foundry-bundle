.. include:: common_parts.rst

%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%
    Bundle quirks
%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%

This bundle came into existence as an attempt to solve the problem
of ``zenstruck/foundry`` taking way too much time persisting
its newly created entities when the numbers of entities reached
a thousand and above.
The reason for that slow speed was the fact
that every entity was immediately persisted and flushed,
which is done with Doctrine and is a very resource engaging process.

So, the the first approach to solve this problem
was to create factories that wouldn't immediately persist
(i.e. to invoke a :code:`Zenstruck\Foundry\Factory::withoutPersisting()` method),
then collect the products of their labor
(i.e. entities wrapped in objects of |Proxy| class)
and persist/flush them in batches.

There is a certain balance between
the workload (count of entities to save),
time that a persist operation takes
and time that it takes to flush the batch.
When the batch is too big, it takes a lot of computing resource
to perform a persist operation with Doctrine calculating a large unit of work.
But when the batches are small and the flush operation is frequent,
we end up with what we started from --
low performance due to the overall overhead of
connecting to the database and executing many small queries.

I found out in my experiments, that by dealing in batches
that do not exceed approximately 10,000 entities,
there is a good chance that time that it takes
to perform both persist and flush operations
is going to be acceptably balanced.

So, I started doing just this until I realized that
merely to :term:`delay persistence` might not be enough --
I still needed to invoke a related :method:`afterPersist()` callback
that could have been configured for a model factory
in its :method:`initialize()` method.

And while the :term:`delayed persistence` approach
didn't interfere with :method:`instantiateWith()`,
:method:`beforeInstantiate()` or :method:`afterInstantiate()` callbacks,
it did prevent the invocation of the :method:`afterPersist()` callback
for factories modified with :method:`withoutPersisting()`.
Was this a quirk or a feature of a ``zenstruck/foundry`` I do not know.

Here is `a permalink to source code`_ of :method:`Zenstruck\Foundry\Factory::create()` method
where you can see that if the :method:`isPersisting()` returns ``false``,
then the code responsible for executing the ``afterPersist`` callbacks is not run.
And there is no available interface (i.e. ``public`` method)
to execute those callbacks afterwards from your code.

.. code-block:: php
   :lineno-start: 99
   :emphasize-lines: 5, 6, 10, 11
   :dedent: 4

        // in Zenstruck\Foundry\Factory

        $proxy = new Proxy($object);

        if (!$this->isPersisting()) {
            return $proxy;
        }

        return $proxy->save()->withoutAutoRefresh(function(Proxy $proxy) use ($attributes) {
            foreach ($this->afterPersist as $callback) {
                $proxy->executeCallback($callback, $attributes);
            }
        });

With this new challenge, the first approach was
to try and identify the factory class
that produces entities of the class
that has just been persisted and flushed,
then get the callback from this class and execute it
passing a newly persisted/flushed entity.

See the bundle's :method:`EntityProxyPersistService::getFactoryForEntityOfClass()` method
for implementation.
You will see that to identify this factory,
``Symfony\Component\DependencyInjection\ServiceLocator`` service is injected,
and it is configured in the bundle's :file:`services.xml`
to collect services tagged with ``foundry.factory`` tag
and store them with the key provided by
:method:`Voltel\ExtraFoundryBundle\Foundry\Factory\AbstractFactory::getClassName()` method,
which is public.
Had :method:`Zenstruck\Foundry\ModelFactory::getClass()` been declared ``public``,
it could have been used instead in the ``ServiceLocator`` declaration.
But it is declared ``protected`` and cannot be used for the task.

.. code-block:: xml
   :emphasize-lines: 8

       <!-- in bundle's "config/services.xml" -->

       <services>
            <!-- ... -->

            <service id="voltel_extra_foundry.persist_service" class="Voltel\ExtraFoundryBundle\Service\FixtureEntity\EntityProxyPersistService">
               <argument type="service" id="doctrine" />
               <argument type="tagged_locator" tag="foundry.factory" default-index-method="getClassName" />
            </service>

            <!-- ... -->
       </services>

If you extend your factory classes from |AbstractFactory|,
you can *"help"* service locator to identify the proper factory
a little bit faster.
Otherwise, the algorithm will iterate every factory in scope
(i.e. services tagged with ``foundry.factory`` tag),
try to "force-invoke" the protected :method:`getClass()` method
to see if it matches the class name of the entity in question.
Not a *big deal* really, as it's all done blazingly fast.

But to get access to the :method:`afterPersist()` callbacks,
it invokes the :method:`initialize` method
(which is expected to set an array of callbacks),
and then invoke each of the callbacks in the loop.
It is not an elegant solution at all, especially considering the fact
that with this approach there is no way
we can identify attributes that were used
during the :method:`Factory::create()` invocation.
If any of the :method:`afterPersist()` callbacks depended on the attributes array,
the results were going to be unpredictable.

So, this is when the need to get away from
:method:`addProxy` and :method:`addProxyBatch` became obvious
in favor of :method:`createOne` and :method:`createMany`
to not only save in the internals entities for :term:`delayed persistence`,
but to save a factory that produced those entities as well,
and invoke :method:`afterPersist` callbacks that could exist on the factory
after the flush operation.

Right now, the bundle has in its guts the :method:`addProxy` and :method:`addProxyBatch`
methods, which once were ``public``, now are declared ``protected``
but may be removed in future versions.

----------------------------------------------------------------------

On the whole, the trickery described above wouldn't even be needed
if there were a legitimate interface to execute the :method:`afterPersist()` callbacks
from inside the created |Proxy| object,
or at least from inside the factory that "spawned" the entity in question.


.. Replaces:

.. |bundle| replace:: `VoltelExtraFoundryBundle`

.. |Proxy| replace:: :class:`\Zenstruck\Foundry\Proxy`

.. |AbstractFactory| replace:: :class:`Voltel\ExtraFoundryBundle\Foundry\Factory\AbstractFactory`


.. Links:

.. _`a permalink to source code`: https://github.com/zenstruck/foundry/blob/9d424010aa73b3d2be0443bab3056cb83b43896e/src/Factory.php#L103