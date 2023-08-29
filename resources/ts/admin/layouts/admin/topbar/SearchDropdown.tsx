import React from 'react';
import { FaSearch } from 'react-icons/fa';
import { Dropdown, DropdownToggle, DropdownMenu } from 'reactstrap';

import SearchForm from './SearchForm';

interface IProps {

}

const SearchDropdown: React.FC<IProps> = ({ }) => {
    const [open, setOpen] = React.useState(false);

    return (
        <>
            <Dropdown nav className='no-arrow d-sm-none' isOpen={open} toggle={() => setOpen((prevState) => !prevState)}>
                <DropdownToggle nav tag='a' href='#' id="searchDropdown">
                    <span className='position-relative'>
                        <FaSearch className='fa-fw' />
                    </span>
                </DropdownToggle>

                {/* Dropdown - Search */}
                <DropdownMenu end className='p-3 shadow animated--grow-in'>
                    <SearchForm className='me-auto w-100' />
                </DropdownMenu>
            </Dropdown>
        </>
    );
}

export default SearchDropdown;
