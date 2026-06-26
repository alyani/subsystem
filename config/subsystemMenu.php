<?php

return [
   'Managers' => [
       'icon' => 'fa fa-user-circle',
       'child' => [
           'Manager list' => [
               'routeName' => 'admin.manager.list',
               'active' => [
                   'admin.manager.list',
                   'admin.manager.create',
                   'admin.manager.edit',
               ],
           ],
           'Role list' => [
               'routeName' => 'admin.role.list',
               'active' => [
                   'admin.role.list',
                   'admin.role.create',
                   'admin.role.edit',
               ],
           ],
       ],
    ],

    'Finance' => [
        'icon' => 'fa fa-dollar',
        'child' => [
            'Payment list' => [
               'routeName' => 'admin.payment.list',
                'active' => [
                    'admin.payment.list',
                ],
            ],
            'Withdrawal list' => [
               'routeName' => 'admin.withdrawal.list',
                'active' => [
                    'admin.withdrawal.list',
                ],
            ],
            'Transaction list' => [
               'routeName' => 'admin.transaction.list',
                'active' => [
                    'admin.transaction.list',
                ],
            ],
        ],
    ],

//    'Faq' => [
//        'icon' => 'fa fa-comment',
//        'child' => [
//            'Faq list' => [
//                'routeName' => 'admin.faq.list',
//                'active' => [
//                    'admin.faq.create',
//                    'admin.faq.list',
//                    'admin.faq.edit',
//                ]
//            ],
//            'Faq category list' => [
//                'routeName' => 'admin.faqCategory.list',
//                'active' => [
//                    'admin.faqCategory.list',
//                    'admin.faqCategory.create',
//                    'admin.faqCategory.edit',
//                ]
//            ],
//        ]
//    ],
    'Blog' => [
        'icon' => 'fa fa-newspaper',
        'child' => [
            'Articles' => [
                'routeName' => 'admin.article.list',
                'active' => [
                        'admin.article.create',
                        'admin.article.list',
                        'admin.article.edit',
                        'admin.article.show',
                ],
            ],
            'Article categories' => [
                'routeName' => 'admin.articleCategory.list',
                'active' => [
                        'admin.articleCategory.create',
                        'admin.articleCategory.list',
                        'admin.articleCategory.edit',
                ],
            ],
        ],
    ],
];
