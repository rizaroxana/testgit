<?php

namespace App\Repositories;

use Carbon\Carbon;
use Illuminate\Database\Query\Builder;


/**
 * Class ProdusRepository
 *
 */
class ProdusRepository extends RepositoryBase
{

    /**
     * Insert a new product in the database
     *
     * @param string $slug
     * @param string $status
     * @param string $type
     * @param integer $position
     * @param integer $parentId
     * @param string|null $publishedOn
     * @return int
     */
    public function create($nume, $descriere, $buc, $status)
    {
        $id = null;

        $contentId = $this->queryTable()->insertGetId(
            [
                'nume' => $nume,
                'status' => $status,
                'buc' => $buc,
                'descriere' => $descriere
            ]
        );

       // $this->reposition($contentId, $position);

        return $contentId;
    }

    /**
     * Update a category record, recalculate position, regenerate tree and return the category id
     *
     * @param $id
     * @param string $slug
     * @param string $status
     * @param string $type
     * @param integer $position
     * @param integer $parentId
     * @param string|null $publishedOn
     * @param string|null $archivedOn
     * @return int $categoryId
     */
    public function update(
        $id,
        $slug,
        $status,
        $type,
        $position,
        $parentId,
        $publishedOn,
        $archivedOn
    ) {
        $this->queryTable()->where('id', $id)->update(
            [
                'slug' => $slug,
                'status' => $status,
                'type' => $type,
                'position' => $position,
                'parent_id' => $parentId,
                'published_on' => $publishedOn,
                'created_on' => Carbon::now()->toDateTimeString(),
                'archived_on' => $archivedOn,
            ]
        );

        $this->reposition($id, $position);

        return $id;
    }

    /**
     * @param int $id
     * @param bool $deleteChildren
     * @return int
     */
    public function delete($id, $deleteChildren = false)
    {
        $content = $this->getById($id);

        // unlink fields and data
        $this->unlinkField($id);
        $this->unlinkDatum($id);

        if ($deleteChildren) {
            // unlink children content
            $this->unlinkChildren($id);
        }

        $delete = $this->queryTable()->where('id', $id)->delete();

        $this->otherChildrenRepositions($content['parent_id'], $id, 0);

        return $delete;
    }

    /**
     * Unlink all fields for a content id, or pass in the field id to delete a specific content field link
     *
     * @param $contentId
     * @param null $fieldId
     * @return int
     */
    public function unlinkField($contentId, $fieldId = null)
    {
        if (!is_null($fieldId)) {
            return $this->contentFieldsQuery()
                ->where('content_id', $contentId)
                ->where('field_id', $fieldId)
                ->delete();
        }

        return $this->contentFieldsQuery()->where('content_id', $contentId)->delete();
    }

    /**
     * Unlink all datum for a content id, or pass in the field id to delete a specific content datum link
     *
     * @param $contentId
     * @param null $datumId
     * @return int
     */
    public function unlinkDatum($contentId, $datumId = null)
    {
        if (!is_null($datumId)) {
            return $this->contentDataQuery()
                ->where('content_id', $contentId)
                ->where('datum_id', $datumId)
                ->delete();
        }

        return $this->contentDataQuery()->where('content_id', $contentId)->delete();
    }

    /**
     * @param $id
     * @return int
     */
    public function unlinkChildren($id)
    {
        return $this->queryTable()->where('parent_id', $id)->update(['parent_id' => null]);
    }

    /**
     * Insert a new record in railcontent_content_data
     * @param integer $contentId
     * @param integer $datumId
     * @return int
     */
    public function linkDatum($contentId, $datumId)
    {
        return $this->contentDataQuery()->insertGetId(
            [
                'content_id' => $contentId,
                'datum_id' => $datumId
            ]);
    }

    /**
     * Insert a new record in railcontent_content_fields
     * @param integer $contentId
     * @param integer $fieldId
     * @return int
     */
    public function linkField($contentId, $fieldId)
    {
        return $this->contentFieldsQuery()->insertGetId(
            [
                'content_id' => $contentId,
                'field_id' => $fieldId
            ]);
    }

