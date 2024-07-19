<?php

if (! defined('ROLES_DEFINED')) {
    // Define constants for role IDs
    define('EDIT_PROFILE_ROLE', 'edit_profile');
    define('CHANGE_AVATAR_ROLE', 'change_avatar');
    define('WRITE_POSTS_ROLE', 'write_posts');
    define('MANAGE_COMMENTS_ROLE', 'manage_comments');
    define('VIEW_CONTACT_MESSAGES_ROLE', 'view_contact_messages');
    define('RECEIVE_CONTACT_MESSAGES_ROLE', 'receive_contact_messages');
    define('CHANGE_CONTACT_SETTINGS_ROLE', 'change_contact_settings');
    define('MANAGE_PROJECTS_ROLE', 'manage_projects');
    define('MANAGE_USERS_ROLE', 'manage_users');
    define('MANAGE_BACKUPS_ROLE', 'manage_backups');
    define('MANAGE_IMAGES_ROLE', 'manage_images');

    define('ROLES_DEFINED', true);
}

return [
    'roles' => [
        // Roles definition
        [
            'id' => EDIT_PROFILE_ROLE,
            'name' => 'Edit Profile',
            'description' => 'Allows users to modify their profile information.',
        ],
        [
            'id' => CHANGE_AVATAR_ROLE,
            'name' => 'Change Avatar',
            'description' => 'Allows user to modify their profile picture (avatar).',
        ],
        [
            'id' => WRITE_POSTS_ROLE,
            'name' => 'Write Posts',
            'description' => 'Allows users to create and publish new posts.',
        ],
        [
            'id' => MANAGE_COMMENTS_ROLE,
            'name' => 'Manage Comments',
            'description' => 'Allows users to moderate and manage comments on posts.',
        ],
        [
            'id' => VIEW_CONTACT_MESSAGES_ROLE,
            'name' => 'View Contact Messages',
            'description' => 'Allows users to see messages sent through the contact form.',
        ],
        [
            'id' => RECEIVE_CONTACT_MESSAGES_ROLE,
            'name' => 'Receive Contact Messages',
            'description' => 'Allows users to receive messages sent through the contact form.',
        ],
        [
            'id' => CHANGE_CONTACT_SETTINGS_ROLE,
            'name' => 'Change Contact Settings',
            'description' => 'Allows users to modify settings related to the contact form.',
        ],
        [
            'id' => MANAGE_PROJECTS_ROLE,
            'name' => 'Manage Projects',
            'description' => 'Allows users to create, edit, and delete projects.',
        ],
        [
            'id' => MANAGE_USERS_ROLE,
            'name' => 'Manage Users',
            'description' => 'Allows users to manage other users, such as creating or deleting user accounts.',
        ],
        [
            'id' => MANAGE_BACKUPS_ROLE,
            'name' => 'Manage Backups',
            'description' => 'Allows users to manage backups, such as performing and downloading backups.',
        ],
        [
            'id' => MANAGE_IMAGES_ROLE,
            'name' => 'Manage Images',
            'description' => 'Allows users to view, edit, and delete images.',
        ],
    ],

    // Groups definition
    'groups' => [
        [
            'id' => 'admin',
            'roles' => [
                // Roles assigned to the admin group
                EDIT_PROFILE_ROLE,
                CHANGE_AVATAR_ROLE,
                WRITE_POSTS_ROLE,
                MANAGE_COMMENTS_ROLE,
                VIEW_CONTACT_MESSAGES_ROLE,
                RECEIVE_CONTACT_MESSAGES_ROLE,
                CHANGE_CONTACT_SETTINGS_ROLE,
                MANAGE_PROJECTS_ROLE,
                MANAGE_USERS_ROLE,
                MANAGE_BACKUPS_ROLE,
                MANAGE_IMAGES_ROLE,
            ],
        ],
    ],
];
