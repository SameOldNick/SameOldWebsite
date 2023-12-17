import React from 'react';
import { FaSearch } from 'react-icons/fa';
import { Form, InputGroup, Input, Button, FormProps } from 'reactstrap';

import classNames from 'classnames';

interface IProps extends FormProps {

}

const SearchForm: React.FC<IProps> = ({ className, ...props }) => {
    return (
        <>
            <Form className={classNames("navbar-search", className)} {...props}>
                <InputGroup>
                    <Input type="search" className="bg-light border-0 small" placeholder="Search for..." aria-label="Search" />
                    {/* The InputGroupText component does not use the input-group-append class */}
                    <div className='input-group-append'>
                        <Button color='primary' type='button'>
                            <FaSearch className='fa-sm' />
                        </Button>
                    </div>
                </InputGroup>
            </Form>
        </>
    );
}

export default SearchForm;