    /**
     * Get the content and the linked datum from database
     * @param integer $datumId
     * @param integer $contentId
     */
    public function getLinkedDatum($datumId, $contentId)
    {
        $dataIdLabel = ConfigService::$tableData.'.id';

        return $this->contentDataQuery()
            ->leftJoin(ConfigService::$tableData,'datum_id','=',$dataIdLabel)
            ->where(
                [
                    'datum_id' => $datumId,
                    'content_id' => $contentId
                ]
            )->get()->first();
    }

    /**
     * Get the content and the associated field from database
     * @param integer $fieldId
     * @param integer $contentId
     */
    public function getLinkedField($fieldId, $contentId)
    {
        $fieldIdLabel = ConfigService::$tableFields.'.id';

        return $this->contentFieldsQuery()
            ->leftJoin(ConfigService::$tableFields,'field_id','=',$fieldIdLabel)
            ->where(
                [
                    'key' => $fieldId,
                    'content_id' => $contentId
                ]
            )->get()->first();
    }

    /**
     * Get the content and the associated field from database based on key
     * @param string $key
     * @param integer $contentId
     */
    public function getContentLinkedFieldByKey($key, $contentId)
    {
        $fieldIdLabel = ConfigService::$tableFields.'.id';

        return $this->contentFieldsQuery()
            ->leftJoin(ConfigService::$tableFields,'field_id','=',$fieldIdLabel)
            ->where(
                [
                    'key' => $key,
                    'content_id' => $contentId
                ]
            )->get()->first();
    }

    /**
     * Get the content and the associated datum from database based on key
     * @param string $key
     * @param integer $contentId
     */
    public function getContentLinkedDatumByKey($key, $contentId)
    {
        $datumIdLabel = ConfigService::$tableData.'.id';

        return $this->contentDataQuery()
            ->leftJoin(ConfigService::$tableData,'datum_id','=',$datumIdLabel)
            ->where(
                [
                    'key' => $key,
                    'content_id' => $contentId
                ]
            )->get()->first();
    }

    /**
     * @return Builder
     */
    public function queryTable()
    {
        return parent::connection()->table('produs');
    }

    /**
     * @return Builder
     */
    public function queryIndex()
    {
        return $this->queryTable()
            ->select(
                [
                    ConfigService::$tableContent . '.id as id',
                    ConfigService::$tableContent . '.slug as slug',
                    ConfigService::$tableContent . '.status as status',
                    ConfigService::$tableContent . '.type as type',
                    ConfigService::$tableContent . '.position as position',
                    ConfigService::$tableContent . '.parent_id as parent_id',
                    ConfigService::$tableContent . '.published_on as published_on',
                    ConfigService::$tableContent . '.created_on as created_on',
                    ConfigService::$tableContent . '.archived_on as archived_on',
                    //ConfigService::$tableContentFields . '.field_id as field_id',
                    ConfigService::$tableFields . '.id as field_id',
                    ConfigService::$tableFields . '.key as field_key',
                    ConfigService::$tableFields . '.value as field_value',
                    ConfigService::$tableFields . '.type as field_type',
                    ConfigService::$tableFields . '.position as field_position',
                    ConfigService::$tableData. '.id as datum_id',
                    ConfigService::$tableData . '.key as datum_key',
                    ConfigService::$tableData . '.value as datum_value',
                    ConfigService::$tableData . '.position as datum_position',

                ]
            )
            ->leftJoin(
                ConfigService::$tableContentData,
                ConfigService::$tableContentData . '.content_id',
                '=',
                ConfigService::$tableContent . '.id'
            )
            ->leftJoin(
                ConfigService::$tableData,
                ConfigService::$tableData . '.id',
                '=',
                ConfigService::$tableContentData . '.datum_id'
            )
            ->leftJoin(
                ConfigService::$tableContentFields,
                ConfigService::$tableContentFields . '.content_id',
                '=',
                ConfigService::$tableContent . '.id'
            )
            ->leftJoin(
                ConfigService::$tableFields,
                ConfigService::$tableFields . '.id',
                '=',
                ConfigService::$tableContentFields . '.field_id'
            )
            ->groupBy([ConfigService::$tableFields . '.id', ConfigService::$tableContent . '.id', ConfigService::$tableData . '.id']);
    }

