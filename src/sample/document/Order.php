<?php
namespace sample\document;

class Order extends \nosqldb\Document
{
    public $isPay = false;

    public function getDB()
    {
      return 'order';
    }

    public function genUUID()
    {
      return uniqid();
    }

    public function getValidationFilter()
    {
      return array(
        'orderNo'   => [
                          'filter'   => FILTER_VALIDATE_REGEXP,
                          'options'  => ['regexp' => '/^O\d{4}$/'],
                          'flags' => FILTER_NULL_ON_FAILURE,
                       ],
        // 'date'    => array('filter'    => FILTER_VALIDATE_INT,
        //                         'flags'     => FILTER_FORCE_ARRAY,
        //                         'options'   => array('min_range' => 1, 'max_range' => 10)
        //                        ),
        'customerName'     => FILTER_SANITIZE_ENCODED,
        // 'customerId' => [
        //                   'filter' => FILTER_VALIDATE_REGEXP
        //                 ],
        'amount'   => [
                        'filter' => FILTER_VALIDATE_INT,
                        'options' => ['min_range' => 1],
                        'flags' => FILTER_NULL_ON_FAILURE,
                      ],
        //
        'discount' => [
                        'filter' => FILTER_VALIDATE_INT,
                        'option' => [
                                      'default' => 0,
                                      'min_range' => 0,
                                      'max_range' => 10000
                                    ],
                        'required' => false,
                      ],
        'isPay' => [
                    'filter' => FILTER_VALIDATE_BOOLEAN,
                    'option' => ['default' => false],
                    'flags' => FILTER_NULL_ON_FAILURE,
          ]
      );
    }
}
