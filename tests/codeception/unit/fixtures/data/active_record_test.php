<?php
/**
 * @var test\fixtures\ActiveRecordTestFixture $this
 */

return [
    $this::RECORD_ID_NORMAL_1 => [
        'id'         => $this::RECORD_ID_NORMAL_1,
        'number'     => 0,
        'created_at' => 1455353947,
        'updated_at' => 1455353947,
    ],
    $this::RECORD_ID_DELETED_1 => [
        'id'         => $this::RECORD_ID_DELETED_1,
        'number'     => 0,
        'is_deleted' => true,
        'deleted_at' => 1455353947,
        'created_at' => 1455353947,
        'updated_at' => 1455353947,
    ],
    $this::RECORD_ID_NORMAL_2 => [
        'id'         => $this::RECORD_ID_NORMAL_2,
        'number'     => 0,
        'created_at' => 1455353947,
        'updated_at' => 1455353947,
    ],
    $this::RECORD_ID_DELETED_2 => [
        'id'         => $this::RECORD_ID_DELETED_2,
        'number'     => 0,
        'is_deleted' => true,
        'deleted_at' => 1455353947,
        'created_at' => 1455353947,
        'updated_at' => 1455353947,
    ],
    $this::RECORD_ID_WITHOUT_TIME_DATA => [
        'id'         => $this::RECORD_ID_WITHOUT_TIME_DATA,
        'number'     => 0,
        'deleted_at' => 0,
        'created_at' => 0,
        'updated_at' => 0,
    ]
];