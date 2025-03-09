<?php

return [
    'navigation' => [
        'label' => 'Categories',
        'group' => 'Business Section',
    ],
    'table' => [
        'columns' => [
            'name' => 'Name',
            'description' => 'Description',
            'parent' => 'Parent Category',
            'seller' => 'Seller',
            'sub_categories' => 'Sub Categories',
            'deleted_at' => 'Deleted At',
            'created_at' => 'Created At',
            'updated_at' => 'Updated At',
        ],
        'filters' => [
            'parent' => 'Filter by parent',
        ],
        'actions' => [
            'create' => 'Create Category',
            'edit' => 'Edit Category',
            'delete' => 'Delete Category',
        ],
    ],
    'form' => [
        'label' => [
            'name' => 'Name',
            'description' => 'Description',
            'parent_category' => 'Parent Category',
            'is_sub_category' => 'Is Sub Category',
            'seller' => 'Seller'
        ],
        'helper_text' => [
            'name' => 'Enter the category name',
            'sub_category' => 'Check if this is a sub-category'
        ]
    ]
];