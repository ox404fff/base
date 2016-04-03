<?php

namespace test\fixtures;

use yii\test\ActiveFixture;

class ActiveRecordTestFixture extends ActiveFixture
{

    public $modelClass = 'test\base\db\ActiveRecordTestModel';

    /**
     * Normal record ids
     */
    const RECORD_ID_NORMAL_1 = 10;
    /**
     * Normal record ids
     */
    const RECORD_ID_NORMAL_2 = 11;
    /**
     * Deleted record ids
     */
    const RECORD_ID_DELETED_1 = 20;
    /**
     * Deleted record ids
     */
    const RECORD_ID_DELETED_2 = 21;
    /**
     * Record with zero timestamp data
     */
    const RECORD_ID_WITHOUT_TIME_DATA = 31;



}