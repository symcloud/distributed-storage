<?php

/*
 * This file is part of the Symcloud Distributed-Storage.
 *
 * (c) Symcloud and Johannes Wachter
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace Symcloud\Component\Database\Search;

use Symcloud\Component\Database\Metadata\ClassMetadataInterface;
use Symcloud\Component\Database\Model\ModelInterface;
use Symcloud\Component\Database\Search\Hit\Hit;
use Symcloud\Component\Database\Search\Hit\HitInterface;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Finder\Finder;
use ZendSearch\Lucene;

class ZendLuceneAdapter implements SearchAdapterInterface
{
    /**
     * @var string
     */
    const HASH_FIELDNAME = '__hash';

    /**
     * @var string
     */
    private $basePath;

    /**
     * @var Filesystem
     */
    private $fileSystem;

    /**
     * @var Lucene\Index[]
     */
    private $indexes = array();

    /**
     * ZendLuceneAdapter constructor.
     *
     * @param string $basePath
     * @param Filesystem $fileSystem
     */
    public function __construct($basePath, Filesystem $fileSystem)
    {
        $this->basePath = $basePath;
        $this->fileSystem = $fileSystem;
    }

    public function index($hash, ModelInterface $model, ClassMetadataInterface $metadata)
    {
        $indexName = $metadata->getContext();

        $index = $this->getLuceneIndex($indexName);

        // check to see if the subject already exists
        $this->removeExisting($index, $hash);

        $luceneDocument = new Lucene\Document();
        $luceneDocument->addField(Lucene\Document\Field::text(self::HASH_FIELDNAME, $hash));
        foreach ($metadata->getMetadataFields() as $field) {
            $value = $field->getValue($model);
            if (is_string($value)) {
                $luceneDocument->addField(Lucene\Document\Field::keyword($field->getName(), $value));
            } elseif ($value instanceof \DateTime) {
                $luceneDocument->addField(Lucene\Document\Field::keyword($field->getName(), $value->getTimestamp()));
            }
        }
        $index->addDocument($luceneDocument);

        return $luceneDocument;
    }

    public function search($query, $contexts = array())
    {
        $searcher = new Lucene\MultiSearcher();
        foreach ($contexts as $indexName) {
            $indexPath = $this->getIndexPath($indexName);
            if (!file_exists($indexPath)) {
                continue;
            }
            $searcher->addIndex($this->getIndex($indexPath, false));
        }

        $query = Lucene\Search\QueryParser::parse($query);

        try {
            $luceneHits = $searcher->find($query);
        } catch (\RuntimeException $e) {
            if (!preg_match('&non-wildcard characters&', $e->getMessage())) {
                throw $e;
            }
            $luceneHits = array();
        }

        $hits = array();
        foreach ($luceneHits as $luceneHit) {
            /* @var Lucene\Search\QueryHit $luceneHit */
            $luceneDocument = $luceneHit->getDocument();
            $hit = new Hit();

            $hit->setScore($luceneHit->score);
            $hit->setHash($luceneDocument->getFieldValue(self::HASH_FIELDNAME));

            foreach ($luceneDocument->getFieldNames() as $fieldName) {
                $hit->addMetadata($fieldName, $luceneDocument->getFieldValue($fieldName));
            }
            $hits[] = $hit;
        }

        // The MultiSearcher does not support sorting, so we do it here.
        usort(
            $hits,
            function (HitInterface $documentA, HitInterface $documentB) {
                if ($documentA->getScore() < $documentB->getScore()) {
                    return true;
                }

                return false;
            }
        );

        return $hits;
    }

    public function getStatus()
    {
        $finder = new Finder();
        $indexDirs = $finder->directories()->depth('== 0')->in($this->basePath);
        $status = array();
        foreach ($indexDirs as $indexDir) {
            /* @var  $indexDir \Symfony\Component\Finder\SplFileInfo; */
            $indexFinder = new Finder();
            $files = $indexFinder->files()->name('*')->depth('== 0')->in($indexDir->getPathname());
            $indexName = basename($indexDir);
            $index = $this->getIndex($this->getIndexPath($indexName));
            $indexStats = array(
                'size' => 0,
                'nb_files' => 0,
                'nb_documents' => $index->count(),
            );
            foreach ($files as $file) {
                $indexStats['size'] += filesize($file);
                $indexStats['nb_files']++;
            }
            $status['idx:' . $indexName] = json_encode($indexStats);
        }

        return $status;
    }

    public function deindex($hash, ClassMetadataInterface $metadata)
    {
        $index = $this->getLuceneIndex($metadata->getContext());
        $this->removeExisting($index, $hash);
        $index->commit();
    }

    public function deindexAll()
    {
        $this->fileSystem->remove(new \FilesystemIterator($this->basePath));

        return true;
    }

    /**
     * Remove the existing entry for the given Document from the index, if it exists.
     *
     * @param Lucene\Index $index The Zend Lucene Index
     * @param string $hash
     */
    private function removeExisting(Lucene\Index $index, $hash)
    {
        try {
            $hits = $index->find(self::HASH_FIELDNAME . ':' . $hash);
            foreach ($hits as $hit) {
                $index->delete($hit->id);
            }
        } catch (\Exception $ex) {
            // FIXME no result ???
        }
    }

    /**
     * Return (or create) a Lucene index for the given name.
     *
     * @param string $indexName
     *
     * @return Lucene\Index
     */
    private function getLuceneIndex($indexName)
    {
        if (array_key_exists($indexName, $this->indexes)) {
            return $this->indexes[$indexName];
        }

        $indexPath = $this->getIndexPath($indexName);
        if (!file_exists($indexPath)) {
            $this->getIndex($indexPath, true);
        }

        return ($this->indexes[$indexName] = $this->getIndex($indexPath, false));
    }

    /**
     * Determine the index path for a given index name.
     *
     * @param string $indexName
     *
     * @return string
     */
    private function getIndexPath($indexName)
    {
        return sprintf('%s/%s', $this->basePath, $indexName);
    }

    /**
     * Return the index. Note that we override the default ZendSeach index
     * to allow us to catch the exception thrown during __destruct when running
     * functional tests.
     *
     * @param string $indexPath
     * @param bool $create Create an index or open it
     *
     * @return Lucene\Index
     */
    private function getIndex($indexPath, $create = false)
    {
        $index = new Lucene\Index($indexPath, $create);

        return $index;
    }
}
