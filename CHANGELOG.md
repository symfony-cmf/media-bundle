Changelog
=========

1.3.0
-----

* **2016-02-08**: Symfony 2.8 and PHP 7 support.
* **2016-02-05**: Fixed node type definition for metadata in the PHPCR File documents.
  If you saw errors with metadata, you can update the node definitions. (in jackalope-
  jackrabbit that requires exporting a database dump and creating a new repository.)

1.2.0-RC1
---------

* **2014-06-06**: Updated to PSR-4 autoloading

1.1.0
-----

Release 1.1.0

1.1.0-RC2
---------

* **2014-04-14**: ImagineCacheInvalidatorSubscriber receives a service container instance
* **2014-04-11**: drop Symfony 2.2 compatibility

1.1.0-RC1
---------

* **2013-12-26**: 1.0 allowed everybody to edit content if there was no
  firewall configured on a route. This version is more secure, preventing
  editing if there is no firewall configured. If you want to allow everybody
  to edit content, set `cmf_media.upload_file_role: false`.

1.0.0
-----

* **2013-10-09**: [Model] Added cmf:media, cmf:image mixin and changed the node
    types to nt:folder, nt:file and created the cmf:mediaNode.

    To upgrade create a migrator, see https://github.com/symfony-cmf/migration-scripts/blob/master/src/media_bundle_mixin_types.php,
    and run from the project root:

    $ app/console doctrine:phpcr:repository:init
    $ app/console doctrine:phpcr:migrator:migrate media_node_type

    Important is to register the new CND before migrating using the repository
    init command. The migrator moves all nodes to a temporary path and creates
    new nodes using the same properties and the new model. This is needed
    because Jackrabbit does not allow to change the node primary type.

1.0.0-RC2
---------

* **2013-09-29**: [File] changed UploadFileHelper class to an interface and
  a Doctrine implementation

1.0.0-RC1
---------

* **2013-09-11**: Increased minimal symfony-framework dependency to 2.2
* **2013-08-31**: Removed __toString() method
* **2013-08-27**: Elfinder Adapter

