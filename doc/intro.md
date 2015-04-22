# Distributed Storage

This library provides a storage solution. This solution provides interfaces to stores a directory structures for each
user. Basically it stores file as blob objects in a database which is replaceable by adapters.

The used data-model is inspired by the data-model of git. See information about the 
[data-model here ...](https://github.com/symcloud/distributed-storage/blob/master/doc/data-model.md)

![Data-Model](data-model.png)

## Features

* [File Versioning](https://github.com/symcloud/distributed-storage/blob/master/doc/versioning.md): enables get/restore
 old versions of files and trees
* [File Metadata](https://github.com/symcloud/distributed-storage/blob/master/doc/metadata.md): enables to store
 unstructured data to describe images and links between files.
* [Symlinks](https://github.com/symcloud/distributed-storage/blob/master/doc/symlinks.md): enables links to folder and
 directories inside an other directory tree
* [ACL](https://github.com/symcloud/distributed-storage/blob/master/doc/acl.md): enables security layer for files and
 directories based on a external user provider
