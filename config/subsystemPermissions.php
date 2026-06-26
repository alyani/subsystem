<?php

return [
    'manageManagers' => [
        'admin.manager.list' => 'viewManagers',
        'admin.manager.create' => 'createManager',
        'admin.manager.edit' => 'editManager',
    ],

    'manageRoles' => [
        'admin.role.list' => 'viewRoles',
        'admin.role.create' => 'createRole',
        'admin.role.edit' => 'editRole',
        'admin.role.delete' => 'deleteRole',
    ],

    'manageUsers' => [
        'admin.user.list' => 'viewUsers',
        'admin.user.edit' => 'editUser',
        'admin.user.updateStatus' => 'updateUserStatus',
        'admin.userManageBalance' => 'manageUserBalance',
    ],

    'manageFinance' => [
        'admin.payment.list' => 'viewPayments',
        'admin.withdrawal.list' => 'viewWithdrawals',
        'admin.transaction.list' => 'viewTransactions',
    ],

    'manageArticleCategories' => [
        'admin.articleCategory.list' => 'viewArticleCategories',
        'admin.articleCategory.create' => 'createArticleCategory',
        'admin.articleCategory.edit' => 'editArticleCategory',
        'admin.articleCategory.delete' => 'deleteArticleCategory',
    ],

    'manageArticles' => [
        'admin.article.list' => 'viewArticles',
        'admin.article.create' => 'createArticle',
        'admin.article.edit' => 'editArticle',
        'admin.article.delete' => 'deleteArticle',
    ],
];

