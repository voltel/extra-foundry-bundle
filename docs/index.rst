.. VoltelExtraFoundryBundle documentation master file, created by
   sphinx-quickstart on Thu Apr 15 22:04:44 2021.
   You can adapt this file completely to your liking, but it should at least
   contain the root `toctree` directive.

%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%
    VoltelExtraFoundryBundle
%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%
$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$
    A wrapper for ``zenstruck/foundry`` with extra speed persisting thousands of entities
$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$

Contents
==========
.. toctree::
   :maxdepth: 3

   Seeding development database <seed_dev_database>
   Testing your application <testing_your_app>
   Testing the bundle <testing_bundle>
      Glossary<glossary>
      Quirks<bundle_quirks>

Extra
==================

.. * :ref:`genindex`
.. * :ref:`modindex`

* :ref:`search`



Installation
============

Make sure Composer is installed globally, as explained in the
`Installation chapter`_ of the Composer documentation.

----------------------------------

Open a command console, enter your project directory and execute:

.. code-block:: shell

    $ composer require voltel/extra-foundry-bundle

Applications that don't use Symfony Flex
----------------------------------------

Step 1: Download the Bundle
~~~~~~~~~~~~~~~~~~~~~~~~~~~

Open a command console, enter your project directory and execute the
following command to download the latest stable version of this bundle:

.. code-block:: shell

    $ composer require voltel/extra-foundry-bundle --dev

Step 2: Enable the Bundle
~~~~~~~~~~~~~~~~~~~~~~~~~

Then, enable the bundle by adding it to the list of registered bundles
in the ``config/bundles.php`` file of your project:

.. code-block:: php

    // config/bundles.php
    return [
        // ...
        Voltel\ExtraFoundryBundle\VoltelExtraFoundryBundle::class => ['dev' => true, 'test' => true],
    ];


Next step
============

Start by reading how to :doc:`seed your development database <seed_dev_database>`
using |bundle| and its services.


.. Links:

.. _`installation chapter`: https://getcomposer.org/doc/00-intro.md

.. Replace

.. |bundle| replace:: `VoltelExtraFoundryBundle`
