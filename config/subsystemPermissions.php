<?php

return [
    'manageManagers' => [
        'manager.manager.list' => 'viewManagers',
        'manager.manager.create' => 'createManager',
        'manager.manager.edit' => 'editManager',
        'manager.manager.delete' => 'deleteManager',
    ],

    'manageRoles' => [
        'view-roles' => 'viewRoles',
        'create-roles' => 'createRole',
        'edit-roles' => 'editRole',
        'delete-roles' => 'deleteRole',
    ],

    'manageUsers' => [
        'manager.manager.list' => 'viewUsers',
        'manager.manager.edit' => 'editUser',
        'manager.manager.updateStatus' => 'updateUserStatus',
        'manager.userManageBalance' => 'manageUserBalance',
    ],

    'manageFinance' => [
        'manager.payment.list' => 'viewPayments',
        'manager.withdrawal.list' => 'viewWithdrawals',
        'manager.transaction.list' => 'viewTransactions',
    ],

    'manageArticleCategories' => [
        'manager.articleCategory.list' => 'viewArticleCategories',
        'manager.articleCategory.create' => 'createArticleCategory',
        'manager.articleCategory.edit' => 'editArticleCategory',
        'manager.articleCategory.delete' => 'deleteArticleCategory',
    ],

    'manageArticles' => [
        'manager.article.list' => 'viewArticles',
        'manager.article.create' => 'createArticle',
        'manager.article.edit' => 'editArticle',
        'manager.article.delete' => 'deleteArticle',
    ],
];

