import React from 'react';
import { FaSearch } from 'react-icons/fa';
import { Form, InputGroup, Input, Button, FormProps } from 'reactstrap';

import classNames from 'classnames';
import withReactContent from 'sweetalert2-react-content';
import Swal from 'sweetalert2';

type Props = Omit<FormProps, 'onSubmit'>;

const SearchForm: React.FC<Props> = ({ className, ...props }) => {
    const handleSubmit = React.useCallback((e: React.FormEvent<HTMLFormElement>) => {
        e.preventDefault();

        withReactContent(Swal).fire({
            icon: 'info',
            title: 'Coming Soon!',
            text: 'The search feature for the dashboard has not been implemented yet.'
        });
    }, []);

    return (
        <>
            <Form className={classNames("navbar-search", className)} onSubmit={handleSubmit} {...props}>
                <InputGroup>
                    <Input type="search" className="bg-light border-0 small" placeholder="Search for..." aria-label="Search" />
                    {/* The InputGroupText component does not use the input-group-append class */}
                    <div className='input-group-append'>
                        <Button color='primary' type='submit'>
                            <FaSearch className='fa-sm' />
                        </Button>
                    </div>
                </InputGroup>
            </Form>
        </>
    );
}

export default SearchForm;
