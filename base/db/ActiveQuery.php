<?
/**
 * @link https://github.com/ox404fff/base/
 * @author ox404fff
 */

namespace app\base\db;

use yii\db\ActiveQuery as BaseQuery;

/**
 * ActiveQuery represents a DB query associated with an Active Record class.
 */
class ActiveQuery extends BaseQuery
{

    /**
     * @var bool Is soft delete enable
     */
    protected $isSoftDelete = false;

    /**
     * @var string Soft delete attribute name
     */
    protected $softDeleteAttribute = 'is_deleted';


    /**
     * Change soft delete status
     *
     * @param $value
     *
     * @return static
     */
    public function setIsSoftDelete($value)
    {
        $this->isSoftDelete = $value;
        return $this;
    }


    /**
     * Set soft delete attribute name
     *
     * @param $attribute
     *
     * @return static
     */
    public function setSoftDeleteAttribute($attribute)
    {
        $this->softDeleteAttribute = $attribute;
        return $this;
    }


    /**
     * @inheritdoc
     */
    public function createCommand($db = null)
    {
        $this->_modifyCondition();

        return parent::createCommand($db = null);
    }


    /**
     * Exclude deleted records from selection
     */
    private function _modifyCondition()
    {
        if ($this->isSoftDelete) {
            $this->andWhere(['is_deleted' => false]);
        }
    }

}