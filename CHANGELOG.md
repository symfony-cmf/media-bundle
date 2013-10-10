Changelog
=========

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

