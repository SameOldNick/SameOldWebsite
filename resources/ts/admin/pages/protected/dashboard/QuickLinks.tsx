import React from "react";
import DashboardCard from '@admin/components/dashboard/Card';
import { ListGroup, ListGroupItem } from "reactstrap";

const QuickLinks: React.FC = () => {
    return (
        <>
            <DashboardCard>
                <DashboardCard.Header>
                    Quick Links
                </DashboardCard.Header>
                <ListGroup flush>
                    <ListGroupItem><a href="/admin/posts">Manage Blog Articles</a></ListGroupItem>
                    <ListGroupItem><a href="/admin/comments">View Comments</a></ListGroupItem>
                    <ListGroupItem><a href="/admin/contact/messages">Read Contact Messages</a></ListGroupItem>
                    <ListGroupItem><a href="/admin/projects">Manage Projects</a></ListGroupItem>
                    <ListGroupItem><a href="/admin/users">User Management</a></ListGroupItem>
                </ListGroup>
            </DashboardCard>
        </>
    );
}

export default QuickLinks;
