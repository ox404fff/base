<?php

namespace test\base\db;

use test\fixtures\ActiveRecordTestFixture;
use yii\codeception\TestCase;

class ActiveRecordTest extends TestCase
{

    public function fixtures()
    {
        return [
            'active_record_test' => ActiveRecordTestFixture::className(),
        ];
    }


    public function testFind()
    {
        $normalRecord = ActiveRecordTestModel::find()
            ->where(['id' => ActiveRecordTestFixture::RECORD_ID_NORMAL_1])
            ->one();

        $this->assertFalse(empty($normalRecord), 'Normal record should be find');

        $deletedRecord = ActiveRecordTestModel::find()
            ->where(['id' => ActiveRecordTestFixture::RECORD_ID_DELETED_1])
            ->one();

        $this->assertTrue(empty($deletedRecord), 'Normal record should be find');

    }


    public function testFindAll()
    {
        $records = ActiveRecordTestModel::find()->indexBy('id')->all();

        $this->assertTrue(isset($records[ActiveRecordTestFixture::RECORD_ID_NORMAL_1]), 'Normal records must exist');
        $this->assertTrue(isset($records[ActiveRecordTestFixture::RECORD_ID_NORMAL_2]), 'Normal records must exist');

        $this->assertFalse(isset($records[ActiveRecordTestFixture::RECORD_ID_DELETED_1]), 'Deleted records must not exist');
        $this->assertFalse(isset($records[ActiveRecordTestFixture::RECORD_ID_DELETED_2]), 'Deleted records must not exist');

    }


    public function testUpdateAll()
    {
        $countUpdated = ActiveRecordTestModel::updateAll(['number' => 1]);

        $this->assertEquals($countUpdated, 3, 'Count updated records should equals three');

        $deletedRecord = ActiveRecordTestModel::find()
            ->where(['id' => ActiveRecordTestFixture::RECORD_ID_DELETED_1])
            ->setIsSoftDelete(false)->one();

        $this->assertEquals($deletedRecord->number, 0, 'Value attribute "number" should be zero');


    }



    public function testUpdateAllCounters()
    {

        $countUpdated = ActiveRecordTestModel::updateAllCounters(['number' => 1]);

        $this->assertEquals($countUpdated, 3, 'Count updated records should equals three');

        $deletedRecord = ActiveRecordTestModel::find()
                ->where(['id' => ActiveRecordTestFixture::RECORD_ID_DELETED_1])
                ->setIsSoftDelete(false)->one();

        $this->assertEquals($deletedRecord->number, 0, 'Value attribute "number" should be zero');

        $normalRecord = ActiveRecordTestModel::find()
            ->where(['id' => ActiveRecordTestFixture::RECORD_ID_NORMAL_1])
            ->one();

        $this->assertEquals($normalRecord->number, 1, 'Value attribute "number" should be one');

    }


    public function testDelete()
    {
        $normalRecord = ActiveRecordTestModel::find()
            ->where(['id' => ActiveRecordTestFixture::RECORD_ID_NORMAL_1])
            ->one();

        $normalRecord->delete();

        $normalRecord = ActiveRecordTestModel::find()
            ->where(['id' => ActiveRecordTestFixture::RECORD_ID_NORMAL_1])
            ->one();

        $this->assertEmpty($normalRecord,
            'Deleted record must be not found');

        $deletedRecord = ActiveRecordTestModel::find()
            ->where(['id' => ActiveRecordTestFixture::RECORD_ID_NORMAL_1])
            ->setIsSoftDelete(false)->one();

        $this->assertNotEmpty($deletedRecord,
            'Deleted record must exists in database');

        $this->assertNotEmpty($deletedRecord->getAttribute(ActiveRecordTestModel::ATTRIBUTE_DELETED_AT),
            'Time delete must by set');

        $result = $deletedRecord->delete();

        $this->assertEquals($result, 0,
            'Deleted record can not delete again');


        $notDeletedRecord = ActiveRecordTestModel::find()
            ->where(['id' => ActiveRecordTestFixture::RECORD_ID_NORMAL_2])
            ->one();

        $this->assertNotEmpty($notDeletedRecord,
            'Other records must is not deleted');
    }


    public function testDeleteAll()
    {
        $countDeleted = ActiveRecordTestModel::deleteAll();

        $this->assertEquals($countDeleted, 3,
            'Count deleted records should equals three');

        $normalRecord = ActiveRecordTestModel::find()
            ->where(['id' => ActiveRecordTestFixture::RECORD_ID_NORMAL_1])
            ->one();

        $this->assertEmpty($normalRecord,
            'Deleted record must be not found');

        $normalRecord = ActiveRecordTestModel::find()
            ->where(['id' => ActiveRecordTestFixture::RECORD_ID_NORMAL_1])
            ->setIsSoftDelete(false)->one();

        $this->assertNotEmpty($normalRecord,
            'Deleted record must exists in database');

        $this->assertNotEmpty($normalRecord->getAttribute(ActiveRecordTestModel::ATTRIBUTE_DELETED_AT),
            'Time delete must by set');
    }



    public function testIsDeleted()
    {
        $normalRecord = ActiveRecordTestModel::find()
            ->where(['id' => ActiveRecordTestFixture::RECORD_ID_NORMAL_1])
            ->one();

        $this->assertFalse($normalRecord->isDeleted(),
            'isDeleted for normal record should return false');

        $normalRecord->delete();

        $this->assertTrue($normalRecord->isDeleted(),
            'isDeleted after delete record should return true');

        $deletedRecord = ActiveRecordTestModel::find()
            ->where(['id' => ActiveRecordTestFixture::RECORD_ID_DELETED_1])
            ->setIsSoftDelete(false)->one();

        $this->assertTrue($deletedRecord->isDeleted(),
            'isDeleted for already deleted record should return true');
    }


    public function testBehaviorsTimestampCreated()
    {
        $newRecord = new ActiveRecordTestModel();
        $newRecord->number = 0;
        $newRecord->save();

        $this->assertNotEmpty($newRecord->getAttribute(ActiveRecordTestModel::ATTRIBUTE_CREATED_AT),
            'Created time should by not empty');

        $this->assertNotEmpty($newRecord->getAttribute(ActiveRecordTestModel::ATTRIBUTE_UPDATED_AT),
            'Updated time should by not empty');
    }


    public function testBehaviorsTimestampUpdatedAll()
    {
        $countUpdated = ActiveRecordTestModel::updateAll(['number' => 1]);

        $this->assertNotEquals($countUpdated, 0,
            'count updated records must not be equals zero');

        $updatedRecord = ActiveRecordTestModel::findOne(['id' => ActiveRecordTestFixture::RECORD_ID_WITHOUT_TIME_DATA]);

        $this->assertNotEmpty($updatedRecord,
            'updated record must exists');

        $this->assertNotEquals($updatedRecord->updated_at, 0, 'updated time must be set');
    }


    public function testBehaviorsTimestampUpdatedAllCounters()
    {
        $countUpdated = ActiveRecordTestModel::updateAllCounters(['number' => 1]);

        $this->assertNotEquals($countUpdated, 0,
            'count updated records must not be equals zero');

        $updatedRecord = ActiveRecordTestModel::findOne(['id' => ActiveRecordTestFixture::RECORD_ID_WITHOUT_TIME_DATA]);

        $this->assertNotEmpty($updatedRecord,
            'updated record must exists');

        $this->assertNotEquals($updatedRecord->updated_at, 0, 'updated time must be set');
    }

}