    /**
     * @return Builder
     */
    public function contentFieldsQuery()
    {
        return parent::connection()->table(ConfigService::$tableContentFields);
    }

    /**
     * @return Builder
     */
    public function contentDataQuery()
    {
        return parent::connection()->table(ConfigService::$tableContentData);
    }

    /**
     * @param $fieldsWithContent
     * @return array
     */
    private function parseAndGetLinkedContent($fieldsWithContent)
    {
        $linkedContentIdsToGrab = [];

        foreach ($fieldsWithContent as $fieldWithContent) {
            if ($fieldWithContent['field_type'] === 'content_id') {
                $linkedContentIdsToGrab[] = $fieldWithContent['field_value'];
            }
        }

        $linkedContents = [];

        if (!empty($linkedContentIdsToGrab)) {
            $linkedContents = $this->getManyById($linkedContentIdsToGrab);
        }

        $content = [];

        foreach ($fieldsWithContent as $fieldWithContent) {
            $content[$fieldWithContent['id']] = [
                'id' => $fieldWithContent['id'],
                'slug' => $fieldWithContent['slug'],
                'status' => $fieldWithContent['status'],
                'type' => $fieldWithContent['type'],
                'position' => $fieldWithContent['position'],
                'parent_id' => $fieldWithContent['parent_id'],
                'published_on' => $fieldWithContent['published_on'],
                'created_on' => $fieldWithContent['created_on'],
                'archived_on' => $fieldWithContent['archived_on'],
            ];
        }

        foreach ($fieldsWithContent as $fieldWithContent) {
            if (($fieldWithContent['field_key'] === null)&&($fieldWithContent['datum_key'] === null)) {
                continue;
            }

            if ($fieldWithContent['field_type'] === 'content_id') {

                $content[$fieldWithContent['id']]['fields'][$fieldWithContent['field_key']] = [
                    'id' => $linkedContents[$fieldWithContent['field_value']]['id'],
                    'slug' => $linkedContents[$fieldWithContent['field_value']]['slug'],
                    'status' => $linkedContents[$fieldWithContent['field_value']]['status'],
                    'type' => $linkedContents[$fieldWithContent['field_value']]['type'],
                    'position' => $linkedContents[$fieldWithContent['field_value']]['position'],
                    'parent_id' => $linkedContents[$fieldWithContent['field_value']]['parent_id'],
                    'published_on' => $linkedContents[$fieldWithContent['field_value']]['published_on'],
                    'created_on' => $linkedContents[$fieldWithContent['field_value']]['created_on'],
                    'archived_on' => $linkedContents[$fieldWithContent['field_value']]['archived_on'],
                ];

                if (array_key_exists('fields', $linkedContents[$fieldWithContent['field_value']]))
                {
                    foreach ($linkedContents[$fieldWithContent['field_value']]['fields'] as
                             $linkedContentFieldKey => $linkedContentFieldValue) {

                        $content[$fieldWithContent['id']]['fields'][$fieldWithContent['field_key']]
                        ['fields'][$linkedContents[$fieldWithContent['field_value']]['id']][$linkedContentFieldKey] = $linkedContentFieldValue;
                    }
                }

                if (array_key_exists('datum', $linkedContents[$fieldWithContent['field_value']]))
                {
                    foreach ($linkedContents[$fieldWithContent['field_value']]['datum'] as
                             $linkedContentDatumKey => $linkedContentDatumValue) {

                        $content[$fieldWithContent['id']]['fields'][$fieldWithContent['field_key']]
                        ['datum'][$fieldWithContent['field_value']]['datum']['datum_id'] = [
                            'key' => $linkedContentDatumKey,
                            'value' => $linkedContentDatumValue
                        ];
                    }
                }

            } else {
                // put multiple fields with same key in to an array
                if ($fieldWithContent['field_type'] == 'multiple') {

                    $content[$fieldWithContent['id']]
                    ['fields']
                    [$fieldWithContent['field_key']]
                    [$fieldWithContent['field_position']] =
                        $fieldWithContent['field_value'];

                } elseif($fieldWithContent['field_value']) {

                    $content[$fieldWithContent['id']]
                    ['fields']
                    [$fieldWithContent['field_key']] =
                        $fieldWithContent['field_value'];
                }
            }

            //put datum as array key => value
            if($fieldWithContent['datum_value']){
                $content[$fieldWithContent['id']]
                ['datum']
                [$fieldWithContent['datum_key']] =
                    $fieldWithContent['datum_value'];
            }
        }

        return $content;
    }

