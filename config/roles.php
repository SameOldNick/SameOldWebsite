<?php

// Define constants for role IDs
const EDIT_PROFILE_ROLE = 'edit_profile';
const WRITE_POSTS_ROLE = 'write_posts';
const MANAGE_COMMENTS_ROLE = 'manage_comments';
const VIEW_CONTACT_MESSAGES_ROLE = 'view_contact_messages';
const RECEIVE_CONTACT_MESSAGES_ROLE = 'receive_contact_messages';
const CHANGE_CONTACT_SETTINGS_ROLE = 'change_contact_settings';
const MANAGE_PROJECTS_ROLE = 'manage_projects';
const MANAGE_USERS_ROLE = 'manage_users';

return [
    'roles' => [
        // Roles definition
        [
            'id' => EDIT_PROFILE_ROLE,
            'name' => 'Edit Profile',
            'description' => 'Allows users to modify their profile information.',
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
    ],

    // Groups definition
    'groups' => [
        [
            'id' => 'admin',
            'roles' => [
                // Roles assigned to the admin group
                EDIT_PROFILE_ROLE,
                WRITE_POSTS_ROLE,
                MANAGE_COMMENTS_ROLE,
                VIEW_CONTACT_MESSAGES_ROLE,
                RECEIVE_CONTACT_MESSAGES_ROLE,
                CHANGE_CONTACT_SETTINGS_ROLE,
                MANAGE_PROJECTS_ROLE,
                MANAGE_USERS_ROLE,
            ],
        ],
    ],
];
