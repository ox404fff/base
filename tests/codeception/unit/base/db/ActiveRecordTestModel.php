<?php

namespace test\base\db;

use app\base\db\ActiveRecord as BaseActiveRecord;

/**
 * ActiveRecord class for testing base active record
 *
 * @property int       $id               pk
 * @property int       $number           any integer value for tests
 * @property int       $text             any string value for tests
 *
 * @property int       $created_at       date created record
 * @property int       $updated_at       date updated record
 *
 * @property int       $deleted_at       date deleted record
 * @property bool      $is_deleted       is deleted
 *
 * @package app\base\db
 */
class ActiveRecordTestModel extends BaseActiveRecord
{
    /**
     * Real table for testing base active record behaviors
     *
     * @return string
     */
    public static function tableName()
    {
        return 'active_record_test';
    }

}