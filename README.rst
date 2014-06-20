eZDisqus
========
[![Analytics](https://ga-beacon.appspot.com/UA-52121860-1/ezdisqus/readme)](https://github.com/igrigorik/ga-beacon)
**eZDisqus** is an integration of `Disqus commenting system <httsp://disqus.com>`_ in eZ Publish.

The goal is to have a tight integration of the **Disqus** service inside any eZ Publish installation.
This extension provides a `datatype <http://doc.ez.no/eZ-Publish/Technical-manual/4.x/Concepts-and-basics/Content-management/Datatypes>`_
that can be added to any content class.


Features
--------
Out of the box, **eZDisqus** comes with:

- Native datatype
- Dashboard widget
- Comment lookup in admin interface for each content object having Disqus datatype
- Exporters to migrate from an existing commenting system (such as `eZComments <https://github.com/ezsystems/ezcomments>`_ 
  or native eZ Publish comments)

Requirements
------------
eZ Disqus requires eZ Publish version 4.6 (aka **Annapurna**, or 
`Community version 2011.6 <http://share.ez.no/downloads/downloads/ez-publish-community-project-2011.11>`_).

PHP 5.3.x is required if you use the export system as it uses namespaces and some advanced features.
Otherwise, the requirements are the same as for eZ Publish.

Old versions of eZ Publish
''''''''''''''''''''''''''
If you don't use the export API, eZDisqus would probably run smoothly on top of eZ Publish 4.1+.
**However, autoload generator might have unexpected issues** (see `Install 5th point`_) since namespace support has been added
in **Annapurna**. You might need to patch the autoload generator if you have issues
(see `this patch <https://github.com/ezsystems/ezpublish/commit/daaa00>`_).

Install
-------
First of all, you'll need to setup a `new account to the Disqus service <http://disqus.com/admin/register/>`_.

1. Clone the repository in an eZPublish instance extension dir, ``extension/ezdisqus/``
2. Activate the extension in eZ Publish
3. Regenerate autoloads via ``php bin/php/ezpgenerateautoloads.php -e -p``
4. Add the Disqus datatype to the desired content classes
5. Configure the extension by making an override of ``extension/ezdisqus/settings/disqus.ini``
  - Specify your **shortname**
  - Optionnaly enable the **DevelopmentMode** which allows you to test comments on an inaccessible website (e.g. local development)
  - Please note that at the moment **all settings must be shared between admin and front siteaccesses** to work properly.

That's it! Your now ready to use Disqus comments!

Comments migration
------------------
Disqus allows you to import already existing comments into their service.
To make it easy, **eZDisqus** provides an export system that will generate an XML file in the right format (aka *Disqus WXR*).

Export script example from eZComments::

  <?php
  use Disqus\Export\Processor as ExportProcessor,
      Disqus\Export\Exporter\EzComments as EzCommentsExporter,
      Disqus\Export\Formatter\DisqusWXR as DisqusFormatter;
  
  $processor = new ExportProcessor(
      new EzCommentsExporter(),
      new DisqusFormatter()
  );
  $processor->export();
  echo $processor->render();

Note that above script would need to be run with ``ezexec.php``::

  php bin/php/ezexec.php extension/ezdisqus/bin/php/export.php