    /**
     * Update content position and call function that recalculate position for other children
     * @param int $contentId
     * @param int $position
     */
    public function reposition($contentId, $position)
    {
        $parentContentId = $this->queryTable()->where('id', $contentId)->first(['parent_id'])['parent_id']
            ?? null;
        $childContentCount = $this->queryTable()->where('parent_id', $parentContentId)->count();

        if ($position < 1) {
            $position = 1;
        } elseif ($position > $childContentCount) {
            $position = $childContentCount;
        }

        $this->transaction(
            function () use ($contentId, $position, $parentContentId) {
                $this->queryTable()
                    ->where('id', $contentId)
                    ->update(
                        ['position' => $position]
                    );

                $this->otherChildrenRepositions($parentContentId, $contentId, $position);
            }
        );
    }

    /** Update position for other categories with the same parent id
     * @param integer $parentCategoryId
     * @param integer $categoryId
     * @param integer $position
     */
    function otherChildrenRepositions ($parentContentId, $contentId, $position)
    {
        $childContent =
            $this->queryTable()
                ->where('parent_id', $parentContentId)
                ->where('id', '<>', $contentId)
                ->orderBy('position')
                ->get()
                ->toArray();

        $start = 1;

        foreach ($childContent as $child) {
            if($start == $position)
            {
                $start++;
            }

            $this->queryTable()
                ->where('id', $child['id'])
                ->update(
                    ['position' => $start]
                );
            $start++;
        }
    }

    /**
     * Get all contents order by parent and position
     * @return array
     */
    public function getAllContents()
    {
        return $this->queryTable()->get()->toArray();
    }

    public function getContentForVersion($contentId)
    {
        $content = [];

        $fieldsWithContent = $this->queryIndex()
            ->where(ConfigService::$tableContent . '.id', $contentId)
            ->get();

        foreach($fieldsWithContent as $fieldWithContent)
        {
            $content[$fieldWithContent['id']] = [
                'id' => $fieldWithContent['id'],
                'slug' => $fieldWithContent['slug'],
                'status' => $fieldWithContent['status'],
                'type' => $fieldWithContent['type'],
                'position' => $fieldWithContent['position'],
                'parent_id' => $fieldWithContent['parent_id'],
                'published_on' => $fieldWithContent['published_on'],
                'created_on' => $fieldWithContent['created_on'],
                'archived_on' => $fieldWithContent['archived_on'],
            ];
        }

        foreach($fieldsWithContent as $fieldWithContent)
        {

            if($fieldWithContent['datum_id'])
            {
                $content[$fieldWithContent['id']]['datum'][$fieldWithContent['datum_id']][$fieldWithContent['datum_key']] = $fieldWithContent['datum_value'];
            }

            if($fieldWithContent['field_id'])
            {
                $content[$fieldWithContent['id']]['fields'][$fieldWithContent['field_id']][$fieldWithContent['field_key']] =  $fieldWithContent['field_value'];
            }
        }
        return $content[$contentId];
    }

}