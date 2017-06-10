<?php
namespace sample\model;

use \Phalcon\Db\Column as Column;

class Order extends \nosqldb\ModelDoc
{

    protected $isPay;

    protected $customerName;

    protected function documentToModel()
    {
        // 將Document上的值寫入ER Model
    }

    public function tableDefinition()
    {
        return [
          'columns' => [
            new Column('id',
                [
                  'type'          => Column::TYPE_CHAR,
                  'size'          => 13,
                  'notNull'       => true,
                  'autoIncrement' => false,
                  'primary'       => true,
                ]
            ),
            new Column('rev',
                [
                  'type'          => Column::TYPE_CHAR,
                  'size'          => 34,
                  'notNull'       => true,
                  'autoIncrement' => false,
                ]
            ),
            new Column('isPay',
                [
                  'type'          => Column::TYPE_BOOLEAN,
                  'size'          => 1,
                  'notNull'       => true,
                  'autoIncrement' => false,
                ]
            ),
            new Column('customerName',
                [
                  'type'          => Column::TYPE_VARCHAR,
                  'size'          => 255,
                  'notNull'       => true,
                  'autoIncrement' => false,
                ]
            ),
          ],
        ];
    }
}
