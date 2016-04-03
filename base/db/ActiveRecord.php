<?
/**
 * @link https://github.com/ox404fff/base/
 * @author ox404fff
 */
namespace app\base\db;

use yii\behaviors\TimestampBehavior;
use yii\db\ActiveRecord as BaseActiveRecord;
use yii\db\Expression;

/**
 * ActiveRecord is the base class for classes representing relational data in terms of objects.
 * Custom ActiveRecord class, with functionality soft delete and defined behaviors
 *
 * @property int       $created_at       date created record
 * @property int       $updated_at       date updated record
 *
 * @property int       $deleted_at       date deleted record
 * @property bool      $is_deleted       is deleted
 *
 * @package app\base\db
 *
 * @mixed TimestampBehavior
 */
class ActiveRecord extends BaseActiveRecord
{

    /**
     * Time when the record was created
     */
    const ATTRIBUTE_CREATED_AT = 'created_at';

    /**
     * Time when the record was updated
     */
    const ATTRIBUTE_UPDATED_AT = 'updated_at';

    /**
     * TTime when the record was deleted
     */
    const ATTRIBUTE_DELETED_AT = 'deleted_at';

    /**
     * The attribute specifies whether the record is deleted
     */
    const ATTRIBUTE_IS_DELETED = 'is_deleted';

    /**
     * @var bool Is enable soft delete
     */
    public static $isSoftDelete = true;

    /**
     * @var static Singleton instance
     */
    private static $_instance = null;

    /**
     * @return array
     */
    public function behaviors()
    {
        return [
            'timestamp' => [
                'class' => TimestampBehavior::className(),
                'attributes' => [
                    ActiveRecord::EVENT_BEFORE_INSERT => [self::ATTRIBUTE_CREATED_AT, self::ATTRIBUTE_UPDATED_AT],
                    ActiveRecord::EVENT_BEFORE_UPDATE => [self::ATTRIBUTE_UPDATED_AT],
                ],
            ]
        ];
    }


    /**
     * Get singleton model instance
     */
    public static function singleton()
    {
        if (is_null(self::$_instance)) {
            self::$_instance = new static();
        }
        return self::$_instance;
    }


    /**
     * Find with add is not deleted condition
     *
     * @inheritdoc
     */
    public static function find()
    {
        $activeQuery = \Yii::createObject(ActiveQuery::className(), [get_called_class()]);
        /**
         * @var ActiveQuery $activeQuery
         */
        $activeQuery->setIsSoftDelete(self::$isSoftDelete);

        $activeQuery->setSoftDeleteAttribute(self::ATTRIBUTE_IS_DELETED);

        return $activeQuery;
    }


    /**
     * Update with add is not deleted condition
     *
     * @inheritdoc
     */
    public static function updateAll($attributes, $condition = '', $params = [])
    {
        $condition = self::_modifyCondition($condition, $params);
        $attributes = self::_modifyAttributes($attributes);

        return parent::updateAll($attributes, $condition, $params);
    }


    /**
     * Update couners with add is not deleted condition
     *
     * @inheritdoc
     */
    public static function updateAllCounters($counters, $condition = '', $params = [])
    {
        $condition = self::_modifyCondition($condition, $params);

        $n = 0;
        foreach ($counters as $name => $value) {
            $counters[$name] = new Expression("[[$name]]+:bp{$n}", [":bp{$n}" => $value]);
            $n++;
        }

        $counters = self::_modifyAttributes($counters);

        $command = static::getDb()->createCommand();
        $command->update(static::tableName(), $counters, $condition, $params);

        return $command->execute();
    }


    /**
     * Safe delete records
     *
     * @inheritdoc
     */
    public static function deleteAll($condition = '', $params = [])
    {
        if (self::$isSoftDelete) {
            return self::updateAll([self::ATTRIBUTE_IS_DELETED => true, self::ATTRIBUTE_DELETED_AT => time()], $condition, $params);
        } else {
            return parent::deleteAll($condition, $params);
        }
    }


    /**
     * Set is deleted attribute for correct work method isDeleted
     *
     * @inheritdoc
     */
    public function delete()
    {
        if ($this->isDeleted()) {
            return 0;
        }

        $result = parent::delete();
        if ($result) {
            $this->setAttribute(self::ATTRIBUTE_IS_DELETED, true);
            $this->setAttribute(self::ATTRIBUTE_DELETED_AT, time());
        }
        return $result;
    }


    /**
     * Return is deleted record
     *
     * @return bool
     */
    public function isDeleted()
    {
        return (bool) $this->getAttribute(self::ATTRIBUTE_IS_DELETED);
    }


    /**
     * Modify update or delete conditions
     *
     * @param $condition
     * @param $params
     * @return string
     */
    private static function _modifyCondition($condition, &$params)
    {
        if (self::$isSoftDelete) {
            $queryBuilder = static::getDb()->getQueryBuilder();

            $condition = $queryBuilder->buildCondition($condition, $params);

            return $queryBuilder->buildAndCondition('AND', [$condition, [self::ATTRIBUTE_IS_DELETED => false]], $params);
        } else {
            return $condition;
        }
    }


    /**
     * Set updated at attribute value
     *
     * @param $attributes
     * @return mixed
     */
    private static function _modifyAttributes($attributes)
    {
        if (!array_key_exists(self::ATTRIBUTE_UPDATED_AT, $attributes)) {
            $attributes[self::ATTRIBUTE_UPDATED_AT] = time();
        }
        return $attributes;
    }

}