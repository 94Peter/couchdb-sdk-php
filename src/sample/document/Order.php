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
                          'type'    => 'field',
                          'filter'  => FILTER_VALIDATE_REGEXP,
                          'options' => [
                                         'options' => ['regexp' => '/[a-zA-Z\d\W]+$/'],
                                         'flags'   => FILTER_NULL_ON_FAILURE
                                       ]
                       ],
        // 'date'    => array('filter'    => FILTER_VALIDATE_INT,
        //                         'flags'     => FILTER_FORCE_ARRAY,
        //                         'options'   => array('min_range' => 1, 'max_range' => 10)
        //                        ),
        // 'orderDate' => [
        //                   'type'    => 'field',
        //                   'filter'   => FILTER_VALIDATE_REGEXP,
        //                   'options'  => ['regexp' => '/^[0-9]{4}-(0[1-9]|1[0-2])-(0[1-9]|[1-2][0-9]|3[0-1])$/'],
        //                   'flags' => FILTER_NULL_ON_FAILURE,
        //                ],

        'Customer' => [
                          'type'   => 'object',
                          'schema' => $this->getCustomerSchema(),
                          'required' => true,
                      ],
        // 'customerId' => [
        //                   'filter' => FILTER_VALIDATE_REGEXP
        //                 ],
        'amount'   => [
                        'type'     => 'field',
                        'filter'   => FILTER_VALIDATE_INT,
                        'options'  => [
                                        'options' => ['min_range' => 1]
                                      ],
                        'required' => true,
                      ],
        'gst'      => [
                        'type'     => 'field',
                        'filter' => FILTER_VALIDATE_INT,
                        'option' => [
                                      'default' => 0,
                                      'min_range' => 1,
                                      'max_range' => 10000
                                    ],
                        'required' => true,
                      ],
        'Products' => [
                         'type'   => 'objArray',
                         'schema' => $this->getProductSchema(),
                         'required' => true,
                      ],
        'Accounting' => [
                          'type'   => 'object',
                          'schema' => $this->getAccountingSchema(),
                        ]
        );
    }

    private function getCustomerSchema()
    {
        return [
                 'name' => [
                             'type' => 'field',
                             'filter' => FILTER_SANITIZE_ENCODED,
                           ],
               ];
    }

    private function getProductSchema()
    {
        return [
                  'name'  => [
                                'type' => 'field',
                                'filter' => FILTER_SANITIZE_ENCODED,
                                'required' => true,
                             ],
                  'qty'   => [
                                'type'   => 'field',
                                'filter' => FILTER_VALIDATE_INT,
                                'option' => [
                                              'default' => 1,
                                              'min_range' => 1,
                                              'max_range' => 100000
                                            ],
                                'required' => true,
                             ],
                  'price' => [
                                'type'   => 'field',
                                'filter' => FILTER_VALIDATE_FLOAT,
                                'option' => [
                                              'default' => 0,
                                            ],
                                'required' => true,
                             ],
        ];
    }

    private function getAccountingSchema()
    {
        return [
                  'isPay'   => [
                                  'type'   => 'field',
                                  'filter' => FILTER_VALIDATE_BOOLEAN,
                                  'option' => ['default' => false],
                                  'flags' => FILTER_NULL_ON_FAILURE,
                               ],
                  'payDate' => [
                                  'type'   => 'field',
                                  'filter'   => FILTER_VALIDATE_REGEXP,
                                  'options' => [
                                                 'options' => ['regexp' => '/^[0-9]{4}-(0[1-9]|1[0-2])-(0[1-9]|[1-2][0-9]|3[0-1])$/'],
                                                 'flags'   => FILTER_NULL_ON_FAILURE
                                               ]
                               ],
              ];
    }
}
