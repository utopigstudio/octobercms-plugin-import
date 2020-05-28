<?php namespace Utopigs\Import\Classes;

use File;
use Yaml;
use DB;
use Log;
use Illuminate\Filesystem\Filesystem;

class Import
{
    protected $path;

    /**
     * Load data from a file
     * 
     * @param string $filename Name of the file, it must exist in the provided path
     * 
     * @return array
     */
    public function loadImportFile($filename)
    {
        if (!File::exists($this->path.$filename)) {
            return NULL;
        }

        $type = explode('.', $filename);
        $type = array_pop($type);

        if ($type == 'yaml') {
            return (array)Yaml::parseFile($this->path.$filename);
        }

        if ($type == 'php') {
            $filesystem = new Filesystem();
            return $filesystem->getRequire($this->path.$filename);
        }

        if ($type == 'xml') {
            $xml = simplexml_load_file($this->path.$filename, null, LIBXML_NOCDATA | LIBXML_NOBLANKS);
            $xml = json_decode(json_encode((array)$xml), true);
            return $xml;
        }

        return NULL;
    }

    /**
     * Import raw table data
     */
    public function importTable($table, $result)
    {
        DB::table($table)->insert($result);
    }

    /**
     * Import data into a model
     * 
     * @param Model $model Model to use
     * @param array $result Data loaded from the import file
     * @param array $relationNames Names of fields that are many-to-many relations
     * @param array $attachmentFields Names of the fields that are attachments
     */
    public function importModel($model, $result, $relationNames = [], $attachmentFields = [])
    {
        $relations = [];
        $attachments = [];

        foreach ($result as $value) {
            foreach ($relationNames as $relationName) {
                if (isset($value[$relationName])) {
                    $relations[$relationName] = $value[$relationName];
                    unset($value[$relationName]);
                }
            }

            foreach ($attachmentFields as $attachmentField) {
                if (isset($value[$attachmentField])) {
                    $attachments[$attachmentField] = $value[$attachmentField];
                    //don't unset value so required fields validation doesn't fail
                }
            }

            $dbmodel = $model::create($value);

            foreach ($relations as $relationName => $relation_ids) {
                foreach ($relation_ids as $relation_id) {
                    $dbmodel->$relationName()->attach($relation_id);
                }
            }

            foreach ($attachments as $attachmentField => $image_files) {
                if (!is_array($image_files)) $image_files = [$image_files];
                foreach ($image_files as $image_file) {
                    try {
                        $dbmodel->$attachmentField()->create(['data' => $this->path . $image_file]);
                    } catch (\Exception $e) {
                        Log::error($e->getMessage());
                    }
                }
            }

            $dbmodel->save();
        }
    }

}