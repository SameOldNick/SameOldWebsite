import { FaCloudUploadAlt, FaComments, FaEnvelope, FaHome, FaList, FaNewspaper, FaTachometerAlt, FaUsers } from "react-icons/fa";

import DropdownMenuItem from "./items/DropdownMenuItem";
import MenuItem from "./items/MenuItem";
import LinkMenuItem from "./items/LinkMenuItem";

const createMainMenu = (): MenuItem[] => {
    return [
        new LinkMenuItem('Dashboard', '/admin/dashboard', { icon: FaTachometerAlt }),

        new DropdownMenuItem(
            'Homepage',
            [
                new LinkMenuItem('Edit Profile', '/admin/homepage/profile'),
                new LinkMenuItem('Update Skills', '/admin/homepage/skills'),
                new LinkMenuItem('Manage Technologies', '/admin/homepage/technologies'),
            ],
            {
                icon: FaHome,
                roles: ['edit_profile']
            }
        ),

        new DropdownMenuItem(
            'Blog',
            [
                new LinkMenuItem('View All Posts', '/admin/posts'),
                new LinkMenuItem('Create New Post', '/admin/posts/create'),
            ],
            {
                icon: FaNewspaper,
                roles: ['write_posts']
            }
        ),

        new DropdownMenuItem(
            'Comments',
            [
                new LinkMenuItem('View All Comments', '/admin/comments'),
                new LinkMenuItem('Comment Settings', '/admin/comments/settings'),
            ],
            {
                icon: FaComments,
                roles: ['manage_comments']
            }
        ),

        new DropdownMenuItem(
            'Contact',
            [
                new LinkMenuItem(
                    'View Messages',
                    '/admin/contact/messages',
                    {
                        roles: ['view_contact_messages']
                    }
                ),
                new LinkMenuItem(
                    'Manage Settings',
                    '/admin/contact/settings',
                    {
                        roles: ['change_contact_settings']
                    }
                ),
                new LinkMenuItem(
                    'Manage Blacklist',
                    '/admin/contact/blacklist',
                    {
                        roles: ['change_contact_settings']
                    }
                ),
            ],
            {
                icon: FaEnvelope,
                roles: {
                    roles: ['view_contact_messages', 'change_contact_settings'],
                    oneOf: true
                }
            }
        ),

        new DropdownMenuItem(
            'Projects',
            [
                new LinkMenuItem('View All Projects', '/admin/projects'),
                new LinkMenuItem('Create New Project', '/admin/projects/create'),
            ],
            {
                icon: FaList,
                roles: ['manage_projects']
            }
        ),

        new DropdownMenuItem(
            'Backups',
            [
                new LinkMenuItem('View Backups', '/admin/backups'),
                new LinkMenuItem('Backup Settings', '/admin/backups/settings'),
                new LinkMenuItem('Backup Destinations', '/admin/backups/destinations'),
            ],
            {
                icon: FaCloudUploadAlt,
                roles: ['manage_backups']
            }
        ),

        new DropdownMenuItem(
            'Users',
            [
                new LinkMenuItem('View All Users', '/admin/users'),
                new LinkMenuItem('Create New User', '/admin/users/create'),
            ],
            {
                icon: FaUsers,
                roles: ['manage_users']
            }
        ),
    ]
}

export { createMainMenu };
